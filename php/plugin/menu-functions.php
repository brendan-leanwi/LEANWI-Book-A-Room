<?php

/**************************************************************************************************
 * Main Menu and Main Page
 **************************************************************************************************/

function leanwi_add_admin_menu() {
    // Parent menu: "LEANWI Book-A-Room"
    add_menu_page(
        'LEANWI-Book-A-Room',   // Page title (for the parent menu)
        'LEANWI-Book-A-Room',     // Menu title (for the plugin name in the dashboard)
        'manage_options',         // Capability
        'leanwi-book-a-room-main', // Menu slug
        'leanwi_main_page',       // Callback function
        'dashicons-calendar',     // Menu icon (optional)
        6                         // Position
    );

    // Sub-menu: "Documentation"
    add_submenu_page(
        'leanwi-book-a-room-main',    // Parent slug
        'Documentation and Support',  // Page title (for the actual documentation page)
        'Documentation',              // Menu title (this will be the first submenu item)
        'manage_options',             // Capability
        'leanwi-book-a-room-main',    // Menu slug (reuse 'leanwi-book-a-room-main' to link it to the parent page)
        'leanwi_main_page'            // Callback function (this will now display the Documentation page)
    );

    // Sub-menu: "Venues"
    add_submenu_page(
        'leanwi-book-a-room-main',    // Parent slug
        'Venues',                     // Page title
        'Venues',                     // Menu title
        'manage_options',             // Capability
        'leanwi-book-a-room-venues',  // Menu slug
        'leanwi_venues_page'          // Callback function to display venues
    );

    // Sub-menu: "Add Venue"
    add_submenu_page(
        'leanwi-book-a-room-main',
        'Add Venue',
        'Add Venue',
        'manage_options',
        'leanwi-add-venue',
        'leanwi_add_venue_page'
    );

    // Sub-menu: "Edit Venue"
    add_submenu_page(
        'leanwi-book-a-room-main', // Parent slug (linked to Venues submenu)
        'Edit Venue',                 // Page title
        'Edit Venue',                 // Menu title
        'manage_options',             // Capability
        'leanwi-edit-venue',          // Menu slug
        'leanwi_edit_venue_page'      // Callback function to display the edit venue form
    );

    // Sub-menu: "Categories"
    add_submenu_page(
        'leanwi-book-a-room-main',    // Parent slug
        'Categories',                   // Page title
        'Categories',                   // Menu title
        'manage_options',             // Capability
        'leanwi-book-a-room-categories',// Menu slug
        'leanwi_categories_page'        // Callback function to display settings
    );

    // Sub-menu: "Add Category"
    add_submenu_page(
        'leanwi-book-a-room-main',
        'Add Category',
        'Add Category',
        'manage_options',
        'leanwi-add-category',
        'leanwi_add_category_page'
    );

    // Sub-menu: "Edit Category"
    add_submenu_page(
        'leanwi-book-a-room-main', // Parent slug (linked to Categories submenu)
        'Edit Category',                 // Page title
        'Edit Category',                 // Menu title
        'manage_options',             // Capability
        'leanwi-edit-category',          // Menu slug
        'leanwi_edit_category_page'      // Callback function to display the edit venue form
    );

    // Sub-menu: "Audiences"
    add_submenu_page(
        'leanwi-book-a-room-main',    // Parent slug
        'Audiences',                   // Page title
        'Audiences',                   // Menu title
        'manage_options',             // Capability
        'leanwi-book-a-room-audiences',// Menu slug
        'leanwi_audiences_page'        // Callback function to display settings
    );

    // Sub-menu: "Add Audience"
    add_submenu_page(
        'leanwi-book-a-room-main',
        'Add Audience',
        'Add Audience',
        'manage_options',
        'leanwi-add-audience',
        'leanwi_add_audience_page'
    );

    // Sub-menu: "Edit Audience"
    add_submenu_page(
        'leanwi-book-a-room-main', // Parent slug (linked to Audiences submenu)
        'Edit Audience',                 // Page title
        'Edit Audience',                 // Menu title
        'manage_options',             // Capability
        'leanwi-edit-audience',          // Menu slug
        'leanwi_edit_audience_page'      // Callback function to display the edit venue form
    );

    // Sub-menu: "Reports"
    add_submenu_page(
        'leanwi-book-a-room-main',    // Parent slug
        'Reports',                   // Page title
        'Reporting',                   // Menu title
        'manage_options',             // Capability
        'leanwi-book-a-room-reports',// Menu slug
        'leanwi_reports_page'        // Callback function to display settings
    );

    // Sub-menu: "Settings"
    add_submenu_page(
        'leanwi-book-a-room-main',    // Parent slug
        'Settings',                   // Page title
        'Settings',                   // Menu title
        'manage_options',             // Capability
        'leanwi-book-a-room-settings',// Menu slug
        'leanwi_settings_page'        // Callback function to display settings
    );
}

// Hook to create the admin menu
add_action('admin_menu', 'leanwi_add_admin_menu');

// Hide the Add and Edit pages submenus from the left-hand navigation menu using CSS
function leanwi_hide_add_edit_submenus_css() {
    echo '<style>
        #toplevel_page_leanwi-book-a-room-main .wp-submenu a[href="admin.php?page=leanwi-add-venue"] {
            display: none !important;
        }
        #toplevel_page_leanwi-book-a-room-main .wp-submenu a[href="admin.php?page=leanwi-edit-venue"] {
            display: none !important;
        }
        #toplevel_page_leanwi-book-a-room-main .wp-submenu a[href="admin.php?page=leanwi-add-category"] {
            display: none !important;
        }
        #toplevel_page_leanwi-book-a-room-main .wp-submenu a[href="admin.php?page=leanwi-edit-category"] {
            display: none !important;
        }
        #toplevel_page_leanwi-book-a-room-main .wp-submenu a[href="admin.php?page=leanwi-add-audience"] {
            display: none !important;
        }
        #toplevel_page_leanwi-book-a-room-main .wp-submenu a[href="admin.php?page=leanwi-edit-audience"] {
            display: none !important;
        }
    </style>';
}
add_action('admin_head', 'leanwi_hide_add_edit_submenus_css');

// Function to display the main page
function leanwi_main_page() {
    ?>
    <div class="wrap">
        <h1>Documentation and Support</h1>
        <p>Welcome to the LEANWI Book-A-Room plugin!</p>
    </div>
    <?php
}

/**************************************************************************************************
 * Venues
 **************************************************************************************************/

// Function to display the list of venues
function leanwi_venues_page() {
    
    // Display venue list
    echo '<div class="wrap">';
    echo '<h1>Venues</h1>';

    echo '<a href="' . admin_url('admin.php?page=leanwi-add-venue') . '" class="button button-primary">Add Venue</a>'; // Add this button
    echo '<p> </p>'; //Space below the button before the venue table

    echo '<table class="wp-list-table widefat striped">';
    echo '<thead>';
    echo '<tr>';
    echo '<th scope="col">Venue ID</th>';
    echo '<th scope="col">Name</th>';
    echo '<th scope="col">Capacity</th>';
    echo '<th scope="col">Location</th>';
    echo '<th scope="col">Description</th>';
    echo '<th scope="col">Historic</th>';
    echo '<th scope="col">Actions</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';

    // Fetch venues
    $venues = fetch_venues();
    if (isset($venues['error'])) {
        echo '<tr><td colspan="6">' . esc_html($venues['error']) . '</td></tr>';
    } else {
        // Display each venue in a row
        foreach ($venues['venues'] as $venue) {
            echo '<tr>';
            echo '<td>' . esc_html($venue['venue_id']) . '</td>';
            echo '<td>' . esc_html($venue['name']) . '</td>';
            echo '<td>' . esc_html($venue['capacity']) . '</td>';
            echo '<td>' . esc_html($venue['location']) . '</td>';
            echo '<td>' . esc_html($venue['description']) . '</td>';
            echo '<td>' . ($venue['historic'] == 0 ? 'False' : 'True') . '</td>';
            echo '<td>';
            echo '<a href="' . admin_url('admin.php?page=leanwi-edit-venue&venue_id=' . esc_attr($venue['venue_id'])) . '" class="button">Edit</a> ';
            echo '</td>';
            echo '</tr>';
        }
    }

    echo '</tbody>';
    echo '</table>';
    echo '<p> </p>'; //Space below the venue table
    echo 'Please add the following shortcode to your page - [venue_details venue_id="1"]. Where 1 is the Venue ID from the above table';
    echo '</div>';
}
// Function to get venues
function fetch_venues() {
    // Construct the URL for get-venues.php
    $url = plugins_url('LEANWI-Book-A-Room/php/plugin/get-venues.php');
    // Log the URL to the debug.log file
    error_log('Fetching venues from URL: ' . $url); // Log the URL

    // Use wp_remote_get to fetch the data with SSL verification disabled
    $response = wp_remote_get($url, [
        'sslverify' => false, // Disable SSL verification
        'timeout' => 15, // Increase the timeout to 15 seconds
    ]);

    // Check for errors
    if (is_wp_error($response)) {
        // Handle error
        error_log('Error fetching venues: ' . $response->get_error_message());
        return ['error' => 'Unable to fetch venues.'];
    }

    // Get the body of the response
    $body = wp_remote_retrieve_body($response);

    // Decode the JSON response
    $venues = json_decode($body, true);

    return $venues; // Return the venues array
}

function leanwi_add_venue_page() {
    global $wpdb;
    $venue_table = $wpdb->prefix . 'leanwi_booking_venue';
    $hours_table = $wpdb->prefix . 'leanwi_booking_venue_hours';

    // Check if the form has been submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Verify nonce before processing the form
        if (isset($_POST['venue_nonce']) && wp_verify_nonce($_POST['venue_nonce'], 'add_venue_action')) {
            // The nonce is valid; proceed with form processing.
            $name = sanitize_text_field($_POST['name']);
            $capacity = isset($_POST['capacity']) ? intval($_POST['capacity']) : 0;
            $description = sanitize_textarea_field($_POST['description']);
            $location = sanitize_text_field($_POST['location']);
            $image_url = esc_url($_POST['image_url']);
            $extra_text = sanitize_text_field($_POST['extra_text']);
            $max_slots = isset($_POST['max_slots']) ? intval($_POST['max_slots']) : 0;
            $slot_cost = isset($_POST['slot_cost']) ? floatval($_POST['slot_cost']) : 0.00;
            $email_text = sanitize_text_field($_POST['email_text']);
            $page_url = esc_url($_POST['page_url']);

            // Ensure the value has 2 decimal places
            $slot_cost = number_format($slot_cost, 2, '.', '');

            // Insert the new venue into the database
            $inserted = $wpdb->insert(
                $venue_table,
                array(
                    'name' => $name,
                    'capacity' => $capacity,
                    'description' => $description,
                    'location' => $location,
                    'image_url' => $image_url,
                    'extra_text' => $extra_text,
                    'max_slots' => $max_slots,
                    'slot_cost' => $slot_cost,
                    'email_text' => $email_text,
                    'page_url' => $page_url,
                )
            );

            if ($inserted) {
                // Get the newly inserted venue_id
                $venue_id = $wpdb->insert_id;

                // Insert open and close hours for each day
                foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day) {
                    $open_hour = intval($_POST[$day . '_open_hour']);
                    $open_minute = intval($_POST[$day . '_open_minute']);
                    $close_hour = intval($_POST[$day . '_close_hour']);
                    $close_minute = intval($_POST[$day . '_close_minute']);

                    // Insert the hours into the database
                    $wpdb->insert(
                        $hours_table,
                        array(
                            'venue_id' => $venue_id,
                            'day_of_week' => ucfirst($day),
                            'open_time' => sprintf('%02d:%02d:00', $open_hour, $open_minute),
                            'close_time' => sprintf('%02d:%02d:00', $close_hour, $close_minute),
                        )
                    );
                }

                echo '<div class="updated"><p>Venue added successfully.</p></div>';
            } else {
                echo '<div class="error"><p>Error adding venue. Please try again.</p></div>';
            }
        } else {
            // Nonce is invalid; handle the error accordingly.
            wp_die('Nonce verification failed.');
        }
    }    

    // Initialize blank values for the form
    $venue = (object) [
        'venue_id' => '',
        'name' => '',
        'capacity' => '',
        'description' => '',
        'location' => '',
        'max_slots' => '100',
        'image_url' => '',
        'page_url' => '',
        'extra_text' => '',
        'slot_cost' => '0.00',
        'email_text' => 'Thank you for your booking. Please consider this as confirmation of your booking unless we get in touch with you further.'
    ];

    // Initialize hours to default values
    $existing_hours = [];
    foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day) {
        $existing_hours[$day] = (object) ['open_time' => '00:00:00', 'close_time' => '00:00:00'];
    }
?>
    <div class="wrap">
        <h1>Add Venue</h1>
        <form method="post">
            <table class="form-table">
                <tr>
                    <th><label for="venue_id">Venue ID</label></th>
                    <td><input type="text" id="venue_id" value="<?php echo esc_attr($venue->venue_id); ?>" disabled /></td>
                </tr>
                <tr>
                    <th><label for="name">Name</label></th>
                    <td><input type="text" id="name" name="name" value="<?php echo esc_attr($venue->name); ?>" required /></td>
                </tr>
                <tr>
                    <th><label for="capacity">Capacity</label></th>
                    <td><input type="number" id="capacity" name="capacity" value="<?php echo esc_attr($venue->capacity); ?>" required /></td>
                </tr>
                <tr>
                    <th><label for="description">Venue Summary</label></th>
                    <td><textarea id="description" name="description" required style="width: 90%;"><?php echo esc_html($venue->description); ?></textarea></td>
                </tr>
                <tr>
                    <th><label for="extra_text">More Display Text</label></th>
                    <td><textarea id="extra_text" name="extra_text" style="width: 90%;"><?php echo esc_html($venue->extra_text); ?></textarea></td>
                </tr>
                <tr>
                    <th><label for="location">Location</label></th>
                    <td><input type="text" id="location" name="location" value="<?php echo esc_attr($venue->location); ?>" /></td>
                </tr>
                <tr>
                    <th><label for="max_slots">Maximum Bookable Slots Per Day</label></th>
                    <td><input type="number" id="max_slots" name="max_slots" value="<?php echo esc_attr($venue->max_slots); ?>" required /> </td>
                </tr>
                <tr>
                    <th><label for="image_url">Image URL</label></th>
                    <td><input type="text" id="image_url" name="image_url" style="width: 90%;" value="<?php echo esc_attr($venue->image_url); ?>" /></td>
                </tr>
                <tr>
                    <th><label for="slot_cost">Cost per slot</label></th>
                    <td>
                        <div class="currency-input">
                            <span class="currency-symbol">$</span>
                            <input type="text" id="slot_cost" name="slot_cost" value="<?php echo esc_attr(number_format((float) $venue->slot_cost, 2, '.', '')); ?>" required />
                        </div>
                    </td>
                </tr>
                <tr>
                    <th><label for="email_text">Email Text</label></th>
                    <td><textarea id="email_text" name="email_text" style="width: 90%;"><?php echo esc_html($venue->email_text); ?></textarea></td>
                </tr>
                <tr>
                    <th><label for="page_url">Page URL</label></th>
                    <td><input type="text" id="page_url" name="page_url" style="width: 90%;" value="<?php echo esc_attr($venue->page_url); ?>" /></td>
                </tr>
            </table>

            <h2>Open Hours</h2>
            <table class="form-table">
                <?php foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day): ?>
                    <tr>
                        <th><label><?php echo ucfirst($day); ?></label></th>
                        <td>
                            <select name="<?php echo $day; ?>_open_hour">
                                <?php for ($hour = 0; $hour < 24; $hour++): ?>
                                    <option value="<?php echo $hour; ?>">
                                        <?php echo str_pad($hour, 2, '0', STR_PAD_LEFT); ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                            :
                            <select name="<?php echo $day; ?>_open_minute">
                                <?php foreach ([0, 15, 30, 45] as $minute): ?>
                                    <option value="<?php echo $minute; ?>">
                                        <?php echo str_pad($minute, 2, '0', STR_PAD_LEFT); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            to
                            <select name="<?php echo $day; ?>_close_hour">
                                <?php for ($hour = 0; $hour < 24; $hour++): ?>
                                    <option value="<?php echo $hour; ?>">
                                        <?php echo str_pad($hour, 2, '0', STR_PAD_LEFT); ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                            :
                            <select name="<?php echo $day; ?>_close_minute">
                                <?php foreach ([0, 15, 30, 45] as $minute): ?>
                                    <option value="<?php echo $minute; ?>">
                                        <?php echo str_pad($minute, 2, '0', STR_PAD_LEFT); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
            <?php wp_nonce_field('add_venue_action', 'venue_nonce'); ?>
            <p class="submit">
                <input type="submit" class="button button-primary" value="Add Venue" />
            </p>
        </form>
    </div>
<?php
}


function leanwi_edit_venue_page() {
    global $wpdb;
    $venue_table = $wpdb->prefix . 'leanwi_booking_venue';
    $hours_table = $wpdb->prefix . 'leanwi_booking_venue_hours';

    // Check if venue_id is set
    if (isset($_GET['venue_id'])) {
        $venue_id = intval($_GET['venue_id']);
        $venue = $wpdb->get_row($wpdb->prepare("SELECT * FROM $venue_table WHERE venue_id = %d", $venue_id));

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Verify nonce before processing the form
            if (isset($_POST['venue_nonce']) && wp_verify_nonce($_POST['venue_nonce'], 'update_venue_action')) {
                // The nonce is valid; proceed with form processing.
                $name = sanitize_text_field($_POST['name']);
                $capacity = isset($_POST['capacity']) ? intval($_POST['capacity']) : 0;
                $description = sanitize_textarea_field($_POST['description']);
                $location = sanitize_text_field($_POST['location']);
                $image_url = esc_url($_POST['image_url']);
                $extra_text = sanitize_text_field($_POST['extra_text']);
                $max_slots = isset($_POST['max_slots']) ? intval($_POST['max_slots']) : 0;
                $slot_cost = isset($_POST['slot_cost']) ? floatval($_POST['slot_cost']) : 0.00;
                $email_text = sanitize_text_field($_POST['email_text']);
                $historic = isset($_POST['historic']) ? 1 : 0; // Set to 1 if checked, otherwise 0
                $page_url = esc_url($_POST['page_url']);

                // Update the venue in the database
                $updated = $wpdb->update(
                    $venue_table,
                    array(
                        'name' => $name,
                        'capacity' => $capacity,
                        'description' => $description,
                        'location' => $location,
                        'image_url' => $image_url,
                        'extra_text' => $extra_text,
                        'max_slots' => $max_slots,
                        'slot_cost' => $slot_cost,
                        'email_text' => $email_text,
                        'historic' => $historic,
                        'page_url' => $page_url,
                    ),
                    array('venue_id' => $venue_id)
                );
                if($updated) {
                    // Update the open and close hours
                    foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day) {
                        $open_hour = intval($_POST[$day . '_open_hour']);
                        $open_minute = intval($_POST[$day . '_open_minute']);
                        $close_hour = intval($_POST[$day . '_close_hour']);
                        $close_minute = intval($_POST[$day . '_close_minute']);

                        // Update the hours in the database
                        $wpdb->update(
                            $hours_table,
                            array(
                                'open_time' => sprintf('%02d:%02d:00', $open_hour, $open_minute),
                                'close_time' => sprintf('%02d:%02d:00', $close_hour, $close_minute),
                            ),
                            array(
                                'venue_id' => $venue_id,
                                'day_of_week' => ucfirst($day)
                            )
                        );
                    }  
                } else {
                    echo '<div class="error"><p>Error updating venue. Please try again.</p></div>';
                }

                echo '<div class="updated"><p>Venue details updated successfully.</p></div>';
                $venue = $wpdb->get_row($wpdb->prepare("SELECT * FROM $venue_table WHERE venue_id = %d", $venue_id)); // Refresh venue details
            
            } else {
                // Nonce is invalid; handle the error accordingly.
                wp_die('Nonce verification failed.');
            }
        }
    }

    // If venue not found
    if (!$venue) {
        echo '<div class="error"><p>Venue not found.</p></div>';
        return;
    }

    // Fetch existing hours for each day
    $existing_hours = [];
    foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day) {
        $hours = $wpdb->get_row($wpdb->prepare("SELECT open_time, close_time FROM $hours_table WHERE venue_id = %d AND day_of_week = %s", $venue_id, ucfirst($day)));
        $existing_hours[$day] = $hours ? $hours : (object) ['open_time' => '00:00:00', 'close_time' => '00:00:00'];
    }
?>
    <div class="wrap">
        <h1>Edit Venue</h1>
        <form method="post">
            <table class="form-table">
                <tr>
                    <th><label for="venue_id">Venue ID</label></th>
                    <td><input type="text" id="venue_id" value="<?php echo esc_attr($venue->venue_id); ?>" disabled /></td>
                </tr>
                <tr>
                    <th><label for="historic">Historic</label></th>
                    <td>
                        <input type="checkbox" id="historic" name="historic" <?php echo ($venue->historic == 1) ? 'checked' : ''; ?>/>
                        <label for="historic">Checked indicates that this venue is not being used any more. It is historic.</label>
                    </td>
                </tr>
                <tr>
                    <th><label for="name">Name</label></th>
                    <td><input type="text" id="name" name="name" value="<?php echo esc_attr($venue->name); ?>" required /></td>
                </tr>
                <tr>
                    <th><label for="capacity">Capacity</label></th>
                    <td><input type="number" id="capacity" name="capacity" value="<?php echo esc_attr($venue->capacity); ?>" required /></td>
                </tr>
                <tr>
                    <th><label for="description">Venue Summary</label></th>
                    <td><textarea id="description" name="description" required style="width: 90%;"><?php echo esc_html((string)$venue->description); ?></textarea></td>
                </tr>
                <tr>
                    <th><label for="extra_text">More Display Text</label></th>
                    <td><textarea id="extra_text" name="extra_text" style="width: 90%;"><?php echo esc_html((string)$venue->extra_text); ?></textarea></td>
                </tr>
                <tr>
                    <th><label for="location">Location</label></th>
                    <td><input type="text" id="location" name="location" value="<?php echo esc_attr((string)$venue->location); ?>" required /></td>
                </tr>
                <tr>
                    <th><label for="max_slots">Maximum Bookable Slots Per Day</label></th>
                    <td><input type="number" id="max_slots" name="max_slots" value="<?php echo esc_attr($venue->max_slots); ?>" required /></td>
                </tr>
                <tr>
                    <th><label for="image_url">Image URL</label></th>
                    <td><input type="text" id="image_url" name="image_url" style="width: 90%;" value="<?php echo esc_attr((string)$venue->image_url); ?>" /></td>
                </tr>
                <tr>
                    <th><label for="slot_cost">Cost per slot</label></th>
                    <td>
                        <div class="currency-input">
                            <span class="currency-symbol">$</span>
                            <input type="text" id="slot_cost" name="slot_cost" value="<?php echo esc_attr(number_format((float) $venue->slot_cost, 2, '.', '')); ?>" required />
                        </div>
                    </td>
                </tr>
                <tr>
                    <th><label for="email_text">Email Text</label></th>
                    <td><textarea id="email_text" name="email_text" style="width: 90%;"><?php echo esc_html((string)$venue->email_text); ?></textarea></td>
                </tr>
                <tr>
                    <th><label for="page_url">Page URL</label></th>
                    <td><input type="text" id="page_url" name="page_url" style="width: 90%;" value="<?php echo esc_attr((string)$venue->page_url); ?>" /></td>
                </tr>
            </table>

            <h2>Open Hours</h2>
            <table class="form-table">
                <?php foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day): ?>
                    <tr>
                        <th><label><?php echo ucfirst($day); ?></label></th>
                        <td>
                            <select name="<?php echo $day; ?>_open_hour">
                                <?php 
                                $open_hour = intval(explode(':', $existing_hours[$day]->open_time)[0]); // Get open hour
                                for ($hour = 0; $hour < 24; $hour++): ?>
                                    <option value="<?php echo $hour; ?>" <?php selected($open_hour, $hour); ?>>
                                        <?php echo str_pad($hour, 2, '0', STR_PAD_LEFT); ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                            :
                            <select name="<?php echo $day; ?>_open_minute">
                                <?php 
                                $open_minute = intval(explode(':', $existing_hours[$day]->open_time)[1]); // Get open minute
                                foreach ([0, 15, 30, 45] as $minute): ?>
                                    <option value="<?php echo $minute; ?>" <?php selected($open_minute, $minute); ?>>
                                        <?php echo str_pad($minute, 2, '0', STR_PAD_LEFT); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            to
                            <select name="<?php echo $day; ?>_close_hour">
                                <?php 
                                $close_hour = intval(explode(':', $existing_hours[$day]->close_time)[0]); // Get close hour
                                for ($hour = 0; $hour < 24; $hour++): ?>
                                    <option value="<?php echo $hour; ?>" <?php selected($close_hour, $hour); ?>>
                                        <?php echo str_pad($hour, 2, '0', STR_PAD_LEFT); ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                            :
                            <select name="<?php echo $day; ?>_close_minute">
                                <?php 
                                $close_minute = intval(explode(':', $existing_hours[$day]->close_time)[1]); // Get close minute
                                foreach ([0, 15, 30, 45] as $minute): ?>
                                    <option value="<?php echo $minute; ?>" <?php selected($close_minute, $minute); ?>>
                                        <?php echo str_pad($minute, 2, '0', STR_PAD_LEFT); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
            <?php wp_nonce_field('update_venue_action', 'venue_nonce'); ?>
            <p class="submit">
                <input type="submit" class="button button-primary" value="Update Venue" />
            </p>
        </form>
    </div>
<?php
}

/**************************************************************************************************
 * Categories
 **************************************************************************************************/

// Function to display the list of categories
function leanwi_categories_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'leanwi_booking_category';

    /****************************************************************************************************
     * Functions are not currently used
    // Handle delete action with nonce verification
    if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['category_id']) && check_admin_referer('delete_category_action')) {
        $category_id = intval($_GET['category_id']);
        $wpdb->delete($table_name, ['category_id' => $category_id]);
        echo '<div class="updated"><p>Category deleted successfully.</p></div>';
    }

    // Handle category update
    if (isset($_POST['update_category']) && check_admin_referer('edit_category_action')) {
        $wpdb->update(
            $table_name,
            ['category_name' => sanitize_text_field($_POST['category_name']), 'historic' => isset($_POST['historic']) ? 1 : 0],
            ['category_id' => intval($_POST['category_id'])],
            ['%s', '%d'],
            ['%d']
        );
        echo '<div class="updated"><p>Category updated successfully.</p></div>';
    }
    ************************************************************************************************************/

    // Display category list and edit form if needed
    if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['category_id'])) {
        $category_id = intval($_GET['category_id']);
        $category = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE category_id = %d", $category_id));

        if ($category) {
            // Display form to edit category
            echo '<div class="wrap">';
            echo '<h1>Edit Category</h1>';
            echo '<form method="POST">';
            wp_nonce_field('edit_category_action');
            echo '<input type="hidden" name="category_id" value="' . esc_attr($category->category_id) . '">';
            echo '<p>Category Name: <input type="text" name="category_name" value="' . esc_attr($category->category_name) . '"></p>';
            echo '<p>Historic: <input type="checkbox" name="historic" ' . checked(1, $category->historic, false) . '></p>';
            echo '<p><input type="submit" name="update_category" value="Save Changes" class="button button-primary"></p>';
            echo '</form>';
            echo '</div>';
        }
    }

    // Display category list
    echo '<div class="wrap">';
    echo '<h1>Categories</h1>';
    echo '<a href="' . esc_url(admin_url('admin.php?page=leanwi-add-category')) . '" class="button button-primary">Add Category</a>';
    echo '<p> </p>'; // Space below the button before the category table

    echo '<table class="wp-list-table widefat striped">';
    echo '<thead>';
    echo '<tr>';
    echo '<th scope="col">Category ID</th>';
    echo '<th scope="col">Category Name</th>';
    echo '<th scope="col">Historic</th>';
    echo '<th scope="col">Actions</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';

    // Fetch categories
    $categories = fetch_categories();
    if (isset($categories['error'])) {
        echo '<tr><td colspan="4">' . esc_html($categories['error']) . '</td></tr>';
    } else {
        // Display each category in a row
        foreach ($categories['categories'] as $category) {
            echo '<tr>';
            echo '<td>' . esc_html($category['category_id']) . '</td>';
            echo '<td>' . esc_html($category['category_name']) . '</td>';
            echo '<td>' . ($category['historic'] ? 'Yes' : 'No') . '</td>';
            echo '<td>';
            echo '<a href="' . esc_url(admin_url('admin.php?page=leanwi-edit-category&category_id=' . $category['category_id'])) . '" class="button">Edit</a> ';
            // Uncomment to add delete functionality
            //echo '<a href="' . esc_url(wp_nonce_url(admin_url('admin.php?page=leanwi-book-a-room-categories&action=delete&category_id=' . $category['category_id']), 'delete_category_action')) . '" class="button button-danger" onclick="return confirm(\'Are you sure you want to delete this category?\');">Delete</a>';
            echo '</td>';
            echo '</tr>';
        }
    }

    echo '</tbody>';
    echo '</table>';
    echo '</div>';
}

// Function to get categories
function fetch_categories() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'leanwi_booking_category';

    // Fetch categories
    $categories = $wpdb->get_results("SELECT category_id, category_name, historic FROM $table_name", ARRAY_A);

    if (empty($categories)) {
        return ['error' => 'No categories found.'];
    } else {
        return ['categories' => $categories];
    }
}

function leanwi_add_category_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'leanwi_booking_category';

    // Handle form submission
    if (isset($_POST['add_category'])) {
        $wpdb->insert(
            $table_name,
            ['category_name' => sanitize_text_field($_POST['category_name']), 'historic' => isset($_POST['historic']) ? 1 : 0],
            ['%s', '%d']
        );
        echo '<div class="updated"><p>Category added successfully.</p></div>';
    }

    // Display the add category form
    echo '<div class="wrap">';
    echo '<h1>Add Category</h1>';
    echo '<form method="POST">';
    echo '<p>Category Name: <input type="text" name="category_name" required></p>';
    echo '<p>Historic: <input type="checkbox" name="historic"></p>';
    echo '<p><input type="submit" name="add_category" value="Add Category" class="button button-primary"></p>';
    echo '</form>';
    echo '</div>';
}

// Function to handle editing of a category
function leanwi_edit_category_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'leanwi_booking_category';

    // Handle the form submission to update the category
    if (isset($_POST['update_category'])) {
        $category_id = intval($_POST['category_id']);
        $category_name = sanitize_text_field($_POST['category_name']);
        $historic = isset($_POST['historic']) ? 1 : 0; // Check if the "Historic" checkbox is checked

        // Update the category in the database
        $wpdb->update(
            $table_name,
            [
                'category_name' => $category_name,
                'historic' => $historic,
            ],
            ['category_id' => $category_id],
            ['%s', '%d'],
            ['%d']
        );

        echo '<div class="updated"><p>Category updated successfully.</p></div>';
    }

    // Check if a category ID is provided for editing
    if (isset($_GET['category_id'])) {
        $category_id = intval($_GET['category_id']);
        $category = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE category_id = %d", $category_id));

        if ($category) {
            // Display form to edit the category
            echo '<div class="wrap">';
            echo '<h1>Edit Category</h1>';
            echo '<form method="POST">';
            echo '<input type="hidden" name="category_id" value="' . esc_attr($category->category_id) . '">';

            // Display the category name input
            echo '<p>Category Name: <input type="text" name="category_name" value="' . esc_attr($category->category_name) . '" class="regular-text"></p>';

            // Display the checkbox for marking a category as historic
            echo '<p>';
            echo '<label><input type="checkbox" name="historic" ' . checked($category->historic, 1, false) . '> Historic</label>';
            echo '</p>';

            // Submit button to update the category
            echo '<p><input type="submit" name="update_category" value="Save Changes" class="button button-primary"></p>';
            echo '</form>';
            echo '</div>';
        } else {
            // Display a message if the category is not found
            echo '<div class="error"><p>Category not found.</p></div>';
        }
    } else {
        // Redirect back if no category ID is provided
        echo '<div class="error"><p>No category ID provided.</p></div>';
    }
}

/**************************************************************************************************
 * Audiences
 **************************************************************************************************/

// Function to display the list of audiences
function leanwi_audiences_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'leanwi_booking_audience';

    /**********************************************************************************************
     *  These functions re nocrrently being used
     **********************************************************************************************
    // Handle delete action
    if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['audience_id'])) {
        $audience_id = intval($_GET['audience_id']);
        $wpdb->delete($table_name, ['audience_id' => $audience_id]);
        echo '<div class="updated"><p>Audience deleted successfully.</p></div>';
    }

    // Handle audience update
    if (isset($_POST['update_audience'])) {
        $wpdb->update(
            $table_name,
            ['audience_name' => sanitize_text_field($_POST['audience_name']), 'historic' => isset($_POST['historic']) ? 1 : 0],
            ['audience_id' => intval($_POST['audience_id'])],
            ['%s', '%d'],
            ['%d']
        );
        echo '<div class="updated"><p>Audience updated successfully.</p></div>';
    }
    ************************************************************************************************/

    // Display audience list and edit form if needed
    if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['audience_id'])) {
        $audience_id = intval($_GET['audience_id']);
        $audience = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE audience_id = %d", $audience_id));

        if ($audience) {
            // Display form to edit audience
            echo '<div class="wrap">';
            echo '<h1>Edit Audience</h1>';
            echo '<form method="POST">';
            echo '<input type="hidden" name="audience_id" value="' . esc_attr($audience->audience_id) . '">';
            echo '<p>Audience Name: <input type="text" name="audience_name" value="' . esc_attr($audience->audience_name) . '"></p>';
            echo '<p>Historic: <input type="checkbox" name="historic" ' . checked(1, $audience->historic, false) . '></p>';
            echo '<p><input type="submit" name="update_audience" value="Save Changes" class="button button-primary"></p>';
            echo '</form>';
            echo '</div>';
        }
    }

    // Display audience list
    echo '<div class="wrap">';
    echo '<h1>Audiences</h1>';

    echo '<a href="' . admin_url('admin.php?page=leanwi-add-audience') . '" class="button button-primary">Add Audience</a>';
    echo '<p> </p>'; // Space below the button before the audience table

    echo '<table class="wp-list-table widefat striped">';
    echo '<thead>';
    echo '<tr>';
    echo '<th scope="col">Audience ID</th>';
    echo '<th scope="col">Audience Name</th>';
    echo '<th scope="col">Historic</th>';
    echo '<th scope="col">Actions</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';

    // Fetch audiences
    $audiences = fetch_audiences();
    if (isset($audiences['error'])) {
        echo '<tr><td colspan="4">' . esc_html($audiences['error']) . '</td></tr>';
    } else {
        // Display each audience in a row
        foreach ($audiences['audiences'] as $audience) {
            echo '<tr>';
            echo '<td>' . esc_html($audience['audience_id']) . '</td>';
            echo '<td>' . esc_html($audience['audience_name']) . '</td>';
            echo '<td>' . ($audience['historic'] ? 'Yes' : 'No') . '</td>';
            echo '<td>';
            echo '<a href="' . esc_url(admin_url('admin.php?page=leanwi-edit-audience&audience_id=' . esc_attr($audience['audience_id']))) . '" class="button">Edit</a> ';
            // Uncomment if you want delete functionality
            // echo '<a href="?page=leanwi-book-a-room-audiences&action=delete&audience_id=' . esc_attr($audience['audience_id']) . '" class="button button-danger" onclick="return confirm(\'Are you sure you want to delete this audience?\');">Delete</a>';
            echo '</td>';
            echo '</tr>';
        }
    }

    echo '</tbody>';
    echo '</table>';
    echo '</div>';
}

// Function to get audiences
function fetch_audiences() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'leanwi_booking_audience';

    // Fetch audiences
    $audiences = $wpdb->get_results("SELECT audience_id, audience_name, historic FROM $table_name", ARRAY_A);

    if (empty($audiences)) {
        return ['error' => 'No audiences found.'];
    } else {
        return ['audiences' => $audiences];
    }
}

function leanwi_add_audience_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'leanwi_booking_audience';

    // Handle form submission
    if (isset($_POST['add_audience'])) {
        $wpdb->insert(
            $table_name,
            ['audience_name' => sanitize_text_field($_POST['audience_name']), 'historic' => isset($_POST['historic']) ? 1 : 0],
            ['%s', '%d']
        );
        echo '<div class="updated"><p>Audience added successfully.</p></div>';
    }

    // Display the add audience form
    echo '<div class="wrap">';
    echo '<h1>Add Audience</h1>';
    echo '<form method="POST">';
    echo '<p>Audience Name: <input type="text" name="audience_name" required></p>';
    echo '<p>Historic: <input type="checkbox" name="historic"></p>';
    echo '<p><input type="submit" name="add_audience" value="Add Audience" class="button button-primary"></p>';
    echo '</form>';
    echo '</div>';
}

// Function to handle editing of an audience
function leanwi_edit_audience_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'leanwi_booking_audience';

    // Handle the form submission to update the audience
    if (isset($_POST['update_audience'])) {
        $audience_id = intval($_POST['audience_id']);
        $audience_name = sanitize_text_field($_POST['audience_name']);
        $historic = isset($_POST['historic']) ? 1 : 0; // Check if the "Historic" checkbox is checked

        // Update the audience in the database
        $wpdb->update(
            $table_name,
            [
                'audience_name' => $audience_name,
                'historic' => $historic,
            ],
            ['audience_id' => $audience_id],
            ['%s', '%d'],
            ['%d']
        );

        echo '<div class="updated"><p>Audience updated successfully.</p></div>';
    }

    // Check if an audience ID is provided for editing
    if (isset($_GET['audience_id'])) {
        $audience_id = intval($_GET['audience_id']);
        $audience = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE audience_id = %d", $audience_id));

        if ($audience) {
            // Display form to edit the audience
            echo '<div class="wrap">';
            echo '<h1>Edit Audience</h1>';
            echo '<form method="POST">';
            echo '<input type="hidden" name="audience_id" value="' . esc_attr($audience->audience_id) . '">';

            // Display the audience name input
            echo '<p>Audience Name: <input type="text" name="audience_name" value="' . esc_attr($audience->audience_name) . '" class="regular-text"></p>';

            // Display the checkbox for marking an audience as historic
            echo '<p>';
            echo '<label><input type="checkbox" name="historic" ' . checked($audience->historic, 1, false) . '> Historic</label>';
            echo '</p>';

            // Submit button to update the audience
            echo '<p><input type="submit" name="update_audience" value="Save Changes" class="button button-primary"></p>';
            echo '</form>';
            echo '</div>';
        } else {
            // Display a message if the audience is not found
            echo '<div class="error"><p>Audience not found.</p></div>';
        }
    } else {
        // Redirect back if no audience ID is provided
        echo '<div class="error"><p>No audience ID provided.</p></div>';
    }
}

/**************************************************************************************************
 * Reporting
 **************************************************************************************************/

// Function to display the reporting functionality
function leanwi_reports_page() {
    // Fetch venue data from the database
    // Fetch venues
    $venues_response = fetch_venues();
    if (isset($venues_response['error'])) {
        echo '<tr><td colspan="6">' . esc_html($venues_response['error']) . '</td></tr>';
        return; // Exit early if there's an error
    }

    // Ensure venues is set and is an array
    $venues = isset($venues_response['venues']) ? $venues_response['venues'] : [];

    ?>
    <div class="wrap">
        <h1>Reports</h1>
        <form id="leanwi-report-form" method="post" action="<?php echo plugins_url('LEANWI-Book-A-Room/php/plugin/generate-report.php'); ?>">
            <div class="form-row">
                <div class="form-group">
                    <label for="start_date">Start Date:</label>
                    <input type="date" id="start_date" name="start_date" required>
                </div>
                <div class="form-group">
                    <label for="end_date">End Date:</label>
                    <input type="date" id="end_date" name="end_date" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                <label for="venue_info">Select Venue:</label>
                    <select id="venue_info" name="venue_info">
                        <option value="">-- All Venues --</option>
                        <?php foreach ($venues as $venue): ?>
                            <option value="<?php echo esc_attr($venue['venue_id']) . '|' . esc_attr($venue['name']); ?>">
                                <?php echo esc_html($venue['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="include_category">Include Category:</label>
                    <input type="checkbox" id="include_category" name="include_category" value="yes">
                </div>
                <div class="form-group">
                    <label for="include_audience">Include Audience:</label>
                    <input type="checkbox" id="include_audience" name="include_audience" value="yes">
                </div>
            </div>
            <div class="form-row">
                <input type="submit" value="Generate Report" class="button button-primary">
            </div>
        </form>
    </div>

    <style>
        .form-row {
            display: flex;
            align-items: center; /* Center items vertically */
            margin-bottom: 15px; /* Space between rows */
        }
        .form-group {
            margin-right: 20px; /* Space between form elements */
        }
        .form-group label {
            display: block; /* Make label take full width */
            margin-bottom: 5px; /* Space between label and input */
        }
        #leanwi-report-form input[type="date"] {
            padding: 5px; /* Padding inside the date input */
            width: 150px; /* Set a fixed width for the date inputs */
        }
        /* Adjust the checkbox label alignment */
        .form-group input[type="checkbox"] {
            margin-left: 5px; /* Space between checkbox and label */
        }
        /* Style the dropdown */
        #venue_id {
            padding: 5px; /* Padding inside the dropdown */
            width: 150px; /* Set a fixed width for the dropdown */
        }
    </style>
    <?php
}

/**************************************************************************************************
 * Settings
 **************************************************************************************************/

// Function to display settings page
function leanwi_settings_page() {
    ?>
    <div class="wrap">
        <h1>Book-A-Room Settings</h1>
        <form method="post" action="options.php">
            <?php
                // Output security fields for the registered setting
                settings_fields('leanwi_plugin_settings_group');
                
                // Output setting sections and their fields
                do_settings_sections('leanwi-book-a-room-settings');
                
                // Submit button
                submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Function to register settings
function leanwi_register_settings() {
    // Register a setting for "minutes_interval" and "maximum_booking_slots"
    register_setting('leanwi_plugin_settings_group', 'leanwi_minutes_interval');
    register_setting('leanwi_plugin_settings_group', 'leanwi_booking_months');
    register_setting('leanwi_plugin_settings_group', 'leanwi_show_categories');
    register_setting('leanwi_plugin_settings_group', 'leanwi_show_audiences');
    register_setting('leanwi_plugin_settings_group', 'leanwi_send_admin_booking_email');
    register_setting('leanwi_plugin_settings_group', 'leanwi_admin_email_address');
    register_setting('leanwi_plugin_settings_group', 'leanwi_highlighted_button_border_color');
    register_setting('leanwi_plugin_settings_group', 'leanwi_highlighted_button_bg_color');
    register_setting('leanwi_plugin_settings_group', 'leanwi_highlighted_button_text_color');

    // Add a section to the settings page
    add_settings_section(
        'leanwi_main_section',          // Section ID
        'Book-A-Room Settings',         // Section title
        null,                           // Callback function (optional)
        'leanwi-book-a-room-settings'   // Page slug where the section will be displayed
    );
    
    // Add Minutes Interval dropdown
    add_settings_field(
        'leanwi_minutes_interval',      // Field ID
        'Minutes Interval',             // Label for the field
        'leanwi_minutes_interval_field',// Function to display the dropdown
        'leanwi-book-a-room-settings',  // Page slug
        'leanwi_main_section'           // Section ID
    );

    // Add Booking Months in advance input
    add_settings_field(
        'leanwi_booking_months',  // Field ID
        'Booking Months in Advance',         // Label for the field
        'leanwi_booking_months_field', // Function to display the input
        'leanwi-book-a-room-settings',  // Page slug
        'leanwi_main_section'           // Section ID
    );

    // Add 'Show Categories to Users' setting field
    add_settings_field(
        'leanwi_show_categories',       // Field ID
        'Show Categories to Users?',     // Label for the field
        'leanwi_show_categories_field', // Function to display the dropdown
        'leanwi-book-a-room-settings',  // Page slug
        'leanwi_main_section'           // Section ID
    );

    // Add 'Show Audiences to Users' setting field
    add_settings_field(
        'leanwi_show_audiences',        // Field ID
        'Show Audiences to Users?',      // Label for the field
        'leanwi_show_audiences_field',  // Function to display the dropdown
        'leanwi-book-a-room-settings',  // Page slug
        'leanwi_main_section'           // Section ID
    );

    // Add wheter admin should be sent a copy of the booking email field
    add_settings_field(
        'leanwi_send_admin_booking_email',       // Field ID
        'Send Admin a Copy of the Booking Email?',     // Label for the field
        'leanwi_send_admin_booking_email_field', // Function to display the dropdown
        'leanwi-book-a-room-settings',  // Page slug
        'leanwi_main_section'           // Section ID
    );

    // Add Admin email address field
    add_settings_field(
        'leanwi_admin_email_address',  // Field ID
        'Email Address for Booking Emails',         // Label for the field
        'leanwi_admin_email_address_field', // Function to display the input
        'leanwi-book-a-room-settings',  // Page slug
        'leanwi_main_section'           // Section ID
    );

    // Add border color for highlighted buttons field
    add_settings_field(
        'leanwi_highlighted_button_border_color',  // Field ID
        'Border color for highlighted buttons',         // Label for the field
        'leanwi_highlighted_button_border_color_field', // Function to display the input
        'leanwi-book-a-room-settings',  // Page slug
        'leanwi_main_section'           // Section ID
    );

    // Add bg color for highlighted buttons field
    add_settings_field(
        'leanwi_highlighted_button_bg_color',  // Field ID
        'Background color for highlighted buttons',         // Label for the field
        'leanwi_highlighted_button_bg_color_field', // Function to display the input
        'leanwi-book-a-room-settings',  // Page slug
        'leanwi_main_section'           // Section ID
    );

    // Add text color for highlighted buttons field
    add_settings_field(
        'leanwi_highlighted_button_text_color',  // Field ID
        'Text color for highlighted buttons',         // Label for the field
        'leanwi_highlighted_button_text_color_field', // Function to display the input
        'leanwi-book-a-room-settings',  // Page slug
        'leanwi_main_section'           // Section ID
    );
}

// Function to display the Minutes Interval dropdown
function leanwi_minutes_interval_field() {
    $value = get_option('leanwi_minutes_interval', ''); // Get saved value or default to an empty string
    ?>
    <select id="leanwi_minutes_interval" name="leanwi_minutes_interval">
        <option value="15" <?php selected($value, '15'); ?>>15</option>
        <option value="30" <?php selected($value, '30'); ?>>30</option>
        <option value="60" <?php selected($value, '60'); ?>>60</option>
    </select>
    <?php
}

// Function to display the Booking Months in advance input
function leanwi_booking_months_field() {
    $value = get_option('leanwi_booking_months', ''); // Get saved value or default to an empty string
    echo '<input type="number" id="leanwi_booking_months" name="leanwi_booking_months" value="' . esc_attr($value) . '" />';
}

// Function to display 'Show Categories to Users' dropdown
function leanwi_show_categories_field() {
    $value = get_option('leanwi_show_categories', 'no'); // Default to 'no' if no value is set
    ?>
    <select id="leanwi_show_categories" name="leanwi_show_categories">
        <option value="yes" <?php selected($value, 'yes'); ?>>Yes</option>
        <option value="no" <?php selected($value, 'no'); ?>>No</option>
    </select>
    <?php
}

// Function to display 'Show Audiences to Users' dropdown
function leanwi_show_audiences_field() {
    $value = get_option('leanwi_show_audiences', 'no'); // Default to 'no' if no value is set
    ?>
    <select id="leanwi_show_audiences" name="leanwi_show_audiences">
        <option value="yes" <?php selected($value, 'yes'); ?>>Yes</option>
        <option value="no" <?php selected($value, 'no'); ?>>No</option>
    </select>
    <?php
}

// Function to display 'Send admin a booking email' dropdown
function leanwi_send_admin_booking_email_field() {
    $value = get_option('leanwi_send_admin_booking_email', 'no'); // Default to 'no' if no value is set
    ?>
    <select id="leanwi_send_admin_booking_email" name="leanwi_send_admin_booking_email">
        <option value="yes" <?php selected($value, 'yes'); ?>>Yes</option>
        <option value="no" <?php selected($value, 'no'); ?>>No</option>
    </select>
    <?php
}

// Function to display the admin email address input
function leanwi_admin_email_address_field() {
    $value = get_option('leanwi_admin_email_address', ''); // Get saved value or default to an empty string
    echo '<input type="email" id="leanwi_admin_email_address" name="leanwi_admin_email_address" value="' . esc_attr($value) . '" />';
}

// Function to display the highlighted border color input
function leanwi_highlighted_button_border_color_field() {
    $value = get_option('leanwi_highlighted_button_border_color', '#ff9800'); // Get saved value or default to this hex vaue
    echo '<input type="color" id="leanwi_highlighted_button_border_color" name="leanwi_highlighted_button_border_color" value="' . esc_attr($value) . '" />';
}

// Function to display the highlighted bg color input
function leanwi_highlighted_button_bg_color_field() {
    $value = get_option('leanwi_highlighted_button_bg_color', '#ffe0b3'); // Get saved value or default to this hex vaue
    echo '<input type="color" id="leanwi_highlighted_button_bg_color" name="leanwi_highlighted_button_bg_color" value="' . esc_attr($value) . '" />';
}

// Function to display the highlighted bg color input
function leanwi_highlighted_button_text_color_field() {
    $value = get_option('leanwi_highlighted_button_text_color', '#000000'); // Get saved value or default to this hex vaue
    echo '<input type="color" id="leanwi_highlighted_button_text_color" name="leanwi_highlighted_button_text_color" value="' . esc_attr($value) . '" />';
}

// Hook the settings registration function
add_action('admin_init', 'leanwi_register_settings');