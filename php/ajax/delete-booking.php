<?php
namespace LEANWI_Book_A_Room;

add_action('wp_ajax_leanwi_delete_booking', __NAMESPACE__ . '\\leanwi_delete_booking');
add_action('wp_ajax_nopriv_leanwi_delete_booking', __NAMESPACE__ . '\\leanwi_delete_booking');

function leanwi_delete_booking() {
    global $wpdb; // Access the global $wpdb object
    $data = json_decode(file_get_contents('php://input'), true);

    // verify the nonce before processing the rest of the form data
    if (!isset($data['delete_booking_nonce']) || !wp_verify_nonce($data['delete_booking_nonce'], 'delete_booking_action')) {
        wp_send_json_error(['message' => 'Nonce verification failed for delete operation.']);
    }

    $venue_id = isset($data['venue_id']) ? intval($data['venue_id']) : 0;
    $isBookingStaff = isset($data['is_booking_staff']) && filter_var($data['is_booking_staff'], FILTER_VALIDATE_BOOLEAN);
    $unique_id = isset($data['unique_id']) ? sanitize_text_field($data['unique_id']) : '';
    $admin_email_address = isset($data['admin_email_address']) ? sanitize_email($data['admin_email_address']) : '';
    $send_admin_email = isset($data['send_admin_email']) ? sanitize_text_field($data['send_admin_email']) : 'no';
    $cancellation_reason = isset($data['cancellation_reason']) ? sanitize_text_field($data['cancellation_reason']) : '';

    $venue_admin_email = isset($data['venue_admin_email']) ? sanitize_email($data['venue_admin_email']) : '';

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
            if (!$isBookingStaff && strtotime($start_time) < strtotime($current_time)) {
                wp_send_json_error(['message' => 'Cannot cancel booking because the start time is in the past. Please contact the library if this booking needs to be cancelled.']);
            }

            $wpdb->query('START TRANSACTION');
            try {
                $deleted = $wpdb->delete($table_name, ['unique_id' => $unique_id]);
                if ($deleted === false) {
                    throw new \Exception('Failed to delete booking.');
                }

                $wpdb->query('COMMIT');

                $email_data_table = $wpdb->prefix . 'leanwi_booking_venue';
                $email_data = $wpdb->get_row(
                    $wpdb->prepare(
                        "SELECT email_greeting, email_sign_off_text FROM $email_data_table WHERE venue_id = %d",
                        $venue_id
                    )
                );

                $to = sanitize_email($email);
                $subject = 'Your Booking has been cancelled' .  ($isBookingStaff ? ' by library staff' : '.');
                $message = "<p>" . $email_data->email_greeting . " <strong>" . esc_html($name) . "</strong>,</p>" .
                    "<p>Your booking for booking ID <strong>" . esc_html($unique_id) . "</strong> scheduled to start on " . 
                    esc_html($formatted_start_time) . ($isBookingStaff ? " has been cancelled by a member of our staff.</p>" : " has been cancelled.</p>");
                if(!empty($cancellation_reason)) {
                    $message .= "<p>REASON GIVEN: " . esc_html($cancellation_reason) . "</p>";
                }
                $message .= "<p>If this was done in error, please rebook.</p>" .
                    "<p>If you are unsure as to the reason please contact library staff before making another booking.</p>" .
                    "<p>" . nl2br(esc_html($email_data->email_sign_off_text)) . "</p>";

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

                if (!empty($venue_admin_email) && is_email($venue_admin_email)){
                    $to = sanitize_email($venue_admin_email);
                    $subject = 'A booking has been cancelled!';
                    $admin_message = "<p>Hello venue administrator, the following booking has been cancelled:<br></p> $message";
                    $admin_mail_sent = wp_mail($to, $subject, $admin_message, $headers);
                    if ($mail_sent && !$admin_mail_sent) {
                        $errorMessage .= 'Cancellation successful, but failed to send email to venue administrator.';
                    }
                }

                if (isset($errorMessage)) {
                    wp_send_json_success(['message' => $errorMessage ? $errorMessage : 'Booking cancelled successfully.']);
                } else {
                    wp_send_json_success(['message' => 'Booking cancelled successfully.']);
                }

            } catch (Exception $e) {
                $wpdb->query('ROLLBACK');
                error_log('Booking cancellation error: ' . $e->getMessage());
                wp_send_json_error(['message' => 'Failed to cancel booking.']);
            }
        } else {
            wp_send_json_error(['message' => 'Booking not found.']);
        }

    } else {
        wp_send_json_error(['message' => 'Missing required data.']);
    }
}
?>
