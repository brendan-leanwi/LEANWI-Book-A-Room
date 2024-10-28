<?php
// Load WordPress environment
require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');

global $wpdb; // Access the global $wpdb object

// verify the nonce before processing the rest of the form data
if (!isset($_POST['fetch_booking_nonce']) || !wp_verify_nonce($_POST['fetch_booking_nonce'], 'fetch_booking_action')) {
    echo json_encode(['success' => false, 'error' => 'Nonce verification failed.']);
    exit;
}

// Sanitize and validate inputs
$unique_id = isset($_POST['unique_id']) ? sanitize_text_field($_POST['unique_id']) : '';
$venue_id = isset($_POST['venue_id']) ? intval($_POST['venue_id']) : 0;

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
        // Validate venue_id
        if ($results[0]['venue_id'] != $venue_id) {
            echo json_encode(['error' => "The Booking ID does not belong to this venue."]);
        } else {
            // Sanitize output data with specific handling
            $safe_results = array_map(function($result) {
                return [
                    'id' => intval($result['id']),
                    'unique_id' => esc_html($result['unique_id']),
                    'venue_id' => intval($result['venue_id']),
                    'name' => esc_html($result['name']),
                    'email' => sanitize_email($result['email']),
                    'phone' => esc_html($result['phone']),
                    'start_time' => esc_html($result['start_time']), 
                    'end_time' => esc_html($result['end_time']), 
                    'number_of_participants' => intval($result['number_of_participants']),
                    'booking_notes' => esc_html($result['booking_notes']),
                    'category_id' => intval($result['category_id']),
                    'audience_id' => intval($result['audience_id']),
                    'total_cost' => floatval($result['total_cost']),
                    'category_name' => esc_html($result['category_name']),
                    'audience_name' => esc_html($result['audience_name']),
                ];
            }, $results);

            echo json_encode(['success' => true, 'data' => $safe_results]); // Include success key
        }
    } else {
        echo json_encode(['error' => "Booking not found."]);
    }
} else {
    echo json_encode(['error' => "No unique ID provided."]);
}
?>