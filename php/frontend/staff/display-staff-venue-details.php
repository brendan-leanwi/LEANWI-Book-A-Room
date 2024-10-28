<?php
/**
 * Plugin Name: Display Venue Details
 * Description: A plugin to display venue details.
 * Version: 1.0
 * Author: Brendan Tuckey
 */

// Register the shortcode for the venue details
function display_staff_venue_details($atts) {
    // Extract shortcode attributes
    $atts = shortcode_atts(
        array(
            'venue_id' => 1, // Default venue_id
        ),
        $atts
    );
    
    // Get the settings for categories and audiences
    $show_categories = get_option('leanwi_show_categories', 'no'); // Default to 'no' if option not set
    $show_audiences = get_option('leanwi_show_audiences', 'no'); // Default to 'no' if option not set
    $minutes_interval = intval(get_option('leanwi_minutes_interval', 30));

    // Output the HTML and add the venue ID as a hidden field
    ob_start();
    ?>
    <div id="venue-details" style="display: flex;">
        <div style="flex: 1; padding-right: 20px;">
            <h1><span id="venue-name"></span></h1>
            <p></p>
            <p><strong>Capacity:</strong> <span id="venue-capacity"></span></p>
            <p><strong>Description:</strong> <span id="venue-description"></span></p>
            <p><strong>Location:</strong> <span id="venue-location"></span></p>
            <p><strong>Cost:</strong> $<span id="venue-slot-cost">0.00</span> per <?php echo esc_html($minutes_interval); ?> minute time slot</p>
            <p></p>
            <p><span id="venue-extra-text"></span></p>
            <input type="hidden" id="venue_id" value="<?php echo esc_html($atts['venue_id']); ?>">
            <input type="hidden" id="venue-max-slots" value="100">
            <input type="hidden" id="venue-email-text" value="">
            <input type="hidden" id="venue-page-url" value="">

            <div class="booking-container">
                <p><h2>Already know your Booking ID?</h2></p>
                <p> </p>
                <form id="retrieve-booking" method="POST">
                    <!-- Set up nonce verification for the fetch and delete actions -->
                    <?php wp_nonce_field('delete_booking_action', 'delete_booking_nonce'); ?>

                    <label for="unique_id" class="find-label">Booking ID:</label>
                    <input type="text" id="unique_id" name="unique_id" class="find-input" required>
                    
                    <div class="button-container">
                        <button type="submit" class="find-button">View Booking</button>
                        <button type="button" id="delete-booking" class="find-button" style="background-color: red; color: white;">Delete Booking</button>
                    </div>
                </form>
            </div>    

        </div>
        <div style="flex: 1;">
            <img id="venue-image" src="" alt="Venue Image">
        </div>
    </div>
 
    <p><br></p>
    <h2>Available Days Calendar</h2>
    <p><br></p>
    <div id="calendar-navigation">
        <button id="prev-month" aria-label="Previous Month">&larr;</button>
        <h3><span id="current-month"></span></h3>
        <button id="next-month" aria-label="Next Month">&rarr;</button>
    </div>
    <div id="calendar"></div>

    <div id="day-bookings-container" style="display: none;">
        <p><br></p>
        <p>
        <h2 id="day-bookings-heading"></h2>
        </p>
        <table class="wp-list-table widefat striped" id="bookings-table">
            <thead>
                <tr>
                    <th scope="col">Name</th>
                    <th scope="col">Email</th>
                    <th scope="col">Phone</th>
                    <th scope="col">Start Time</th>
                    <th scope="col">End Time</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <?php
    return ob_get_clean();
}

add_shortcode('staff_venue_details', 'display_staff_venue_details');
?>
