<?php
/**
 * Plugin Name: Display Recurring Bookings
 * Description: A plugin to allow staff to implement recurring booking functionality in the LEANWI Room Booking System.
 * Version: 1.0
 * Author: Brendan Tuckey
 */

// Register the shortcode for the venue grid
function display_recurring_bookings() {
    
    $minutes_interval = intval(get_option('leanwi_minutes_interval', 15));
    global $wpdb;

    // Get the selected or today's date
    $today_date = isset($_GET['selected_date']) ? sanitize_text_field($_GET['selected_date']) : date('Y-m-d');
    $day_of_week = date('l', strtotime($today_date));
    
    // Query venue hours using the day of week for the selected date
    $venue_hours = $wpdb->get_results($wpdb->prepare("
        SELECT vh.venue_id, vh.open_time, vh.close_time, v.name, v.page_url, v.capacity, v.description, v.location
        FROM {$wpdb->prefix}leanwi_booking_venue_hours vh
        JOIN {$wpdb->prefix}leanwi_booking_venue v ON vh.venue_id = v.venue_id
        WHERE vh.day_of_week = %s
        AND NOT (vh.open_time = '00:00:00' AND vh.close_time = '00:00:00')
    ", $day_of_week));

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
    foreach ($venue_hours as $vh) {
        foreach ($time_slots as $slot) {
            $slot_time = strtotime($today_date . ' ' . $slot);
    
            // Check if the slot time is within the open and close time for the venue
            if ($slot_time < strtotime($today_date . ' ' . $vh->open_time) || $slot_time >= strtotime($today_date . ' ' . $vh->close_time)) {
                $grid[$vh->venue_id][$slot] = '<td class="na-cell">N/A</td>';
            } else {
                $is_booked = false;
                $booking_cell_content = '';
    
                foreach ($bookings as $booking) {
                    // Compare slot times with bookings' start and end times
                    if (
                        $booking->venue_id == $vh->venue_id &&
                        $slot_time >= strtotime($booking->start_time) &&
                        $slot_time < strtotime($booking->end_time)
                    ) {
                        $is_booked = true;
    
                        // Display organization if available, otherwise name
                        $display_name = !empty($booking->organization)
                            ? esc_html($booking->organization)
                            : esc_html($booking->name);
    
                        // Construct link to the booking
                        $booking_link = esc_url($vh->page_url . '?booking_id=' . $booking->unique_id);
    
                        // Build the cell content
                        $booking_cell_content = '<a href="' . $booking_link . '" class="booking-link">'
                            . $display_name . '</a>';
                        break;
                    }
                }
    
                $grid[$vh->venue_id][$slot] = $is_booked
                    ? '<td class="booked-cell">' . $booking_cell_content . '</td>'
                    : '<td></td>';
            }
        }
    }

    // Output the date picker form
    echo '<form id="booking-date-selector" method="GET" action="">
    <label for="selected_date">Staff Selected Date:</label>
    <input type="date" id="selected_date" name="selected_date" value="' . esc_attr($today_date) . '">
    <input type="submit" value="Update Grid">
    </form>';

    $formatted_date = date('F j, Y', strtotime($today_date));
    echo '<p><h2 style="text-align: center;">Bookings for ' . $formatted_date . '</h2></p><p> </p>';

    // Output the grid as a table
    echo '<table class="booking-grid-table">';
    echo '<thead>';
    echo '<tr><th>Time Slot</th>';
    foreach ($venue_hours as $vh) {
        // Build the tooltip text
        $tooltip = sprintf(
            "Capacity: %s\nLocation: %s\nDescription: %s",
            esc_html($vh->capacity),
            esc_html($vh->location),
            esc_html($vh->description)
        );
        echo '<th><a href="' . esc_url($vh->page_url) . '" target="_blank" class="venue-link" title="' . esc_attr($tooltip) . '">' 
            . esc_html($vh->name) 
            . ' <span class="link-icon">↗</span></a></th>';
    }
    
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    foreach ($time_slots as $slot) {
        echo '<tr>';
        echo '<td>' . esc_html($slot) . '</td>';
        foreach ($venue_hours as $vh) {
            echo $grid[$vh->venue_id][$slot];
        }
        echo '</tr>';
    }
    echo '</tbody>';
    echo '</table>';

}

add_shortcode('staff_recurring_bookings', 'display_recurring_bookings');
?>