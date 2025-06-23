<?php
namespace LEANWI_Book_A_Room;

add_action('wp_ajax_leanwi_get_available_times', __NAMESPACE__ . '\\leanwi_get_available_times');
add_action('wp_ajax_nopriv_leanwi_get_available_times', __NAMESPACE__ . '\\leanwi_get_available_times');

function leanwi_get_available_times() {

    global $wpdb;

    // Get venue_id from the query string and sanitize it
    if (!isset($_GET['venue_id']) || empty($_GET['venue_id'])) {
        wp_send_json_error(['message' => 'Error: venue_id is required. (get_available_times)']);
    }

    // Sanitize and validate venue_id and date
    $venue_id = intval($_GET['venue_id']);
    $selected_date = isset($_GET['date']) ? sanitize_text_field($_GET['date']) : date('Y-m-d'); // Default to today if not provided

    if ($venue_id <= 0 || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $selected_date)) {
        wp_send_json_error(['message' => 'Error:invalid venue_id or date. (get_available_times)']);
    }

    // Sanitize unique_id if present
    $unique_id = isset($_GET['unique_id']) ? sanitize_text_field($_GET['unique_id']) : null;

    // Define table names with prefix
    $venue_hours_table = $wpdb->prefix . 'leanwi_booking_venue_hours';
    $participant_table = $wpdb->prefix . 'leanwi_booking_participant';

    // Get formatted inputs
    $day_of_week = date('l', strtotime($selected_date));
    $current_month_day = date('m-d', strtotime($selected_date));

    // Prepare the SQL query
    $venue_hours_query = "
        SELECT vh.open_time, vh.close_time
        FROM {$venue_hours_table} vh
        WHERE vh.venue_id = %d
        AND vh.day_of_week = %s
        AND (
            (DATE_FORMAT(vh.start_date, '%%m-%%d') <= DATE_FORMAT(vh.end_date, '%%m-%%d')
            AND %s BETWEEN DATE_FORMAT(vh.start_date, '%%m-%%d') AND DATE_FORMAT(vh.end_date, '%%m-%%d'))
            OR
            (DATE_FORMAT(vh.start_date, '%%m-%%d') > DATE_FORMAT(vh.end_date, '%%m-%%d')
            AND (
                %s >= DATE_FORMAT(vh.start_date, '%%m-%%d')
                OR %s <= DATE_FORMAT(vh.end_date, '%%m-%%d')
            )
            )
        )
    ";

    // Execute the prepared query
    $hours = $wpdb->get_row(
        $wpdb->prepare(
            $venue_hours_query,
            $venue_id,
            $day_of_week,
            $current_month_day,
            $current_month_day,
            $current_month_day
        )
    );

    // Fetch existing bookings for the selected date
    $bookings = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT start_time, end_time, unique_id FROM $participant_table WHERE venue_id = %d AND DATE(start_time) = %s",
            $venue_id,
            $selected_date
        ),
        ARRAY_A
    );

    // Calculate all slot times with booking status
    $slots_with_booking_status = [];
    if ($hours) {
        $start_time = new \DateTime($selected_date . ' ' . $hours->open_time);
        $end_time = new \DateTime($selected_date . ' ' . $hours->close_time);
        
        // Get the minutes interval from settings
        $minutes_interval = get_option('leanwi_minutes_interval', 30); // Default to 30 if not set
        $interval = new \DateInterval('PT' . $minutes_interval . 'M'); // Create DateInterval based on the retrieved value

        // Create all possible slots
        while ($start_time < $end_time) {
            $slot_end_time = clone $start_time;
            $slot_end_time->add($interval);

            $is_booked = false;
            $is_booked_for_unique_id = false;

            // Check if this slot is booked
            foreach ($bookings as $booking) {
                $booking_start = new \DateTime($booking['start_time']);
                $booking_end = new \DateTime($booking['end_time']);

                // If slot is within the booking range
                if (($start_time < $booking_end && $slot_end_time > $booking_start) || 
                    ($start_time >= $booking_start && $slot_end_time <= $booking_end)) {
                    
                    $is_booked = true;
                    
                    // Check if the booking is related to the provided unique_id
                    if ($unique_id && $booking['unique_id'] == $unique_id) {
                        $is_booked_for_unique_id = true;
                    }

                    break;
                }
            }

            // Add the slot with its booking status
            $slots_with_booking_status[] = [
                'start' => esc_html($start_time->format('H:i')),
                'end' => esc_html($slot_end_time->format('H:i')),
                'booked' => $is_booked,
                'is_booked_for_unique_id' => $is_booked_for_unique_id
            ];

            // Move to the next slot
            $start_time->add($interval);
        }
    }

    // Output all slot times with booking status as JSON
    wp_send_json_success($slots_with_booking_status);
}
?>