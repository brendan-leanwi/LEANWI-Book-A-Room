<?php
/**
 * Plugin Name: Display Name Search
 * Description: A plugin to allow the user to display bookings based on a name or organization search.
 * Version: 1.0
 * Author: Brendan Tuckey
 */

// Register the shortcode for the venue grid
function display_staff_name_search() {
    global $wpdb;

    // Check if a search query is submitted
    $search_query = isset($_GET['staff_search']) ? sanitize_text_field($_GET['staff_search']) : '';
    $results_html = '';

    // If a search query is provided, search the database
    if ($search_query) {
        $table_participants = $wpdb->prefix . 'leanwi_booking_participant';
        $table_venues = $wpdb->prefix . 'leanwi_booking_venue';

        // SQL query to search in name or organization and join with venue table
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
            WHERE p.name LIKE %s OR p.organization LIKE %s
            ORDER BY p.start_time ASC
        ", '%' . $search_query . '%', '%' . $search_query . '%'));

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
                $booking_url = esc_url($row->page_url . '?booking_id=' . $row->unique_id);

                $results_html .= sprintf(
                    '<tr>
                        <td style="border: 1px solid #ddd; padding: 8px;">%s</td>
                        <td style="border: 1px solid #ddd; padding: 8px;">%s</td>
                        <td style="border: 1px solid #ddd; padding: 8px;">%s</td>
                        <td style="border: 1px solid #ddd; padding: 8px;">%s</td>
                        <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">
                            <a href="%s" style="color: blue; text-decoration: underline;">View</a>
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
    return '
        <form method="get" style="margin-bottom: 20px;">
            <input type="text" name="staff_search" value="' . esc_attr($search_query) . '" placeholder="Search by name or organization" style="padding: 8px; width: 80%;" required>
            <button type="submit" style="padding: 8px;">Search</button>
        </form>
        ' . $results_html;
}
add_shortcode('staff_name_search', 'display_staff_name_search');

?>