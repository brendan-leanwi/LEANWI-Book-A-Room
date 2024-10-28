<?php
// Include WordPress functions
require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the start and end dates from the form
    $start_date = sanitize_text_field($_POST['start_date']);
    $end_date = sanitize_text_field($_POST['end_date']);

    // Format the start and end dates for the headings
    $formatted_start_date = date('F j, Y', strtotime($start_date));
    $formatted_end_date = date('F j, Y', strtotime($end_date));

    // Get checkbox values (will be 'yes' if checked, otherwise 'no')
    $include_category = isset($_POST['include_category']) ? 'yes' : 'no';
    $include_audience = isset($_POST['include_audience']) ? 'yes' : 'no';

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

    // Fetch data from the database
    global $wpdb;
    $participant_table = $wpdb->prefix . 'leanwi_booking_participant';  
    $audience_table = $wpdb->prefix . 'leanwi_booking_audience';
    $category_table = $wpdb->prefix . 'leanwi_booking_category'; 

    // Create a CSV file
    $upload_dir = wp_upload_dir();
    $csv_file_path = $upload_dir['basedir'] . '/leanwi_reports/';
    $csv_file_url = $upload_dir['baseurl'] . '/leanwi_reports/';

    // Ensure the reports folder exists
    if (!file_exists($csv_file_path)) {
        wp_mkdir_p($csv_file_path);
    }

    // Generate a file name with a timestamp
    if (!empty($venue_info)) {
        $csv_filename = 'report_venue_' . $venue_id . '_' . time() . '.csv';
    } else {
        $csv_filename = 'report_' . time() . '.csv';
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
            COUNT(bp.id) AS 'Total Bookings', 
            SUM(bp.number_of_participants) AS 'Total Participants',
            SUM(TIMESTAMPDIFF(MINUTE, bp.start_time, bp.end_time)) AS 'Total Time',
            SUM(bp.total_cost) AS 'Total Income'
        FROM 
            $participant_table bp
        WHERE 
            DATE(bp.start_time) BETWEEN %s AND %s
    ";
    if (!empty($venue_info)) {
        $sql .= " AND bp.venue_id = %d";
        $args[] = $venue_id;
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
        fputcsv($file, ["Summary of all Data - $formatted_start_date To $formatted_end_date"]);
        
        // Add column headers to the CSV file
        fputcsv($file, array_keys($results[0]));

        // Format the 'Total Income' field
        $results[0]['Total Income'] = '$' . number_format($results[0]['Total Income'], 2);

        // Add data rows to the CSV file
        fputcsv($file, $results[0]); // Only one row for summary
    }

    // Include audience data if checked
    if ($include_audience === 'yes') {
        $sql = "
            SELECT 
                ba.audience_name AS 'Audience Name',
                COUNT(bp.id) AS 'Total Bookings', 
                SUM(bp.number_of_participants) AS 'Total Participants',
                SUM(TIMESTAMPDIFF(MINUTE, bp.start_time, bp.end_time)) AS 'Total Time',
                SUM(bp.total_cost) AS 'Total Income'
            FROM 
                $participant_table bp
            JOIN 
                $audience_table ba ON bp.audience_id = ba.audience_id
            WHERE 
                DATE(bp.start_time) BETWEEN %s AND %s
            AND ba.audience_id > 1
        ";
        if (!empty($venue_info)) {
            $sql .= " AND bp.venue_id = %d";
        }
        $sql .= " GROUP BY ba.audience_name";

        $results = $wpdb->get_results(
            $wpdb->prepare($sql, ...$args),
            ARRAY_A
        );

        if (!empty($results)) {
            // Add heading for the summary section
            fputcsv($file, [' ']);
            fputcsv($file, ['~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~']);
            fputcsv($file, ["Summary of Data by Audience - $formatted_start_date To $formatted_end_date"]);

            // Add column headers to the CSV file
            fputcsv($file, array_keys($results[0]));

            // Add data rows to the CSV file
            foreach ($results as $row) {
                $row['Total Income'] = '$' . number_format($row['Total Income'], 2); // Format as currency
                fputcsv($file, $row);
            }
        }
    }

    // Include category data if checked
    if ($include_category === 'yes') {
        $sql = "
            SELECT 
                bc.category_name AS 'Category Name',
                COUNT(bp.id) AS 'Total Bookings', 
                SUM(bp.number_of_participants) AS 'Total Participants',
                SUM(TIMESTAMPDIFF(MINUTE, bp.start_time, bp.end_time)) AS 'Total Time',
                SUM(bp.total_cost) AS 'Total Income'
            FROM 
                $participant_table bp
            JOIN 
                $category_table bc ON bp.category_id = bc.category_id
            WHERE 
                DATE(bp.start_time) BETWEEN %s AND %s
            AND bc.category_id > 1
        ";
        if (!empty($venue_info)) {
            $sql .= " AND bp.venue_id = %d";
        }
        $sql .= " GROUP BY bc.category_name";
        $results = $wpdb->get_results(
            $wpdb->prepare($sql, ...$args),
            ARRAY_A
        );

        if (!empty($results)) {
            // Add heading for the summary section
            fputcsv($file, [' ']);
            fputcsv($file, ['~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~']);
            fputcsv($file, ["Summary of Data by Category - $formatted_start_date To $formatted_end_date"]);

            // Add column headers to the CSV file
            fputcsv($file, array_keys($results[0]));

            // Add data rows to the CSV file
            foreach ($results as $row) {
                $row['Total Income'] = '$' . number_format($row['Total Income'], 2); // Format as currency
                fputcsv($file, $row);
            }
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
