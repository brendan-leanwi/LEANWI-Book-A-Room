<?php
// Load WordPress environment
require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';

global $wpdb;

// Initialize the response
$response = [
    'success' => false,
    'message' => 'An unexpected error occurred.',
];

// Check if nonce is valid
if (!isset($_POST['submit_recurrence_nonce']) || !wp_verify_nonce($_POST['submit_recurrence_nonce'], 'submit_recurrence_action')) {
    $response['message'] = 'Invalid form submission. Unverified Nonce.';
    echo json_encode($response);
    exit;
}

// Collect and sanitize input data
$venue_id = isset($_POST['venue_id']) ? intval($_POST['venue_id']) : 0;
$recurrence_type = isset($_POST['recurrence_type']) ? sanitize_text_field($_POST['recurrence_type']) : '';
$recurrence_interval = isset($_POST['recurrence_interval']) ? intval($_POST['recurrence_interval']) : 1;
$recurrence_start_date = isset($_POST['recurrence_start_date']) ? sanitize_text_field($_POST['recurrence_start_date']) : '';
$recurrence_end_date = isset($_POST['recurrence_end_date']) ? sanitize_text_field($_POST['recurrence_end_date']) : '';
$recurrence_day_of_week = isset($_POST['recurrence_day_of_week']) ? intval($_POST['recurrence_day_of_week']) : null;
$recurrence_week_of_month = isset($_POST['recurrence_week_of_month']) ? intval($_POST['recurrence_week_of_month']) : null;
$start_time = isset($_POST['start_time']) ? sanitize_text_field($_POST['start_time']) : '';
$end_time = isset($_POST['end_time']) ? sanitize_text_field($_POST['end_time']) : '';
$name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
$organization = isset($_POST['organization']) ? sanitize_text_field($_POST['organization']) : null;
$email = isset($_POST['email']) ? sanitize_email($_POST['email']) : null;
$phone = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : null;
$participants = isset($_POST['participants']) ? intval($_POST['participants']) : 0;
$notes = isset($_POST['notes']) ? sanitize_textarea_field($_POST['notes']) : null;
$category_id = isset($_POST['category']) ? intval($_POST['category']) : null;
$audience_id = isset($_POST['audience']) ? intval($_POST['audience']) : null;

$cost = 0.00; //Do I need to add anything for this? Like add a cost field on my form the staff can just enter a cost amount into?

// Validate required fields
if (!$venue_id || !$recurrence_type || !$recurrence_start_date || !$recurrence_end_date || !$start_time || !$end_time || !$name || $participants <= 0) {
    $response['message'] = 'Please fill out all required fields.';
    echo json_encode($response);
    exit;
}

// Validate and sanitize the recurrence start date
if (empty($recurrence_start_date) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $recurrence_start_date)) {
    $response['message'] = 'Invalid start date format.';
    echo json_encode($response);
    exit;
}
// Validate and sanitize the recurrence end date
if (empty($recurrence_end_date) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $recurrence_end_date)) {
    $response['message'] = 'Invalid end date format.';
    echo json_encode($response);
    exit;
}

// Ensure recurrence_start_date is earlier than recurrence_end_date
try {
    $start_date_obj = new DateTime($recurrence_start_date);
    $end_date_obj = new DateTime($recurrence_end_date);
    if ($start_date_obj >= $end_date_obj) {
        $response['message'] = 'Recurrence start date must be earlier than the end date.';
        echo json_encode($response);
        exit;
    }
} catch (Exception $e) {
    $response['message'] = 'Invalid date values provided.';
    echo json_encode($response);
    exit;
}

//Validate start_time and end_time formatting
if (!preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $start_time) ||
    !preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $end_time)) {
    $response['message'] = 'Invalid time format.';
    echo json_encode($response);
    exit;
}

// Ensure start_time is earlier than end_time
try {
    $start_time_obj = new DateTime($start_time);
    $end_time_obj = new DateTime($end_time);
    if ($start_time_obj >= $end_time_obj) {
        $response['message'] = 'Start time must be earlier than end time.';
        echo json_encode($response);
        exit;
    }
} catch (Exception $e) {
    $response['message'] = 'Invalid time values provided.';
    echo json_encode($response);
    exit;
}

// Validate recurrence type
$valid_recurrence_types = ['daily', 'weekly', 'monthly', 'nth_weekday'];
if (!in_array($recurrence_type, $valid_recurrence_types, true)) {
    $response['message'] = 'Invalid recurrence type.';
    echo json_encode($response);
    exit;
}

// Prepare data for insertion
$table_name = $wpdb->prefix . 'leanwi_booking_recurrence';

$data = [
    'recurrence_type' => $recurrence_type,
    'recurrence_interval' => $recurrence_interval,
    'recurrence_start_date' => $recurrence_start_date,
    'recurrence_end_date' => $recurrence_end_date,
    'recurrence_day_of_week' => $recurrence_day_of_week,
    'recurrence_week_of_month' => $recurrence_week_of_month,
    'start_time' => $start_time,
    'end_time' => $end_time,
    'venue_id' => $venue_id,
    'organization' => $organization,
    'name' => $name,
    'email' => $email,
    'phone' => $phone,
    'number_of_participants' => $participants,
    'booking_notes' => $notes,
    'category_id' => $category_id,
    'audience_id' => $audience_id,
];

// Start a transaction
$wpdb->query('START TRANSACTION');

try {
    // Insert data into the database
    $inserted = $wpdb->insert($table_name, $data);

    if (!$inserted) {
        throw new Exception('Failed to save the recurring booking.');
    }
    // Retrieve the last inserted ID
    $recurrence_id = $wpdb->insert_id;

    // Combine $recurrence_start_date with $start_time and $end_time
    try {
        $start_time_value = new DateTime("$recurrence_start_date $start_time"); // e.g., '2024-11-01 06:00:00'
        $end_time_value = new DateTime("$recurrence_start_date $end_time"); // e.g., '2024-11-01 08:00:00'
    } catch (Exception $e) {
        $response['message'] = 'Invalid date or time provided.';
        echo json_encode($response);
        exit;
    }

    // Adjust $end_recurrence_date to ensure it ends at the end of the day (23:59:59)
    try {
        $end_recurrence_date = new DateTime($recurrence_end_date);
        $end_recurrence_date->setTime(23, 59, 59); // Set to 23:59:59
    } catch (Exception $e) {
        $response['message'] = 'Invalid recurrence end date provided.';
        echo json_encode($response);
        exit;
    }

    // Debugging logs for verification
    error_log("Recurrence Start Date: " . $start_time_value->format('Y-m-d H:i:s'));
    error_log("Recurrence End Date: " . $end_recurrence_date->format('Y-m-d H:i:s'));
    error_log("Start Time: " . $start_time_value->format('Y-m-d H:i:s'));
    error_log("End Time: " . $end_time_value->format('Y-m-d H:i:s'));

    // Generate recurrence dates
    $recurringDates = [];
    while ($start_time_value <= $end_recurrence_date) {
        $current_date = clone $start_time_value; // Avoid modifying the original
        if ($recurrence_type === 'nth_weekday') {
            // Nth weekday logic
            $month = $current_date->format('m');
            $year = $current_date->format('Y');

            // Calculate the nth weekday of the month
            $first_day_of_month = new DateTime("first day of {$year}-{$month}");

            // Determine the weekday of the first day of the month (0=Sunday, 1=Monday, etc.)
            $first_day_weekday = (int)$first_day_of_month->format('w');

            // Calculate the offset to the desired day of the week
            $day_offset = ($recurrence_day_of_week - $first_day_weekday + 7) % 7;
            $nth_weekday = clone $first_day_of_month;
            $nth_weekday->modify("+{$day_offset} days");

            // Add weeks to reach the nth occurrence
            if ($recurrence_week_of_month > 1) {
                $nth_weekday->modify("+".($recurrence_week_of_month - 1)." weeks");
            }

            // Ensure the calculated date is still in the correct month
            if ($nth_weekday->format('m') == $month) {
                $recurringDates[] = [
                    'unique_id' => substr(md5(uniqid(rand(), true)), 0, 7),
                    'start_time' => $nth_weekday->format('Y-m-d') . ' ' . $start_time_value->format('H:i:s'),
                    'end_time' => $nth_weekday->format('Y-m-d') . ' ' . $end_time_value->format('H:i:s'),
                ];
            }

            // Debug the calculated date
            error_log("Calculated nth_weekday: " . $nth_weekday->format('Y-m-d'));

            $start_time_value->modify("+1 month"); // Move to the next month
        } else {
            // Handle daily, weekly, monthly
            $recurringDates = handleOtherRecurrences(
                $recurringDates,
                $recurrence_type,
                $current_date,
                $start_time_value,
                $end_time_value,
                $recurrence_interval,
                $recurrence_day_of_week
            );
        }
    }

    // Insert into booking participants
    foreach ($recurringDates as $booking) {
        error_log('venue_id:' . $venue_id . 'end_time' . $booking['end_time'] . 'start_time:' . $booking['start_time']);
        $existingBooking = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}leanwi_booking_participant 
            WHERE venue_id = %d 
            AND ((start_time <= %s AND end_time > %s) OR (start_time < %s AND end_time >= %s))",
            $venue_id, $booking['end_time'], $booking['start_time'], $booking['end_time'], $booking['start_time']
        ));

        if ($existingBooking > 0) {
            throw new Exception("Conflict detected for venue at {$booking['start_time']}.");
        }

        $unique_id = substr(md5(rand()), 0, 7);
        $wpdb->insert("{$wpdb->prefix}leanwi_booking_participant", [
            'unique_id' => $unique_id,
            'venue_id' => $venue_id,
            'recurrence_id' => $recurrence_id,
            'start_time' => $booking['start_time'],
            'end_time' => $booking['end_time'],
            'number_of_participants' => $participants,
            'total_cost' => $cost,
            'organization' => $organization,
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'booking_notes' => $notes,
            'category_id' => $category_id,
            'audience_id' => $audience_id,
        ]);

        if (!$inserted) {
            throw new Exception('Failed to insert a booking participant.');
        }
    }

    // Commit the transaction
    $wpdb->query('COMMIT');

    $response['success'] = true;
    $response['message'] = 'Recurring booking has been successfully saved.';

} catch (Exception $e) {
    // Roll back the transaction
    $wpdb->query('ROLLBACK');

    $response['success'] = false;
    $response['message'] = $e->getMessage();
}

// Return JSON response
echo json_encode($response);
exit;

function handleOtherRecurrences($recurringDates, $recurrence_type, $current_date, $start_time_value, $end_time_value, $recurrence_interval, $recurrence_day_of_week) {
    switch ($recurrence_type) {
        case 'daily':
            $start_time_value->modify("+{$recurrence_interval} days");
            break;
        case 'weekly':
            // Use $recurrence_day_of_week to adjust the day within the week
            $start_time_value->modify("+{$recurrence_interval} weeks");
            $day_difference = $recurrence_day_of_week - $start_time_value->format('w');
            if ($day_difference !== 0) {
                $start_time_value->modify("{$day_difference} days");
            }
            break;
        case 'monthly':
            $start_time_value->modify("+{$recurrence_interval} months");
            break;
    }

    $recurringDates[] = [
        'unique_id' => substr(md5(uniqid(rand(), true)), 0, 7),
        'start_time' => $current_date->format('Y-m-d') . ' ' . $start_time_value->format('H:i:s'),
        'end_time' => $current_date->format('Y-m-d') . ' ' . $end_time_value->format('H:i:s'),
    ];

    return $recurringDates;
}

?>