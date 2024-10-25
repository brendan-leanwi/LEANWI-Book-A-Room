<?php
// Load WordPress environment to access $wpdb
require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';

global $wpdb;

$categories = [];
$category_table = $wpdb->prefix . 'leanwi_booking_category';

// Fetch categories
// Fetch categories using $wpdb->get_results()
$category_sql = "SELECT category_id, category_name, historic FROM $category_table";
$category_result = $wpdb->get_results($category_sql, ARRAY_A);

// Check if categories were found
if (!empty($category_result)) {
    $categories = $category_result;
}

// Return data as JSON
header('Content-Type: application/json');
echo json_encode($categories);
?>
