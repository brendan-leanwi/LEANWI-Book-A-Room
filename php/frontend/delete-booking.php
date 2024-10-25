<?php
// Load WordPress environment
require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');

global $wpdb; // Access the global $wpdb object

$data = json_decode(file_get_contents('php://input'), true);
$unique_id = isset($data['unique_id']) ? sanitize_text_field($data['unique_id']) : '';
$admin_email_address = isset($data['admin_email_address']) ? sanitize_email($data['admin_email_address']) : '';
$send_admin_email = isset($data['send_admin_email']) ? sanitize_text_field($data['send_admin_email']) : 'no';


if (!empty($unique_id)) {

    // Get details from the participant table to send in an email before we delete the record
    $table_name = $wpdb->prefix . 'leanwi_booking_participant';
    $sql = $wpdb->prepare("SELECT * FROM $table_name WHERE unique_id = %s", $unique_id);

    // Execute the query
    $results = $wpdb->get_results($sql, ARRAY_A);

    if (!empty($results)) {
        $row = $results[0]; // Assuming one result
        $name = $row['name'];
        $email = $row['email'];
        $start_time = $row['start_time'];
        // Format start_time to 'October 23, 2024 at 10:30am'
        $formatted_start_time = date('F j, Y \a\t g:ia', strtotime($start_time));

        // Check if start_time is in the past
        $current_time = current_time('mysql'); // Get current time in MySQL format
        if (strtotime($start_time) < strtotime($current_time)) {
            echo json_encode(['success' => false, 'message' => 'Cannot cancel the booking because the start time is in the past.']);
            exit;
        }
        
        // Start a transaction to ensure atomicity
        $wpdb->query('START TRANSACTION');

        try {
            // Delete all timeslots for this unique booking
            $deleted = $wpdb->delete($table_name, ['unique_id' => $unique_id]);

            if ($deleted === false) {
                throw new Exception('Failed to delete booking.');
            }

            // Commit the transaction
            $wpdb->query('COMMIT');

            // Send email to inform that the booking no longer exists
            $to = 'btuckey@leanwi.org'; // For testing purposes, replace later with $email
            $subject = 'Your Booking Cancellation';
            $message = "<p>Hi <strong>" . $name . "</strong>,</p>" .
                "<p>Your booking for booking ID <strong>$unique_id</strong> scheduled to start on " . 
                $formatted_start_time . " has been cancelled.</p>" .
                "<p>If this was done in error, please rebook.</p>" .
                "<p>If this is a surprise, check with library staff before making another booking.</p>" .
                "<p>Best regards,<br>Booking Team</p>";

            // Set headers to send HTML email
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

            // Send email using wp_mail
            $mail_sent = wp_mail($to, $subject, $message, $headers);

            if (!$mail_sent) {
                $errorMessage = 'Cancellation successful, but failed to send email to user.';
            }

            // Handle admin email
            if ($send_admin_email === 'yes' && !empty($admin_email_address)) {
                $to = $admin_email_address;
                $subject = 'A booking has been cancelled!';
                $admin_message = "<p>Hello administrator, the following booking has been cancelled:<br></p> $message";
                $admin_mail_sent = wp_mail($to, $subject, $admin_message, $headers);
                if (!$admin_mail_sent) {
                    $errorMessage = 'Cancellation successful, but failed to send email to administrator.';
                }
            }

            echo json_encode(['success' => true, 'message' => 'Booking cancelled successfully.']);

        } catch (Exception $e) {
            // Rollback the transaction if something goes wrong
            $wpdb->query('ROLLBACK');
            echo json_encode(['success' => false, 'message' => 'Failed to cancel booking: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Booking not found.']);
    }

} else {
    echo json_encode(['success' => false, 'message' => 'Missing required data.']);
}
?>
