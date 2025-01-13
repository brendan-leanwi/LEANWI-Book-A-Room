<?php
namespace LEANWI_Book_A_Room;
/*
Plugin Name:  LEANWI Book A Room
GitHub URI:   https://github.com/brendan-leanwi/LEANWI-Book-A-Room
Description:  Room Booking functionality compatible with LEANWI Divi WordPress websites
Version:      1.2.13
Author:       Brendan Tuckey
Author URI:   https://github.com/brendan-leanwi
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
require_once plugin_dir_path(__FILE__) . 'php/plugin/plugin-updates.php';
require_once plugin_dir_path(__FILE__) . 'php/frontend/display-venue-grid.php'; // Contains the page and shortcode for the venue_grid shortcode
require_once plugin_dir_path(__FILE__) . 'php/frontend/staff/display-staff-venue-grid.php';
require_once plugin_dir_path(__FILE__) . 'php/frontend/staff/display-staff-name-search.php';
require_once plugin_dir_path(__FILE__) . 'php/frontend/display-booking-search.php';
require_once plugin_dir_path(__FILE__) . 'php/frontend/staff/display-staff-booking-search.php';
require_once plugin_dir_path(__FILE__) . 'php/frontend/staff/display-recurring-bookings.php';

// Hook to run when the plugin is activated
register_activation_hook(__FILE__, __NAMESPACE__ . '\\leanwi_create_tables');

// Hook to run when the plugin is uninstalled
register_uninstall_hook(__FILE__, __NAMESPACE__ . '\\leanwi_drop_tables');

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
            'showZeroCost' => get_option('leanwi_show_zero_cost', 'no'), // Default to No
            'sendAdminEmail' => get_option('leanwi_send_admin_booking_email', 'no'), // Default to No to not send an email if not set
            'adminEmailAddress' => get_option('leanwi_admin_email_address', ''), // Default to empty string if not set
            'highlightedButtonBgColor' => get_option('leanwi_highlighted_button_bg_color', '#ffe0b3'), // Highlighted button Background color
            'highlightedButtonBorderColor' => get_option('leanwi_highlighted_button_border_color', '#ff9800'), // Highlighted button Border color
            'highlightedButtonTextColor' => get_option('leanwi_highlighted_button_text_color', '#000000'), // Highlighted button Text color
            'showCategories' => get_option('leanwi_show_categories', 'no'),
            'showAudiences' => get_option('leanwi_show_audiences', 'no'),
            'enableRecaptcha' => get_option('leanwi_enable_recaptcha', 'no'),
            'recaptchaSiteKey' => get_option('leanwi_recaptcha_site_key', '')
        ));

        wp_enqueue_script('venue-booking-js');
    }

    if (is_page() && has_shortcode(get_post()->post_content, 'staff_venue_details')) {
        wp_register_script(
            'staff-venue-booking-js',
            plugin_dir_url(__FILE__) . 'js/staff-venue-booking.js',
            array('jquery'),
            filemtime(plugin_dir_path(__FILE__) . 'js/staff-venue-booking.js'), 
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

    if (is_page() && has_shortcode(get_post()->post_content, 'venue_grid')) {
        wp_register_script(
            'venue-grid-js',
            plugin_dir_url(__FILE__) . 'js/venue-grid.js',
            array('jquery'),
            filemtime(plugin_dir_path(__FILE__) . 'js/venue-grid.js'), 
            true
        );

        wp_enqueue_script('venue-grid-js');
    }

    if (is_page() && has_shortcode(get_post()->post_content, 'staff_venue_grid')) {
        wp_register_script(
            'staff-venue-grid-js',
            plugin_dir_url(__FILE__) . 'js/staff-venue-grid.js',
            array('jquery'),
            filemtime(plugin_dir_path(__FILE__) . 'js/staff-venue-grid.js'), 
            true
        );

        wp_enqueue_script('staff-venue-grid-js');
    }

    if (is_page() && has_shortcode(get_post()->post_content, 'staff_name_search')) {
        wp_register_script(
            'staff-name-search-js',
            plugin_dir_url(__FILE__) . 'js/staff-name-search.js',
            array('jquery'),
            filemtime(plugin_dir_path(__FILE__) . 'js/staff-name-search.js'), 
            true
        );

        wp_enqueue_script('staff-name-search-js');
    }

    if (is_page() && has_shortcode(get_post()->post_content, 'booking_search')) {
        wp_register_script(
            'booking-search-js',
            plugin_dir_url(__FILE__) . 'js/booking-search.js',
            array('jquery'),
            filemtime(plugin_dir_path(__FILE__) . 'js/booking-search.js'), 
            true
        );

        wp_enqueue_script('booking-search-js');
    }

    if (is_page() && has_shortcode(get_post()->post_content, 'staff_booking_search')) {
        wp_register_script(
            'staff-booking-search-js',
            plugin_dir_url(__FILE__) . 'js/staff-booking-search.js',
            array('jquery'),
            filemtime(plugin_dir_path(__FILE__) . 'js/staff-booking-search.js'), 
            true
        );

        wp_enqueue_script('staff-booking-search-js');
    }

    if (is_page() && has_shortcode(get_post()->post_content, 'staff_recurring_bookings')) {
        wp_register_script(
            'staff-recurring-bookings-js',
            plugin_dir_url(__FILE__) . 'js/staff-recurring-bookings.js',
            array('jquery'),
            filemtime(plugin_dir_path(__FILE__) . 'js/staff-recurring-bookings.js'), 
            true
        );

        // Localize the maximum booking slots setting and maxMonths
        wp_localize_script('staff-recurring-bookings-js', 'bookingSettings', array(
            'minutesInterval' => intval(get_option('leanwi_minutes_interval', 30)), // Default to 30 minutes if not set
            'highlightedButtonBgColor' => get_option('leanwi_highlighted_button_bg_color', '#ffe0b3'), // Highlighted button Background color
            'highlightedButtonBorderColor' => get_option('leanwi_highlighted_button_border_color', '#ff9800'), // Highlighted button Border color
            'highlightedButtonTextColor' => get_option('leanwi_highlighted_button_text_color', '#000000'), // Highlighted button Text color
            'enableRecaptcha' => get_option('leanwi_enable_recaptcha', 'no'),
            'recaptchaSiteKey' => get_option('leanwi_recaptcha_site_key', '')
        ));

        wp_enqueue_script('staff-recurring-bookings-js');
    }
}
add_action('wp_enqueue_scripts', __NAMESPACE__ . '\\leanwi_enqueue_scripts');


function enqueue_custom_styles() {
    if (is_page() && (
        has_shortcode(get_post()->post_content, 'venue_grid') || 
        has_shortcode(get_post()->post_content, 'staff_venue_grid') || 
        has_shortcode(get_post()->post_content, 'staff_recurring_bookings') || 
        has_shortcode(get_post()->post_content, 'staff_venue_details') || 
        has_shortcode(get_post()->post_content, 'venue_details') || 
        has_shortcode(get_post()->post_content, 'booking_search') ||
        has_shortcode(get_post()->post_content, 'staff_booking_search') ||
        has_shortcode(get_post()->post_content, 'staff_name_search')))
    {
        wp_enqueue_style('custom-calendar-style', plugin_dir_url(__FILE__) . 'css/booking-style.css');
    }
}
add_action('wp_enqueue_scripts', __NAMESPACE__ . '\\enqueue_custom_styles');
