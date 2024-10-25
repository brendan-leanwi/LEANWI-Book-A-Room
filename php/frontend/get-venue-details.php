<?php
// Load WordPress environment
require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');

global $wpdb; // Access the global $wpdb object

// Check if venue_id is set
if (isset($_GET['venue_id'])) {
    $venue_id = (int) $_GET['venue_id'];

    // Prepare the SQL query using $wpdb to prevent SQL injection
    $table_name = $wpdb->prefix . 'leanwi_booking_venue'; // Table with prefix
    $sql = $wpdb->prepare("SELECT * FROM $table_name WHERE venue_id = %d", $venue_id);

    // Execute the query
    $venue = $wpdb->get_row($sql, ARRAY_A); // Fetch a single row as an associative array

    // Check if a result was found
    if ($venue) {
        echo json_encode($venue); // Return venue details as JSON
    } else {
        echo json_encode(['error' => "No venue found with ID $venue_id"]);
    }
} else {
    echo json_encode(['error' => "Invalid venue ID"]);
}
?>