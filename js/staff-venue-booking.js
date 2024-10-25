document.addEventListener("DOMContentLoaded", function () {
    const venueId = document.getElementById('venue_id').value;
    console.log('Venue Id:', venueId); // Add this to see the response

    // Get the user-defined colors from bookingSettings
    const highlightedButtonBgColor = bookingSettings.highlightedButtonBgColor || '#ffe0b3';
    const highlightedButtonBorderColor = bookingSettings.highlightedButtonBorderColor || '#ff9800';
    const highlightedButtonTextColor = bookingSettings.highlightedButtonTextColor || '#000000';

    // Function to extract query parameters from the URL
    function getQueryParam(param) {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get(param);
    }

    // Get booking_id from the URL and set it in the unique_id input field if it exists
    const bookingId = getQueryParam('booking_id');
    if (bookingId) {
        document.getElementById('unique_id').value = bookingId;
    }

    // Update the CSS variables in the :root selector
    document.documentElement.style.setProperty('--highlighted-button-bg', highlightedButtonBgColor);
    document.documentElement.style.setProperty('--highlighted-button-border', highlightedButtonBorderColor);
    document.documentElement.style.setProperty('--highlighted-button-text', highlightedButtonTextColor);

    let currentMonth = new Date(); // The month that is currently being displayed
    let maxMonths = bookingSettings.maxMonths || 1; // Use the localized data
    let currentDetailsDate = new Date();

    fetch(`/wp-content/plugins/LEANWI-Book-A-Room/php/frontend/get-venue-details.php?venue_id=${venueId}`)
        .then(response => response.json())
        .then(venue => {
            console.log('Venue details:',venue); // Add this to see the response
            document.getElementById('venue-name').textContent = venue.name;
            document.getElementById('venue-capacity').textContent = venue.capacity;
            document.getElementById('venue-description').textContent = venue.description;
            document.getElementById('venue-location').textContent = venue.location;
            document.getElementById('venue-image').src = venue.image_url;
            document.getElementById('venue-extra-text').textContent = venue.extra_text;
            document.getElementById('venue-email-text').value = venue.email_text;
            document.getElementById('venue-max-slots').value = venue.max_slots;
            document.getElementById('venue-slot-cost').textContent = venue.slot_cost; // Update the displayed cost
            document.getElementById('venue-page-url').value = venue.page_url;

            // Initialize the calendar on page load
            document.body.style.cursor = 'wait';
            updateCalendar(venueId);        
            document.body.style.cursor = 'default';
        })
        .catch(error => console.error('Error fetching venue details:', error));
    
        function updateCalendar(venueId, callback) {
            let selectedDayElement = null; // Store the selected day element for later

            // Fetch the available days for the venue
            fetch(`/wp-content/plugins/LEANWI-Book-A-Room/php/frontend/get-available-days.php?venue_id=${venueId}`)
                .then(response => response.json())
                .then(availableDays => {
                    const calendar = document.getElementById('calendar');
                    const currentMonthDisplay = document.getElementById('current-month');
                    const today = new Date(); // Get today's date
        
                    // Clear existing content
                    calendar.innerHTML = '';
        
                    // Get the year and month we're currently viewing
                    const year = currentMonth.getFullYear();
                    const month = currentMonth.getMonth();
                    const monthName = currentMonth.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
                    currentMonthDisplay.textContent = monthName;
        
                    // First and last days of the current month
                    const firstDay = new Date(year, month, 1);
                    const lastDay = new Date(year, month + 1, 0);
        
                    // Create a grid for the calendar (7 columns for 7 days of the week)
                    const calendarGrid = document.createElement('div');
                    calendarGrid.style.display = 'grid';
                    calendarGrid.style.gridTemplateColumns = 'repeat(7, 1fr)';
                    calendarGrid.style.gap = '10px';
        
                    // Add weekday headers (Sunday to Saturday)
                    const dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                    dayNames.forEach(dayName => {
                        const dayHeader = document.createElement('div');
                        dayHeader.textContent = dayName;
                        dayHeader.style.fontWeight = 'bold';
                        calendarGrid.appendChild(dayHeader);
                    });
        
                    // Add empty cells for days before the first day of the month
                    for (let i = 0; i < firstDay.getDay(); i++) {
                        const emptyCell = document.createElement('div');
                        calendarGrid.appendChild(emptyCell);
                    }
        
                    // Loop through all the days of the current month
                    for (let day = 1; day <= lastDay.getDate(); day++) {
                        const currentDate = new Date(year, month, day);
                        currentDate.setHours(0, 0, 0, 0); // Set time to midnight to avoid timezone issues

                        const dayName = currentDate.toLocaleDateString('en-US', { weekday: 'long' });
                        const dateFormatted = currentDate.toLocaleDateString('en-CA'); // Returns 'YYYY-MM-DD' format in local time

                        // Reset both `currentDate` and `today` to midnight for date-only comparison
                        const currentDateMidnight = new Date(currentDate.setHours(0, 0, 0, 0));
                        const todayMidnight = new Date(today.setHours(0, 0, 0, 0));

                        // Create day element
                        const dayElement = document.createElement('div');
                        dayElement.className = 'calendar-day';
                        dayElement.textContent = day;
                        dayElement.style.padding = '10px';
                        dayElement.style.border = '1px solid #ccc';
                        dayElement.style.textAlign = 'center';

                        // Find the corresponding available day data by matching the day name
                        const dayData = availableDays.find(day => day.day_of_week === dayName);

                        if (dayData && dayData.open_time !== '00:00:00' && dayData.close_time !== '00:00:00') {
                            // Available day: make it clickable
                            dayElement.style.cursor = 'pointer';
                            dayElement.style.backgroundColor = '#fff';
                            dayElement.classList.add('available');

                            dayElement.addEventListener('click', function () {
                                if (selectedDayElement) {
                                    selectedDayElement.classList.remove('highlighted'); // Remove highlight from previous selection
                                }
                                dayElement.classList.add('highlighted'); // Highlight the clicked day
                                selectedDayElement = dayElement; // Store the selected day

                                // Show "wait" cursor
                                document.body.style.cursor = 'wait';  // Change cursor to wait
                                dayElement.style.cursor = 'wait';

                                fetchDayBookings(venueId, dateFormatted)
                                    .then(bookings => {
                                        // Revert cursor back to default
                                        document.body.style.cursor = 'default';
                                        dayElement.style.cursor = 'pointer';

                                        // Directly call showContactForm with the available times
                                        if (bookings.success && bookings.data.length > 0) {
                                            showDayBookings(bookings.data, dateFormatted);
                                        } else {
                                            alert(bookings.error);
                                        }
                                    })
                                    .catch(error => {
                                        // Ensure cursor reverts even in case of an error
                                        document.body.style.cursor = 'default';
                                        dayElement.style.cursor = 'pointer';
                                        console.error('Error fetching bookings: ', error);
                                        alert('An error occurred while fetching bookings. Please try again.');
                                    });
                            });
                        } else {
                            // Non-available day: grey it out
                            dayElement.style.backgroundColor = '#f0f0f0';
                            dayElement.style.color = '#ccc';
                            dayElement.classList.add('unavailable');
                        }

                        // Append day element to the calendar grid
                        calendarGrid.appendChild(dayElement);
                    }

                    // Append the calendar grid to the main calendar div
                    calendar.appendChild(calendarGrid);
        
                    // Call the callback if provided
                    if (callback) {
                        callback();
                    }

                    // Update the visibility of the navigation arrows
                    updateNavigationButtons();
                })
                .catch(error => console.error('Error fetching available days:', error));
        }
        
        // Handle navigation buttons
        document.getElementById('prev-month').addEventListener('click', function () {
            const today = new Date();
            if (currentMonth > today) {  // Won't go to any month prior to today
                currentMonth.setMonth(currentMonth.getMonth() - 1); // Go to the previous month
                updateCalendar(venueId); // Refresh the calendar with the new month
            }
        });
        
        document.getElementById('next-month').addEventListener('click', function () {
            const today = new Date();
            const currentYear = today.getFullYear();
            const currentMonthNumber = today.getMonth(); // October = 9
        
            // Calculate the future month and handle overflow correctly
            let futureMonthNumber = currentMonthNumber + parseInt(maxMonths); // Add maxMonths
            let futureYear = currentYear;
        
            if (futureMonthNumber > 11) {
                futureYear += Math.floor(futureMonthNumber / 12); // Adjust the year if overflow occurs
                futureMonthNumber = futureMonthNumber % 12; // Adjust the month number to stay within 0-11 range
            }
            const maxMonthLimit = new Date(futureYear, futureMonthNumber, 1); // Correctly create the future date
        
            // Only allow navigation if within the maxMonths limit
            if (currentMonth < maxMonthLimit) {
                currentMonth.setMonth(currentMonth.getMonth() + 1); // Go to the next month
                updateCalendar(venueId); // Refresh the calendar with the new month
            }
        });
        
        // Function to update the visibility of navigation buttons
        function updateNavigationButtons() {
            const today = new Date();
        
            // Hide the 'Previous' button if viewing the current month or earlier
            if (currentMonth.getFullYear() === today.getFullYear() && currentMonth.getMonth() <= today.getMonth()) {
                document.getElementById('prev-month').style.visibility = 'hidden';
            } else {
                document.getElementById('prev-month').style.visibility = 'visible';
            }
        
            // Calculate the future month limit
            const futureMonthNumber = today.getMonth() + parseInt(maxMonths);
            const futureYear = today.getFullYear() + Math.floor(futureMonthNumber / 12);
            const adjustedFutureMonthNumber = futureMonthNumber % 12;
            const maxMonthLimit = new Date(futureYear, adjustedFutureMonthNumber, 1);
        
            // Hide the 'Next' button if viewing beyond the maxMonths limit
            if (currentMonth >= maxMonthLimit) {
                document.getElementById('next-month').style.visibility = 'hidden';
            } else {
                document.getElementById('next-month').style.visibility = 'visible';
            }
        }
        
        // Initialize the calendar on page load
        updateCalendar(venueId);        
        
        function fetchDayBookings(venueId, date) {
            currentDetailsDate = date;
            console.log('venueId: ', venueId, 'date: ', date);
            return fetch(`/wp-content/plugins/LEANWI-Book-A-Room/php/frontend/staff/get-day-bookings.php?venue_id=${venueId}&date=${date}`)
                .then(response => response.json())
                .catch(error => {
                    console.error('Error fetching bookings:', error);
                    return []; // Return an empty array on error
                });
        }
        
        function showDayBookings(bookings, day) {
            const dayBookingsContainer = document.querySelector('#day-bookings-container');
            const dayBookingsHeading = document.getElementById('day-bookings-heading');
            const bookingsTableBody = document.querySelector('#bookings-table tbody');

            let page_url = document.getElementById('venue-page-url').value;
            // Ensure page_url does not have a trailing slash
            if (page_url.endsWith('/')) {
                page_url = page_url.slice(0, -1); // Remove the trailing slash
            }

            // Split the 'YYYY-MM-DD' string and create a date in the local time zone
            const [year, month, date] = day.split('-');
            const selectedDate = new Date(year, month - 1, date); // Month is 0-indexed in JavaScript
        
            // Format the date
            const formattedDate = selectedDate.toLocaleDateString('en-US', {
                month: 'long',
                day: 'numeric',
                year: 'numeric'
            });
            dayBookingsHeading.textContent = `Bookings for ${formattedDate}`;
        
            // Clear any existing rows
            bookingsTableBody.innerHTML = '';
        
            // Loop through each booking and add it to the table
            bookings.forEach(booking => {
                const row = document.createElement('tr');
        
                const nameCell = document.createElement('td');
                nameCell.textContent = booking.name;
        
                const emailCell = document.createElement('td');
                emailCell.textContent = booking.email;
        
                const phoneCell = document.createElement('td');
                phoneCell.textContent = booking.phone;
        
                const startTimeCell = document.createElement('td');
                const startTime = new Date(booking.start_time);
                startTimeCell.textContent = startTime.toLocaleTimeString('en-US', {
                    hour: '2-digit',
                    minute: '2-digit'
                });
        
                const endTimeCell = document.createElement('td');
                const endTime = new Date(booking.end_time);
                endTimeCell.textContent = endTime.toLocaleTimeString('en-US', {
                    hour: '2-digit',
                    minute: '2-digit'
                });
        
                const actionCell = document.createElement('td');
                const viewLink = document.createElement('a');

                // Use the page_url from the hidden input and append the booking unique_id
                viewLink.href = `${page_url}?booking_id=${booking.unique_id}`;
                viewLink.textContent = 'View';
                viewLink.classList.add('button'); // Add WordPress button styling if needed
                actionCell.appendChild(viewLink);
        
                // Append all cells to the row
                row.appendChild(nameCell);
                row.appendChild(emailCell);
                row.appendChild(phoneCell);
                row.appendChild(startTimeCell);
                row.appendChild(endTimeCell);
                row.appendChild(actionCell);
        
                // Add the row to the table
                bookingsTableBody.appendChild(row);
            });
        
            // Show the Bookings container
            dayBookingsContainer.style.display = 'block';
        }
        
        /*****************************************************************************************
         * FUNCTIONALITY FOR DEALING WITH A BOOKING BY ITS UNIQUE ID
         * ****************************************************************************************/
        document.querySelector('#retrieve-booking').addEventListener('submit', function (event) {
            event.preventDefault();
            // Get the unique_id from the input
            const uniqueId = document.getElementById('unique_id').value;
            let page_url = document.getElementById('venue-page-url').value;

            // Ensure page_url does not have a trailing slash
            if (page_url.endsWith('/')) {
                page_url = page_url.slice(0, -1); // Remove the trailing slash
            }

            // Construct the URL with the booking ID
            const redirectUrl = `${page_url}?booking_id=${uniqueId}`;

            // Redirect the user to the constructed URL
            window.location.href = redirectUrl;
        });

        // Handle the Delete Booking button click
        document.querySelector('#delete-booking').addEventListener('click', function () {
            const uniqueId = document.getElementById('unique_id').value;
            // Add admin email data from settings
            const adminEmailAddress = bookingSettings.adminEmailAddress;
            const sendAdminEmail = bookingSettings.sendAdminEmail;

            console.log('uniqueId: ',uniqueId);
            if (confirm(`Are you sure you want to delete the booking with ID: ${uniqueId}?`)) {
                
                // Show "wait" cursor
                document.body.style.cursor = 'wait';  // Change cursor to wait
                // Disable the submit button to prevent multiple clicks
                const submitButton = document.querySelector('.find-button[type="button"]');
                submitButton.disabled = true;
                submitButton.style.cursor = 'wait';

                // Make a fetch call to delete the booking
                fetch(`/wp-content/plugins/LEANWI-Book-A-Room/php/frontend/staff/staff-delete-booking.php`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ 
                        unique_id: uniqueId, 
                        admin_email_address: adminEmailAddress,
                        send_admin_email: sendAdminEmail
                    }),
                })
                .then(response => response.json())
                .then(data => {
                    // Show "default" cursor
                    document.body.style.cursor = 'default';
                    submitButton.disabled = false;
                    submitButton.style.cursor = 'default';

                    if (data.success) {
                        alert('Booking deleted successfully.');
                        // Optionally, clear the input field or redirect the user
                        document.getElementById('unique_id').value = '';
                    } else {
                        alert('Failed to delete booking: ' + data.message);
                    }
                })
                .catch(error => {
                    document.body.style.cursor = 'default';
                    submitButton.disabled = false;
                    submitButton.style.cursor = 'default';
                    console.error('Error deleting booking:', error);
                    alert('An error occurred while deleting the booking. Please try again.');
                });
            }
        });

});
