<?php
namespace LEANWI_Book_A_Room;

add_action('wp_ajax_leanwi_staff_mark_payment', __NAMESPACE__ . '\\leanwi_staff_mark_payment');
add_action('wp_ajax_nopriv_leanwi_staff_mark_payment', __NAMESPACE__ . '\\leanwi_staff_mark_payment');

function leanwi_staff_mark_payment() {
    // Check if it's an AJAX request
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get the JSON payload
        $input = json_decode(file_get_contents('php://input'), true);
        $booking_id = sanitize_text_field($input['booking_id']);
        $new_status = isset($input['new_status']) ? (int) $input['new_status'] : null;
        $nonce = isset($input['nonce']) ? $input['nonce'] : '';

        // Verify the nonce
        if (!wp_verify_nonce($nonce, 'mark_payment_nonce')) {
            
            wp_send_json_error(['message' => 'Invalid nonce in mark payment']);
        }

        if ($new_status === null) {
            wp_send_json_error(['message' => 'Invalid payment status.']);
        }

        global $wpdb;
        $table = $wpdb->prefix . 'leanwi_booking_participant';

        // Update the has_paid field
        $updated = $wpdb->update(
            $table,
            ['has_paid' => $new_status], // Toggle payment status
            ['unique_id' => $booking_id], // Where condition
            ['%d'],
            ['%s']
        );

        if ($updated !== false) {
            $message = $new_status == 1 ? 'Booking marked as paid.' : 'Booking marked as unpaid.';
            wp_send_json_success(['message' => $message]);
        } else {
            wp_send_json_error(['message' => 'Failed to update payment status.']);
        }
    }

    // If not a POST request, return an error
    wp_send_json_error(['message' => 'Invalid request method.']);
}