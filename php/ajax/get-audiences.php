<?php
namespace LEANWI_Book_A_Room;

add_action('wp_ajax_leanwi_get_audiences', __NAMESPACE__ . '\\leanwi_get_audiences');
add_action('wp_ajax_nopriv_leanwi_get_audiences', __NAMESPACE__ . '\\leanwi_get_audiences');

function leanwi_get_audiences() {
    global $wpdb;

    $audiences = [];
    $audience_table = $wpdb->prefix . 'leanwi_booking_audience';

    // Fetch audiences using $wpdb->get_results()
    $audience_sql = "SELECT audience_id, audience_name FROM $audience_table WHERE historic != 1 ORDER BY display_order ASC";
    $audience_result = $wpdb->get_results($audience_sql, ARRAY_A);

    // Sanitize data before assigning it to the $audiences array
    if (!empty($audience_result)) {
        foreach ($audience_result as $audience) {


            $audiences[] = [
                'audience_id' => intval($audience['audience_id']), // Ensure integer for ID
                'audience_name' => isset($audience['audience_name']) && $audience['audience_name'] !== null ? html_entity_decode($audience['audience_name'], ENT_QUOTES) : ''
            ];
        }
    }

    // Return data as JSON
    wp_send_json_success($audiences);
}
?>