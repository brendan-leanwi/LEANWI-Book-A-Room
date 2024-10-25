<?php
// Load WordPress environment to access $wpdb
require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';

global $wpdb; // Access the global $wpdb object

// Initialize arrays for categories and audiences
$categories = [];
$audiences = [];

// Fetch categories using $wpdb
$category_table = $wpdb->prefix . 'leanwi_booking_category'; // Add table prefix
$category_sql = "SELECT category_id, category_name, historic FROM $category_table WHERE historic = 0";
$categories = $wpdb->get_results($category_sql, ARRAY_A); // Fetch categories as associative arrays

// Fetch audiences using $wpdb
$audience_table = $wpdb->prefix . 'leanwi_booking_audience'; // Add table prefix
$audience_sql = "SELECT audience_id, audience_name, historic FROM $audience_table WHERE historic = 0";
$audiences = $wpdb->get_results($audience_sql, ARRAY_A); // Fetch audiences as associative arrays

// Return data as JSON
header('Content-Type: application/json');
echo json_encode([
    'categories' => $categories,
    'audiences' => $audiences
]);
?>