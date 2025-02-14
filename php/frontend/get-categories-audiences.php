<?php
// Load WordPress environment to access $wpdb
require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';

global $wpdb; // Access the global $wpdb object

// Initialize sanitized arrays for categories and audiences
$categories = [];
$audiences = [];

// Fetch and sanitize categories using $wpdb
$category_table = $wpdb->prefix . 'leanwi_booking_category';
$category_sql = "SELECT category_id, category_name, display_order, historic FROM $category_table WHERE historic = 0 ORDER BY display_order ASC";
$category_results = $wpdb->get_results($category_sql, ARRAY_A);

if (!empty($category_results)) {
    foreach ($category_results as $category) {
        $categories[] = [
            'category_id' => intval($category['category_id']), // Ensure integer
            'category_name' => esc_html($category['category_name']), // Escape for HTML safety
            'display_order' => intval($category['display_order']), // Ensure integer
            'historic' => intval($category['historic']) // Ensure integer for boolean/flag field
        ];
    }
}

// Fetch and sanitize audiences using $wpdb
$audience_table = $wpdb->prefix . 'leanwi_booking_audience';
$audience_sql = "SELECT audience_id, audience_name, display_order, historic FROM $audience_table WHERE historic = 0 ORDER BY display_order ASC";
$audience_results = $wpdb->get_results($audience_sql, ARRAY_A);

if (!empty($audience_results)) {
    foreach ($audience_results as $audience) {
        $audiences[] = [
            'audience_id' => intval($audience['audience_id']), // Ensure integer
            'audience_name' => esc_html($audience['audience_name']), // Escape for HTML safety
            'display_order' => intval($category['display_order']), // Ensure integer
            'historic' => intval($audience['historic']) // Ensure integer for boolean/flag field
        ];
    }
}

// Return data as JSON
header('Content-Type: application/json');
echo json_encode([
    'categories' => $categories,
    'audiences' => $audiences
]);
?>
