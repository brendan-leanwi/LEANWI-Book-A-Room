<?php
// Load WordPress environment to access $wpdb
require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';

global $wpdb;

$audiences = [];
$audience_table = $wpdb->prefix . 'leanwi_booking_audience';

// Fetch audiences using $wpdb->get_results()
$audience_sql = "SELECT audience_id, audience_name FROM $audience_table WHERE historic != 1 ORDER BY display_order ASC";
$audience_result = $wpdb->get_results($audience_sql, ARRAY_A);

// Check if audiences were found and sanitize output
if (!empty($audience_result)) {
    foreach ($audience_result as $audience) {
        $sanitized_audience = [
            'audience_id' => intval($audience['audience_id']),
            'audience_name' => esc_html($audience['audience_name'])
        ];
        $audiences[] = $sanitized_audience;
    }
}

// Return data as JSON
header('Content-Type: application/json');
echo json_encode($audiences);
?>
