<?php
namespace LEANWI_Book_A_Room;

add_action('wp_ajax_leanwi_get_affirmations', __NAMESPACE__ . '\\leanwi_get_affirmations');
add_action('wp_ajax_nopriv_leanwi_get_affirmations', __NAMESPACE__ . '\\leanwi_get_affirmations');

function leanwi_get_affirmations() {
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
                'affirmation' => sanitize_textarea_field($affirmation['affirmation'])
            ];
            $affirmations[] = $sanitized_affirmation;
        }
    }

    // Return data as JSON
    wp_send_json_success($affirmations);
}
?>
