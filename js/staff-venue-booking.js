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

    // Get booking_id from the URL and validate it
    const bookingId = getQueryParam('booking_id');
    if (bookingId && /^[a-zA-Z0-9]{7}$/.test(bookingId)) {  // Check for 7 alphanumeric characters
        document.getElementById('unique_id').value = bookingId;
    } else if (bookingId) {
        console.warn("Invalid booking ID format: must be exactly 7 alphanumeric characters.");
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
            document.getElementById('venue-name').textContent = escapeHtml(venue.name);
            document.getElementById('venue-capacity').textContent = escapeHtml(venue.capacity);
            document.getElementById('venue-description').textContent = escapeHtml(venue.description);
            document.getElementById('venue-location').textContent = escapeHtml(venue.location);
            document.getElementById('venue-image').src = venue.image_url; // Ensure image URL is safe
            document.getElementById('venue-extra-text').textContent = escapeHtml(venue.extra_text);
            document.getElementById('venue-email-text').value = escapeHtml(venue.email_text);
            document.getElementById('venue-max-slots').value = venue.max_slots;
            document.getElementById('venue-slot-cost').textContent = escapeHtml(venue.slot_cost);
            document.getElementById('venue-page-url').value = venue.page_url;

            // Initialize the calendar on page load
            document.body.style.cursor = 'wait';
            updateCalendar(venueId);        
            document.body.style.cursor = 'default';
        })
        .catch(error => console.error('Error fetching venue details:', error));
    
        let selectedDayElement = null;
        function updateCalendar(venueId, callback) {
        
            // Fetch available days for the venue
            fetch(`/wp-content/plugins/LEANWI-Book-A-Room/php/frontend/get-available-days.php?venue_id=${encodeURIComponent(venueId)}`)
                .then(response => response.json())
                .then(availableDays => {
                    const calendar = document.getElementById('calendar');
                    const currentMonthDisplay = document.getElementById('current-month');
                    const today = new Date();
                    
                    calendar.innerHTML = ''; // Clear previous content
                    const year = currentMonth.getFullYear();
                    const month = currentMonth.getMonth();
                    currentMonthDisplay.textContent = currentMonth.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
        
                    const firstDay = new Date(year, month, 1);
                    const lastDay = new Date(year, month + 1, 0);
        
                    const calendarGrid = createCalendarGrid();
                    calendar.appendChild(calendarGrid);
        
                    addWeekdayHeaders(calendarGrid);
                    addEmptyStartCells(calendarGrid, firstDay);
        
                    for (let day = 1; day <= lastDay.getDate(); day++) {
                        const currentDate = new Date(year, month, day);
                        const dateFormatted = currentDate.toISOString().split('T')[0]; // "YYYY-MM-DD"
                        const dayData = availableDays.find(d => d.day_of_week === currentDate.toLocaleDateString('en-US', { weekday: 'long' }));
        
                        const dayElement = createDayElement(day, dayData, currentDate, today, dateFormatted);
                        if (dayElement) calendarGrid.appendChild(dayElement);
                    }
        
                    if (callback) callback();
                    updateNavigationButtons();
                })
                .catch(error => console.error('Error fetching available days:', error));
        }
        
        function createCalendarGrid() {
            const grid = document.createElement('div');
            grid.classList.add('calendar-grid'); // Apply the CSS class
            return grid;
        }        
        
        function addWeekdayHeaders(calendarGrid) {
            const dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            dayNames.forEach(dayName => {
                const dayHeader = document.createElement('div');
                dayHeader.textContent = dayName;
                dayHeader.classList.add('day-header'); // Use CSS class for styling
                calendarGrid.appendChild(dayHeader);
            });
        }
        
        function addEmptyStartCells(calendarGrid, firstDay) {
            for (let i = 0; i < firstDay.getDay(); i++) {
                calendarGrid.appendChild(document.createElement('div'));
            }
        }
        
        function createDayElement(day, dayData, currentDate, today, dateFormatted) {
            const dayElement = document.createElement('div');
            dayElement.className = 'calendar-day';
            dayElement.textContent = day;
        
            const isToday = currentDate.toISOString().split('T')[0] === today.toISOString().split('T')[0];
            const isAvailable = dayData && dayData.open_time !== '00:00:00' && dayData.close_time !== '00:00:00';
        
            if (isAvailable) {
                dayElement.classList.add('available');
                dayElement.addEventListener('click', () => handleDayClick(dayElement, venueId, dateFormatted));
            } else {
                dayElement.classList.add('unavailable');
            }
        
            return dayElement;
        }
        
        function handleDayClick(dayElement, venueId, dateFormatted) {
            if (selectedDayElement) selectedDayElement.classList.remove('highlighted');
            dayElement.classList.add('highlighted');
            selectedDayElement = dayElement;
        
            document.body.style.cursor = 'wait';
            dayElement.style.cursor = 'wait';
        
            fetchDayBookings(venueId, dateFormatted)
                .then(bookings => {
                    document.body.style.cursor = 'default';
                    dayElement.style.cursor = 'pointer';
        
                    if (bookings.success && bookings.data.length > 0) {
                        showDayBookings(bookings.data, dateFormatted);
                    } else {
                        alert(bookings.error);
                    }
                })
                .catch(error => {
                    document.body.style.cursor = 'default';
                    dayElement.style.cursor = 'pointer';
                    console.error('Error fetching bookings:', error);
                    alert('An error occurred while fetching bookings. Please try again.');
                });
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
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.json();
                })
                .catch(error => {
                    console.error('Error fetching bookings:', error);
                    return []; // Return an empty array on error
                });
        }
        
        function showDayBookings(bookings, day) {
            const dayBookingsContainer = document.querySelector('#day-bookings-container');
            const dayBookingsHeading = document.getElementById('day-bookings-heading');
            const bookingsTableBody = document.querySelector('#bookings-table tbody');
        
            // Ensure the page_url does not have a trailing slash
            let page_url = document.getElementById('venue-page-url').value.trim();
            if (page_url.endsWith('/')) {
                page_url = page_url.slice(0, -1); // Remove the trailing slash
            }
        
            // Create a local date object from the provided day
            const [year, month, date] = day.split('-').map(Number);
            const selectedDate = new Date(year, month - 1, date);
            const formattedDate = formatDate(selectedDate);
        
            // Update the heading
            dayBookingsHeading.textContent = `Bookings for ${formattedDate}`;
        
            // Clear existing rows
            bookingsTableBody.innerHTML = '';
        
            // Loop through each booking and add it to the table
            bookings.forEach(booking => {
                const row = document.createElement('tr');
                const { name, email, phone, start_time, end_time, unique_id } = booking;
        
                row.appendChild(createTableCell(name));
                row.appendChild(createTableCell(email));
                row.appendChild(createTableCell(phone));
                row.appendChild(createTableCell(formatTime(start_time)));
                row.appendChild(createTableCell(formatTime(end_time)));
                row.appendChild(createActionCell(page_url, unique_id));
        
                // Add the row to the table
                bookingsTableBody.appendChild(row);
            });
        
            // Show the Bookings container
            dayBookingsContainer.style.display = 'block';
        }
        
        // Helper function to create a table cell
        function createTableCell(content) {
            const cell = document.createElement('td');
            cell.textContent = content || ''; // Default to empty string if content is missing
            return cell;
        }
        
        // Helper function to create the action cell with a view link
        function createActionCell(page_url, unique_id) {
            const actionCell = document.createElement('td');
            const viewLink = document.createElement('a');
            viewLink.href = `${page_url}?booking_id=${unique_id}&passer=staff`;
            viewLink.textContent = 'View';
            viewLink.classList.add('button'); // Add WordPress button styling if needed
            actionCell.appendChild(viewLink);
            return actionCell;
        }
        
        // Helper function to format time
        function formatTime(time) {
            const date = new Date(time);
            return date.toLocaleTimeString('en-US', {
                hour: '2-digit',
                minute: '2-digit'
            });
        }
        
        // Helper function to format date
        function formatDate(date) {
            return date.toLocaleDateString('en-US', {
                month: 'long',
                day: 'numeric',
                year: 'numeric'
            });
        }
        

        function escapeHtml(html) {
            const text = document.createElement("textarea");
            text.textContent = html;
            return text.innerHTML;
        }
        
        
        /*****************************************************************************************
         * FUNCTIONALITY FOR DEALING WITH A BOOKING BY ITS UNIQUE ID
         * ****************************************************************************************/
        document.querySelector('#retrieve-booking').addEventListener('submit', function (event) {
            event.preventDefault();
            // Get the unique_id from the input
            const uniqueId = document.getElementById('unique_id').value.trim();
            const page_url = document.getElementById('venue-page-url').value.trim();

            // Validate the uniqueId
            if (!uniqueId) {
                alert('Please enter a valid booking ID.');
                return;
            }

            // Construct the URL with the booking ID
            const redirectUrl = constructRedirectUrl(page_url, uniqueId);

            // Redirect the user to the constructed URL
            window.location.href = redirectUrl;
        });

        // Helper function to construct the redirect URL
        function constructRedirectUrl(pageUrl, bookingId) {
            // Ensure pageUrl does not have a trailing slash
            const trimmedUrl = pageUrl.endsWith('/') ? pageUrl.slice(0, -1) : pageUrl;
            return `${trimmedUrl}?booking_id=${bookingId}&passer=staff`;
        }

        // Handle the Delete Booking button click
        document.querySelector('#delete-booking').addEventListener('click', function () {
            const uniqueId = document.getElementById('unique_id').value;
            // Add admin email data from settings
            const adminEmailAddress = bookingSettings.adminEmailAddress;
            const sendAdminEmail = bookingSettings.sendAdminEmail;
            const deleteNonce = document.querySelector('#delete_booking_nonce').value; // Retrieve delete nonce

            if (!uniqueId) {
                alert('Please enter a valid booking ID.');
                return;
            }

            console.log('uniqueId: ',uniqueId);
            if (confirm(`Are you sure you want to delete the booking with ID: ${sanitizeInput(uniqueId)}?`)) {
                toggleCursorState(true);
        
                // Disable the submit button to prevent multiple clicks
                const submitButton = document.querySelector('.find-button[type="button"]');
                submitButton.disabled = true;
        
                // Make a fetch call to delete the booking
                fetch(`/wp-content/plugins/LEANWI-Book-A-Room/php/frontend/staff/staff-delete-booking.php`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ 
                        unique_id: uniqueId, 
                        admin_email_address: adminEmailAddress,
                        send_admin_email: sendAdminEmail,
                        delete_booking_nonce: deleteNonce // Include the nonce
                    }),
                })
                .then(response => response.json())
                .then(data => {
                    toggleCursorState(false);
                    submitButton.disabled = false;
        
                    if (data.success) {
                        alert('Booking deleted successfully.');
                        document.getElementById('unique_id').value = ''; // Clear the input field
                    } else {
                        alert(`Failed to delete booking: ${sanitizeInput(data.message)}`);
                    }
                })
                .catch(error => {
                    toggleCursorState(false);
                    submitButton.disabled = false;
                    console.error('Error deleting booking:', error);
                    alert('An error occurred while deleting the booking. Please try again.');
                });
            }
        });

        // Function to toggle the cursor state
        function toggleCursorState(isWaiting) {
            document.body.style.cursor = isWaiting ? 'wait' : 'default';
        }

        // Function to sanitize user input
        function sanitizeInput(input) {
            const tempElement = document.createElement('div');
            tempElement.textContent = input; // Use textContent to escape HTML
            return tempElement.innerHTML;
        }

});
