<?php
/*
Plugin Name:  LEANWI Book A Room
Plugin URI:   https://base1.librarieswin.org
Description:  Room Booking functionality compatible with LEANWI Divi WordPress websites
Version:      1.0
Author:       Brendan Tuckey
Author URI:   https://leanwi.org
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:  leanwi-tutorial
Domain Path:  /languages
*/

// Require additional PHP files
require_once plugin_dir_path(__FILE__) . 'php/plugin/menu-functions.php';  // Menu Functions File
require_once plugin_dir_path(__FILE__) . 'php/plugin/schema.php'; //File containing table create and drop statements
require_once plugin_dir_path(__FILE__) . 'php/frontend/display-venue-details.php'; // Contains the page and shortcode for the venue_details shortcode
require_once plugin_dir_path(__FILE__) . 'php/frontend/staff/display-staff-venue-details.php'; // Contains the page and shortcode for the staff_venue_details shortcode
//require_once plugin_dir_path(__FILE__) . 'php/plugin/generate-report.php';

// Hook to run when the plugin is activated
register_activation_hook(__FILE__, 'leanwi_create_tables');

// Hook to run when the plugin is uninstalled
register_uninstall_hook(__FILE__, 'leanwi_drop_tables');

// Register the JavaScript files
function leanwi_enqueue_scripts() {
    if (is_page() && has_shortcode(get_post()->post_content, 'venue_details')) {
        wp_register_script(
            'venue-booking-js',
            plugin_dir_url(__FILE__) . 'js/venue-booking.js',
            array('jquery'),
            filemtime(plugin_dir_path(__FILE__) . 'js/venue-booking.js'), // Version based on file modification time
            true
        );

        // Localize the maximum booking slots setting and maxMonths
        wp_localize_script('venue-booking-js', 'bookingSettings', array(
            'maxMonths' => intval(get_option('leanwi_booking_months', 2)), // Default to 2 months if not set
            'minutesInterval' => intval(get_option('leanwi_minutes_interval', 30)), // Default to 30 minutes if not set
            'sendAdminEmail' => get_option('leanwi_send_admin_booking_email', 'no'), // Default to No to not send an email if not set
            'adminEmailAddress' => get_option('leanwi_admin_email_address', ''), // Default to empty string if not set
            'highlightedButtonBgColor' => get_option('leanwi_highlighted_button_bg_color', '#ffe0b3'), // Highlighted button Background color
            'highlightedButtonBorderColor' => get_option('leanwi_highlighted_button_border_color', '#ff9800'), // Highlighted button Border color
            'highlightedButtonTextColor' => get_option('leanwi_highlighted_button_text_color', '#000000') // Highlighted button Text color
        ));

        wp_enqueue_script('venue-booking-js');
    }
    else if (is_page() && has_shortcode(get_post()->post_content, 'staff_venue_details')) {
        wp_register_script(
            'staff-venue-booking-js',
            plugin_dir_url(__FILE__) . 'js/staff-venue-booking.js',
            array('jquery'),
            filemtime(plugin_dir_path(__FILE__) . 'js/staff-venue-booking.js'), // Version based on file modification time
            true
        );

        // Localize the maximum booking slots setting and maxMonths
        wp_localize_script('staff-venue-booking-js', 'bookingSettings', array(
            'maxMonths' => intval(get_option('leanwi_booking_months', 2)), // Default to 2 months if not set
            'minutesInterval' => intval(get_option('leanwi_minutes_interval', 30)), // Default to 30 minutes if not set
            'sendAdminEmail' => get_option('leanwi_send_admin_booking_email', 'no'), // Default to No to not send an email if not set
            'adminEmailAddress' => get_option('leanwi_admin_email_address', ''), // Default to empty string if not set
            'highlightedButtonBgColor' => get_option('leanwi_highlighted_button_bg_color', '#ffe0b3'), // Highlighted button Background color
            'highlightedButtonBorderColor' => get_option('leanwi_highlighted_button_border_color', '#ff9800'), // Highlighted button Border color
            'highlightedButtonTextColor' => get_option('leanwi_highlighted_button_text_color', '#000000') // Highlighted button Text color
        ));

        wp_enqueue_script('staff-venue-booking-js');
    }
}
add_action('wp_enqueue_scripts', 'leanwi_enqueue_scripts');

// Add this to your plugin or theme's PHP file
function enqueue_custom_styles() {
    wp_enqueue_style('custom-calendar-style', plugin_dir_url(__FILE__) . 'css/booking-style.css');
}
add_action('wp_enqueue_scripts', 'enqueue_custom_styles');