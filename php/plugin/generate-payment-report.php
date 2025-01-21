<?php
// Include WordPress functions
require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');

// Verify the nonce before processing the form
if (!isset($_POST['leanwi_generate_report_nonce']) || !wp_verify_nonce($_POST['leanwi_generate_report_nonce'], 'leanwi_generate_report')) {
    wp_die('Nonce verification failed. Please reload the page and try again.');
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the start and end dates from the form
    $start_date = sanitize_text_field($_POST['start_date']);
    $end_date = sanitize_text_field($_POST['end_date']);

    // Format the start and end dates for the headings
    $formatted_start_date = date('F j, Y', strtotime($start_date));
    $formatted_end_date = date('F j, Y', strtotime($end_date));

    // Get checkbox values (will be 'yes' if checked, otherwise 'no')
    $include_paid = isset($_POST['include_paid']) ? 'yes' : 'no';
    $include_unpaid = isset($_POST['include_unpaid']) ? 'yes' : 'no';

    // Split the venue_info to get venue_id and name
    $venue_id = '';
    $venue_name = '';
    if (isset($_POST['venue_info']) && !empty($_POST['venue_info'])) {
        $venue_info = sanitize_text_field($_POST['venue_info']);
        list($venue_id, $venue_name) = explode('|', $venue_info);
        $venue_id = intval($venue_id);
    }

    // Ensure the start date is not after the end date
    if (strtotime($start_date) > strtotime($end_date)) {
        die('Start date cannot be after end date.');
    }
    // Ensure they have selected one or both paid and unpaid
    if ($include_paid === 'no' && $include_unpaid === 'no') {
        die('Please select at least one payment type.');
    }

    // Fetch data from the database
    global $wpdb;
    $participant_table = $wpdb->prefix . 'leanwi_booking_participant';
    $venue_table = $wpdb->prefix . 'leanwi_booking_venue'; 

    // Create a CSV file
    $upload_dir = wp_upload_dir();
    $csv_file_path = $upload_dir['basedir'] . '/leanwi_booking_reports/';
    $csv_file_url = $upload_dir['baseurl'] . '/leanwi_booking_reports/';

    // Ensure the reports folder exists
    if (!file_exists($csv_file_path)) {
        wp_mkdir_p($csv_file_path);
    }

    // Check the number of existing reports
    $report_files = glob($csv_file_path . '*.csv');
    if (count($report_files) > 100) {
        wp_die('You have reached the maximum number of saved reports (100). You will need to delete old reports before creating any new ones.');
    }

    // Generate a file name with a timestamp
    if (!empty($venue_info)) {
        $csv_filename = 'payment_report_venue_' . $venue_id . '_' . time() . '.csv';
    } else {
        $csv_filename = 'payment_report_' . time() . '.csv';
    }
    $csv_file_path .= $csv_filename;
    $csv_file_url .= $csv_filename;

    // Open the file for writing
    $file = fopen($csv_file_path, 'w');

    if ($file === false) {
        die('Could not open the file for writing.');
    }

    // Prepare the arguments for the query
    $args = [$start_date, $end_date];

    // Add summary data regardless of category or audience
    $sql = "
        SELECT 
                p.name AS 'Booker', 
                p.organization AS 'Organization', 
                v.name AS 'Venue', 
                p.start_time AS 'Start Date/Time',
                p.total_cost AS 'Total Cost',
                CASE 
                    WHEN p.has_paid = 1 THEN 'PAID'
                    WHEN p.has_paid = 0 THEN 'UNPAID'
                    ELSE ' '
                END AS 'Paid?'
            FROM {$participant_table} p
            JOIN {$venue_table} v ON p.venue_id = v.venue_id
            WHERE p.start_time BETWEEN %s AND %s
            AND p.total_cost > 0
    ";

    if ($include_paid === 'yes' && $include_unpaid === 'no') {
        $sql .= " AND p.has_paid = 1";
    }
    else if ($include_unpaid === 'yes' && $include_paid === 'no') {
        $sql .= " AND p.has_paid = 0";
    }

    $results = $wpdb->get_results(
        $wpdb->prepare($sql, ...$args),
        ARRAY_A
    );

    if (!empty($results)) {
        // Add heading for the summary section
        fputcsv($file, [' ']);
        if (!empty($venue_info)) {
            fputcsv($file, ["REPORT FOR VENUE: $venue_name"]);
            fputcsv($file, [' ']);
        }

        fputcsv($file, ['~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~']);
        fputcsv($file, ["Payment Report - $formatted_start_date To $formatted_end_date"]);
        
        // Add column headers to the CSV file
        fputcsv($file, array_keys($results[0]));
        
        // Add data rows to the CSV file
        foreach ($results as $row) {
            // Format the 'Total Cost' field
            $row['Total Cost'] = '$' . number_format($row['Total Cost'], 2);

            // Add data rows to the CSV file
            fputcsv($file, $row); 
        }
    }
    
    fputcsv($file, [' ']);
    fputcsv($file, ['~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~']);

    fclose($file);

    // Redirect to the CSV file for download
    header('Location: ' . $csv_file_url);
    exit;
}
?>
