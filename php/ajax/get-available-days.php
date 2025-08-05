<?php
namespace LEANWI_Book_A_Room;

add_action('wp_ajax_leanwi_get_available_days', __NAMESPACE__ . '\\leanwi_get_available_days');
add_action('wp_ajax_nopriv_leanwi_get_available_days', __NAMESPACE__ . '\\leanwi_get_available_days');

function leanwi_get_available_days() {
    // Get venue_id from the query string and sanitize it
    if (!isset($_GET['venue_id']) || empty($_GET['venue_id'])) {
        // Return error response
        wp_send_json_error(['message' => 'Error: venue_id is required.(get_available_days)']);
    }

    // Sanitize and validate venue_id
    $venue_id = intval($_GET['venue_id']);
    //$selected_date = isset($_GET['date']) ? sanitize_text_field($_GET['date']) : date('Y-m-d'); // Default to today if not provided

    // Validate venue_id and date format
    if ($venue_id <= 0){// || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $selected_date)) {
       wp_send_json_error(['message' => 'Error: invalid venue_id']);
    }

    global $wpdb; // Access the global $wpdb object

    // Fetch available days from the database
    $table_name = $wpdb->prefix . 'leanwi_booking_venue_hours'; // Add the table prefix

    $days_closed = $wpdb->get_col($wpdb->prepare("
        SELECT DISTINCT start_date FROM {$wpdb->prefix}leanwi_booking_venue_closings
        WHERE start_date = end_date
        AND start_time = '00:00:00' AND end_time = '23:59:00'
        AND (%d = -1 OR venue_id = %d OR venue_id = -1)
    ", $venue_id, $venue_id));

    // Prepare and execute the query safely using $wpdb
    $sql = $wpdb->prepare("SELECT day_of_week, open_time, close_time FROM $table_name WHERE venue_id = %d", $venue_id);
    $available_days = $wpdb->get_results($sql, ARRAY_A);

    // Escape output data for additional security
    foreach ($available_days as &$day) {
        $day['day_of_week'] = esc_html($day['day_of_week']);
        $day['open_time'] = esc_html($day['open_time']);
        $day['close_time'] = esc_html($day['close_time']);
    }

    // Return data as JSON
    wp_send_json_success([
        'available_days' => $available_days,
        'closure_dates' => $days_closed
    ]);

}
?>
