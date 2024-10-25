<?php
// Load WordPress environment to access $wpdb
require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';

global $wpdb; // Access the global $wpdb object

// Sanitize incoming POST data
$day = sanitize_text_field($_POST['day']);
$name = sanitize_text_field($_POST['name']);
$email = sanitize_email($_POST['email']);
$phone = sanitize_text_field($_POST['phone']);
$participants = intval($_POST['participants']);
$notes = sanitize_textarea_field($_POST['notes']);
$category = intval($_POST['category']);
$audience = intval($_POST['audience']);
$venue_id = intval($_POST['venue_id']);

// Get the start and end time directly from the POST data
$start_time = sanitize_text_field($_POST['start_time']); // Passed from the form
$end_time = sanitize_text_field($_POST['end_time']); // Passed from the form
$current_time = sanitize_text_field($_POST['current_time']); // Passed from the form

if (!$current_time || !strtotime($current_time) || !$end_time || !strtotime($end_time) || !$start_time || !strtotime($start_time)) {
    $success = false;
    $errorMessage = 'Invalid time format for all or either start, end and current times.';
}

$minutes_interval = isset($_POST['minutes_interval']) ? sanitize_text_field($_POST['minutes_interval']) : 30;

// Create a DateTime object for the end time and add the minutes_interval
$endDateTime = new DateTime($end_time);
$endDateTime->modify("+$minutes_interval minutes");
$adjusted_end_time = $endDateTime->format('Y-m-d H:i:s'); // Format the adjusted end time as 'YYYY-MM-DD HH:MM:SS'

$admin_email_address = isset($_POST['admin_email_address']) ? sanitize_email($_POST['admin_email_address']) : '';
$send_admin_email = isset($_POST['send_admin_email']) ? sanitize_text_field($_POST['send_admin_email']) : 'no';
$email_text = isset($_POST['email_text']) ? sanitize_text_field($_POST['email_text']) : '';
$total_cost = isset($_POST['total_cost']) && !empty($_POST['total_cost']) ? floatval(number_format((float) sanitize_text_field($_POST['total_cost']), 2, '.', '')) : 0.00;
$page_url = isset($_POST['page_url']) ? sanitize_text_field($_POST['page_url']) : '';
$venue_name = sanitize_text_field($_POST['venue_name']);
$unique_id = isset($_POST['unique_id']) ? sanitize_text_field($_POST['unique_id']) : '';

$success = true;
$errorMessage = '';
$bookingAlreadyExisted = false;

$currentDateTime = new DateTime($current_time); // Create a DateTime object for the current time
$startDateTime = new DateTime($start_time); // Create a DateTime object for the start time

// Check if the start time is in the past
if ($startDateTime < $currentDateTime) {
    $success = false;
    $errorMessage = 'The selected start time is in the past. Please choose a future time for the booking.';
}

if ($success) {
    $participant_table = $wpdb->prefix . 'leanwi_booking_participant';

    // Check if the unique_id already exists
    if (!empty($unique_id)) {
        $bookingAlreadyExisted = true;
        // Delete the existing entry if it exists for this unique_id
        $delete_result = $wpdb->delete(
            $participant_table,
            ['unique_id' => $unique_id],
            ['%s']
        );

        if ($delete_result === false) {
            $success = false;
            $errorMessage = 'Failed to remove the previous booking for unique ID: ' . $unique_id;
        }
    }
    else{
        // Generate a unique_id for the booking
        $unique_id = substr(md5(rand()), 0, 7); // Generates a random 7-character string
    }
}

// Insert booking records into leanwi_booking_participant
if ($success) {
    $insert_result = $wpdb->insert(
        $participant_table,
        [
            'unique_id'            => $unique_id,
            'venue_id'             => $venue_id,
            'name'                 => $name,
            'email'                => $email,
            'phone'                => $phone,
            'start_time'           => $start_time,
            'end_time'             => $adjusted_end_time,
            'number_of_participants' => $participants,
            'booking_notes'         => $notes,
            'category_id'          => $category,
            'audience_id'          => $audience,
            'total_cost'            => $total_cost
        ],
        ['%s', '%d', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%d', '%d', '%f']
    );

    if ($insert_result === false) {
        $success = false;
        $errorMessage = 'Failed to book time slot: ' . $time;
    }
}

// Send email if booking is successful
if ($success) {
    // Ensure the page URL ends without a trailing '/' before appending the query string
    $page_url = rtrim($page_url, '/'); // Remove the trailing '/' if it exists

    // Email details
    $to = 'btuckey@leanwi.org';// For testing purposes just use my email $email; // Send the email to the user's email address
    $subject = 'Your Booking Confirmation';
    if ($bookingAlreadyExisted) {
        $subject = 'Your Booking has been updated';
    }
    $message = "<p>Hi <strong>$name</strong>,</p>";
    if ($bookingAlreadyExisted) {
        $message .= "<p>Here are the most recent details for your updated booking. Your booking ID is: <strong>$unique_id</strong>.</p>";
    }
    else{
        $message .= "<p>Thank you for your booking. Your booking ID is: <strong>$unique_id</strong>.</p>";
    }
    $message .= "<p>You can use this ID to find and modify your booking by going to this page: " .
           "<a href='" . $page_url . "?booking_id=" . $unique_id . "'>" . $page_url . "?booking_id=" . $unique_id . "</a> " .
           "and entering the above ID.</p>";

    // Conditionally display $email_text if it has content
    if (!empty($email_text)) {
        $message .= "<p>$email_text</p>";
    }

    $message .= "<p><strong>Here are the details of your booking:</strong></p>" .
            "<p><strong>Venue:</strong> $venue_name<br>" .
            "<strong>Date:</strong> " . date('F j, Y', strtotime($start_time)) . "<br>" .
            "<strong>Start Time:</strong> " . date('g:i A', strtotime($start_time)) . "<br>" .
            "<strong>End Time:</strong> " . date('g:i A', strtotime($adjusted_end_time)) . "<br>" .
            "<strong>Number of Participants:</strong> $participants<br>" .
            "<strong>Booking Notes:</strong> " . (!empty($notes) ? $notes : 'None') . "</p>";

    // Conditionally display total cost if greater than 0.00
    if ($total_cost > 0.00) {
        $message .= "<p><strong>Total Cost:</strong> $" . number_format($total_cost, 2) . "</p>";
    }

    $message .= "<p>Best regards,<br>" .
                "Booking Team</p>";

    // Set headers to send HTML email
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

    // Send email using wp_mail
    $mail_sent = wp_mail($to, $subject, $message, $headers);

    // Handle email sending result (if necessary)
    if (!$mail_sent) {
        $errorMessage = 'Booking successful, but failed to send confirmation email to user.';
    }
    else {
        if ($send_admin_email === 'yes' && !empty($admin_email_address)){
            $to = $admin_email_address;
            $subject = 'A booking has been made!';
            if ($bookingAlreadyExisted) {
                $subject = 'A booking has been updated';
            }
            $admin_message = "<p>Hello administrator, the following booking has been made.<br></p> $message";
            $admin_mail_sent = wp_mail($to, $subject, $admin_message, $headers);
            if (!$admin_mail_sent) {
                $errorMessage = 'Booking successful, but failed to send confirmation email to administrator.';
            }
        }
    }
}

// Send JSON response back to the client
header('Content-Type: application/json');
$response = [
    'success' => $success,
    'message' => $success ? 'Booking successful!' : $errorMessage
];

echo json_encode($response);
?>