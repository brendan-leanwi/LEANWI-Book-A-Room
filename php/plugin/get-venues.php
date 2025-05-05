<?php
// Load WordPress environment to access $wpdb
require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';

global $wpdb; // Access the global $wpdb object

// Query to get venues
$venue_table = $wpdb->prefix . 'leanwi_booking_venue';
$sql = "SELECT venue_id, name, display_order, capacity, location, historic FROM $venue_table ORDER BY display_order"; // Use the $venue_table variable
$venues = $wpdb->get_results($sql, ARRAY_A); // Fetch results as an associative array

// Function to sanitize venue data
function sanitize_venue_data($venue) {
    return [
        'venue_id'    => intval($venue['venue_id']), // Sanitize integer
        'name'        => esc_html($venue['name']), // Sanitize string
        'display_order' => intval($venue['display_order']), // Sanitize integer
        'capacity'    => intval($venue['capacity']), // Sanitize integer
        'location'    => esc_html($venue['location']), // Sanitize string
        'historic'    => intval($venue['historic']), // Sanitize boolean
    ];
}

// Sanitize each venue before returning
$sanitized_venues = array_map('sanitize_venue_data', $venues);

// Output as JSON
header('Content-Type: application/json');
echo json_encode(['venues' => $sanitized_venues]);
?>
