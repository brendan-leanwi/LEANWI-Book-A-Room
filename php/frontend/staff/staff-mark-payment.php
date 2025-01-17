<?php

// Load WordPress environment
require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');

// Check if it's an AJAX request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the JSON payload
    $input = json_decode(file_get_contents('php://input'), true);
    $booking_id = sanitize_text_field($input['booking_id']);
    $new_status = isset($input['new_status']) ? (int) $input['new_status'] : null;

    if ($new_status === null) {
        echo json_encode(['success' => false, 'message' => 'Invalid payment status.']);
        exit;
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
        echo json_encode(['success' => true, 'message' => $message]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update payment status.']);
    }
    exit;
}

// If not a POST request, return an error
echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
exit;