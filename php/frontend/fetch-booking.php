<?php
// Load WordPress environment
require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');

global $wpdb; // Access the global $wpdb object

$unique_id = $_POST['unique_id'];
$venue_id = $_POST['venue_id'];

if (!empty($unique_id)) {
    // Get the prefixed table names
    $participant_table = $wpdb->prefix . 'leanwi_booking_participant';
    $category_table = $wpdb->prefix . 'leanwi_booking_category';
    $audience_table = $wpdb->prefix . 'leanwi_booking_audience';

    // Prepare SQL statement using $wpdb to get booking and user details
    $sql = $wpdb->prepare("
        SELECT bp.*, bc.category_name, ba.audience_name
        FROM $participant_table bp
        JOIN $category_table bc ON bp.category_id = bc.category_id
        JOIN $audience_table ba ON bp.audience_id = ba.audience_id
        WHERE bp.unique_id = %s
    ", $unique_id);

    // Execute the query
    $results = $wpdb->get_results($sql, ARRAY_A);

    if (!empty($results)) {
        // Check if the venue_id matches
        if ($results[0]['venue_id'] != $venue_id) {
            echo json_encode(['error' => "The Booking ID does not belong to this venue."]);
        } else {
            echo json_encode(['success' => true, 'data' => $results]); // Include success key
        }
    } else {
        echo json_encode(['error' => "Booking not found."]);
        
    }
} else {
    echo json_encode(['error' => "No unique ID provided."]);
}
?>
