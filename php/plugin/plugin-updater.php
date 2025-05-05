<?php
namespace LEANWI_Book_A_Room;

// Define GitHub repository details
const GITHUB_REPO = 'brendan-leanwi/LEANWI-Book-A-Room';
const PLUGIN_SLUG = 'leanwi-book-a-room';
const PLUGIN_FILE = 'LEANWI-Book-A-Room/leanwi-book-a-room.php';

/**
 * Check GitHub for plugin updates and inject update data into WordPress.
 */
function check_for_plugin_updates($transient) {
    if (empty($transient->checked) || !isset($transient->checked[PLUGIN_FILE])) {
        return $transient;
    }

    $current_version = $transient->checked[PLUGIN_FILE];
    $release = get_latest_github_release();

    if (!$release || empty($release->tag_name)) {
        return $transient;
    }

    $latest_version = ltrim($release->tag_name, 'v');

    if (version_compare($current_version, $latest_version, '<')) {
        $transient->response[PLUGIN_FILE] = (object)[
            'slug'        => PLUGIN_SLUG,
            'plugin'      => PLUGIN_FILE,
            'new_version' => $latest_version,
            'url'         => "https://github.com/" . GITHUB_REPO,
            'package'     => $release->zipball_url,
        ];
    }

    return $transient;
}
add_filter('site_transient_update_plugins', __NAMESPACE__ . '\\check_for_plugin_updates');

/**
 * Add details to the plugin info screen in WP admin.
 */
function plugin_update_info($res, $action, $args) {
    if ($action !== 'plugin_information' || empty($args->slug) || $args->slug !== PLUGIN_SLUG) {
        return $res;
    }

    $release = get_latest_github_release();
    if (!$release || empty($release->tag_name)) {
        return $res;
    }

    $latest_version = ltrim($release->tag_name, 'v');

    $plugin = (object)[
        'name'            => 'LEANWI Book A Room',
        'slug'            => PLUGIN_SLUG,
        'version'         => $latest_version,
        'new_version'     => $latest_version,
        'author'          => '<a href="https://github.com/brendan-leanwi">Brendan Tuckey</a>',
        'homepage'        => 'https://github.com/' . GITHUB_REPO,
        'download_link'   => $release->zipball_url,
        'requires'        => '5.0',
        'tested'          => '6.5',
        'sections'        => [
            'description' => 'Room Booking functionality compatible with LEANWI Divi WordPress websites.',
        ],
    ];

    return $plugin;
}
add_filter('plugins_api', __NAMESPACE__ . '\\plugin_update_info', 10, 3);

/**
 * Rename plugin directory if installed via GitHub ZIP (which appends random hash).
 */
function override_post_install($true, $hook_extra, $result) {
    global $wp_filesystem;

    if (isset($hook_extra['plugin']) && $hook_extra['plugin'] === PLUGIN_FILE) {
        $corrected_path = trailingslashit(WP_PLUGIN_DIR) . 'LEANWI-Book-A-Room';

        // Move plugin to expected folder
        if ($wp_filesystem->move($result['destination'], $corrected_path, true)) {
            $result['destination'] = $corrected_path;
        }
    }

    return $result;
}
add_filter('upgrader_post_install', __NAMESPACE__ . '\\override_post_install', 10, 3);

/**
 * Enable auto-updates for this plugin.
 */
add_filter('auto_update_plugin', function($update, $item) {
    if (isset($item->slug) && $item->slug === PLUGIN_SLUG) {
        return true;
    }
    return $update;
}, 10, 2);

/**
 * Fetch the latest release from GitHub API with appropriate headers.
 */
function get_latest_github_release() {
    $api_url = "https://api.github.com/repos/" . GITHUB_REPO . "/releases/latest";

    $response = wp_remote_get($api_url, [
        'headers' => [
            'Accept'     => 'application/vnd.github.v3+json',
            'User-Agent' => 'WordPress/' . get_bloginfo('version') . '; ' . home_url(),
        ],
        'timeout' => 15,
    ]);

    if (is_wp_error($response)) {
        return null;
    }

    $body = wp_remote_retrieve_body($response);
    return json_decode($body);
}
