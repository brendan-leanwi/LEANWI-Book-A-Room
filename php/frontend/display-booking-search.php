<?php
/**
 * Plugin Name: Display Booking Search
 * Description: A plugin to allow the user to display bookings based on agiven booking ref.
 * Version: 1.1
 * Author: Brendan Tuckey
 */

// Register the shortcode for the venue grid
function display_booking_search() {
    global $wpdb;

    // Check if a search query is submitted
    $search_query = isset($_GET['booking_search']) ? sanitize_text_field($_GET['booking_search']) : '';
    //$start_date = isset($_GET['start_date']) ? sanitize_text_field($_GET['start_date']) : date('Y-m-d');
    //$end_date = isset($_GET['end_date']) ? sanitize_text_field($_GET['end_date']) : date('Y-m-d', strtotime('+1 month'));
    $results_html = '<style>

    /* Wrapper for centering the table */
    .table-container {
        display: flex; /* Use flexbox to center */
        justify-content: center; /* Center horizontally */
        padding: 20px; /* Optional: Add some padding around the table */
    }

    /* Center the table and make it responsive */
    table {
        border-collapse: collapse; /* Remove gaps between table cells */
        width: 100%; /* Allow table to scale responsively */
        max-width: 800px; /* Set a max width */
        border: 1px solid #ddd; /* Optional: Add a border around the table */
    }

    /* Consistent cell borders and padding */
    table td, table th {
        border: 1px solid #ddd; /* Single border around cells */
        padding: 8px;
        text-align: left; /* Align text to the left for readability */
    }

    /* Responsive styling for smaller screens */
    @media screen and (max-width: 768px) {
        table {
            display: block;
            width: 100%;
            overflow-x: auto; /* Allow horizontal scrolling */
        }
        thead {
            display: none; /* Hide table headers */
        }
        tbody tr {
            display: flex;
            flex-direction: column;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            padding: 10px;
        }
        tbody td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 5px 0;
            border: none;
        }
        tbody td::before {
            content: attr(data-label);
            font-weight: bold;
            flex-shrink: 0;
            margin-right: 10px;
        }
    }
    </style>';

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
            WHERE (p.unique_id = %s)
            ORDER BY p.start_time ASC
        ", $search_query));

        // Create results table HTML
        if (!empty($results)) {
            $results_html .= '<div class="table-container">';
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
                        <td data-label="Venue Name" style="border: 1px solid #ddd; padding: 8px;">%s</td>
                        <td data-label="Name" style="border: 1px solid #ddd; padding: 8px;">%s</td>
                        <td data-label="Organization" style="border: 1px solid #ddd; padding: 8px;">%s</td>
                        <td data-label="Start Time" style="border: 1px solid #ddd; padding: 8px;">%s</td>
                        <td data-label="Action" style="border: 1px solid #ddd; padding: 8px; text-align: center;">
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

            $results_html .= '</tbody></table></div>';
        } else {
            $results_html = '<p>No results found for your search.</p>';
        }
    }

    // Return the search form and results
    return '
        <form method="get" style="margin-bottom: 20px;">
            <input type="text" name="booking_search" value="' . esc_attr($search_query) . '" placeholder="Enter your Booking ID" style="padding: 8px; width: 70%;" required>
            <button type="submit" style="padding: 8px;">Search</button>
        </form>
        ' . $results_html;
}
add_shortcode('booking_search', 'display_booking_search');
?>
