<?php
namespace LEANWI_Book_A_Room;

// Function to create the necessary tables on plugin activation
function leanwi_create_tables() {
    // Load WordPress environment to access $wpdb
    require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';

    global $wpdb;

    $charset_collate = $wpdb->get_charset_collate();
    $engine = "ENGINE=InnoDB";

    // SQL for creating leanwi_booking_venue table
    $sql1 = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}leanwi_booking_venue (
        venue_id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        capacity INT NOT NULL,
        description TEXT,
        location VARCHAR(255),
        max_slots INT NOT NULL DEFAULT 100,
        slot_cost DECIMAL(10,2) DEFAULT 0.00,
        image_url VARCHAR(255),
        page_url VARCHAR(255),
        conditions_of_use_url VARCHAR(255),
        display_affirmations TINYINT(1) DEFAULT 1,
        extra_text TEXT,
        email_text TEXT,
        historic TINYINT(1) DEFAULT 0
    ) $engine $charset_collate;";

    // SQL for creating leanwi_booking_venue_hours table
    $sql2 = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}leanwi_booking_venue_hours (
        hour_id INT AUTO_INCREMENT PRIMARY KEY,
        venue_id INT NOT NULL,
        day_of_week ENUM('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday') NOT NULL,
        open_time TIME NOT NULL,
        close_time TIME NOT NULL,
        FOREIGN KEY (venue_id) REFERENCES {$wpdb->prefix}leanwi_booking_venue(venue_id) ON DELETE CASCADE
    ) $engine $charset_collate;";

    // SQL for creating leanwi_booking_category table
    $sql3 = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}leanwi_booking_category (
        category_id INT AUTO_INCREMENT PRIMARY KEY,
        category_name VARCHAR(255) NOT NULL,
        historic TINYINT(1) DEFAULT 0
    ) $engine $charset_collate;";

    // SQL for creating leanwi_booking_audience table
    $sql4 = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}leanwi_booking_audience (
        audience_id INT AUTO_INCREMENT PRIMARY KEY,
        audience_name VARCHAR(255) NOT NULL,
        historic TINYINT(1) DEFAULT 0
    ) $engine $charset_collate;";

    $sql5 = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}leanwi_booking_recurrence (
        recurrence_id INT AUTO_INCREMENT PRIMARY KEY,
        recurrence_type ENUM('daily', 'weekly', 'monthly', 'nth_weekday') NOT NULL,
        recurrence_interval INT DEFAULT 1, -- Number of days/weeks/months between recurrences
        recurrence_start_date DATE NOT NULL, -- When the recurrence is to start
        recurrence_end_date DATE NOT NULL, -- When the recurrence stops
        recurrence_day_of_week TINYINT, -- For weekly or nth_weekday recurrences (0=Sunday, 6=Saturday)
        recurrence_week_of_month TINYINT, -- For nth_weekday recurrences (1=First, 2=Second, -1=last, -2=2nd last...)
        start_time TIME NOT NULL, -- HH:MM Hours and minutes the recurrence will start (No year/yonth/day necessary)
        end_time TIME NOT NULL, -- HH:MM Hours and minutes the recurrence will end (No year/yonth/day necessary)
        venue_id INT NOT NULL,
        organization VARCHAR(255),
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255),
        phone VARCHAR(20),
        number_of_participants INT NOT NULL,
        booking_notes TEXT,
        category_id INT,
        audience_id INT
        --historic TINYINT(1) DEFAULT 0 ????
    ) $engine $charset_collate;";

    // SQL for creating leanwi_booking_participant table
    $sql6 = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}leanwi_booking_participant (
        id INT AUTO_INCREMENT PRIMARY KEY,
        unique_id CHAR(7) NOT NULL UNIQUE,
        venue_id INT NOT NULL,
        recurrence_id INT,
        organization VARCHAR(255),
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255),
        phone VARCHAR(20),
        start_time DATETIME NOT NULL,
        end_time DATETIME NOT NULL,
        number_of_participants INT NOT NULL,
        booking_notes TEXT,
        category_id INT,
        audience_id INT,
        total_cost DECIMAL(10,2) DEFAULT 0.00,
        FOREIGN KEY (venue_id) REFERENCES {$wpdb->prefix}leanwi_booking_venue(venue_id) ON DELETE CASCADE
    ) $engine $charset_collate;";

    // SQL for creating leanwi_booking_category table
    $sql7 = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}leanwi_booking_affirmation (
        id INT AUTO_INCREMENT PRIMARY KEY,
        affirmation TEXT NOT NULL
    ) $engine $charset_collate;";

    // Execute the SQL queries
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    //*************************************************************************************** */
    // Doesn't work as one call like this - perhaps it runs them all at the same time?
    //dbDelta([$sql1, $sql2, $sql3, $sql4, $sql5, $sql6]);
    //*************************************************************************************** */
    
    dbDelta($sql1);
    // Debug logging to track SQL execution
    if ($wpdb->last_error) {
        error_log('DB Error1: ' . $wpdb->last_error); // Logs the error to wp-content/debug.log
    }
    dbDelta($sql2);
    // Debug logging to track SQL execution
    if ($wpdb->last_error) {
        error_log('DB Error2: ' . $wpdb->last_error); // Logs the error to wp-content/debug.log
    }
    dbDelta($sql3);
    // Debug logging to track SQL execution
    if ($wpdb->last_error) {
        error_log('DB Error3: ' . $wpdb->last_error); // Logs the error to wp-content/debug.log
    }
    dbDelta($sql4);
    // Debug logging to track SQL execution
    if ($wpdb->last_error) {
        error_log('DB Error4: ' . $wpdb->last_error); // Logs the error to wp-content/debug.log
    }
    dbDelta($sql5);
    // Debug logging to track SQL execution
    if ($wpdb->last_error) {
        error_log('DB Error5: ' . $wpdb->last_error); // Logs the error to wp-content/debug.log
    }
    dbDelta($sql6);
    // Debug logging to track SQL execution
    if ($wpdb->last_error) {
        error_log('DB Error6: ' . $wpdb->last_error); // Logs the error to wp-content/debug.log
    }
    dbDelta($sql7);
    // Debug logging to track SQL execution
    if ($wpdb->last_error) {
        error_log('DB Error7: ' . $wpdb->last_error); // Logs the error to wp-content/debug.log
    }

    // Insert default category
    $wpdb->insert(
        "{$wpdb->prefix}leanwi_booking_category",
        array(
            'category_id' => 1,
            'category_name' => 'Uncategorized',
            'historic' => 0
        ),
        array('%d', '%s', '%d') // Data types
    );

    // Insert default audience
    $wpdb->insert(
        "{$wpdb->prefix}leanwi_booking_audience",
        array(
            'audience_id' => 1,
            'audience_name' => 'Uncategorized',
            'historic' => 0
        ),
        array('%d', '%s', '%d') // Data types
    );
}


// Function to drop the tables on plugin uninstall
function leanwi_drop_tables() {
    global $wpdb;

    // SQL to drop the tables
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}leanwi_booking_participant");
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}leanwi_booking_venue_hours");
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}leanwi_booking_category");
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}leanwi_booking_audience");
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}leanwi_booking_affirmation");
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}leanwi_user");
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}leanwi_booking_venue");
}
