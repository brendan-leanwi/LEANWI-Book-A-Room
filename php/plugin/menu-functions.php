<?php
namespace LEANWI_Book_A_Room;
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
        __NAMESPACE__ . '\\leanwi_main_page',       // Callback function
        'dashicons-calendar',     // Menu icon (optional)
        6                         // Position
    );

    add_submenu_page(
        'leanwi-book-a-room-main',    // Parent slug
        'Documentation and Support',  // Page title (for the actual documentation page)
        'Documentation',              // Menu title (this will be the first submenu item)
        'manage_options',             // Capability
        'leanwi-book-a-room-main',    // Menu slug (reuse 'leanwi-book-a-room-main' to link it to the parent page)
        __NAMESPACE__ . '\\leanwi_main_page'            // Callback function (this will now display the Documentation page)
    );

    // Sub-menu: "Venues"
    add_submenu_page(
        'leanwi-book-a-room-main',    // Parent slug
        'Venues',                     // Page title
        'Venues',                     // Menu title
        'manage_options',             // Capability
        'leanwi-book-a-room-venues',  // Menu slug
        __NAMESPACE__ . '\\leanwi_venues_page'          // Callback function to display venues
    );

    // Sub-menu: "Add Venue"
    add_submenu_page(
        'leanwi-book-a-room-main',
        'Add Venue',
        'Add Venue',
        'manage_options',
        'leanwi-add-venue',
        __NAMESPACE__ . '\\leanwi_add_venue_page'
    );

    // Sub-menu: "Delete Venue"
    add_submenu_page(
        'leanwi-book-a-room-main',
        'Delete Venue',
        'Delete Venue',
        'manage_options',
        'leanwi-delete-venue',
        __NAMESPACE__ . '\\leanwi_delete_venue_page'
    );

    // Sub-menu: "Edit Venue"
    add_submenu_page(
        'leanwi-book-a-room-main', // Parent slug (linked to Venues submenu)
        'Edit Venue',                 // Page title
        'Edit Venue',                 // Menu title
        'manage_options',             // Capability
        'leanwi-edit-venue',          // Menu slug
        __NAMESPACE__ . '\\leanwi_edit_venue_page'      // Callback function to display the edit venue form
    );

    // Sub-menu: "Categories"
    add_submenu_page(
        'leanwi-book-a-room-main',    // Parent slug
        'Categories',                   // Page title
        'Categories',                   // Menu title
        'manage_options',             // Capability
        'leanwi-book-a-room-categories',// Menu slug
        __NAMESPACE__ . '\\leanwi_categories_page'        // Callback function to display CATEGORIES
    );

    // Sub-menu: "Add Category"
    add_submenu_page(
        'leanwi-book-a-room-main',
        'Add Category',
        'Add Category',
        'manage_options',
        'leanwi-add-category',
        __NAMESPACE__ . '\\leanwi_add_category_page'
    );

    // Sub-menu: "Edit Category"
    add_submenu_page(
        'leanwi-book-a-room-main', // Parent slug (linked to Categories submenu)
        'Edit Category',                 // Page title
        'Edit Category',                 // Menu title
        'manage_options',             // Capability
        'leanwi-edit-category',          // Menu slug
        __NAMESPACE__ . '\\leanwi_edit_category_page'      // Callback function to display the edit venue form
    );

    // Sub-menu: "Audiences"
    add_submenu_page(
        'leanwi-book-a-room-main',    // Parent slug
        'Audiences',                   // Page title
        'Audiences',                   // Menu title
        'manage_options',             // Capability
        'leanwi-book-a-room-audiences',// Menu slug
        __NAMESPACE__ . '\\leanwi_audiences_page'        // Callback function to display audiences
    );

    // Sub-menu: "Add Audience"
    add_submenu_page(
        'leanwi-book-a-room-main',
        'Add Audience',
        'Add Audience',
        'manage_options',
        'leanwi-add-audience',
        __NAMESPACE__ . '\\leanwi_add_audience_page'
    );

    // Sub-menu: "Edit Audience"
    add_submenu_page(
        'leanwi-book-a-room-main', // Parent slug (linked to Audiences submenu)
        'Edit Audience',                 // Page title
        'Edit Audience',                 // Menu title
        'manage_options',             // Capability
        'leanwi-edit-audience',          // Menu slug
        __NAMESPACE__ . '\\leanwi_edit_audience_page'      // Callback function to display the edit venue form
    );

    // Sub-menu: "Affirmations"
    add_submenu_page(
        'leanwi-book-a-room-main',    // Parent slug
        'Affirmations',                   // Page title
        'Affirmations',                   // Menu title
        'manage_options',             // Capability
        'leanwi-book-a-room-affirmations',// Menu slug
        __NAMESPACE__ . '\\leanwi_affirmations_page'        // Callback function to display affirmations
    );

    // Sub-menu: "Add Affirmation"
    add_submenu_page(
        'leanwi-book-a-room-main',
        'Add Affirmation',
        'Add Affirmation',
        'manage_options',
        'leanwi-add-affirmation',
        __NAMESPACE__ . '\\leanwi_add_affirmation_page'
    );

    // Sub-menu: "Delete Affirmation"
    add_submenu_page(
        'leanwi-book-a-room-main',
        'Delete Affirmation',
        'Delete Affirmation',
        'manage_options',
        'leanwi-delete-affirmation',
        __NAMESPACE__ . '\\leanwi_delete_affirmation_page'
    );

    // Sub-menu: "Edit Affirmation"
    add_submenu_page(
        'leanwi-book-a-room-main', // Parent slug (linked to Audiences submenu)
        'Edit Affirmation',                 // Page title
        'Edit Affirmation',                 // Menu title
        'manage_options',             // Capability
        'leanwi-edit-affirmation',          // Menu slug
        __NAMESPACE__ . '\\leanwi_edit_affirmation_page'      // Callback function to display the edit venue form
    );

    // Sub-menu: "Reports"
    add_submenu_page(
        'leanwi-book-a-room-main',    // Parent slug
        'Reports',                   // Page title
        'Reporting',                   // Menu title
        'manage_options',             // Capability
        'leanwi-book-a-room-reports',// Menu slug
        __NAMESPACE__ . '\\leanwi_reports_page'        // Callback function to display the reports page
    );

    // Sub-menu: "Settings"
    add_submenu_page(
        'leanwi-book-a-room-main',    // Parent slug
        'Settings',                   // Page title
        'Settings',                   // Menu title
        'manage_options',             // Capability
        'leanwi-book-a-room-settings',// Menu slug
        __NAMESPACE__ . '\\leanwi_settings_page'        // Callback function to display settings
    );

    // Sub-menu: "Staff"
    add_submenu_page(
        'leanwi-book-a-room-main',    // Parent slug
        'Booking Staff',                   // Page title
        'Staff',                   // Menu title
        'manage_options',             // Capability
        'leanwi-booking-staff',// Menu slug
        __NAMESPACE__ . '\\leanwi_booking_staff_page'        // Callback function to display settings
    );
}


// Hook to create the admin menu
add_action('admin_menu', __NAMESPACE__ . '\\leanwi_add_admin_menu');

// Hide the Add and Edit pages submenus from the left-hand navigation menu using CSS
function leanwi_hide_add_edit_submenus_css() {
    echo '<style>
        #toplevel_page_leanwi-book-a-room-main .wp-submenu a[href="admin.php?page=leanwi-add-venue"] {
            display: none !important;
        }
        #toplevel_page_leanwi-book-a-room-main .wp-submenu a[href="admin.php?page=leanwi-delete-venue"] {
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
        #toplevel_page_leanwi-book-a-room-main .wp-submenu a[href="admin.php?page=leanwi-add-affirmation"] {
            display: none !important;
        }
        #toplevel_page_leanwi-book-a-room-main .wp-submenu a[href="admin.php?page=leanwi-delete-affirmation"] {
            display: none !important;
        }
        #toplevel_page_leanwi-book-a-room-main .wp-submenu a[href="admin.php?page=leanwi-edit-affirmation"] {
            display: none !important;
        }
    </style>';
}
add_action('admin_head', __NAMESPACE__ . '\\leanwi_hide_add_edit_submenus_css');


// Function to display the main page which is our documentation page
function leanwi_main_page() {
    $doc_file = plugin_dir_path(dirname(dirname(__FILE__))) . 'docs/documentation.html';

    if (!file_exists($doc_file)) {
        $content = "<h2>Documentation Not Found</h2><p>Please ensure `documentation.html` exists in the `docs/` directory.</p>";
    } else {
        $content = file_get_contents($doc_file);
    }
    ?>

    <div class="wrap">
        <h1>LEANWI Book-A-Room Documentation</h1>
        <div id="documentation-content" style="border: 1px solid #ddd; padding: 15px; background: #fff;"></div>
    </div>

    <script>
        // Function to load the HTML file dynamically
        function loadHtmlPage(page) {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', "<?php echo plugin_dir_url(dirname(dirname(__FILE__))); ?>" + "docs/" + page, true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    var content = xhr.responseText;
                    document.getElementById("documentation-content").innerHTML = content;
                    //attachLinkEvents();  // Reattach link events after new content is loaded
                }
            };
            xhr.send();
        }

        // Load the default content (documentation.html)
        var content = <?php echo json_encode($content); ?>;
        document.getElementById("documentation-content").innerHTML = content;

        // Attach event listeners to the dynamically rendered links
        /*
        function attachLinkEvents() {
            document.getElementById('documentation-content').addEventListener('click', function (e) {
                let target = e.target.closest('a'); 
                
                let pageMap = {
                  //  'Back to Main Documentation Page': 'documentation.html',
                  //  'Initial setup (Settings)': 'initial-setup-settings.html',
                  //  'Initial setup (Staff)': 'initial-setup-staff.html',
                  //  'Initial setup (Affirmations)': 'initial-setup-affirmations.html',
                  //  'Initial setup (Categories and Audiences)': 'initial-setup-categories-audiences.html',
                  //  'Setting up your first Venue': 'first-venue-setup.html',
                  //  'Setting up Pages Using Shortcodes': 'shortcodes-use.html',
                  //  'How to use the Recurring Bookings page': 'recurring-bookings-use.html',
                  //  'Adding a mail client': 'mail-client-setup.html',
                   // '(Rooms Example Page)': 'example_pages/rooms-landing-page-example.html',
                   // '(Venue Example Page)': 'example_pages/venue-page-example.html',
                   // '(Recurring Bookings Example Page)': 'example_pages/recurring-bookings-page-example.html',
                   // '(Payments and Feedback Example Page)': 'example_pages/payments-feedback-page-example.html',
                   // '(Room Availability Example Page)': 'example_pages/check-availability-page-example.html',
                   // 'venue': 'first-venue-setup.html',
                   // 'Recurring Bookings page': 'shortcodes-use.html'
                };

                let page = pageMap[target.innerText.trim()]; // Use innerText and trim for better matching
                if (page) {
                    loadHtmlPage(page); // Load new content
                    setTimeout(() => window.scrollTo(0, 0), 50); // Scroll to top AFTER content loads
                }
            });
        }

        // Attach events after the initial content is loaded
        attachLinkEvents();
        */
    </script>

    <?php
}


/**************************************************************************************************
 * Venues
 **************************************************************************************************/

// Function to display the list of venues
function leanwi_venues_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'leanwi_booking_venue';

    // Process display order update if the form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_display_order'])) {
        if (isset($_POST['display_order']) && is_array($_POST['display_order'])) {
            foreach ($_POST['display_order'] as $venue_id => $display_order) {
                $venue_id = intval($venue_id);
                $display_order = intval($display_order);

                $wpdb->update(
                    $table_name,
                    ['display_order' => $display_order],
                    ['venue_id' => $venue_id],
                    ['%d'],
                    ['%d']
                );
            }
            echo '<div class="updated notice"><p>Display order updated successfully.</p></div>';
        }
    }

    // Display venue list
    echo '<div class="wrap">';
    echo '<h1>Venues</h1>';

    echo '<a href="' . admin_url('admin.php?page=leanwi-add-venue') . '" class="button button-primary">Add Venue</a>'; // Add this button
    echo '<p> </p>'; //Space below the button before the venue table

    echo '<form method="POST">';
    echo '<table class="wp-list-table widefat striped">';
    echo '<thead>';
    echo '<tr>';
    echo '<th scope="col">Venue ID</th>';
    echo '<th scope="col">Name</th>';
    echo '<th scope="col">Display Order</th>';
    echo '<th scope="col">Capacity</th>';
    echo '<th scope="col">Location</th>';
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
        foreach ($venues as $venue) {
            echo '<tr>';
            echo '<td>' . esc_html($venue['venue_id']) . '</td>';
            echo '<td>' . esc_html($venue['name']) . '</td>';
            echo '<td><input type="number" name="display_order[' . esc_attr($venue['venue_id']) . ']" value="' . esc_attr($venue['display_order']) . '" style="width: 60px;"></td>';
            echo '<td>' . esc_html($venue['capacity']) . '</td>';
            echo '<td>' . esc_html($venue['location']) . '</td>';
            echo '<td>' . ($venue['historic'] == 0 ? 'False' : 'True') . '</td>';
            echo '<td>';
            echo '<a href="' . esc_url(admin_url('admin.php?page=leanwi-edit-venue&venue_id=' . esc_attr($venue['venue_id']))) . '" class="button">Edit</a> ';
            echo '<a href="' . esc_url(admin_url('admin.php?page=leanwi-delete-venue&venue_id=' . esc_attr($venue['venue_id']))) . '" class="button" onclick="return confirm(\'Are you sure you want to delete this venue?\');">Delete</a>';
            echo '</td>';
            echo '</tr>';
        }
    }

    echo '</tbody>';
    echo '</table>';
    echo '<p><input type="submit" name="save_display_order" value="Save Display Order" class="button button-primary"></p>';
    echo '</form>';
    echo '<p> </p>'; //Space below the venue table
    echo 'Please add the following shortcode to your page - [venue_details venue_id="1"]. Where 1 is the Venue ID from the above table';
    echo '</div>';
}

// Function to get venues
function fetch_venues() {
    global $wpdb;

    $venue_table = $wpdb->prefix . 'leanwi_booking_venue';

    // Make sure table exists
    if ($wpdb->get_var("SHOW TABLES LIKE '$venue_table'") !== $venue_table) {
        return ['error' => "Table $venue_table does not exist"];
    }

    $venues = $wpdb->get_results("SELECT venue_id, name, display_order, capacity, location, historic FROM $venue_table ORDER BY display_order", ARRAY_A);

    if ($venues === null) {
        return ['error' => 'Query failed', 'last_error' => $wpdb->last_error];
    }

    return array_map(function ($venue) {
        return [
            'venue_id' => intval($venue['venue_id']),
            'name' => esc_html($venue['name']),
            'display_order' => intval($venue['display_order']),
            'capacity' => intval($venue['capacity']),
            'location' => esc_html($venue['location']),
            'historic' => intval($venue['historic']),
        ];
    }, $venues);
}

// Function to handle deletion
function leanwi_delete_venue_page() {
    global $wpdb;
    $venue_table = $wpdb->prefix . 'leanwi_booking_venue';
    $hours_table = $wpdb->prefix . 'leanwi_booking_venue_hours';
    $participant_table =  $wpdb->prefix . 'leanwi_booking_participant';

    if (isset($_GET['venue_id'])) {
        $venue_id = intval($_GET['venue_id']);
        $participant = $wpdb->get_row($wpdb->prepare("SELECT 1 FROM $participant_table WHERE venue_id = %d", $venue_id));

        if($participant){
            echo '<div class="error"><p>Venue could not be deleted as there are meetings associated with the venue.</p></div>';
        } else {
            $wpdb->delete(
                $venue_table,
                ['venue_id' => $venue_id],
                ['%d']
            );
            $wpdb->delete(
                $hours_table,
                ['venue_id' => $venue_id],
                ['%d']
            );
            echo '<div class="deleted"><p>Venue deleted successfully.</p></div>';
        }
    } else {
        // Handle the case where no ID is provided
        echo '<div class="error"><p>No Venue ID provided for deletion.</p></div>';
    }
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
            $name = wp_unslash($name);

            $capacity = isset($_POST['capacity']) ? intval($_POST['capacity']) : 0;

            $description = sanitize_textarea_field($_POST['description']);
            $description = wp_unslash($description);

            $location = sanitize_text_field($_POST['location']);
            $location = wp_unslash($location);

            $image_url = esc_url($_POST['image_url']);

            $extra_text = sanitize_textarea_field($_POST['extra_text']);
            $extra_text = wp_unslash($extra_text);

            $max_slots = isset($_POST['max_slots']) ? intval($_POST['max_slots']) : 0;
            $slot_cost = isset($_POST['slot_cost']) ? floatval($_POST['slot_cost']) : 0.00;

            $page_url = esc_url($_POST['page_url']);
            $conditions_of_use_url = esc_url($_POST['conditions_of_use_url']);
            $display_affirmations = isset($_POST['display_affirmations']) ? 1 : 0;

            $booking_notes_label = sanitize_text_field($_POST['booking_notes_label']);
            $booking_notes_label = wp_unslash($booking_notes_label);

            $days_before_booking = isset($_POST['days_before_booking']) ? intval($_POST['days_before_booking']) : 0;
            $venue_admin_email = isset($_POST['venue_admin_email']) ? sanitize_email($_POST['venue_admin_email']) : '';
            $use_business_days_only = isset($_POST['use_business_days_only']) ? 1 : 0;

            $bookable_by_staff_only = isset($_POST['bookable_by_staff_only']) ? 1 : 0;
            $updated_by_staff_only = isset($_POST['updated_by_staff_only']) ? 1 : 0;

            $email_greeting = sanitize_text_field($_POST['email_greeting']);
            $email_greeting = wp_unslash($email_greeting);

            $email_opening_text = sanitize_textarea_field($_POST['email_opening_text']);
            $email_opening_text = wp_unslash($email_opening_text);

            $email_update_opening_text = sanitize_textarea_field($_POST['email_update_opening_text']);
            $email_update_opening_text = wp_unslash($email_update_opening_text);

            $email_need_assistance_text = sanitize_textarea_field($_POST['email_need_assistance_text']);
            $email_need_assistance_text = wp_unslash($email_need_assistance_text);

            $email_modify_booking_text = sanitize_textarea_field($_POST['email_modify_booking_text']);
            $email_modify_booking_text = wp_unslash($email_modify_booking_text);

            $email_sign_off_text = sanitize_textarea_field($_POST['email_sign_off_text']);
            $email_sign_off_text = wp_unslash($email_sign_off_text);

            // Ensure the value has 2 decimal places
            $slot_cost = number_format($slot_cost, 2, '.', '');

            // Find the max display_order and increment
            $max_order = $wpdb->get_var("SELECT MAX(display_order) FROM $venue_table");
            $new_order = ($max_order !== null) ? $max_order + 1 : 1;

            // Insert the new venue into the database
            $inserted = $wpdb->insert(
                $venue_table,
                array(
                    'name' => $name,
                    'display_order' => $new_order,
                    'capacity' => $capacity,
                    'description' => $description,
                    'location' => $location,
                    'image_url' => $image_url,
                    'extra_text' => $extra_text,
                    'max_slots' => $max_slots,
                    'slot_cost' => $slot_cost,
                    'page_url' => $page_url,
                    'conditions_of_use_url' => $conditions_of_use_url,
                    'display_affirmations' => $display_affirmations,
                    'booking_notes_label' => $booking_notes_label,
                    'days_before_booking' => $days_before_booking,
                    'venue_admin_email' => $venue_admin_email,
                    'use_business_days_only' => $use_business_days_only,
                    'bookable_by_staff_only' => $bookable_by_staff_only,
                    'updated_by_staff_only' => $updated_by_staff_only,
                    'email_greeting' => $email_greeting,
                    'email_opening_text' => $email_opening_text,
                    'email_update_opening_text' => $email_update_opening_text,
                    'email_need_assistance_text' => $email_need_assistance_text,
                    'email_modify_booking_text' => $email_modify_booking_text,
                    'email_sign_off_text' => $email_sign_off_text
                )
            );

            if ($inserted) {
                $venue_id = $wpdb->insert_id;
                $hours_table = $wpdb->prefix . 'leanwi_booking_venue_hours';
            
                // Normalize function (convert date to 2000-mm-dd)
                function normalize_date($date_str) {
                    $parts = explode('-', $date_str);
                    return '2000-' . $parts[1] . '-' . $parts[2];
                }
            
                // Build and normalize the ranges
                $set_count = count($_POST['hours_label']);
                $ranges = [];
            
                for ($i = 0; $i < $set_count; $i++) {
                    $label = sanitize_text_field($_POST['hours_label'][$i]);
                    $raw_start = sanitize_text_field($_POST['hours_start_date'][$i]);
                    $raw_end = sanitize_text_field($_POST['hours_end_date'][$i]);
                    $start = normalize_date($raw_start);
                    $end = normalize_date($raw_end);
            
                    // If wrapping range (e.g., Sept -> May), split into two parts
                    if ($end < $start) {
                        $ranges[] = [ 'start' => $start, 'end' => '2000-12-31', 'index' => $i ];
                        $ranges[] = [ 'start' => '2000-01-01', 'end' => $end, 'index' => $i ];
                    } else {
                        $ranges[] = [ 'start' => $start, 'end' => $end, 'index' => $i ];
                    }
                }
            
                // Sort ranges by start date
                usort($ranges, function ($a, $b) {
                    return strcmp($a['start'], $b['start']);
                });
            
                // Check for overlaps and gaps
                $last_end = '2000-01-01';
                foreach ($ranges as $range) {
                    if ($range['start'] > $last_end) {
                        echo '<div class="error"><p>Error: Gap detected between ' . $last_end . ' and ' . $range['start'] . '.</p></div>';
                        return;
                    }
                    if ($range['start'] < $last_end) {
                        echo '<div class="error"><p>Error: Overlapping ranges around ' . $range['start'] . '.</p></div>';
                        return;
                    }
                    $last_end = date('Y-m-d', strtotime($range['end'] . ' +1 day'));
                }
            
                // Ensure full year coverage
                if ($last_end !== '2001-01-01') {
                    echo '<div class="error"><p>Error: Date ranges do not cover the full year. Last range ends on ' . $last_end . '.</p></div>';
                    return;
                }
            
                // Proceed with inserts since validation passed
                $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                for ($i = 0; $i < $set_count; $i++) {
                    $label = sanitize_text_field($_POST['hours_label'][$i]);
                    $start_date = sanitize_text_field($_POST['hours_start_date'][$i]);
                    $end_date = sanitize_text_field($_POST['hours_end_date'][$i]);
            
                    foreach ($days as $day) {
                        $open_hour = intval($_POST[$day . '_open_hour'][$i]);
                        $open_minute = intval($_POST[$day . '_open_minute'][$i]);
                        $close_hour = intval($_POST[$day . '_close_hour'][$i]);
                        $close_minute = intval($_POST[$day . '_close_minute'][$i]);
            
                        $wpdb->insert(
                            $hours_table,
                            array(
                                'venue_id' => $venue_id,
                                'label' => $label,
                                'start_date' => $start_date,
                                'end_date' => $end_date,
                                'day_of_week' => ucfirst($day),
                                'open_time' => sprintf('%02d:%02d:00', $open_hour, $open_minute),
                                'close_time' => sprintf('%02d:%02d:00', $close_hour, $close_minute),
                            )
                        );
                    }
                }
            
                echo '<div class="updated"><p>Venue added successfully.</p></div>';
            }
             else {
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
        'conditions_of_use_url' => '',
        'display_affirmations' => 1,
        'booking_notes_label' => 'Booking Notes:',
        'days_before_booking' => 0,
        'venue_admin_email' => '',
        'use_business_days_only' => 0,
        'bookable_by_staff_only' => 0,
        'updated_by_staff_only' => 0,
        'email_greeting' => "Hello",
        'email_opening_text' => "Thank you for choosing our library for your upcoming event!\nYour booking is automatically confirmed but our library staff will review the details of your event to ensure eligibility.",
        'email_update_opening_text' => "Here are the most recent details of your updated booking.\nYour booking is automatically confirmed but our library staff will review the details of your event to ensure eligibility.",
        'email_need_assistance_text' => "If you have any questions or need further assistance reach out to our team by phone or replying to this email.",
        'email_modify_booking_text' => "To Cancel or Modify a Booking: Enter your Booking ID and make changes to your booking at this link:",
        'email_sign_off_text' => "Sincerely,\nLibrary Booking Staff"
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
                    <td><input type="text" id="name" name="name" value="<?php echo esc_attr($venue->name); ?>" required style="width: 90%;" /></td>
                </tr>
                <tr>
                    <th><label for="bookable_by_staff_only">This venue will only be bookable by staff</label></th>
                    <td><input type="checkbox" id="bookable_by_staff_only" name="bookable_by_staff_only" <?php echo ($venue->bookable_by_staff_only == 1) ? 'checked' : ''; ?>/></td>
                </tr>
                <tr>
                    <th><label for="updated_by_staff_only">This venue will only be updatable by staff</label></th>
                    <td>
                        <input type="checkbox" id="updated_by_staff_only" name="updated_by_staff_only" <?php echo ($venue->updated_by_staff_only == 1) ? 'checked' : ''; ?>/>
                        If checked, users will contact staff to make booking changes rather than be able to make the changes themselves.
                    </td>
                </tr>
                <tr>
                    <th><label for="capacity">Capacity</label></th>
                    <td><input type="number" id="capacity" name="capacity" value="<?php echo esc_attr($venue->capacity); ?>" required /></td>
                </tr>
                <tr>
                    <th><label for="description">Venue Summary</label></th>
                    <td><textarea id="description" name="description" required style="width: 90%;"><?php echo esc_textarea($venue->description); ?></textarea></td>
                </tr>
                <tr>
                    <th><label for="extra_text">More Display Text</label></th>
                    <td><textarea id="extra_text" name="extra_text" style="width: 90%;"><?php echo esc_textarea($venue->extra_text); ?></textarea></td>
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
                    <th><label for="days_before_booking">Bookings Days in Advance</label></th>
                    <td>
                        <input type="number" id="days_before_booking" name="days_before_booking" value="<?php echo esc_attr($venue->days_before_booking); ?>" required />
                        <label for="days_before_booking">(0 indicates a booking can be made on the same day)</label>
                        <input type="checkbox" id="use_business_days_only" name="use_business_days_only" <?php echo ($venue->use_business_days_only == 1) ? 'checked' : ''; ?>/>
                        <label for="use_business_days_only"><strong>Calculate using business days only?</strong> (i.e exclude weekends)</label>
                    </td>
                </tr>
                <tr>
                    <th><label for="image_url">Image URL</label></th>
                    <td><input type="text" id="image_url" name="image_url" style="width: 90%;" value="<?php echo esc_attr($venue->image_url); ?>" /></td>
                </tr>
                <tr>
                    <th><label for="conditions_of_use_url">Conditions of Use URL</label></th>
                    <td><input type="text" id="conditions_of_use_url" name="conditions_of_use_url" style="width: 90%;" value="<?php echo esc_attr($venue->conditions_of_use_url); ?>" /></td>
                </tr>
                <tr>
                    <th><label for="venue_admin_email">Venue Admin Email</label></th>
                    <td><input type="email" id="venue_admin_email" name="venue_admin_email" style="width: 90%;" value="<?php echo esc_attr($venue->venue_admin_email); ?>" /></td>
                </tr>
                <tr>
                    <th><label for="display_affirmations">Display affirmations for this venue?</label></th>
                    <td><input type="checkbox" id="display_affirmations" name="display_affirmations" <?php echo ($venue->display_affirmations == 1) ? 'checked' : ''; ?>/></td>
                </tr>
                <tr>
                    <th><label for="booking_notes_label">Booking Notes Label Text</label></th>
                    <td><input type="text" id="booking_notes_label" name="booking_notes_label" value="<?php echo esc_attr($venue->booking_notes_label); ?>" style="width: 90%;" /></td>
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
                    <th><label for="email_greeting">Email greeting</label></th>
                    <td><input type="text" id="email_greeting" name="email_greeting" value="<?php echo esc_attr($venue->email_greeting); ?>" required style="width: 90%;" /></td>
                </tr>
                <tr>
                    <th><label for="email_opening_text">Email opening text for confirmation emails</label></th>
                    <td><textarea id="email_opening_text" name="email_opening_text" style="width: 90%;"><?php echo esc_html($venue->email_opening_text); ?></textarea></td>
                </tr>
                <tr>
                    <th><label for="email_update_opening_text">Email update text for confirmation emails</label></th>
                    <td><textarea id="email_update_opening_text" name="email_update_opening_text" style="width: 90%;"><?php echo esc_html($venue->email_update_opening_text); ?></textarea></td>
                </tr>
                <tr>
                    <th><label for="email_need_assistance_text">Email need assistance text for confirmation emails</label></th>
                    <td><textarea id="email_need_assistance_text" name="email_need_assistance_text" style="width: 90%;"><?php echo esc_html($venue->email_need_assistance_text); ?></textarea></td>
                </tr>
                <tr>
                    <th><label for="email_modify_booking_text">Email "how to modify" text for confirmation emails</label></th>
                    <td><textarea id="email_modify_booking_text" name="email_modify_booking_text" style="width: 90%;"><?php echo esc_html($venue->email_modify_booking_text); ?></textarea></td>
                </tr>
                <tr>
                    <th><label for="email_sign_off_text">Email sign off text</label></th>
                    <td><textarea id="email_sign_off_text" name="email_sign_off_text" style="width: 90%;"><?php echo esc_html($venue->email_sign_off_text); ?></textarea></td>
                </tr>
                <tr>
                    <th><label for="page_url">Page URL</label></th>
                    <td><input type="text" id="page_url" name="page_url" style="width: 90%;" value="<?php echo esc_attr($venue->page_url); ?>" /></td>
                </tr>
            </table>

            <div id="hours-sets-container">
                <div class="hours-set">
                    <h2>Open Hours</h2>
                    <button type="button" class="remove-hours-set button" style="float: right;">Remove This Set of Hours</button>
                    <table class="form-table">
                        <?php
                        $start_of_year = '2000-01-01';
                        $end_of_year = '2000-12-31';
                        ?>
                        <tr>
                            <th><label>Hours Label</label></th>
                            <td><input type="text" name="hours_label[]" style="width: 50%;" value="Entire Year" required /></td>
                        </tr>
                        <tr>
                            <th><label>Start Date:</label></th>
                            <td>
                                <input type="date" name="hours_start_date[]" value="<?php echo esc_attr($start_of_year); ?>" required>
                                Please select a start month and day and choose any year (which is ignored)
                            </td>
                        </tr>
                        <tr>
                            <th><label>End Date:</label></th>
                            <td>
                                <input type="date" name="hours_end_date[]" value="<?php echo esc_attr($end_of_year); ?>" required>
                                Please select an ending month and day and choose any year (which is ignored)
                            </td>
                        </tr>
                        <?php foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day): ?>
                            <tr>
                                <th><label><?php echo ucfirst($day); ?></label></th>
                                <td>
                                    <select name="<?php echo $day; ?>_open_hour[]">
                                        <?php for ($hour = 0; $hour < 24; $hour++): ?>
                                            <option value="<?php echo $hour; ?>">
                                                <?php echo str_pad($hour, 2, '0', STR_PAD_LEFT); ?>
                                            </option>
                                        <?php endfor; ?>
                                    </select>
                                    :
                                    <select name="<?php echo $day; ?>_open_minute[]">
                                        <?php foreach ([0, 15, 30, 45] as $minute): ?>
                                            <option value="<?php echo $minute; ?>">
                                                <?php echo str_pad($minute, 2, '0', STR_PAD_LEFT); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    to
                                    <select name="<?php echo $day; ?>_close_hour[]">
                                        <?php for ($hour = 0; $hour < 24; $hour++): ?>
                                            <option value="<?php echo $hour; ?>">
                                                <?php echo str_pad($hour, 2, '0', STR_PAD_LEFT); ?>
                                            </option>
                                        <?php endfor; ?>
                                    </select>
                                    :
                                    <select name="<?php echo $day; ?>_close_minute[]">
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
                    <hr>
                </div>
            </div>

            <!-- Add Hours Set Button -->
            <p><button type="button" id="add-hours-set" class="button">Add Another Set of Hours</button></p>
            
            <!-- Script to clone the hours code below -->
            <script>
            document.getElementById('add-hours-set').addEventListener('click', function () {
                const container = document.getElementById('hours-sets-container');
                const sets = container.getElementsByClassName('hours-set');
                const lastSet = sets[sets.length - 1];
                const newSet = lastSet.cloneNode(true);

                // Clear all input values
                newSet.querySelectorAll('input, select').forEach(field => {
                    if (field.type === 'text' || field.type === 'date') {
                        field.value = field.name === 'hours_label[]' ? 'Modify This' : '';
                    } else {
                        field.selectedIndex = 0;
                    }
                });

                container.appendChild(newSet);
                attachRemoveListeners();
            });

            function attachRemoveListeners() {
                document.querySelectorAll('.remove-hours-set').forEach(button => {
                    button.onclick = function () {
                        const sets = document.querySelectorAll('.hours-set');
                        if (sets.length > 1) {
                            this.parentElement.remove();
                        } else {
                            alert('You must have at least one set of hours.');
                        }
                    };
                });
            }

            // Initial setup on page load
            attachRemoveListeners();
            </script>

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
                $name = wp_unslash($name);

                $capacity = isset($_POST['capacity']) ? intval($_POST['capacity']) : 0;

                $description = sanitize_textarea_field($_POST['description']);
                $description = wp_unslash($description);

                $location = sanitize_text_field($_POST['location']);
                $location = wp_unslash($location);

                $image_url = esc_url($_POST['image_url']);

                $extra_text = sanitize_textarea_field($_POST['extra_text']);
                $extra_text = wp_unslash($extra_text);

                $max_slots = isset($_POST['max_slots']) ? intval($_POST['max_slots']) : 0;
                $slot_cost = isset($_POST['slot_cost']) ? floatval($_POST['slot_cost']) : 0.00;

                $historic = isset($_POST['historic']) ? 1 : 0; // Set to 1 if checked, otherwise 0
                $page_url = esc_url($_POST['page_url']);
                $conditions_of_use_url = esc_url($_POST['conditions_of_use_url']);
                $display_affirmations = isset($_POST['display_affirmations']) ? 1 : 0;

                $booking_notes_label = sanitize_text_field($_POST['booking_notes_label']);
                $booking_notes_label = wp_unslash($booking_notes_label);
                
                $days_before_booking = isset($_POST['days_before_booking']) ? intval($_POST['days_before_booking']) : 0;
                $venue_admin_email = isset($_POST['venue_admin_email']) ? sanitize_email($_POST['venue_admin_email']) : '';
                $use_business_days_only = isset($_POST['use_business_days_only']) ? 1 : 0;

                $bookable_by_staff_only = isset($_POST['bookable_by_staff_only']) ? 1 : 0;
                $updated_by_staff_only = isset($_POST['updated_by_staff_only']) ? 1 : 0;

                $email_greeting = sanitize_text_field($_POST['email_greeting']);
                $email_greeting = wp_unslash($email_greeting);

                $email_opening_text = sanitize_textarea_field($_POST['email_opening_text']);
                $email_opening_text = wp_unslash($email_opening_text);

                $email_update_opening_text = sanitize_textarea_field($_POST['email_update_opening_text']);
                $email_update_opening_text = wp_unslash($email_update_opening_text);

                $email_need_assistance_text = sanitize_textarea_field($_POST['email_need_assistance_text']);
                $email_need_assistance_text = wp_unslash($email_need_assistance_text);

                $email_modify_booking_text = sanitize_textarea_field($_POST['email_modify_booking_text']);
                $email_modify_booking_text = wp_unslash($email_modify_booking_text);

                $email_sign_off_text = sanitize_textarea_field($_POST['email_sign_off_text']);
                $email_sign_off_text = wp_unslash($email_sign_off_text);
        
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
                        'historic' => $historic,
                        'page_url' => $page_url,
                        'conditions_of_use_url' => $conditions_of_use_url,
                        'display_affirmations' => $display_affirmations,
                        'booking_notes_label' => $booking_notes_label,
                        'days_before_booking' => $days_before_booking,
                        'venue_admin_email' => $venue_admin_email,
                        'use_business_days_only' => $use_business_days_only,
                        'bookable_by_staff_only' => $bookable_by_staff_only,
                        'updated_by_staff_only' => $updated_by_staff_only,
                        'email_greeting' => $email_greeting,
                        'email_opening_text' => $email_opening_text,
                        'email_update_opening_text' => $email_update_opening_text,
                        'email_need_assistance_text' => $email_need_assistance_text,
                        'email_modify_booking_text' => $email_modify_booking_text,
                        'email_sign_off_text' => $email_sign_off_text
                    ),
                    array('venue_id' => $venue_id)
                );
        
                if ($updated === false) {
                    $error_message = $wpdb->last_query;
                    echo '<div class="error"><p>Error updating venue: ' . esc_html($error_message) . '</p></div>';
                }
        
                $hours_table = $wpdb->prefix . 'leanwi_booking_venue_hours';

                //Get and Normalize current data and check it's ok
                function normalize_date($date_str) {
                    $parts = explode('-', $date_str);
                    return '2000-' . $parts[1] . '-' . $parts[2];
                }

                $set_count = count($_POST['hours_label']);
                $ranges = [];

                for ($i = 0; $i < $set_count; $i++) {
                    $label = sanitize_text_field($_POST['hours_label'][$i]);
                    $raw_start = sanitize_text_field($_POST['hours_start_date'][$i]);
                    $raw_end = sanitize_text_field($_POST['hours_end_date'][$i]);
                    $start = normalize_date($raw_start);
                    $end = normalize_date($raw_end);

                    if ($end < $start) {
                        $ranges[] = [ 'start' => $start, 'end' => '2000-12-31', 'index' => $i ];
                        $ranges[] = [ 'start' => '2000-01-01', 'end' => $end, 'index' => $i ];
                    } else {
                        $ranges[] = [ 'start' => $start, 'end' => $end, 'index' => $i ];
                    }
                }

                usort($ranges, function ($a, $b) {
                    return strcmp($a['start'], $b['start']);
                });

                // Overlap and gap check
                $last_end = '2000-01-01';
                $ranges_ok = true;
                foreach ($ranges as $range) {
                    if ($range['start'] > $last_end) {
                        echo '<div class="error"><p>Error: Gap detected between ' . $last_end . ' and ' . $range['start'] . '.</p></div>';
                        $ranges_ok = false;
                        break;
                    }
                    if ($range['start'] < $last_end) {
                        echo '<div class="error"><p>Error: Overlapping ranges around ' . $range['start'] . '.</p></div>';
                        $ranges_ok = false;
                        break;
                    }
                    $last_end = date('Y-m-d', strtotime($range['end'] . ' +1 day'));
                }

                if ($last_end !== '2001-01-01') {
                    echo '<div class="error"><p>Error: Date ranges do not cover the full year. Last range ends on ' . $last_end . '.</p></div>';
                    $ranges_ok = false;
                }

                if($ranges_ok) {
                    // Proceed with deleting then re-inserting validated hours

                    // Delete existing hours for this venue
                    $wpdb->delete($hours_table, ['venue_id' => $venue_id]);

                    // Insert current hours configuration
                    $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                    for ($i = 0; $i < $set_count; $i++) {
                        $label = sanitize_text_field($_POST['hours_label'][$i]);
                        $start_date = sanitize_text_field($_POST['hours_start_date'][$i]);
                        $end_date = sanitize_text_field($_POST['hours_end_date'][$i]);

                        foreach ($days as $day) {
                            $open_hour = intval($_POST[$day . '_open_hour'][$i]);
                            $open_minute = intval($_POST[$day . '_open_minute'][$i]);
                            $close_hour = intval($_POST[$day . '_close_hour'][$i]);
                            $close_minute = intval($_POST[$day . '_close_minute'][$i]);

                            $wpdb->insert(
                                $hours_table,
                                array(
                                    'venue_id' => $venue_id,
                                    'label' => $label,
                                    'start_date' => $start_date,
                                    'end_date' => $end_date,
                                    'day_of_week' => ucfirst($day),
                                    'open_time' => sprintf('%02d:%02d:00', $open_hour, $open_minute),
                                    'close_time' => sprintf('%02d:%02d:00', $close_hour, $close_minute),
                                )
                            );
                        }
                    }

                    echo '<div class="updated"><p>Venue details updated successfully.</p></div>';
                }
            } else {
                // Nonce is invalid; handle the error accordingly.
                wp_die('Nonce verification failed.');
            }
            // Refresh venue details
            $venue = $wpdb->get_row($wpdb->prepare("SELECT * FROM $venue_table WHERE venue_id = %d", $venue_id));
        }        
    }

    // If venue not found
    if (!$venue) {
        echo '<div class="error"><p>Venue not found.</p></div>';
        return;
    }

    // Fetch existing hours for each day
    $raw_results = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT label, start_date, end_date, day_of_week, open_time, close_time 
             FROM $hours_table 
             WHERE venue_id = %d 
             ORDER BY start_date, FIELD(day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')",
            $venue_id
        )
    );
    
    // Reorganize into grouped sets
    $existing_hours_sets = [];
    foreach ($raw_results as $row) {
        $key = $row->label . '|' . $row->start_date . '|' . $row->end_date;
        if (!isset($existing_hours_sets[$key])) {
            $existing_hours_sets[$key] = [
                'label' => $row->label,
                'start_date' => $row->start_date,
                'end_date' => $row->end_date,
                'days' => []
            ];
        }
        $existing_hours_sets[$key]['days'][strtolower($row->day_of_week)] = [
            'open_time' => $row->open_time,
            'close_time' => $row->close_time
        ];
    }

    //error_log('Existing Hours Sets: ' . json_encode($existing_hours_sets, JSON_PRETTY_PRINT));

    
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
                    <td><input type="text" id="name" name="name" value="<?php echo esc_attr($venue->name); ?>" required style="width: 90%;" /></td>
                </tr>
                <tr>
                    <th><label for="bookable_by_staff_only">This venue will only be bookable by staff</label></th>
                    <td><input type="checkbox" id="bookable_by_staff_only" name="bookable_by_staff_only" <?php echo ($venue->bookable_by_staff_only == 1) ? 'checked' : ''; ?>/></td>
                </tr>
                <tr>
                    <th><label for="updated_by_staff_only">This venue will only be updatable by staff</label></th>
                    <td>
                        <input type="checkbox" id="updated_by_staff_only" name="updated_by_staff_only" <?php echo ($venue->updated_by_staff_only == 1) ? 'checked' : ''; ?>/>
                        If checked, users will contact staff to make booking changes rather than be able to make the changes themselves.
                    </td>
                </tr>
                <tr>
                    <th><label for="capacity">Capacity</label></th>
                    <td><input type="number" id="capacity" name="capacity" value="<?php echo esc_attr($venue->capacity); ?>" required /></td>
                </tr>
                <tr>
                    <th><label for="description">Venue Summary</label></th>
                    <td><textarea id="description" name="description" required style="width: 90%;"><?php echo esc_textarea($venue->description); ?></textarea></td>
                </tr>
                <tr>
                    <th><label for="extra_text">More Display Text</label></th>
                    <td><textarea id="extra_text" name="extra_text" style="width: 90%;"><?php echo esc_textarea($venue->extra_text); ?></textarea></td>
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
                    <th><label for="days_before_booking">Bookings Days in Advance</label></th>
                    <td>
                        <input type="number" id="days_before_booking" name="days_before_booking" value="<?php echo esc_attr($venue->days_before_booking); ?>" required />
                        <label for="days_before_booking">(0 indicates a booking can be made on the same day)</label>
                        <input type="checkbox" id="use_business_days_only" name="use_business_days_only" <?php echo ($venue->use_business_days_only == 1) ? 'checked' : ''; ?>/>
                        <label for="use_business_days_only"><strong>Calculate using business days only?</strong> (i.e exclude weekends)</label>
                    </td>
                </tr>
                <tr>
                    <th><label for="image_url">Image URL</label></th>
                    <td><input type="text" id="image_url" name="image_url" style="width: 90%;" value="<?php echo esc_attr((string)$venue->image_url); ?>" /></td>
                </tr>
                <tr>
                    <th><label for="conditions_of_use_url">Conditions of Use URL</label></th>
                    <td><input type="text" id="conditions_of_use_url" name="conditions_of_use_url" style="width: 90%;" value="<?php echo esc_attr($venue->conditions_of_use_url); ?>" /></td>
                </tr>
                <tr>
                    <th><label for="venue_admin_email">Venue Admin Email</label></th>
                    <td><input type="email" id="venue_admin_email" name="venue_admin_email" style="width: 90%;" value="<?php echo esc_attr($venue->venue_admin_email); ?>" /></td>
                </tr>
                <tr>
                    <th><label for="display_affirmations">Display affirmations for this venue?</label></th>
                    <td><input type="checkbox" id="display_affirmations" name="display_affirmations" <?php echo ($venue->display_affirmations == 1) ? 'checked' : ''; ?>/></td>
                </tr>
                <tr>
                    <th><label for="booking_notes_label">Booking Notes Label Text</label></th>
                    <td><input type="text" id="booking_notes_label" name="booking_notes_label" value="<?php echo esc_attr($venue->booking_notes_label); ?>" style="width: 90%;" /></td>
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
                    <th><label for="email_greeting">Email greeting</label></th>
                    <td><input type="text" id="email_greeting" name="email_greeting" value="<?php echo esc_attr($venue->email_greeting); ?>" required style="width: 90%;" /></td>
                </tr>
                <tr>
                    <th><label for="email_opening_text">Email opening text for confirmation emails</label></th>
                    <td><textarea id="email_opening_text" name="email_opening_text" style="width: 90%;"><?php echo esc_html($venue->email_opening_text); ?></textarea></td>
                </tr>
                <tr>
                    <th><label for="email_update_opening_text">Email update text for confirmation emails</label></th>
                    <td><textarea id="email_update_opening_text" name="email_update_opening_text" style="width: 90%;"><?php echo esc_html($venue->email_update_opening_text); ?></textarea></td>
                </tr>
                <tr>
                    <th><label for="email_need_assistance_text">Email need assistance text for confirmation emails</label></th>
                    <td><textarea id="email_need_assistance_text" name="email_need_assistance_text" style="width: 90%;"><?php echo esc_html($venue->email_need_assistance_text); ?></textarea></td>
                </tr>
                <tr>
                    <th><label for="email_modify_booking_text">Email "how to modify" text for confirmation emails"</label></th>
                    <td><textarea id="email_modify_booking_text" name="email_modify_booking_text" style="width: 90%;"><?php echo esc_html($venue->email_modify_booking_text); ?></textarea></td>
                </tr>
                <tr>
                    <th><label for="email_sign_off_text">Email sign off text</label></th>
                    <td><textarea id="email_sign_off_text" name="email_sign_off_text" style="width: 90%;"><?php echo esc_html($venue->email_sign_off_text); ?></textarea></td>
                </tr>

                <tr>
                    <th><label for="page_url">Page URL</label></th>
                    <td><input type="text" id="page_url" name="page_url" style="width: 90%;" value="<?php echo esc_attr((string)$venue->page_url); ?>" /></td>
                </tr>
            </table>

            <h2>Open Hours</h2>
            <div id="hours-sets-container">
                <?php if (!empty($existing_hours_sets)): ?>
                    <?php foreach ($existing_hours_sets as $index => $set): ?>
                        <div class="hours-set">
                            <button type="button" class="remove-hours-set button" style="float: right;">Remove This Set of Hours</button>
                            <table class="form-table">
                                <tr>
                                    <th><label>Hours Label</label></th>
                                    <td>
                                        <input type="text" name="hours_label[]" style="width: 50%;" value="<?php echo esc_attr($set['label']); ?>" required />
                                    </td>
                                </tr>
                                <tr>
                                    <th><label>Start Date:</label></th>
                                    <td>
                                        <input type="date" name="hours_start_date[]" value="<?php echo esc_attr($set['start_date']); ?>" required>
                                        Please select a start month and day and choose any year (which is ignored)
                                    </td>
                                </tr>
                                <tr>
                                    <th><label>End Date:</label></th>
                                    <td>
                                        <input type="date" name="hours_end_date[]" value="<?php echo esc_attr($set['end_date']); ?>" required>
                                        Please select an ending month and day and choose any year (which is ignored)
                                    </td>
                                </tr>
                                <?php foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day): ?>
                                    <?php
                                        $open_time = isset($set['days'][$day]['open_time']) ? $set['days'][$day]['open_time'] : '00:00:00';
                                        $close_time = isset($set['days'][$day]['close_time']) ? $set['days'][$day]['close_time'] : '00:00:00';
                                        
                                        $open_parts = explode(':', $open_time);
                                        $close_parts = explode(':', $close_time);
                                        
                                        $open_hour = intval($open_parts[0] ?? 0);
                                        $open_minute = intval($open_parts[1] ?? 0);
                                        $close_hour = intval($close_parts[0] ?? 0);
                                        $close_minute = intval($close_parts[1] ?? 0);
                                        
                                    ?>
                                    <tr>
                                        <th><label><?php echo ucfirst($day); ?></label></th>
                                        <td>
                                            <select name="<?php echo $day; ?>_open_hour[]">
                                                <?php for ($hour = 0; $hour < 24; $hour++): ?>
                                                    <option value="<?php echo $hour; ?>" <?php selected($open_hour, $hour); ?>>
                                                        <?php echo str_pad($hour, 2, '0', STR_PAD_LEFT); ?>
                                                    </option>
                                                <?php endfor; ?>
                                            </select>
                                            :
                                            <select name="<?php echo $day; ?>_open_minute[]">
                                                <?php foreach ([0, 15, 30, 45] as $minute): ?>
                                                    <option value="<?php echo $minute; ?>" <?php selected($open_minute, $minute); ?>>
                                                        <?php echo str_pad($minute, 2, '0', STR_PAD_LEFT); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            to
                                            <select name="<?php echo $day; ?>_close_hour[]">
                                                <?php for ($hour = 0; $hour < 24; $hour++): ?>
                                                    <option value="<?php echo $hour; ?>" <?php selected($close_hour, $hour); ?>>
                                                        <?php echo str_pad($hour, 2, '0', STR_PAD_LEFT); ?>
                                                    </option>
                                                <?php endfor; ?>
                                            </select>
                                            :
                                            <select name="<?php echo $day; ?>_close_minute[]">
                                                <?php foreach ([0, 15, 30, 45] as $minute): ?>
                                                    <option value="<?php echo $minute; ?>" <?php selected($close_minute, $minute); ?>>
                                                        <?php echo str_pad($minute, 2, '0', STR_PAD_LEFT); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                            <hr>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Fallback: display one empty set if no existing sets -->
                    <table class="form-table">
                        <?php
                        $start_of_year = '2000-01-01';
                        $end_of_year = '2000-12-31';
                        ?>
                        <tr>
                            <th><label>Hours Label</label></th>
                            <td><input type="text" name="hours_label[]" style="width: 50%;" value="Entire Year" required /></td>
                        </tr>
                        <tr>
                            <th><label>Start Date:</label></th>
                            <td>
                                <input type="date" name="hours_start_date[]" value="<?php echo esc_attr($start_of_year); ?>" required>
                                Please select a start month and day and choose any year (which is ignored)
                            </td>
                        </tr>
                        <tr>
                            <th><label>End Date:</label></th>
                            <td>
                                <input type="date" name="hours_end_date[]" value="<?php echo esc_attr($end_of_year); ?>" required>
                                Please select an ending month and day and choose any year (which is ignored)
                            </td>
                        </tr>
                        <?php foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day): ?>
                            <tr>
                                <th><label><?php echo ucfirst($day); ?></label></th>
                                <td>
                                    <select name="<?php echo $day; ?>_open_hour[]">
                                        <?php for ($hour = 0; $hour < 24; $hour++): ?>
                                            <option value="<?php echo $hour; ?>">
                                                <?php echo str_pad($hour, 2, '0', STR_PAD_LEFT); ?>
                                            </option>
                                        <?php endfor; ?>
                                    </select>
                                    :
                                    <select name="<?php echo $day; ?>_open_minute[]">
                                        <?php foreach ([0, 15, 30, 45] as $minute): ?>
                                            <option value="<?php echo $minute; ?>">
                                                <?php echo str_pad($minute, 2, '0', STR_PAD_LEFT); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    to
                                    <select name="<?php echo $day; ?>_close_hour[]">
                                        <?php for ($hour = 0; $hour < 24; $hour++): ?>
                                            <option value="<?php echo $hour; ?>">
                                                <?php echo str_pad($hour, 2, '0', STR_PAD_LEFT); ?>
                                            </option>
                                        <?php endfor; ?>
                                    </select>
                                    :
                                    <select name="<?php echo $day; ?>_close_minute[]">
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
                    <hr>
                <?php endif; ?>
            </div>

            <p><button type="button" id="add-hours-set" class="button">Add Another Set of Hours</button></p>

            <script>
            document.addEventListener('DOMContentLoaded', function () {
                const container = document.getElementById('hours-sets-container');
                const addButton = document.getElementById('add-hours-set');

                // Delegate click to remove buttons
                container.addEventListener('click', function (e) {
                    if (e.target.classList.contains('remove-hours-set')) {
                        const set = e.target.closest('.hours-set');
                        if (set) {
                            set.remove();
                        }
                    }
                });

                addButton.addEventListener('click', function () {
                    const sets = container.querySelectorAll('.hours-set');
                    if (sets.length === 0) return;

                    const clone = sets[0].cloneNode(true);

                    // Clear inputs/selects
                    clone.querySelectorAll('input, select').forEach(el => {
                        if (el.tagName === 'INPUT') {
                            el.value = '';
                        } else if (el.tagName === 'SELECT') {
                            el.selectedIndex = 0;
                        }
                    });

                    container.appendChild(clone);
                });
            });
            </script>


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

    // Process display order update if the form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_display_order'])) {
        if (isset($_POST['display_order']) && is_array($_POST['display_order'])) {
            foreach ($_POST['display_order'] as $category_id => $display_order) {
                $category_id = intval($category_id);
                $display_order = intval($display_order);

                $wpdb->update(
                    $table_name,
                    ['display_order' => $display_order],
                    ['category_id' => $category_id],
                    ['%d'],
                    ['%d']
                );
            }
            echo '<div class="updated notice"><p>Display order updated successfully.</p></div>';
        }
    }

    // Display category list
    echo '<div class="wrap">';
    echo '<h1>Categories</h1>';

    echo '<a href="' . admin_url('admin.php?page=leanwi-add-category') . '" class="button button-primary">Add Category</a>';
    echo '<p> </p>'; // Space below the button before the category table

    echo '<form method="POST">';
    echo '<table class="wp-list-table widefat striped">';
    echo '<thead>';
    echo '<tr>';
    echo '<th scope="col">Category ID</th>';
    echo '<th scope="col">Category Name</th>';
    echo '<th scope="col">Display Order</th>';
    echo '<th scope="col">Historic</th>';
    echo '<th scope="col">Actions</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';

    // Fetch categories
    $categories = fetch_categories();
    if (isset($categories['error'])) {
        echo '<tr><td colspan="5">' . esc_html($categories['error']) . '</td></tr>';
    } else {
        // Display each category in a row
        foreach ($categories['categories'] as $category) {
            echo '<tr>';
            echo '<td>' . esc_html($category['category_id']) . '</td>';
            echo '<td>' . esc_html($category['category_name']) . '</td>';
            echo '<td><input type="number" name="display_order[' . esc_attr($category['category_id']) . ']" value="' . esc_attr($category['display_order']) . '" style="width: 60px;"></td>';
            echo '<td>' . ($category['historic'] ? 'Yes' : 'No') . '</td>';
            echo '<td>';
            echo '<a href="' . esc_url(admin_url('admin.php?page=leanwi-edit-category&category_id=' . esc_attr($category['category_id']))) . '" class="button">Edit</a> ';
            echo '</td>';
            echo '</tr>';
        }
    }

    echo '</tbody>';
    echo '</table>';

    echo '<p><input type="submit" name="save_display_order" value="Save Display Order" class="button button-primary"></p>';
    echo '</form>';
    echo '</div>';
}

// Function to get categories
function fetch_categories() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'leanwi_booking_category';

    // Fetch categories
    $categories = $wpdb->get_results("SELECT category_id, category_name, display_order, historic FROM $table_name", ARRAY_A);

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
        // Find the max display_order and increment
        $max_order = $wpdb->get_var("SELECT MAX(display_order) FROM $table_name");
        $new_order = ($max_order !== null) ? $max_order + 1 : 1;

        $category_name = sanitize_text_field($_POST['category_name']);
        $category_name = wp_unslash($category_name);

        $wpdb->insert(
            $table_name,
            ['category_name' => $category_name, 
             'display_order' => $new_order,
             'historic' => isset($_POST['historic']) ? 1 : 0],
            ['%s', '%d', '%d']
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
        $category_name = wp_unslash($category_name);
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

    // Process display order update if the form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_display_order'])) {
        if (isset($_POST['display_order']) && is_array($_POST['display_order'])) {
            foreach ($_POST['display_order'] as $audience_id => $display_order) {
                $audience_id = intval($audience_id);
                $display_order = intval($display_order);

                $wpdb->update(
                    $table_name,
                    ['display_order' => $display_order],
                    ['audience_id' => $audience_id],
                    ['%d'],
                    ['%d']
                );
            }
            echo '<div class="updated notice"><p>Display order updated successfully.</p></div>';
        }
    }

    // Display audience list
    echo '<div class="wrap">';
    echo '<h1>Audiences</h1>';

    echo '<a href="' . admin_url('admin.php?page=leanwi-add-audience') . '" class="button button-primary">Add Audience</a>';
    echo '<p> </p>'; // Space below the button before the audience table

    echo '<form method="POST">';
    echo '<table class="wp-list-table widefat striped">';
    echo '<thead>';
    echo '<tr>';
    echo '<th scope="col">Audience ID</th>';
    echo '<th scope="col">Audience Name</th>';
    echo '<th scope="col">Display Order</th>';
    echo '<th scope="col">Historic</th>';
    echo '<th scope="col">Actions</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';

    // Fetch audiences
    $audiences = fetch_audiences();
    if (isset($audiences['error'])) {
        echo '<tr><td colspan="5">' . esc_html($audiences['error']) . '</td></tr>';
    } else {
        // Display each audience in a row
        foreach ($audiences['audiences'] as $audience) {
            echo '<tr>';
            echo '<td>' . esc_html($audience['audience_id']) . '</td>';
            echo '<td>' . esc_html($audience['audience_name']) . '</td>';
            echo '<td><input type="number" name="display_order[' . esc_attr($audience['audience_id']) . ']" value="' . esc_attr($audience['display_order']) . '" style="width: 60px;"></td>';
            echo '<td>' . ($audience['historic'] ? 'Yes' : 'No') . '</td>';
            echo '<td>';
            echo '<a href="' . esc_url(admin_url('admin.php?page=leanwi-edit-audience&audience_id=' . esc_attr($audience['audience_id']))) . '" class="button">Edit</a> ';
            echo '</td>';
            echo '</tr>';
        }
    }

    echo '</tbody>';
    echo '</table>';

    echo '<p><input type="submit" name="save_display_order" value="Save Display Order" class="button button-primary"></p>';
    echo '</form>';
    echo '</div>';
}


// Function to get audiences
function fetch_audiences() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'leanwi_booking_audience';

    // Fetch audiences
    $audiences = $wpdb->get_results("SELECT audience_id, audience_name, display_order, historic FROM $table_name", ARRAY_A);

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
        // Find the max display_order and increment
        $max_order = $wpdb->get_var("SELECT MAX(display_order) FROM $table_name");
        $new_order = ($max_order !== null) ? $max_order + 1 : 1;
        $audience_name = sanitize_text_field($_POST['audience_name']);
        $audience_name = wp_unslash($audience_name);

        $wpdb->insert(
            $table_name,
            ['audience_name' => $audience_name,
             'display_order' => $new_order,
             'historic' => isset($_POST['historic']) ? 1 : 0],
            ['%s', '%d', '%d']
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
        $audience_name = wp_unslash($audience_name);
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
 * Affirmations
 **************************************************************************************************/

// Function to display the list of affirmations
function leanwi_affirmations_page() {
    global $wpdb;

    // Display affirmations list
    echo '<div class="wrap">';
    echo '<h1>Affirmations</h1>';

    echo '<a href="' . admin_url('admin.php?page=leanwi-add-affirmation') . '" class="button button-primary">Add Affirmation</a>';
    echo '<p> </p>'; // Space below the button before the affirmation table

    echo '<table class="wp-list-table widefat striped">';
    echo '<thead>';
    echo '<tr>';
    echo '<th scope="col">ID</th>';
    echo '<th scope="col" width="75%">Affirmation</th>';
    echo '<th scope="col" style="text-align: right; padding-right: 40px;">Actions</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';

    // Fetch affirmations
    $affirmations = fetch_affirmations();
    if (empty($affirmations)) {
        echo '<tr><td colspan="3">No affirmations found.</td></tr>';
    } else {
        // Display each affirmation in a row
        foreach ($affirmations as $affirmation) {
            echo '<tr>';
            echo '<td>' . esc_html($affirmation['id']) . '</td>';
            echo '<td>' . esc_html($affirmation['affirmation']) . '</td>';
            echo '<td style="text-align: right;">';
            echo '<a href="' . esc_url(admin_url('admin.php?page=leanwi-edit-affirmation&id=' . esc_attr($affirmation['id']))) . '" class="button">Edit</a> ';
            echo '<a href="' . esc_url(admin_url('admin.php?page=leanwi-delete-affirmation&id=' . esc_attr($affirmation['id']))) . '" class="button" onclick="return confirm(\'Are you sure you want to delete this affirmation?\');">Delete</a>';
            echo '</td>';
            echo '</tr>';
        }
    }

    echo '</tbody>';
    echo '</table>';
    echo '</div>';
}

// Function to get affirmations
function fetch_affirmations() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'leanwi_booking_affirmation';

    // Fetch affirmations and check for database errors
    $affirmations = $wpdb->get_results("SELECT id, affirmation FROM $table_name", ARRAY_A);
    if ($wpdb->last_error) {
        return ['error' => $wpdb->last_error];
    }

    return $affirmations ?: []; // Return an empty array if no results are found
}

// Function to handle deletion
function leanwi_delete_affirmation_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'leanwi_booking_affirmation';

    // Check if an ID is provided for deleting
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $wpdb->delete(
            $table_name,
            ['id' => $id],
            ['%d']
        );
        echo '<div class="deleted"><p>Affirmation deleted successfully.</p></div>';
    } else {
        // Handle the case where no ID is provided
        echo '<div class="error"><p>No affirmation ID provided for deletion.</p></div>';
    }
}

function leanwi_add_affirmation_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'leanwi_booking_affirmation';

    // Handle form submission
    if (isset($_POST['add_affirmation'])) {
        $wpdb->insert(
            $table_name,
            ['affirmation' => sanitize_text_field($_POST['affirmation'])],
            ['%s']
        );
        echo '<div class="updated"><p>Affirmation added successfully.</p></div>';
    }

    // Display the add affirmation form
    echo '<div class="wrap">';
    echo '<h1>Add Affirmation</h1>';
    echo '<form method="POST">';
    echo '<p>Affirmation:<br><textarea name="affirmation" rows="5" cols="50" required></textarea></p>';
    echo '<p><input type="submit" name="add_affirmation" value="Add Affirmation" class="button button-primary"></p>';
    echo '</form>';
    echo '</div>';
}

// Function to handle editing of an affirmation
function leanwi_edit_affirmation_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'leanwi_booking_affirmation';

    // Handle the form submission to update the affirmation
    if (isset($_POST['update_affirmation'])) {
        $id = intval($_POST['id']);
        $affirmation = sanitize_text_field($_POST['affirmation']);

        // Update the affirmation in the database
        $wpdb->update(
            $table_name,
            ['affirmation' => $affirmation],
            ['id' => $id],
            ['%s'],
            ['%d']
        );

        echo '<div class="notice notice-success"><p>Affirmation updated successfully.</p></div>';
    }

    // Check if an ID is provided for editing
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $affirmation = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        if ($affirmation) {
            // Display form to edit the affirmation
            echo '<div class="wrap">';
            echo '<h1>Edit Affirmation</h1>';
            echo '<form method="POST">';
            echo '<input type="hidden" name="id" value="' . esc_attr($affirmation->id) . '">';

            // Display the Affirmation input
            echo '<p>Affirmation:<br><textarea name="affirmation" rows="5" cols="50" required>' . esc_textarea($affirmation->affirmation) . '</textarea></p>';
            
            // Submit button to update the affirmation
            echo '<p><input type="submit" name="update_affirmation" value="Save Changes" class="button button-primary"></p>';
            echo '</form>';
            echo '</div>';
        } else {
            // Display a message if the affirmation is not found
            echo '<div class="notice notice-error"><p>Affirmation not found.</p></div>';
        }
    } else {
        // Display a message if no ID is provided
        echo '<div class="notice notice-error"><p>No ID provided.</p></div>';
    }
}


/**************************************************************************************************
 * Reporting
 **************************************************************************************************/

// Function to display the reporting functionality
function leanwi_reports_page() {
    // Fetch venue data from the database
    // Fetch venues
    $venues = fetch_venues();
    if (isset($venues['error'])) {
        echo '<tr><td colspan="6">' . esc_html($venues['error']) . '</td></tr>';
        return;
    }
    
    // Define the directory path for reports
    $upload_dir = wp_upload_dir();
    $reports_dir = $upload_dir['basedir'] . '/leanwi_booking_reports/';
    
    // Get report files
    $report_files = glob($reports_dir . '*.csv');
    $report_count = count($report_files);

    ?>
    <div class="wrap">
        <h1>Reports</h1>
        <form id="leanwi-usage-report-form" method="post" action="<?php echo plugins_url('LEANWI-Book-A-Room/php/plugin/generate-usage-report.php'); ?>">
            <h2>Room Usage Reporting</h2>
            <?php wp_nonce_field('leanwi_generate_report', 'leanwi_generate_report_nonce'); ?>
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
                <input type="submit" value="Generate Usage Report" class="button button-primary">
            </div>
        </form>
        <hr>

        <form id="leanwi-payment-report-form" method="post" action="<?php echo plugins_url('LEANWI-Book-A-Room/php/plugin/generate-payment-report.php'); ?>">
            <h2>Payment Reporting</h2>
            <?php wp_nonce_field('leanwi_generate_report', 'leanwi_generate_report_nonce'); ?>
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
                    <label for="include_paid">Include Paid:</label>
                    <input type="checkbox" id="include_paid" name="include_paid" value="yes">
                </div>
                <div class="form-group">
                    <label for="include_unpaid">Include Unpaid:</label>
                    <input type="checkbox" id="include_unpaid" name="include_unpaid" value="yes">
                </div>
            </div>
            <div class="form-row">
                <input type="submit" value="Generate Payment Report" class="button button-primary">
            </div>
        </form>
        <hr>

        <form id="leanwi-organization-report-form" method="post" action="<?php echo plugins_url('LEANWI-Book-A-Room/php/plugin/generate-organization-report.php'); ?>">
            <h2>Organization Reporting</h2>
            <?php wp_nonce_field('leanwi_generate_report', 'leanwi_generate_report_nonce'); ?>
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
            </div>
            <div class="form-row">
                <input type="submit" value="Generate Organization Report" class="button button-primary">
            </div>
        </form>
        <hr>

        <!-- Handle form submission so we can update the number of reports we have on the server after the report has been created -->
        <script type="text/javascript">
            function handleFormSubmit(event) {
                
                const form = this;
                const formData = new FormData(form);
                formData.append('_ajax_nonce', '<?php echo wp_create_nonce('leanwi_generate_report_nonce'); ?>');

                // Disable the submit button to prevent double submission
                const submitButton = form.querySelector("input[type='submit']");
                submitButton.disabled = true;

                // Perform AJAX request to generate the report
                fetch(form.action, {
                    method: "POST",
                    body: formData,
                })
                .then(response => {
                    if (!response.ok) throw new Error("Failed to generate report.");
                    return response.text();
                })
                .then(() => {
                    return fetch("<?php echo admin_url('admin-ajax.php'); ?>?action=leanwi_get_report_count", {
                        method: "GET",
                        credentials: "same-origin",
                    });
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.querySelector(".purge-reports-section p").innerText = 
                            `You currently have ${data.data.report_count} reports sitting on the server.`;
                    }
                })
                .catch(error => alert(error.message))
                .finally(() => {
                    submitButton.disabled = false; // Re-enable the button after completion
                });
            };
            // Attach the event listener to both forms
            document.getElementById("leanwi-usage-report-form").onsubmit = handleFormSubmit;
            document.getElementById("leanwi-payment-report-form").onsubmit = handleFormSubmit;
            document.getElementById("leanwi-organization-report-form").onsubmit = handleFormSubmit;
        </script>

        <div class="purge-reports-section">
            <p>You currently have <?php echo esc_html($report_count); ?> reports sitting on the server.</p>
            <form method="post" action="" onsubmit="return confirmPurge();">
                <?php wp_nonce_field('leanwi_purge_reports', 'leanwi_purge_reports_nonce'); ?>
                <input type="hidden" name="purge_reports" value="1">
                <input type="submit" value="Purge Old Reports" class="button button-secondary">
            </form>
        </div>

        <script type="text/javascript">
            function confirmPurge() {
                return confirm("Are you sure you want to purge all old reports? This action cannot be undone.");
            }
        </script>
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
        #leanwi-usage-report-form input[type="date"] {
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
        .purge-reports-section {
            margin-top: 20px;
        }
    </style>
    <?php
}

// Handle report purge action
function leanwi_purge_reports() {
    if (isset($_POST['purge_reports']) && $_POST['purge_reports'] == '1') {
        // Verify the nonce
        if (!isset($_POST['leanwi_purge_reports_nonce']) || !wp_verify_nonce($_POST['leanwi_purge_reports_nonce'], 'leanwi_purge_reports')) {
            wp_die('Nonce verification failed. Please reload the page and try again.');
        }
        // Define the directory path for reports
        $upload_dir = wp_upload_dir();
        $reports_dir = $upload_dir['basedir'] . '/leanwi_booking_reports/';
        
        // Get all report files in the directory
        $report_files = glob($reports_dir . '*.csv');
        
        // Delete each file
        foreach ($report_files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        
        // Redirect back to the reports page
        wp_safe_redirect(admin_url('admin.php?page=leanwi-book-a-room-reports'));
        exit;
    }   
}
add_action('admin_init', __NAMESPACE__ . '\\leanwi_purge_reports');

// AJAX handler for fetching updated report count
function leanwi_get_report_count() {
    // Check if the user has the required permissions
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Unauthorized', 403);
    }

    // Get the directory path for reports
    $upload_dir = wp_upload_dir();
    $reports_dir = $upload_dir['basedir'] . '/leanwi_booking_reports/';
    
    // Count the reports
    $report_files = glob($reports_dir . '*.csv');
    $report_count = count($report_files);

    // Return the count
    wp_send_json_success(['report_count' => $report_count]);
}
add_action('wp_ajax_leanwi_get_report_count', __NAMESPACE__ . '\\leanwi_get_report_count');


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
    register_setting('leanwi_plugin_settings_group', 'leanwi_show_physical_address');
    register_setting('leanwi_plugin_settings_group', 'leanwi_show_zero_cost');
    register_setting('leanwi_plugin_settings_group', 'leanwi_show_categories');
    register_setting('leanwi_plugin_settings_group', 'leanwi_show_audiences');
    register_setting('leanwi_plugin_settings_group', 'leanwi_send_admin_booking_email');
    register_setting('leanwi_plugin_settings_group', 'leanwi_admin_email_address');
    register_setting('leanwi_plugin_settings_group', 'leanwi_feedback_form_link');
    register_setting('leanwi_plugin_settings_group', 'leanwi_highlighted_button_border_color');
    register_setting('leanwi_plugin_settings_group', 'leanwi_highlighted_button_bg_color');
    register_setting('leanwi_plugin_settings_group', 'leanwi_highlighted_button_text_color');

    // Register settings for reCAPTCHA enable, site key, and secret key
    register_setting('leanwi_plugin_settings_group', 'leanwi_enable_recaptcha');
    register_setting('leanwi_plugin_settings_group', 'leanwi_recaptcha_site_key');
    register_setting('leanwi_plugin_settings_group', 'leanwi_recaptcha_secret_key');

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
        __NAMESPACE__ . '\\leanwi_minutes_interval_field',// Function to display the dropdown
        'leanwi-book-a-room-settings',  // Page slug
        'leanwi_main_section'           // Section ID
    );

    // Add Booking Months in advance input
    add_settings_field(
        'leanwi_booking_months',  // Field ID
        'Booking Months in Advance',         // Label for the field
        __NAMESPACE__ . '\\leanwi_booking_months_field', // Function to display the input
        'leanwi-book-a-room-settings',  // Page slug
        'leanwi_main_section'           // Section ID
    );

    // Add 'Show Physical Address to Users?' setting field
    add_settings_field(
        'leanwi_show_physical_address',       // Field ID
        'Users Enter a Physical Address?', // Label for the field
        __NAMESPACE__ . '\\leanwi_show_physical_address_field', // Function to display the dropdown
        'leanwi-book-a-room-settings',  // Page slug
        'leanwi_main_section'           // Section ID
    );

    // Add 'Show Zero Cost to Users?' setting field
    add_settings_field(
        'leanwi_show_zero_cost',       // Field ID
        'Show Cost to Users if Zero?', // Label for the field
        __NAMESPACE__ . '\\leanwi_show_zero_cost_field', // Function to display the dropdown
        'leanwi-book-a-room-settings',  // Page slug
        'leanwi_main_section'           // Section ID
    );

    // Add 'Show Categories to Users' setting field
    add_settings_field(
        'leanwi_show_categories',       // Field ID
        'Show Categories to Users?',     // Label for the field
        __NAMESPACE__ . '\\leanwi_show_categories_field', // Function to display the dropdown
        'leanwi-book-a-room-settings',  // Page slug
        'leanwi_main_section'           // Section ID
    );

    // Add 'Show Audiences to Users' setting field
    add_settings_field(
        'leanwi_show_audiences',        // Field ID
        'Show Audiences to Users?',      // Label for the field
        __NAMESPACE__ . '\\leanwi_show_audiences_field',  // Function to display the dropdown
        'leanwi-book-a-room-settings',  // Page slug
        'leanwi_main_section'           // Section ID
    );

    // Add wheter admin should be sent a copy of the booking email field
    add_settings_field(
        'leanwi_send_admin_booking_email',       // Field ID
        'Send Admin a Copy of the Booking Email?',     // Label for the field
        __NAMESPACE__ . '\\leanwi_send_admin_booking_email_field', // Function to display the dropdown
        'leanwi-book-a-room-settings',  // Page slug
        'leanwi_main_section'           // Section ID
    );

    // Add Admin email address field
    add_settings_field(
        'leanwi_admin_email_address',  // Field ID
        'Booking Admin Email Address',         // Label for the field
        __NAMESPACE__ . '\\leanwi_admin_email_address_field', // Function to display the input
        'leanwi-book-a-room-settings',  // Page slug
        'leanwi_main_section'           // Section ID
    );

    // Add Admin email address field
    add_settings_field(
        'leanwi_feedback_form_link',  // Field ID
        'URL Link to Feedback Form',         // Label for the field
        __NAMESPACE__ . '\\leanwi_feedback_form_link_field', // Function to display the input
        'leanwi-book-a-room-settings',  // Page slug
        'leanwi_main_section'           // Section ID
    );

    // Add border color for highlighted buttons field
    add_settings_field(
        'leanwi_highlighted_button_border_color',  // Field ID
        'Border color for highlighted buttons',         // Label for the field
        __NAMESPACE__ . '\\leanwi_highlighted_button_border_color_field', // Function to display the input
        'leanwi-book-a-room-settings',  // Page slug
        'leanwi_main_section'           // Section ID
    );

    // Add bg color for highlighted buttons field
    add_settings_field(
        'leanwi_highlighted_button_bg_color',  // Field ID
        'Background color for highlighted buttons',         // Label for the field
        __NAMESPACE__ . '\\leanwi_highlighted_button_bg_color_field', // Function to display the input
        'leanwi-book-a-room-settings',  // Page slug
        'leanwi_main_section'           // Section ID
    );

    // Add text color for highlighted buttons field
    add_settings_field(
        'leanwi_highlighted_button_text_color',  // Field ID
        'Text color for highlighted buttons',         // Label for the field
        __NAMESPACE__ . '\\leanwi_highlighted_button_text_color_field', // Function to display the input
        'leanwi-book-a-room-settings',  // Page slug
        'leanwi_main_section'           // Section ID
    );

    // Add field to enable/disable reCAPTCHA
    add_settings_field(
        'leanwi_enable_recaptcha',
        'Enable reCAPTCHA',
        __NAMESPACE__ . '\\leanwi_enable_recaptcha_field',
        'leanwi-book-a-room-settings',
        'leanwi_main_section'
    );

    // Add field for reCAPTCHA site key
    add_settings_field(
        'leanwi_recaptcha_site_key',
        'reCAPTCHA Site Key',
        __NAMESPACE__ . '\\leanwi_recaptcha_site_key_field',
        'leanwi-book-a-room-settings',
        'leanwi_main_section'
    );

    // Add field for reCAPTCHA secret key
    add_settings_field(
        'leanwi_recaptcha_secret_key',
        'reCAPTCHA Secret Key',
        __NAMESPACE__ . '\\leanwi_recaptcha_secret_key_field',
        'leanwi-book-a-room-settings',
        'leanwi_main_section'
    );
}

// Hook the settings registration function
add_action('admin_init', __NAMESPACE__ . '\\leanwi_register_settings');

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

// Function to display 'Show Physical_address to Users' dropdown
function leanwi_show_physical_address_field() {
    $value = get_option('leanwi_show_physical_address', 'no'); // Default to 'no' if no value is set
    ?>
    <select id="leanwi_show_physical_address" name="leanwi_show_physical_address">
        <option value="yes" <?php selected($value, 'yes'); ?>>Yes</option>
        <option value="no" <?php selected($value, 'no'); ?>>No</option>
    </select>
    <?php
}

// Function to display 'Show Zero Cost to Users' dropdown
function leanwi_show_zero_cost_field() {
    $value = get_option('leanwi_show_zero_cost', 'no'); // Default to 'no' if no value is set
    ?>
    <select id="leanwi_show_zero_cost" name="leanwi_show_zero_cost">
        <option value="yes" <?php selected($value, 'yes'); ?>>Yes</option>
        <option value="no" <?php selected($value, 'no'); ?>>No</option>
    </select>
    <?php
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
    echo '<input type="email" id="leanwi_admin_email_address" name="leanwi_admin_email_address" value="' . esc_attr($value) . '"  style="width: 75%;"/>';
}

// Function to display the admin email address input
function leanwi_feedback_form_link_field() {
    $value = get_option('leanwi_feedback_form_link', ''); // Get saved value or default to an empty string
    echo '<input type="url" id="leanwi_feedback_form_link" name="leanwi_feedback_form_link" value="' . esc_attr($value) . '"  style="width: 75%;"/>';
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
    echo '<hr style="margin-top: 40px; border: 1px solid #ccc;">'; // Adds a horizontal line before the reCAPTCHA fields
}

// Function to display 'Enable reCAPTCHA' dropdown
function leanwi_enable_recaptcha_field() {
    $value = get_option('leanwi_enable_recaptcha', 'no'); // Default to 'no' if not set
    ?>
    <select id="leanwi_enable_recaptcha" name="leanwi_enable_recaptcha">
        <option value="yes" <?php selected($value, 'yes'); ?>>Yes</option>
        <option value="no" <?php selected($value, 'no'); ?>>No</option>
    </select>
    <?php
}

// Function to display the reCAPTCHA site key input
function leanwi_recaptcha_site_key_field() {
    $value = get_option('leanwi_recaptcha_site_key', ''); // Get saved value or default to an empty string
    echo '<input type="password" id="leanwi_recaptcha_site_key" name="leanwi_recaptcha_site_key" value="' . esc_attr($value) . '" style="width: 75%;" />';
}

// Function to display the reCAPTCHA secret key input
function leanwi_recaptcha_secret_key_field() {
    $value = get_option('leanwi_recaptcha_secret_key', ''); // Get saved value or default to an empty string
    echo '<input type="password" id="leanwi_recaptcha_secret_key" name="leanwi_recaptcha_secret_key" value="' . esc_attr($value) . '" style="width: 75%;" />';
    echo '<hr style="margin-top: 40px; margin-bottom: 20px; border: 1px solid #ccc;">'; // Adds a horizontal line before the reCAPTCHA fields
}



/**************************************************************************************************
 * Booking Staff Management Page
 **************************************************************************************************/

function leanwi_booking_staff_page() {
    // Ensure only administrators can access this page.
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }

    // Handle role assignment form submission.
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['leanwi_assign_booking_staff'])) {
        $user_id = intval($_POST['user_id']);
        $user = get_user_by('ID', $user_id);
        if ($user) {
            $user->add_role('booking_staff');
            echo '<div class="updated"><p>User assigned the "Booking Staff" role successfully.</p></div>';
        } else {
            echo '<div class="error"><p>Invalid user selected.</p></div>';
        }
    }

    // Handle role unassignment form submission.
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['leanwi_unassign_booking_staff'])) {
        $user_id = intval($_POST['leanwi_unassign_booking_staff']);
        $user = get_user_by('ID', $user_id);
        if ($user) {
            $user->remove_role('booking_staff');
            echo '<div class="updated"><p>User unassigned the "Booking Staff" role successfully.</p></div>';
        } else {
            echo '<div class="error"><p>Invalid user selected for unassignment.</p></div>';
        }
    }

    // Display the user assignment form.
    echo '<div class="wrap">';
    echo '<h1>Manage Booking Staff</h1>';
    echo '<form method="post">';
    echo '<label for="user_id">Select User:</label>';
    echo '<select name="user_id" id="user_id">';
    $users = get_users();
    foreach ($users as $user) {
        echo '<option value="' . esc_attr($user->ID) . '">' . esc_html($user->display_name) . '</option>';
    }
    echo '</select>';
    echo '<button type="submit" name="leanwi_assign_booking_staff" class="button-primary">Assign Booking Staff Role</button>';
    echo '</form>';

    // List users with the "booking_staff" role and provide unassignment option.
    $staff_users = get_users(['role' => 'booking_staff']);
    if (!empty($staff_users)) {
        echo '<h2>Current Booking Staff</h2>';
        echo '<ul>';
        foreach ($staff_users as $staff_user) {
            echo '<li>';
            echo esc_html($staff_user->display_name) . ' (' . esc_html($staff_user->user_email) . ')';
            echo ' <form method="post" style="display:inline;">';
            echo '<input type="hidden" name="leanwi_unassign_booking_staff" value="' . esc_attr($staff_user->ID) . '">';
            echo '<button type="submit" class="button-secondary">Remove</button>';
            echo '</form>';
            echo '</li>';
        }
        echo '</ul>';
    } else {
        echo '<p>No users have been assigned the "Booking Staff" role yet.</p>';
    }

    echo '</div>';
}


/**************************************************************************************************
 * Role Registration on Plugin Activation
 **************************************************************************************************/

 function leanwi_register_booking_staff_role() {
    add_role(
        'booking_staff',
        __('Booking Staff', 'leanwi-book-a-room'), // Added text domain for translation.
        [
            'read' => true, // Allow basic dashboard access.
            'manage_staff_booking_pages' => true, // Allow management of staff pages and use of staff functionality on booking pages
        ]
    );
}
register_activation_hook(__FILE__, __NAMESPACE__ . '\\leanwi_register_booking_staff_role');

// Ensure the "booking_staff" role exists for backward compatibility on plugin load.
add_action('init', function() {
    if (!get_role('booking_staff')) {
        leanwi_register_booking_staff_role();
    }
});
