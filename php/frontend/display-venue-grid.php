<?php
/**
 * Plugin Name: Display Venue Grid
 * Description: A plugin to display a grid of available times for each venue.
 * Version: 1.0
 * Author: Brendan Tuckey
 */

// Register the shortcode for the venue grid
function display_venue_grid() {
    // Get the selected or today's date
    $minutes_interval = intval(get_option('leanwi_minutes_interval', 15));
    global $wpdb;

    $today_date = isset($_GET['selected_date']) ? sanitize_text_field($_GET['selected_date']) : date('Y-m-d');
    $day_of_week = date('l', strtotime($today_date));
    
    // Query venue hours using the day of week for the selected date
    $venue_hours = $wpdb->get_results($wpdb->prepare("
        SELECT vh.venue_id, vh.open_time, vh.close_time, v.name, v.page_url, v.capacity, v.description, v.location
        FROM {$wpdb->prefix}leanwi_booking_venue_hours vh
        JOIN {$wpdb->prefix}leanwi_booking_venue v ON vh.venue_id = v.venue_id
        WHERE vh.day_of_week = %s
    ", $day_of_week));
    //Removed code from above select statement
    //AND NOT (vh.open_time = '00:00:00' AND vh.close_time = '00:00:00')

    // Query bookings for the selected date
    $bookings = $wpdb->get_results($wpdb->prepare("
        SELECT venue_id, start_time, end_time, unique_id, organization, name
        FROM {$wpdb->prefix}leanwi_booking_participant
        WHERE DATE(start_time) = %s
    ", $today_date));

    // Calculate time slots for the selected date
    $time_slots = [];
    $earliest_time = '23:59:59';
    $latest_time = '00:00:00';
    foreach ($venue_hours as $vh) {
        $earliest_time = min($earliest_time, $vh->open_time);
        $latest_time = max($latest_time, $vh->close_time);
    }

    // Generate time slots based on the open and close time
    $current_time = strtotime($earliest_time);
    while ($current_time < strtotime($latest_time)) {
        $time_slots[] = date('h:i a', $current_time);
        $current_time = strtotime('+' . $minutes_interval . ' minutes', $current_time);
    }

    // Build grid
    $grid = [];
    date_default_timezone_set('America/Chicago');

    //Check if the user has staff privileges to display grid accordingling
    $current_user = wp_get_current_user();
    $is_booking_staff = in_array('booking_staff', (array) $current_user->roles);

    foreach ($venue_hours as $vh) {
        foreach ($time_slots as $slot) {
            $slot_time = strtotime($today_date . ' ' . $slot);
            $grid[$vh->venue_id][$slot] = build_grid_cell($vh, $slot_time, $is_booking_staff, $today_date, $bookings);
        }
    }
    
    // Output the date picker form
    $output = '<form id="booking-date-selector" method="GET" action="">
    <label for="selected_date">Selected Date:</label>
    <input type="date" id="selected_date" name="selected_date" value="' . esc_attr($today_date) . '">
    <input type="submit" value="Update Grid">
    </form>';

    $formatted_date = date('F j, Y', strtotime($today_date));
    $output .= '<p><h2 style="text-align: center;">Bookings for ' . $formatted_date . '</h2></p><p> </p>';

    // Output the grid as a table
    $output .= '<table class="booking-grid-table">';
    $output .= '<thead>';
    $output .= '<tr><th>Time Slot</th>';
    foreach ($venue_hours as $vh) {
        // Build the tooltip text
        $tooltip = sprintf(
            "Capacity: %s\nLocation: %s\nDescription: %s",
            esc_html($vh->capacity),
            esc_html($vh->location),
            esc_html($vh->description)
        );
        $output .= '<th><a href="' . esc_url($vh->page_url) . '" target="_blank" class="venue-link" title="' . esc_attr($tooltip) . '">' 
            . esc_html($vh->name) 
            . ' <span class="link-icon">↗</span></a></th>';
    }
    
    $output .= '</tr>';
    $output .= '</thead>';
    $output .= '<tbody>';
    foreach ($time_slots as $slot) {
        $output .= '<tr>';
        $output .= '<td>' . esc_html($slot) . '</td>';
        foreach ($venue_hours as $vh) {
            $output .= $grid[$vh->venue_id][$slot];
        }
        $output .= '</tr>';
    }
    $output .= '</tbody>';
    $output .= '</table>';

    return $output;

}

function build_grid_cell($vh, $slot_time, $is_booking_staff, $today_date, $bookings) {
    // Check if the slot time is within the venue's open and close times
    if ($slot_time < strtotime($today_date . ' ' . $vh->open_time) || $slot_time >= strtotime($today_date . ' ' . $vh->close_time)) {
        return '<td class="na-cell">N/A</td>';
    }

    $is_booked = false;
    $booking_cell_content = '';

    foreach ($bookings as $booking) {
        // Check if the current time slot matches a booking
        if (
            $booking->venue_id == $vh->venue_id &&
            $slot_time >= strtotime($booking->start_time) &&
            $slot_time < strtotime($booking->end_time)
        ) {
            $is_booked = true;

            if ($is_booking_staff) {
                // If the user is a Booking Staff, include booking details and a link
                $display_name = !empty($booking->organization)
                    ? esc_html($booking->organization)
                    : esc_html($booking->name);

                $booking_link = esc_url($vh->page_url . '?booking_id=' . $booking->unique_id);
                $booking_cell_content = '<a href="' . $booking_link . '" class="booking-link" target="_blank">' . $display_name . '</a>';
            }
            break;
        }
    }

    // If not booked and the time slot is in the past, mark it as N/A for non-staff
    if (!$is_booking_staff && !$is_booked && $slot_time < time()) {
        return '<td class="na-cell">N/A</td>';
    }

    // Return the appropriate cell based on booking status
    return $is_booked
        ? '<td class="booked-cell">' . ($is_booking_staff ? $booking_cell_content : 'Booked') . '</td>'
        : '<td class="available-cell"><a href="' . esc_url($vh->page_url) . '?selected_date=' . esc_attr($today_date) . '&time_slot=' . urlencode(date('H:i', $slot_time)) . '" target="_blank">Available Slot</a></td>';
}

add_shortcode('venue_grid', 'display_venue_grid');
?>