<?php
namespace LEANWI_Book_A_Room;

add_action('wp_ajax_leanwi_delete_recurrence', __NAMESPACE__ . '\\leanwi_delete_recurrence');
add_action('wp_ajax_nopriv_leanwi_delete_recurrence', __NAMESPACE__ . '\\leanwi_delete_recurrence');

function leanwi_delete_recurrence() {

    global $wpdb; // Access the global $wpdb object
    $data = json_decode(file_get_contents('php://input'), true);

    // Check if nonce is valid
    if (!isset($data['delete_recurrence_nonce']) || !wp_verify_nonce($data['delete_recurrence_nonce'], 'delete_recurrence_action')) {
        wp_send_json_error(['message' => 'Invalid form submission. Unverified Nonce.']);
    }

    $recurrence_id = isset($data['recurrence_id']) ? intval($data['recurrence_id']) : 0;

    if (!$recurrence_id) {
        wp_send_json_error(['message' => 'Invalid recurrence ID.']);
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

        wp_send_json_success(['message' => "$recurrence_deleted recurrence(s) and $participants_deleted participant(s) deleted."]);
    } catch (Exception $e) {
        // Rollback in case of error
        $wpdb->query('ROLLBACK');
        wp_send_json_error(['message' => $e->getMessage()]);
    }
}
?>
