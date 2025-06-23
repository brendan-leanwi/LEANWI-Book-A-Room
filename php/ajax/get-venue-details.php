<?php
namespace LEANWI_Book_A_Room;

add_action('wp_ajax_leanwi_get_venue_details', __NAMESPACE__ . '\\leanwi_get_venue_details');
add_action('wp_ajax_nopriv_leanwi_get_venue_details', __NAMESPACE__ . '\\leanwi_get_venue_details');

function leanwi_get_venue_details() {
    global $wpdb;

    $venue_id = isset($_GET['venue_id']) ? intval($_GET['venue_id']) : 0;

    if ($venue_id === 0) {
        wp_send_json_error(['message' => 'Invalid venue ID']);
    }

    $table_name = $wpdb->prefix . 'leanwi_booking_venue';
    $sql = $wpdb->prepare("SELECT * FROM $table_name WHERE venue_id = %d", $venue_id);
    $venue = $wpdb->get_row($sql, ARRAY_A);

    if (!$venue) {
        wp_send_json_error(['message' => "No venue found with ID $venue_id"]);
    }

    $sanitized_venue = [
        'venue_id' => intval($venue['venue_id']),
        'name' => sanitize_text_field($venue['name']),
        'capacity' => intval($venue['capacity']),
        'description' => sanitize_textarea_field($venue['description']),
        'location' => sanitize_text_field($venue['location']),
        'max_slots' => intval($venue['max_slots']),
        'slot_cost' => floatval($venue['slot_cost']),
        'image_url' => esc_url($venue['image_url']),
        'page_url' => esc_url($venue['page_url']),
        'conditions_of_use_url' => esc_url($venue['conditions_of_use_url']),
        'display_affirmations' => intval($venue['display_affirmations']),
        'extra_text' => sanitize_textarea_field($venue['extra_text']),
        'booking_notes_label' => !empty($venue['booking_notes_label']) ? sanitize_text_field($venue['booking_notes_label']) : '',
        'historic' => intval($venue['historic']),
        'days_before_booking' => intval($venue['days_before_booking']),
        'venue_admin_email' => sanitize_email($venue['venue_admin_email'] ?? ''),
        'use_business_days_only' => intval($venue['use_business_days_only']),
        'bookable_by_staff_only' => intval($venue['bookable_by_staff_only']),
        'updated_by_staff_only' => intval($venue['updated_by_staff_only']),
    ];

    wp_send_json_success($sanitized_venue);
}
