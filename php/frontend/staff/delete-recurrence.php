<?php

// Include WordPress functions
require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');

global $wpdb; // Access the global $wpdb object
$data = json_decode(file_get_contents('php://input'), true);

// Check if nonce is valid
if (!isset($data['delete_recurrence_nonce']) || !wp_verify_nonce($data['delete_recurrence_nonce'], 'delete_recurrence_action')) {
    $response['message'] = 'Invalid form submission. Unverified Nonce.';
    echo json_encode($response);
    exit;
}

$recurrence_id = isset($data['recurrence_id']) ? intval($data['recurrence_id']) : 0;

if (!$recurrence_id) {
    echo json_encode(['success' => false, 'error' => 'Invalid recurrence ID.']);
    exit;
}

try {
    // Start a transaction
    $wpdb->query('START TRANSACTION');

    // Delete from leanwi_booking_recurrence table
    $recurrence_deleted = $wpdb->delete(
        "{$wpdb->prefix}leanwi_booking_recurrence",
        ['recurrence_id' => $recurrence_id],
        ['%d']
    );

    // Delete from leanwi_booking_participant table
    $current_date = current_time('mysql'); // Get current date and time
    $participants_deleted = $wpdb->query(
        $wpdb->prepare(
            "DELETE FROM {$wpdb->prefix}leanwi_booking_participant 
             WHERE recurrence_id = %d 
             AND start_time > %s",
            $recurrence_id,
            $current_date
        )
    );

    // Commit the transaction
    $wpdb->query('COMMIT');

    echo json_encode([
        'success' => true,
        'message' => "$recurrence_deleted recurrence(s) and $participants_deleted participant(s) deleted."
    ]);
} catch (Exception $e) {
    // Rollback in case of error
    $wpdb->query('ROLLBACK');
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);

    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log('JSON Decode Error: ' . json_last_error_msg());
        echo json_encode(['success' => false, 'error' => 'Invalid JSON received.']);
        exit;
    }
}

exit;
?>
