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

    // Check if the user has staff privileges to display grid accordingly
    $current_user = wp_get_current_user();
    $is_booking_staff = in_array('booking_staff', (array) $current_user->roles);

    // Pass the result to JavaScript.
    echo '<script>';
    echo 'const isBookingStaff = ' . json_encode($is_booking_staff) . ';';
    echo '</script>';

    $today_date = isset($_GET['selected_date']) ? sanitize_text_field($_GET['selected_date']) : date('Y-m-d');
    $selected_venue_id = isset($_GET['venue_id']) ? intval($_GET['venue_id']) : 0; // Get the selected venue ID
    $day_of_week = date('l', strtotime($today_date));
    
    // Query all venues for the dropdown
    $query = "SELECT venue_id, name FROM {$wpdb->prefix}leanwi_booking_venue";
    if(!$is_booking_staff) {
        $query .= " WHERE bookable_by_staff_only = 0";
    }
    $query .= " ORDER BY display_order";
    $venues = $wpdb->get_results($query);

    // Extract the month and day from the selected date (e.g. '04-30')
    $current_month_day = date('m-d', strtotime($today_date));

    $venue_hours_query = "
        SELECT vh.venue_id, vh.open_time, vh.close_time, v.name, v.use_business_days_only, 
            v.days_before_booking, v.page_url, v.capacity, v.description, v.location
        FROM {$wpdb->prefix}leanwi_booking_venue_hours vh
        JOIN {$wpdb->prefix}leanwi_booking_venue v ON vh.venue_id = v.venue_id
        WHERE vh.day_of_week = %s
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

    if(!$is_booking_staff) {
        $venue_hours_query .= " AND v.bookable_by_staff_only = 0";
    }
    $venue_hours_query .= $selected_venue_id ? $wpdb->prepare(" AND vh.venue_id = %d", $selected_venue_id) : " ORDER BY display_order";

    $venue_hours = $wpdb->get_results(
        $wpdb->prepare($venue_hours_query, $day_of_week, $current_month_day, $current_month_day, $current_month_day)
    );

    // Query bookings for the selected date and venue (if applicable)
    $bookings_query = "
        SELECT p.venue_id, p.start_time, p.end_time, p.unique_id, p.organization, p.name
        FROM {$wpdb->prefix}leanwi_booking_participant p
        JOIN {$wpdb->prefix}leanwi_booking_venue v ON v.venue_id = p.venue_id
        WHERE DATE(p.start_time) = %s
    ";
    if(!$is_booking_staff) {
        $bookings_query .= " AND v.bookable_by_staff_only = 0";
    }
    $bookings_query .= $selected_venue_id ? $wpdb->prepare(" AND v.venue_id = %d", $selected_venue_id) : "";
    
    $bookings = $wpdb->get_results($wpdb->prepare($bookings_query, $today_date));

    // Calculate time slots for the selected date
    $time_slots = [];
    $earliest_time = '23:59:59';
    $latest_time = '00:00:00';
    foreach ($venue_hours as $vh) {
        if(!($vh->open_time === '00:00:00' && $vh->close_time === '00:00:00')) {
            $earliest_time = min($earliest_time, $vh->open_time);
            $latest_time = max($latest_time, $vh->close_time);
        }
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

    foreach ($venue_hours as $vh) {
        foreach ($time_slots as $slot) {
            $slot_time = strtotime($today_date . ' ' . $slot);
            $grid[$vh->venue_id][$slot] = build_grid_cell($vh, $slot_time, $is_booking_staff, $today_date, $bookings);
        }
    }

    // Output the date picker form and venue dropdown
    $output = '
        <style>
            #booking-date-selector {
                display: flex;
                flex-wrap: wrap; /* Ensure elements wrap nicely on smaller screens */
                gap: 5px; /* Add spacing between elements */
                justify-content: center; /* Center-align the form elements */
                margin-bottom: 5px; /* Add space below the form */
            }

            #booking-date-selector label,
            #booking-date-selector input[type="date"],
            #booking-date-selector select,
            #booking-date-selector button[type="button"],
            #booking-date-selector input[type="submit"] {
                margin: 5px 0; /* Add top and bottom margin to each element */
                padding: 8px; /* Add padding for better touch targets */
                font-size: 14px; /* Ensure readability on smaller screens */
            }
        </style>';
    $output .= '<form id="booking-date-selector" method="GET" action="">';
    $output .= '<label for="selected_date">Selected Date:</label>';
    $output .= '<input type="date" id="selected_date" name="selected_date" value="' . esc_attr($today_date) . '">';
    
    // Venue dropdown
    $output .= '<label for="venue_id" style="margin-left: 10px;">Select Venue:</label>';
    $output .= '<select id="venue_id" name="venue_id" style="margin-left: 5px;">';
    $output .= '<option value="0">All Venues</option>'; // Default option to show all venues
    foreach ($venues as $venue) {
        $selected = $selected_venue_id == $venue->venue_id ? 'selected' : '';
        $output .= '<option value="' . esc_attr($venue->venue_id) . '" ' . $selected . '>' . esc_html($venue->name) . '</option>';
    }
    $output .= '</select>';

    $output .= '<input type="submit" value="Update Page" style="margin-left: 10px;">';

    $output .= '<button type="button" id="next-day-button" style="margin-left: 10px;">Go to Next Day</button>';
    $output .= '</form>';

    // Display the date and grid header
    $formatted_date = date('F j, Y', strtotime($today_date));
    $output .= '<h2 style="text-align: center; margin: 30px 0;">Bookings for ' . $day_of_week . ' ' . $formatted_date . '</h2>';

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
        $output .= '<th><a href="' . esc_url($vh->page_url) . '" target="_blank" rel="noopener noreferrer" class="venue-link"'
            . ' title="' . esc_attr($tooltip) . '"'
            . ' aria-label="' . esc_attr(
                $vh->name . '. Capacity: ' . $vh->capacity . '. Location: ' . $vh->location ) . '">'
            . esc_html($vh->name)
            . '</a></th>';

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

// Function to subtract business days
function subtractBusinessDays(DateTime $date, int $days): DateTime {
    while ($days > 0) {
        $date->modify('-1 day'); // Move back one day
        if (!in_array($date->format('N'), [6, 7])) { // Skip Saturdays (6) and Sundays (7)
            $days--;
        }
    }
    return $date;
}

function build_grid_cell($vh, $slot_time, $is_booking_staff, $today_date, $bookings) {
    // Check if the slot time is within the venue's open and close times
    if ($slot_time < strtotime($today_date . ' ' . $vh->open_time) || $slot_time >= strtotime($today_date . ' ' . $vh->close_time)) {
        return '<td class="na-cell" title="The room is not bookable at this time of day.">N/A</td>';
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

    if (!$is_booking_staff && !$is_booked) {
        $now = new DateTime();
        $slotDateTime = DateTime::createFromFormat('U', $slot_time);
    
        // CASE 1: Slot has already passed
        if ($slotDateTime < $now) {
            return '<td class="na-cell na-past" title="This time slot is in the past.">N/A</td>';
        }
    
        // CASE 2: Not enough advance notice
        if ($vh->days_before_booking > 0) {
            $slotDate = clone $slotDateTime;
            $slotDate->setTime(0, 0, 0);
    
            if ($vh->use_business_days_only == 0) {
                $cutoffDateTime = clone $slotDate;
                $cutoffDateTime->modify('-' . max(0, $vh->days_before_booking - 1) . ' days'); // Reduce cutoff by 1 day so that days_before_booking = 1 means "can't book today", but tomorrow is OK.
            } else {
                $cutoffDateTime = subtractBusinessDays(clone $slotDate, max(0, $vh->days_before_booking - 1)); // Reduce cutoff by 1 day so that days_before_booking = 1 means "can't book today", but tomorrow is OK.
            }
    
            if ($now > $cutoffDateTime) {
                return '<td class="na-cell na-cutoff" title="Bookings must be made at least ' . $vh->days_before_booking . ($vh->use_business_days_only ? ' business' : '') . ' day(s) in advance.">N/A</td>';
            }
        }
    }       
    
    // Return the appropriate cell based on booking status
    return $is_booked
        ? '<td class="booked-cell">' . ($is_booking_staff ? $booking_cell_content : 'Booked') . '</td>'
        : '<td class="available-cell">' .
            '<a href="' . esc_url($vh->page_url) . '?selected_date=' . esc_attr($today_date) . '&time_slot=' . urlencode(date('H:i', $slot_time)) . '"' .
            ' target="_blank" rel="noopener noreferrer" class="booking-slot"' .
            ' aria-label="' . esc_attr(date('g:i A', $slot_time) . ' slot available for the ' . $vh->name . '. Clicking takes you to make a booking for this time.') . '">' .
            'Available Slot</a>' .
            '</td>';

}

add_shortcode('venue_grid', 'display_venue_grid');
?>