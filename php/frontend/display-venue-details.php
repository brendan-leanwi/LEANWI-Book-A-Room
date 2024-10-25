<?php
/**
 * Plugin Name: Display Venue Details
 * Description: A plugin to display venue details.
 * Version: 1.0
 * Author: Brendan Tuckey
 */

// Register the shortcode for the venue details
function display_venue_details($atts) {
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

            <div class="booking-container">
                <p><h2 id="previous_booking_h2">Looking for a previously placed booking?</h2></p>
                <p> </p>
                <form id="retrieve-booking" method="POST">
                    <label for="unique_id" class="find-label">Booking ID:</label>
                    <input type="text" id="unique_id" name="unique_id" class="find-input" required>
                    
                    <div class="button-container">
                        <button type="submit" class="find-button">Retrieve Booking</button>
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
    
    <div id="contact-form-container" style="display: none;">
        <form id="booking-form" method="POST" style="max-width: 600px; margin: 0 auto;">
            <p><br></p>
            <p>
            <h2 id="available-times-heading"></h2>
            </p>
            <p><br></p>
            <input type="hidden" id="venue_id" value="<?php echo esc_html($atts['venue_id']); ?>">
            <input type="hidden" id="day" name="day">
            
            <label for="time">Select Time(s):</label>
            <div id="time-select" style="display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 15px;"></div>

            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required style="width: 100%; padding: 8px; margin-bottom: 10px;">

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required style="width: 100%; padding: 8px; margin-bottom: 10px;">

            <label for="phone">Phone Number:</label>
            <input type="text" id="phone" name="phone" required style="width: 100%; padding: 8px; margin-bottom: 10px;">

            <label for="participants">Number of Participants:</label>
            <input type="number" id="participants" name="participants" required style="width: 100%; padding: 8px; margin-bottom: 10px;">

            <label for="notes">Booking Notes:</label>
            <textarea id="notes" name="notes" style="width: 100%; height: 100px; padding: 8px; margin-bottom: 10px;"></textarea>
            
            <?php if ($show_categories === 'yes'): ?>
                <label for="category">Category:</label>
                <select id="category" name="category" required style="width: 100%; padding: 8px; margin-bottom: 10px;"></select>
            <?php else: ?>
                <input type="hidden" name="category" value="1"> <!-- Default value for category -->
            <?php endif; ?>

            <?php if ($show_audiences === 'yes'): ?>
                <label for="audience">Audience:</label>
                <select id="audience" name="audience" required style="width: 100%; padding: 8px; margin-bottom: 10px;"></select>
            <?php else: ?>
                <input type="hidden" name="audience" value="1"> <!-- Default value for audience -->
            <?php endif; ?>

            <p id="total-cost-text"><strong>Total Cost:</strong> $<span id="total-cost">0.00</span></p>

            <button type="submit" class="book-button">Save Booking</button>
        </form>

    </div>


    <?php
    return ob_get_clean();
}

add_shortcode('venue_details', 'display_venue_details');
?>
