<?php
// Load WordPress environment
require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');

global $wpdb; // Access the global $wpdb object

// Retrieve the venue_id and date from the GET request
$venue_id = $_GET['venue_id'];
$date = $_GET['date'];

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
        echo json_encode(['success' => true, 'data' => $results]);
    } else {
        echo json_encode(['error' => "No bookings found for this venue on the specified date."]);
    }
} else {
    echo json_encode(['error' => "Missing venue ID or date."]);
}
?>
