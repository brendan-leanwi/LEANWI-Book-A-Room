<?php
// Load WordPress environment to access $wpdb
require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';

global $wpdb;

$affirmations = [];
$table = $wpdb->prefix . 'leanwi_booking_affirmation';

// Fetch affirmations using $wpdb->get_results()
$sql = "SELECT id, affirmation FROM $table";
$result = $wpdb->get_results($sql, ARRAY_A);

// Check if affirmations were found and sanitize output
if (!empty($result)) {
    foreach ($result as $affirmation) {
        $sanitized_affirmation = [
            'id' => intval($affirmation['id']),
            'affirmation' => esc_html($affirmation['affirmation'])
        ];
        $affirmations[] = $sanitized_affirmation;
    }
}

// Return data as JSON
header('Content-Type: application/json');
echo json_encode($affirmations);
?>
