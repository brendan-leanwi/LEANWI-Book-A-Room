<?php
namespace LEANWI_Book_A_Room;

add_action('wp_ajax_leanwi_get_recurrence_details', __NAMESPACE__ . '\\leanwi_get_recurrence_details');
add_action('wp_ajax_nopriv_leanwi_get_recurrence_details', __NAMESPACE__ . '\\leanwi_get_recurrence_details');

function leanwi_get_recurrence_details() {
    global $wpdb;

    // Validate input
    $recurrence_id = isset($_GET['recurrence_id']) ? intval($_GET['recurrence_id']) : 0;
    if (!$recurrence_id) {
        wp_send_json_error(['message' => 'Invalid recurrence ID.']);
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
        wp_send_json_error(['message' => 'Recurrence not found']);
    }
    
    wp_send_json_success($recurrence, JSON_UNESCAPED_SLASHES);
}