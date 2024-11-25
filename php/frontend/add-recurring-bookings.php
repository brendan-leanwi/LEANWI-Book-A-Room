<?php
// Needs converting to a called php file and checking logic etc
function addRecurringBookings($startDateTime, $endDateTime, $recurrenceType, $recurrenceInterval, $recurrenceEndDate, $recurrenceDayOfWeek = null, $recurrenceWeekOfMonth = null) {
    global $wpdb;

    $startDateTime = new DateTime($startDateTime);
    $endDateTime = new DateTime($endDateTime);
    $endRecurrence = new DateTime($recurrenceEndDate);

    $recurringDates = [];

    while ($startDateTime <= $endRecurrence) {
        if ($recurrenceType === 'nth_weekday') {
            $month = $startDateTime->format('m');
            $year = $startDateTime->format('Y');

            // Find the nth weekday of the month
            $nthWeekday = new DateTime("first day of {$year}-{$month}");
            $nthWeekday->modify("+".($recurrenceWeekOfMonth - 1)." weeks");
            $nthWeekday->modify("+{$recurrenceDayOfWeek} days");

            if ($nthWeekday->format('m') == $month) {
                // Ensure it's the correct month
                $recurringDates[] = [
                    'unique_id' => substr(md5(uniqid(rand(), true)), 0, 7),
                    'start_time' => $nthWeekday->format('Y-m-d') . ' ' . $startDateTime->format('H:i:s'),
                    'end_time' => $nthWeekday->format('Y-m-d') . ' ' . $endDateTime->format('H:i:s'),
                ];
            }

            $startDateTime->modify("+1 month"); // Move to the next month
        } else {
            // Handle other recurrence types (daily, weekly, monthly) as before
            $recurringDates = handleOtherRecurrences($recurringDates, $recurrenceType, $startDateTime, $endDateTime, $recurrenceInterval);
        }
    }

    // Insert recurring dates into leanwi_booking_participant
    foreach ($recurringDates as $booking) {
        //Throw an exception if there's a clash in the bookings
        $existingBooking = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}leanwi_booking_participant 
             WHERE venue_id = %d AND 
                   ((start_time <= %s AND end_time > %s) OR 
                    (start_time < %s AND end_time >= %s))",
            $venueId, $endTime, $startTime, $endTime, $startTime
        ));
        
        if ($existingBooking > 0) {
            throw new Exception("Conflict detected for venue at {$startTime}.");
        }
        
        $wpdb->insert("{$wpdb->prefix}leanwi_booking_participant", [
            'unique_id' => $booking['unique_id'],
            'start_time' => $booking['start_time'],
            'end_time' => $booking['end_time'],
            'venue_id' => $venueId, // Replace with appropriate value
            'number_of_participants' => $participants, // Replace with appropriate value
            'total_cost' => $cost, // Replace with appropriate value
        ]);
    }

    return count($recurringDates); // Return the number of bookings added
}

function handleOtherRecurrences($recurringDates, $recurrenceType, $startDateTime, $endDateTime, $recurrenceInterval) {
    switch ($recurrenceType) {
        case 'daily':
            $startDateTime->modify("+{$recurrenceInterval} days");
            $endDateTime->modify("+{$recurrenceInterval} days");
            break;
        case 'weekly':
            $startDateTime->modify("+{$recurrenceInterval} weeks");
            $endDateTime->modify("+{$recurrenceInterval} weeks");
            break;
        case 'monthly':
            $startDateTime->modify("+{$recurrenceInterval} months");
            $endDateTime->modify("+{$recurrenceInterval} months");
            break;
    }
    return $recurringDates;
}

?>