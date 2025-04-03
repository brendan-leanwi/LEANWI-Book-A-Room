<?php
// Load WordPress environment
require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');

global $wpdb;

// Table names
$recurrence_table = $wpdb->prefix . 'leanwi_booking_recurrence';
$venue_table = $wpdb->prefix . 'leanwi_booking_venue';

// Query with JOIN
$sql = "
    SELECT  
        r.recurrence_id,
        r.recurrence_type,
        r.recurrence_interval,
        r.recurrence_end_date,
        r.start_time,
        r.end_time,
        r.organization,
        r.name AS recurrence_name,
        v.name AS venue_name
    FROM $recurrence_table r
    LEFT JOIN $venue_table v ON r.venue_id = v.venue_id
    ORDER BY v.name ASC, r.organization ASC, r.name ASC
";

$recurrences = $wpdb->get_results($sql, ARRAY_A);

// Sanitize and prepare the response
$sanitized_recurrences = array_map(function($recurrence) {
    return [
        'recurrence_id' => intval($recurrence['recurrence_id']),
        'venue_name' => sanitize_text_field($recurrence['venue_name']),
        'recurrence_type' => esc_html($recurrence['recurrence_type']),
        'recurrence_interval' => intval($recurrence['recurrence_interval']),
        'recurrence_end_date' => esc_html($recurrence['recurrence_end_date']),
        'start_time' => esc_html($recurrence['start_time']),
        'end_time' => esc_html($recurrence['end_time']),
        'organization' => sanitize_text_field($recurrence['organization'] ?? 'N/A'),
        'recurrence_name' => sanitize_text_field($recurrence['recurrence_name']),
    ];
}, $recurrences);

//echo json_encode($sanitized_recurrences);
echo json_encode($sanitized_recurrences, JSON_UNESCAPED_SLASHES);
