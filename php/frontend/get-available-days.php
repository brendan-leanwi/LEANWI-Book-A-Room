<?php
// Load WordPress environment to access $wpdb
require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';

// Get venue_id from the query string
if (!isset($_GET['venue_id']) || empty($_GET['venue_id'])) {
    // Return error response
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Error: venue_id is required.'
    ]);
    exit; // Stop further script execution
}

// Sanitize and convert venue_id to an integer
$venue_id = intval($_GET['venue_id']);
$selected_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d'); // Default to today if not provided

// Validate venue_id and date (optional but recommended)
if ($venue_id <= 0 || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $selected_date)) {
    echo json_encode([]);
    exit;
}

global $wpdb; // Access the global $wpdb object

// Fetch available days from the database using $wpdb
$table_name = $wpdb->prefix . 'leanwi_booking_venue_hours'; // Add the table prefix

// Prepare and execute the query to prevent SQL injection
$sql = $wpdb->prepare("SELECT day_of_week, open_time, close_time FROM $table_name WHERE venue_id = %d", $venue_id);
$available_days = $wpdb->get_results($sql, ARRAY_A);

// Output available days as JSON
header('Content-Type: application/json');
echo json_encode($available_days);
?>
