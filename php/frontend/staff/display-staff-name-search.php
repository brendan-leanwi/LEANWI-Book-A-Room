<?php
/**
 * Plugin Name: Display Name Search
 * Description: A plugin to allow the user to display bookings based on a name or organization search.
 * Version: 1.1
 * Author: Brendan Tuckey
 */

// Register the shortcode for the venue grid
function display_staff_name_search() {
    global $wpdb;

    // Check if the user has staff privileges
    $current_user = wp_get_current_user();
    $is_booking_staff = in_array('booking_staff', (array) $current_user->roles);

    // If the user is not a staff member, display a restricted access message
    if (!$is_booking_staff) {
        return '';
    }

    // Check if a search query is submitted
    $search_query = isset($_GET['staff_search']) ? sanitize_text_field($_GET['staff_search']) : '';
    $start_date = isset($_GET['start_date']) ? sanitize_text_field($_GET['start_date']) : date('Y-m-d');
    $end_date = isset($_GET['end_date']) ? sanitize_text_field($_GET['end_date']) : date('Y-m-d', strtotime('+1 month'));
    $results_html = '';

    // If a search query is provided, search the database
    if ($search_query) {
        $table_participants = $wpdb->prefix . 'leanwi_booking_participant';
        $table_venues = $wpdb->prefix . 'leanwi_booking_venue';

        // SQL query to search in name or organization and join with venue table, filtered by date range
        $results = $wpdb->get_results($wpdb->prepare("
            SELECT 
                p.name AS participant_name, 
                p.organization, 
                v.name AS venue_name, 
                v.page_url, 
                p.unique_id, 
                p.start_time
            FROM $table_participants p
            JOIN $table_venues v ON p.venue_id = v.venue_id
            WHERE (p.name LIKE %s OR p.organization LIKE %s)
            AND p.start_time BETWEEN %s AND %s
            ORDER BY p.start_time ASC
        ", '%' . $search_query . '%', '%' . $search_query . '%', $start_date, $end_date));

        // Create results table HTML
        if (!empty($results)) {
            $results_html .= '<table style="width:100%; border-collapse: collapse; margin-top: 20px;">';
            $results_html .= '<thead><tr>
                <th style="border: 1px solid #ddd; padding: 8px;">Venue Name</th>
                <th style="border: 1px solid #ddd; padding: 8px;">Name</th>
                <th style="border: 1px solid #ddd; padding: 8px;">Organization</th>
                <th style="border: 1px solid #ddd; padding: 8px;">Start Time</th>
                <th style="border: 1px solid #ddd; padding: 8px;">Action</th>
            </tr></thead><tbody>';

            foreach ($results as $row) {
                $booking_url = esc_url($row->page_url . '?booking_id=' . $row->unique_id . '&passer=staff');

                $results_html .= sprintf(
                    '<tr>
                        <td style="border: 1px solid #ddd; padding: 8px;">%s</td>
                        <td style="border: 1px solid #ddd; padding: 8px;">%s</td>
                        <td style="border: 1px solid #ddd; padding: 8px;">%s</td>
                        <td style="border: 1px solid #ddd; padding: 8px;">%s</td>
                        <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">
                            <a href="%s" style="color: blue; text-decoration: underline;" target="_blank">View</a>
                        </td>
                    </tr>',
                    esc_html($row->venue_name),
                    esc_html($row->participant_name),
                    esc_html($row->organization),
                    esc_html($row->start_time),
                    $booking_url
                );
            }

            $results_html .= '</tbody></table>';
        } else {
            $results_html = '<p>No results found for your search.</p>';
        }
    }

    // Return the search form and results
    return '<H2>Search by Name</H2> 
        <form method="get" style="margin-bottom: 20px;">
            <input type="text" name="staff_search" value="' . esc_attr($search_query) . '" placeholder="Search by name or organization" style="padding: 8px; width: 70%;" required>
            <input type="date" name="start_date" value="' . esc_attr($start_date) . '" style="padding: 8px;">
            <input type="date" name="end_date" value="' . esc_attr($end_date) . '" style="padding: 8px;">
            <button type="submit" style="padding: 8px;">Search</button>
        </form>
        ' . $results_html;
}
add_shortcode('staff_name_search', 'display_staff_name_search');
?>
