<?php
// Load WordPress environment to access $wpdb
require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';

global $wpdb;

$audiences = [];
$audience_table = $wpdb->prefix . 'leanwi_booking_audience';

// Fetch audiences
// Fetch audiences using $wpdb->get_results()
$audience_sql = "SELECT audience_id, audience_name, historic FROM $audience_table";
$audience_result = $wpdb->get_results($audience_sql, ARRAY_A);

// Check if audiences were found
if (!empty($audience_result)) {
    $audiences = $audience_result;
}

// Return data as JSON
header('Content-Type: application/json');
echo json_encode($audiences);
?>
