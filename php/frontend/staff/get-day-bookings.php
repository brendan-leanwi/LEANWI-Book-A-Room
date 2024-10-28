<?php
// Load WordPress environment
require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');

global $wpdb; // Access the global $wpdb object

// Retrieve the venue_id and date from the GET request with sanitization
$venue_id = isset($_GET['venue_id']) ? intval($_GET['venue_id']) : '';
$date = isset($_GET['date']) ? sanitize_text_field($_GET['date']) : '';

if (!empty($venue_id) && !empty($date)) {
    // Get the prefixed participant table name
    $participant_table = $wpdb->prefix . 'leanwi_booking_participant';

    // Prepare SQL query to retrieve bookings for the specified date and venue
    $sql = $wpdb->prepare("
        SELECT *
        FROM $participant_table
        WHERE venue_id = %d
        AND DATE(start_time) = %s
        ORDER BY start_time
    ", $venue_id, $date);

    // Execute the query and get the results
    $results = $wpdb->get_results($sql, ARRAY_A);

    if (!empty($results)) {
        // Sanitize results before outputting
        foreach ($results as &$row) {
            $row['name'] = esc_html($row['name']); // Sanitize name
            $row['email'] = sanitize_email($row['email']); // Sanitize email
            $row['phone'] = esc_html($row['phone']); // Sanitize phone
            $row['booking_notes'] = esc_html($row['booking_notes']); // Sanitize booking notes
            $row['start_time'] = esc_html($row['start_time']); // Escape date
            $row['end_time'] = esc_html($row['end_time']); // Escape date
            $row['number_of_participants'] = intval($row['number_of_participants']); // Cast to integer
            $row['total_cost'] = floatval($row['total_cost']); // Cast to float
        }
        echo wp_json_encode(['success' => true, 'data' => $results]);
    } else {
        echo wp_json_encode(['error' => "No bookings found for this venue on the specified date."]);
    }
} else {
    echo wp_json_encode(['error' => "Missing venue ID or date."]);
}
?>
