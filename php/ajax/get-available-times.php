<?php
namespace LEANWI_Book_A_Room;

add_action('wp_ajax_leanwi_get_available_times', __NAMESPACE__ . '\\leanwi_get_available_times');
add_action('wp_ajax_nopriv_leanwi_get_available_times', __NAMESPACE__ . '\\leanwi_get_available_times');

function leanwi_get_available_times() {
    global $wpdb;

    if (!isset($_GET['venue_id']) || empty($_GET['venue_id'])) {
        wp_send_json_error(['message' => 'Error: venue_id is required. (get_available_times)']);
    }

    $venue_id = intval($_GET['venue_id']);
    $selected_date = isset($_GET['date']) ? sanitize_text_field($_GET['date']) : date('Y-m-d');

    if ($venue_id <= 0 || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $selected_date)) {
        wp_send_json_error(['message' => 'Error:invalid venue_id or date. (get_available_times)']);
    }

    $unique_id = isset($_GET['unique_id']) ? sanitize_text_field($_GET['unique_id']) : null;

    $venue_hours_table = $wpdb->prefix . 'leanwi_booking_venue_hours';
    $participant_table = $wpdb->prefix . 'leanwi_booking_participant';
    $closings_table = $wpdb->prefix . 'leanwi_booking_venue_closings';

    $day_of_week = date('l', strtotime($selected_date));
    $current_month_day = date('m-d', strtotime($selected_date));

    // Step 1: Check for full-day closures
    $full_day_closed = $wpdb->get_var($wpdb->prepare("
        SELECT COUNT(*) FROM {$closings_table}
        WHERE start_date = %s AND end_date = %s
        AND start_time = '00:00:00' AND end_time = '23:59:00'
        AND (%d = -1 OR venue_id = %d OR venue_id = -1)
    ", $selected_date, $selected_date, $venue_id, $venue_id));

    if ($full_day_closed > 0) {
        wp_send_json_success([]); // Return no available slots
    }

    // Step 2: Fetch general hours
    $hours = $wpdb->get_row(
        $wpdb->prepare("
            SELECT open_time, close_time
            FROM {$venue_hours_table}
            WHERE venue_id = %d
            AND day_of_week = %s
            AND (
                (DATE_FORMAT(start_date, '%%m-%%d') <= DATE_FORMAT(end_date, '%%m-%%d')
                AND %s BETWEEN DATE_FORMAT(start_date, '%%m-%%d') AND DATE_FORMAT(end_date, '%%m-%%d'))
                OR
                (DATE_FORMAT(start_date, '%%m-%%d') > DATE_FORMAT(end_date, '%%m-%%d')
                AND (
                    %s >= DATE_FORMAT(start_date, '%%m-%%d')
                    OR %s <= DATE_FORMAT(end_date, '%%m-%%d')
                ))
            )
        ",
            $venue_id, $day_of_week, $current_month_day, $current_month_day, $current_month_day
        )
    );

    if (!$hours) {
        wp_send_json_success([]); // No hours defined means no availability
    }

    // Step 3: Fetch existing bookings
    $bookings = $wpdb->get_results(
        $wpdb->prepare("
            SELECT start_time, end_time, unique_id FROM $participant_table
            WHERE venue_id = %d AND DATE(start_time) = %s
        ",
            $venue_id, $selected_date
        ),
        ARRAY_A
    );

    // Step 4: Fetch partial-day closures for this date
    $closures = $wpdb->get_results($wpdb->prepare("
        SELECT start_time, end_time FROM {$closings_table}
        WHERE start_date = %s AND end_date = %s
        AND (%d = -1 OR venue_id = %d OR venue_id = -1)
        AND NOT (start_time = '00:00:00' AND end_time = '23:59:00')
    ", $selected_date, $selected_date, $venue_id, $venue_id), ARRAY_A);

    // Step 5: Generate available slots
    $slots_with_booking_status = [];

    $start_time = new \DateTime("{$selected_date} {$hours->open_time}");
    $end_time = new \DateTime("{$selected_date} {$hours->close_time}");

    $minutes_interval = get_option('leanwi_minutes_interval', 30);
    $interval = new \DateInterval("PT{$minutes_interval}M");

    while ($start_time < $end_time) {
        $slot_end_time = clone $start_time;
        $slot_end_time->add($interval);

        $is_booked = false;
        $is_booked_for_unique_id = false;
        $is_closed = false;

        // Check against existing bookings
        foreach ($bookings as $booking) {
            $booking_start = new \DateTime($booking['start_time']);
            $booking_end = new \DateTime($booking['end_time']);

            if (($start_time < $booking_end && $slot_end_time > $booking_start) ||
                ($start_time >= $booking_start && $slot_end_time <= $booking_end)) {
                $is_booked = true;
                if ($unique_id && $booking['unique_id'] == $unique_id) {
                    $is_booked_for_unique_id = true;
                }
                break;
            }
        }

        // Check against closure periods
        foreach ($closures as $closure) {
            $closure_start = new \DateTime("{$selected_date} {$closure['start_time']}");
            $closure_end = new \DateTime("{$selected_date} {$closure['end_time']}");

            if ($start_time < $closure_end && $slot_end_time > $closure_start) {
                $is_closed = true;
                break;
            }
        }

        if (!$is_closed) {
            $slots_with_booking_status[] = [
                'start' => esc_html($start_time->format('H:i')),
                'end' => esc_html($slot_end_time->format('H:i')),
                'booked' => $is_booked,
                'is_booked_for_unique_id' => $is_booked_for_unique_id
            ];
        }

        $start_time->add($interval);
    }

    wp_send_json_success($slots_with_booking_status);
}

?>