<?php
// Load WordPress environment
require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');

global $wpdb; // Access the global $wpdb object

// Check if venue_id is set
if (isset($_GET['venue_id'])) {
    $venue_id = (int) $_GET['venue_id'];

    // Prepare the SQL query using $wpdb to prevent SQL injection
    $table_name = $wpdb->prefix . 'leanwi_booking_venue';
    $sql = $wpdb->prepare("SELECT * FROM $table_name WHERE venue_id = %d", $venue_id);

    // Execute the query
    $venue = $wpdb->get_row($sql, ARRAY_A); // Fetch a single row as an associative array

    // Check if a result was found and sanitize data
    if ($venue) {
        // Sanitize data before outputting
        $sanitized_venue = [
            'venue_id' => intval($venue['venue_id']),
            'name' => sanitize_text_field($venue['name']),
            'capacity' => intval($venue['capacity']),
            'description' => sanitize_textarea_field($venue['description']), // Allows safe HTML tags
            'location' => sanitize_text_field($venue['location']),
            'max_slots' => intval($venue['max_slots']),
            'slot_cost' => floatval($venue['slot_cost']),
            'image_url' => esc_url($venue['image_url']),
            'page_url' => esc_url($venue['page_url']),
            'conditions_of_use_url' => esc_url($venue['conditions_of_use_url']),
            'display_affirmations' => intval($venue['display_affirmations']),
            'extra_text' => sanitize_textarea_field($venue['extra_text']),
            'email_text' => sanitize_textarea_field($venue['email_text']),
            'booking_notes_label' => !empty($venue['booking_notes_label']) ? sanitize_text_field($venue['booking_notes_label']) : '',
            'historic' => intval($venue['historic']),
            'days_before_booking' => intval($venue['days_before_booking']),
            'venue_admin_email' => sanitize_email($venue['venue_admin_email'] ?? ''),
            'use_business_days_only' => intval($venue['use_business_days_only']),
            'bookable_by_staff_only' => intval($venue['bookable_by_staff_only'])
        ];

        echo json_encode($sanitized_venue, JSON_UNESCAPED_SLASHES); // Return sanitized venue details as JSON
    } else {
        echo json_encode(['error' => "No venue found with ID $venue_id"]);
    }
} else {
    echo json_encode(['error' => "Invalid venue ID"]);
}
?>
