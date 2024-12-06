<?php
// Load WordPress environment
require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');

global $wpdb;

// Validate input
$recurrence_id = isset($_GET['recurrence_id']) ? intval($_GET['recurrence_id']) : 0;
if (!$recurrence_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid recurrence ID']);
    exit;
}

// Query the database
$table_recurrence = $wpdb->prefix . 'leanwi_booking_recurrence';
$table_venue = $wpdb->prefix . 'leanwi_booking_venue';

$sql = $wpdb->prepare("
    SELECT 
        r.*,
        v.name AS venue_name
    FROM $table_recurrence r
    LEFT JOIN $table_venue v ON r.venue_id = v.venue_id
    WHERE r.recurrence_id = %d
", $recurrence_id);

$recurrence = $wpdb->get_row($sql, ARRAY_A);

// Validate and sanitize data
if (!$recurrence) {
    echo json_encode(['success' => false, 'message' => 'Recurrence not found']);
    exit;
}

$recurrence = array_map('esc_html', $recurrence);
echo json_encode($recurrence);
