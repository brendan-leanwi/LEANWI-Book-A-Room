<?php
// Load WordPress environment
require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');

// Check if it's an AJAX request
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $_SERVER['CONTENT_TYPE'] !== 'application/json') {
    // If not a POST request, return an error
    echo json_encode(['success' => false, 'message' => 'Invalid request method or content type.']);
    exit;
}
// Get the JSON payload
$input = json_decode(file_get_contents('php://input'), true);
header('Content-Type: application/json; charset=utf-8');

$unique_id = sanitize_text_field($input['booking_id']);
$nonce = isset($input['nonce']) ? sanitize_text_field($input['nonce']) : '';

$new_status = 1;
$success = true;

$email_from_name = get_option('leanwi_email_from_name', 'Library Booking Team');

//Get the URL for the feedback form - likely a google form
$feedback_form_url = esc_url(get_option('leanwi_feedback_form_link', ''));
if (empty($feedback_form_url)) {
    echo json_encode(['success' => false, 'message' => 'Could not find a feedback form to link to']);
    exit;
}

// Verify the nonce
if (!wp_verify_nonce($nonce, 'mark_payment_nonce')) {
    echo json_encode(['success' => false, 'message' => 'Invalid nonce.']);
    exit;
}

global $wpdb;
$participant_table = $wpdb->prefix . 'leanwi_booking_participant';
$venue_table = $wpdb->prefix . 'leanwi_booking_venue';

//Get some values we need from the participant table
if (empty($unique_id)) {
    echo json_encode(['success' => false, 'message' => 'Booking ID is required.']);
    exit;
}

// Prepare SQL statement using $wpdb to get booking and user details
$sql = $wpdb->prepare("
    SELECT p.name, p.email, p.start_time, p.total_cost, v.name AS venue_name
    FROM $participant_table p
    JOIN $venue_table v ON p.venue_id = v.venue_id
    WHERE p.unique_id = %s
", $unique_id);

// Execute the query
$results = $wpdb->get_results($sql, ARRAY_A);
$result = [];

if (!empty($results)) {
    $result = $results[0]; // only expect one result
} else {
    $success = false;
}

//Construct the email
if ($success) {

    // Email details
    $email = sanitize_email($result['email']);
    $to = $email;
    $subject = "We'd appreciate your feedback";
    
    $message = "<p>Hi <strong>" . esc_html($result['name']) . "</strong>,</p>" .
    "<p>Thank you for booking one of our rooms recently. We hope our facility met your approval. " .
    "If you'd like to share feedback with us about the meeting room, you can do so using this " .
    "<a href='" . esc_url($feedback_form_url) . "'>" . "online form</a>.</p>";

    $message .= "<p><strong>Here are the details of your booking:</strong></p>" .
            "<p><strong>Venue:</strong> " . esc_html($result['venue_name']) . "<br>" .
            "<strong>Date:</strong> " . date('F j, Y', strtotime($result['start_time'])) . "<br>" .
            "<strong>Start Time:</strong> " . date('g:i A', strtotime($result['start_time'])) . "<br>";

    // Conditionally display total cost if greater than 0.00
    if (!empty($result['total_cost']) && floatval($result['total_cost']) > 0) {
        $message .= "<p><strong>Total Cost:</strong> $" . number_format(floatval($result['total_cost']), 2) . " (Please pay promptly if you have not already done so)</p>";
    }

    $message .= "<p>Thank you again!</p>" .
                "<p>" . $email_from_name . "</p>";

    // Set headers to send HTML email
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

    //Send the email
    if (!is_email($email)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email address.']);
        exit;
    }
    
    // Send email using wp_mail
    $mail_sent = wp_mail($to, $subject, $message, $headers);

    if (!$mail_sent) {
        echo json_encode(['success' => false, 'message' => 'Failed to send email. Please try again later.']);
        exit;
    }

    // Update the feedback_request_sent field in the participant table
    $updated = $wpdb->update(
        $participant_table,
        ['feedback_request_sent' => $new_status], // Toggle payment status
        ['unique_id' => $unique_id], // Where condition
        ['%d'],
        ['%s']
    );

    if ($updated === false) {
        echo json_encode(['success' => false, 'message' => 'Failed to send feedback request email.']);
    } else {
        $message = 'Feedback request email sent successfully.';
        echo json_encode(['success' => true, 'message' => $message]);
    } 
    exit;
}
else {
    echo json_encode(['success' => false, 'message' => 'Failed to find booking. No email was sent.']);
}

