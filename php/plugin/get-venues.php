<?php
// Load WordPress environment to access $wpdb
require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';

global $wpdb; // Access the global $wpdb object

// Query to get venues
$venue_table = $wpdb->prefix . 'leanwi_booking_venue';
$sql = "SELECT * FROM $venue_table"; // Use the $venue_table variable
$venues = $wpdb->get_results($sql, ARRAY_A); // Fetch results as an associative array

// Output as JSON
header('Content-Type: application/json');
echo json_encode(['venues' => $venues]);
?>
