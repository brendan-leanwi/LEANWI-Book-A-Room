<?php
// Load WordPress environment to access $wpdb
require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';

global $wpdb;

$categories = [];
$category_table = $wpdb->prefix . 'leanwi_booking_category';

// Fetch categories using $wpdb->get_results()
$category_sql = "SELECT category_id, category_name FROM $category_table WHERE historic != 1 ORDER BY display_order ASC";
$category_result = $wpdb->get_results($category_sql, ARRAY_A);

// Sanitize data before assigning it to the $categories array
if (!empty($category_result)) {
    foreach ($category_result as $category) {


        $categories[] = [
            'category_id' => intval($category['category_id']), // Ensure integer for ID
            'category_name' => isset($category['category_name']) && $category['category_name'] !== null ? html_entity_decode($category['category_name'], ENT_QUOTES) : ''
        ];
    }
}

// Return data as JSON
header('Content-Type: application/json');
echo json_encode($categories);
?>