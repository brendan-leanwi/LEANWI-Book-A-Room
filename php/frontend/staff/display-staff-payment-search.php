<?php
/**
 * Plugin Name: Display Name Search
 * Description: A plugin to allow the user to display bookings based on a name or organization search.
 * Version: 1.1
 * Author: Brendan Tuckey
 */

// Register the shortcode for the venue grid
function display_payment_name_search() {
    global $wpdb;

    // Check if the user has staff privileges
    $current_user = wp_get_current_user();
    $is_booking_staff = in_array('booking_staff', (array) $current_user->roles);

    // If the user is not a staff member, display a restricted access message
    if (!$is_booking_staff) {
        return 'You do not have the correct permissions to access this page.';
    }

    // Check if a search query is submitted
    $search_query = isset($_GET['name_search']) ? sanitize_text_field($_GET['name_search']) : '';
    $start_date = isset($_GET['start_date']) ? sanitize_text_field($_GET['start_date']) : date('Y-m-d', strtotime('-1 month'));
    $end_date = isset($_GET['end_date']) ? sanitize_text_field($_GET['end_date']) : date('Y-m-d');
    $unpaid_only = isset($_GET['unpaid_only']) ? $_GET['unpaid_only'] === '1' : true;
    $results_html = '';

    $table_participants = $wpdb->prefix . 'leanwi_booking_participant';
    $table_venues = $wpdb->prefix . 'leanwi_booking_venue';

    // If a search query is provided, search the database
    if ($search_query) {
        // SQL query to search in name or organization and join with venue table, filtered by date range
        $sql = "
            SELECT 
                p.name AS participant_name, 
                p.organization, 
                v.name AS venue_name, 
                v.page_url, 
                p.unique_id, 
                p.start_time,
                p.total_cost,
                p.has_paid
            FROM {$table_participants} p
            JOIN {$table_venues} v ON p.venue_id = v.venue_id
            WHERE (p.name LIKE %s OR p.organization LIKE %s)
            AND p.start_time BETWEEN %s AND %s
        ";

        // Add the unpaid condition if $unpaid_only is true
        if ($unpaid_only) {
            $sql .= " AND p.has_paid = 0";
        }

        // Append the ORDER BY clause
        $sql .= " ORDER BY p.start_time ASC";

        // Prepare and execute the query
        $results = $wpdb->get_results(
            $wpdb->prepare(
                $sql,
                '%' . $search_query . '%',
                '%' . $search_query . '%',
                $start_date,
                $end_date
            )
        );
    }
    else {

        $sql = "
            SELECT 
                p.name AS participant_name, 
                p.organization, 
                v.name AS venue_name, 
                v.page_url, 
                p.unique_id, 
                p.start_time,
                p.total_cost,
                p.has_paid
            FROM {$table_participants} p
            JOIN {$table_venues} v ON p.venue_id = v.venue_id
            WHERE p.start_time BETWEEN %s AND %s
        ";

        // Add the unpaid condition if $unpaid_only is true
        if ($unpaid_only) {
            $sql .= " AND p.has_paid = 0";
        }

        // Append the ORDER BY clause
        $sql .= " ORDER BY p.start_time ASC";

        // Prepare and execute the query
        $results = $wpdb->get_results(
            $wpdb->prepare(
                $sql,
                $start_date,
                $end_date
            )
        );
    }


    // Create results table HTML
    if (!empty($results)) {
        $results_html .= '<table style="width:100%; border-collapse: collapse; margin-top: 20px;">';
        $results_html .= '<thead><tr>
            <th style="border: 1px solid #ddd; padding: 8px;">Venue Name</th>
            <th style="border: 1px solid #ddd; padding: 8px;">Name</th>
            <th style="border: 1px solid #ddd; padding: 8px;">Organization</th>
            <th style="border: 1px solid #ddd; padding: 8px;">Start Time</th>
            <th style="border: 1px solid #ddd; padding: 8px;">Total Cost</th>
            <th style="border: 1px solid #ddd; padding: 8px;">Has paid?</th>
            <th style="border: 1px solid #ddd; padding: 8px;"> </th>
            <th style="border: 1px solid #ddd; padding: 8px;"> </th>
        </tr></thead><tbody>';

        foreach ($results as $row) {
            $booking_url = esc_url($row->page_url . '?booking_id=' . $row->unique_id);
        
            // Determine action text and data for toggling payment status
            $toggle_text = $row->has_paid == 0 ? 'Mark as Paid' : 'Mark as Unpaid';
            $new_payment_status = $row->has_paid == 0 ? 1 : 0;
        
            // Generate a nonce for the AJAX request
            $mark_payment_nonce = wp_create_nonce('mark_payment_nonce');

            $results_html .= sprintf(
                '<tr>
                    <td style="border: 1px solid #ddd; padding: 8px;">%s</td>
                    <td style="border: 1px solid #ddd; padding: 8px;">%s</td>
                    <td style="border: 1px solid #ddd; padding: 8px;">%s</td>
                    <td style="border: 1px solid #ddd; padding: 8px;">%s</td>
                    <td style="border: 1px solid #ddd; padding: 8px;">%s</td>
                    <td style="border: 1px solid #ddd; padding: 8px;">%s</td>
                    <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">
                        <a href="%s" style="color: blue; text-decoration: underline;" target="_blank">View</a>
                    </td><td style="border: 1px solid #ddd; padding: 8px; text-align: center;">
                        <a href="#" class="toggle-paid-link" 
                           data-booking-id="%s" 
                           data-new-status="%s" 
                           data-nonce="%s"
                           style="color: blue; text-decoration: underline;">%s</a>
                    </td>
                </tr>',
                esc_html($row->venue_name),
                esc_html($row->participant_name),
                esc_html($row->organization),
                esc_html($row->start_time),
                esc_html($row->total_cost),
                esc_html($row->has_paid == 0 ? 'No' : 'Yes'),
                $booking_url,
                esc_attr($row->unique_id),
                esc_attr($new_payment_status), // Pass the new payment status
                esc_attr($mark_payment_nonce), // Include the nonce
                esc_html($toggle_text) // Show the correct action text
            );
        }

        $results_html .= '</tbody></table>';
    } else {
        $results_html = '<p>No results found for your search.</p>';
    }

    // Return the search form and results
    return '<H2>Search by Name</H2> 
    <form method="get" style="margin-bottom: 20px;">
        <input type="text" name="name_search" value="' . esc_attr($search_query) . '" placeholder="Search by name or organization" style="padding: 8px; width: 50%;">
        <input type="date" name="start_date" value="' . esc_attr($start_date) . '" style="padding: 8px;">
        <input type="date" name="end_date" value="' . esc_attr($end_date) . '" style="padding: 8px;">

        <!-- Hidden field to send "0" when checkbox is not checked -->
        <input type="hidden" name="unpaid_only" value="0">

        <!-- Checkbox for unpaid only -->
        <label style="margin-left: 8px;">
            <input type="checkbox" name="unpaid_only" value="1" ' . ($unpaid_only ? 'checked' : '') . '> Unpaid only
        </label>

        <button type="submit" style="padding: 8px;">Search</button>
    </form>
    ' . $results_html;
}
add_shortcode('staff_payment_search', 'display_payment_name_search');
?>
