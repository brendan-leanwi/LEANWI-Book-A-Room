<?php

function leanwi_check_for_plugin_updates($transient) {
    // Don't proceed if the transient is empty
    if (empty($transient->checked)) {
        return $transient;
    }

    // Define GitHub repo URL and API endpoint
    $repo = 'brendan-leanwi/LEANWI-Book-A-Room';
    $api_url = "https://api.github.com/repos/{$repo}/releases/latest";

    // Get release information from GitHub
    $response = wp_remote_get($api_url);
    if (is_wp_error($response)) {
        error_log("GitHub API error: " . $response->get_error_message());
        return $transient;
    }

    $release = json_decode(wp_remote_retrieve_body($response));
    if (!isset($release->tag_name)) {
        error_log("GitHub tag_name is not set.");
        return $transient;
    }

    // Get the main plugin file
    $plugin_file = plugin_basename(dirname(__FILE__) . '/../leanwi-book-a-room.php');
    error_log("Plugin file: " . $plugin_file);
    error_log("Checked plugins: " . print_r($transient->checked, true));

    if (!isset($transient->checked[$plugin_file])) {
        error_log("Plugin not found in the checked plugins list.");
        return $transient; // Ensure the plugin is in the checked list
    }

    $latest_version = ltrim($release->tag_name, 'v'); // Remove 'v' if present in the GitHub tag
    $current_version = $transient->checked[$plugin_file] ?? '';

    error_log("Current version: " . $current_version);
    error_log("Latest version from GitHub: " . $latest_version);

    if (version_compare((string)$current_version, (string)$latest_version, '<')) {
        // Define the update data
        error_log("Update available: Current version is older than the latest version.");
        $transient->response[$plugin_file] = (object) array(
            'slug'        => basename(__DIR__),
            'new_version' => $latest_version,
            'package'     => $release->zipball_url, // GitHub zip URL for the release
            'url'         => "https://github.com/{$repo}",
        );
    } else {
        error_log("No update needed: Current version is up-to-date.");
    }
    
    error_log("Transient response after check: " . print_r($transient, true));
    return $transient;
}
add_filter('site_transient_update_plugins', 'leanwi_check_for_plugin_updates');


function leanwi_plugin_update_info($false, $action, $response) {
    // Define the plugin slug (folder name of your plugin)
    $plugin_slug = basename(__DIR__);

    if (isset($response->slug) && $response->slug === $plugin_slug) {
        // Define GitHub repo URL and API endpoint
        $repo = 'brendan-leanwi/LEANWI-Book-A-Room';
        $api_url = "https://api.github.com/repos/{$repo}/releases/latest";

        $remote_info = wp_remote_get($api_url);
        if (!is_wp_error($remote_info)) {
            $release = json_decode(wp_remote_retrieve_body($remote_info));
            if (isset($release->body)) {
                $response->sections = array(
                    'description' => $release->body,
                );
            }
        }
    }

    return $response;
}
add_filter('plugins_api', 'leanwi_plugin_update_info', 20, 3);

