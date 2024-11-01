<?php
// Load WordPress environment
require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');

global $wpdb; // Access the global $wpdb object
$data = json_decode(file_get_contents('php://input'), true);

// verify the nonce before processing the rest of the form data
if (!isset($data['delete_booking_nonce']) || !wp_verify_nonce($data['delete_booking_nonce'], 'delete_booking_action')) {
    echo json_encode(['success' => false, 'message' => 'Nonce verification failed for delete operation.']);
    exit;
}

$unique_id = isset($data['unique_id']) ? sanitize_text_field($data['unique_id']) : '';
$admin_email_address = isset($data['admin_email_address']) ? sanitize_email($data['admin_email_address']) : '';
$send_admin_email = isset($data['send_admin_email']) ? sanitize_text_field($data['send_admin_email']) : 'no';

if (!empty($unique_id)) {
    $table_name = esc_sql($wpdb->prefix . 'leanwi_booking_participant');
    $sql = $wpdb->prepare("SELECT * FROM $table_name WHERE unique_id = %s", $unique_id);
    $results = $wpdb->get_results($sql, ARRAY_A);

    if (!empty($results)) {
        $row = $results[0];
        $name = esc_html($row['name']);
        $email = esc_html($row['email']);
        $start_time = $row['start_time'];
        $formatted_start_time = date('F j, Y \a\t g:ia', strtotime($start_time));

        $current_time = current_time('mysql');
        if (strtotime($start_time) < strtotime($current_time)) {
            echo wp_json_encode(['success' => false, 'message' => 'Cannot cancel booking because the start time is in the past.']);
            exit;
        }

        $wpdb->query('START TRANSACTION');
        try {
            $deleted = $wpdb->delete($table_name, ['unique_id' => $unique_id]);
            if ($deleted === false) {
                throw new Exception('Failed to delete booking.');
            }

            $wpdb->query('COMMIT');

            $to = sanitize_email($email);
            $subject = 'Your Booking Cancellation';
            $message = "<p>Hi <strong>" . esc_html($name) . "</strong>,</p>" .
                "<p>Your booking for booking ID <strong>" . esc_html($unique_id) . "</strong> scheduled to start on " . 
                esc_html($formatted_start_time) . " has been cancelled.</p>" .
                "<p>If this was done in error, please rebook.</p>" .
                "<p>If this is a surprise, check with library staff before making another booking.</p>" .
                "<p>Best regards,<br>Booking Team</p>";
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $mail_sent = wp_mail($to, $subject, $message, $headers);

            if (!$mail_sent) {
                $errorMessage = 'Cancellation successful, but failed to send email to user.';
            }

            if ($send_admin_email === 'yes' && !empty($admin_email_address) && is_email($admin_email_address)) {
                $to =  sanitize_email($admin_email_address);
                $subject = 'A booking has been cancelled!';
                $admin_message = "<p>Hello administrator, the following booking has been cancelled:<br></p> $message";
                $admin_mail_sent = wp_mail($to, $subject, $admin_message, $headers);
                if ($mail_sent && !$admin_mail_sent) { //Don't overwrite errorMessage if it's already been populated
                    $errorMessage = 'Cancellation successful, but failed to send email to administrator.';
                }
            }

            if (isset($errorMessage)) {
                echo wp_json_encode(['success' => true, 'message' => 'Booking cancelled successfully.', 'error' => $errorMessage]);
            } else {
                echo wp_json_encode(['success' => true, 'message' => 'Booking cancelled successfully.']);
            }

        } catch (Exception $e) {
            $wpdb->query('ROLLBACK');
            error_log('Booking cancellation error: ' . $e->getMessage());
            echo wp_json_encode(['success' => false, 'message' => 'Failed to cancel booking.']);
        }
    } else {
        echo wp_json_encode(['success' => false, 'message' => 'Booking not found.']);
    }

} else {
    echo wp_json_encode(['success' => false, 'message' => 'Missing required data.']);
}
?>
