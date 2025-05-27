<?php
/**
 * Plugin Name: Display Recurring Bookings
 * Description: A plugin to allow staff to implement recurring booking functionality in the LEANWI Room Booking System.
 * Version: 1.0
 * Author: Brendan Tuckey
 */

// Register the shortcode for the venue grid
function display_recurring_bookings() {

    // Fetch venues from the database
    global $wpdb;
    
    // Check if the user has staff privileges
    $current_user = wp_get_current_user();
    $is_booking_staff = in_array('booking_staff', (array) $current_user->roles);

    // If the user is not a staff member, display a restricted access message
    if (!$is_booking_staff) {
        return 'You do not have the permissions required to use this page.';
    }

    $venues_table = "{$wpdb->prefix}leanwi_booking_venue";
    $venues = $wpdb->get_results("SELECT venue_id, name, capacity FROM $venues_table WHERE historic = 0 ORDER BY name ASC");

    if (!$venues) {
        echo "<p>No venues available for selection.</p>";
        return;
    }

    $time_options = generate_time_options();
    $use_recaptcha = get_option('leanwi_enable_recaptcha', 'no'); // Check if reCAPTCHA is enabled
    $recaptcha_site_key = get_option('leanwi_recaptcha_site_key', ''); // Retrieve the reCAPTCHA site key

    ob_start();
    ?>
    <div class="recurring-choices-container" id="recurring-choices-container" style="display: block">
        <h2 id="recurring_choices_heading" style="text-align: center; margin-bottom: 1rem; margin-top: 1rem;">Make a new recurrent booking or find an existing one</h2>
        <form id="recurring-choices" method="POST">
            <div class="button-container">
                <button type="submit" class="find-button">Add a recurrent Booking</button>
                <button type="button" id="retrieve-recurrence" class="find-button">Find a recurrent Booking</button>
            </div>
        </form>
    </div>

    <div class="existing-recurrence-container" id="existing-recurrence-container" style="display: none;">
        <table id="recurrenceTable" class="styled-table">
            <thead>
                <tr>
                    <th>Venue Name</th>
                    <th>Recurrence Type</th>
                    <th>Interval</th>
                    <th>End Date</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Organization</th>
                    <th>Name</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="recurrenceTableBody">
                <!-- Rows will be populated dynamically -->
            </tbody>
        </table>
    </div>

    <?php
    // Only include reCAPTCHA script if enabled
    if ('yes' === $use_recaptcha && !empty($recaptcha_site_key)) {
        ?>
        <script src="https://www.google.com/recaptcha/api.js?render=<?php echo esc_js($recaptcha_site_key); ?>"></script>
        <?php
    }
    ?>
    <div class="recurrence-details-container" id="recurrence-details-container" style="display: none; margin-bottom: 1rem; margin-top: 1rem;">
        <form id="recurrence-details-form" method="POST" style="max-width: 600px; margin: 0 auto;">
            <?php wp_nonce_field('submit_recurrence_action', 'submit_recurrence_nonce'); ?>
            <?php wp_nonce_field('delete_recurrence_action', 'delete_recurrence_nonce'); ?>
            
            <input type="hidden" id="recurrence_id" name="recurrence_id" value="0">


            <label for="venue_id">Select Venue:</label>
            <select id="venue_id" name="venue_id" required style="width: 100%; padding: 8px; margin-bottom: 10px;">
                <option value="" disabled selected>Select a venue</option>
                <?php foreach ($venues as $venue): ?>
                    <option value="<?php echo esc_attr($venue->venue_id); ?>">
                        <?php echo esc_html("{$venue->name} (Capacity: {$venue->capacity})"); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <!-- Recurrence Type -->
            <label for="recurrence_type">Recurrence Type:</label>
            <select id="recurrence_type" name="recurrence_type" required style="width: 100%; padding: 8px; margin-bottom: 10px;">
                <option value="" disabled selected>Select recurrence type</option>
                <option value="daily">Daily</option>
                <option value="weekly">Weekly</option>
                <option value="monthly">Monthly</option>
                <option value="nth_weekday">Nth Weekday</option>
            </select>

            <label for="start_time">Start Time:</label>
            <select id="start_time" name="start_time" required style="width: 100%; padding: 8px; margin-bottom: 10px;">
                <option value="" disabled selected>Select a start time</option>
                <?php foreach ($time_options as $time): ?>
                    <option value="<?php echo esc_attr($time); ?>"><?php echo esc_html($time); ?></option>
                <?php endforeach; ?>
            </select>

            <label for="end_time">End Time:</label>
            <select id="end_time" name="end_time" required style="width: 100%; padding: 8px; margin-bottom: 10px;">
                <option value="" disabled selected>Select an end time</option>
                <?php foreach ($time_options as $time): ?>
                    <option value="<?php echo esc_attr($time); ?>"><?php echo esc_html($time); ?></option>
                <?php endforeach; ?>
            </select>

            <!-- Recurrence Interval -->
            <label for="recurrence_interval">Recurrence Interval: <span class="info-icon" class="info-icon" title="Number of days/weeks/months between recurrences."></span></label>
            <input type="number" id="recurrence_interval" name="recurrence_interval" min="1" value="1" required style="width: 100%; padding: 8px; margin-bottom: 10px;">

            <!-- Recurrence Day of Week -->
            <label for="recurrence_day_of_week">Recurrence Day of Week: <span class="info-icon" title="Only required for weekly or nth weekday recurrences."></span></label>
            <select id="recurrence_day_of_week" name="recurrence_day_of_week" style="width: 100%; padding: 8px; margin-bottom: 10px;">
                <option value="" selected>Select a day</option>
                <option value="0">Sunday</option>
                <option value="1">Monday</option>
                <option value="2">Tuesday</option>
                <option value="3">Wednesday</option>
                <option value="4">Thursday</option>
                <option value="5">Friday</option>
                <option value="6">Saturday</option>
            </select>

            <!-- Recurrence Start Date -->
            <label for="recurrence_start_date">Recurrence Start Date:</label>
            <input type="date" id="recurrence_start_date" name="recurrence_start_date" required style="width: 100%; padding: 8px; margin-bottom: 10px;">

            <!-- Recurrence End Date -->
            <label for="recurrence_end_date">Recurrence End Date:</label>
            <input type="date" id="recurrence_end_date" name="recurrence_end_date" required style="width: 100%; padding: 8px; margin-bottom: 10px;">


            <!-- Recurrence Week of Month -->
            <label for="recurrence_week_of_month">Recurrence Week of Month: <span class="info-icon" title="1=First, 2=Second, -1=Last, -2=2nd Last. Only for nth weekday recurrences."></span></label>
            <input type="number" id="recurrence_week_of_month" name="recurrence_week_of_month" style="width: 100%; padding: 8px; margin-bottom: 10px;">

            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required style="width: 100%; padding: 8px; margin-bottom: 10px;">
            
            <label for="name">Organization:</label>
            <input type="text" id="organization" name="organization" style="width: 100%; padding: 8px; margin-bottom: 10px;">

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" style="width: 100%; padding: 8px; margin-bottom: 10px;">

            <label for="phone">Phone Number:</label>
            <input type="text" id="phone" name="phone" style="width: 100%; padding: 8px; margin-bottom: 10px;">

            <label for="participants">Number of Participants:</label>
            <input type="number" id="participants" name="participants" style="width: 100%; padding: 8px; margin-bottom: 10px;">

            <label for="notes">Booking Notes:</label>
            <textarea id="notes" name="notes" style="width: 100%; height: 100px; padding: 8px; margin-bottom: 10px;"></textarea>

            <!-- Category Section -->
            <label for="category">Category:</label>
            <select id="category" name="category" style="width: 100%; padding: 8px; margin-bottom: 10px;"></select>           

            <!-- Audience Section -->
            <label for="audience">Audience:</label>
            <select id="audience" name="audience" style="width: 100%; padding: 8px; margin-bottom: 10px;"></select>

            <div class="button-container">
                <button type="submit" id="book_button" class="book-button">Save Recurrence and add Bookings</button>
                <button type="button" id="delete_booking" class="book-button" style="background-color: red; color: white; display: none;">Delete Recurrence and future Bookings</button>
            </div>
        </form>
    </div>

    <div id="booking-message" class="booking-message" style="display: none;">
        <!-- Message will be injected here -->
    </div>

    <?php
    return ob_get_clean();
}

add_shortcode('staff_recurring_bookings', 'display_recurring_bookings');

// Generate the time options in 15-minute intervals
function generate_time_options() {
    $times = [];
    $start = strtotime('5:00 AM');
    $end = strtotime('11:45 PM');

    while ($start <= $end) {
        $times[] = date('g:i A', $start);
        $start = strtotime('+15 minutes', $start);
    }
    return $times;
}

?>