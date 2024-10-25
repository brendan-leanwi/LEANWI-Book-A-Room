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
        document.getElementById('previous_booking_h2').textContent = "Your Booking ID has been Entered:";
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

            updateCalendar(venueId);
        })
        .catch(error => console.error('Error fetching venue details:', error));

    //Fetch categories and audiences
    fetch('/wp-content/plugins/LEANWI-Book-A-Room/php/frontend/get-categories-audiences.php')
        .then(response => response.json())
        .then(data => {
            // Populate Category dropdown
            const categorySelect = document.getElementById('category');
            data.categories.forEach(category => {
                let option = document.createElement('option');
                option.value = category.category_id;
                option.textContent = category.category_name;
                categorySelect.appendChild(option);
            });

            // Populate Audience dropdown
            const audienceSelect = document.getElementById('audience');
            data.audiences.forEach(audience => {
                let option = document.createElement('option');
                option.value = audience.audience_id;
                option.textContent = audience.audience_name;
                audienceSelect.appendChild(option);
            });
        })
        .catch(error => console.error('Error fetching categories and audiences:', error));

    
        function updateCalendar(venueId, callback, bookingData = null) {
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

                        // If the day is in the past, grey it out (compare only dates)
                        if (currentDateMidnight < todayMidnight && year === today.getFullYear() && month === today.getMonth()) {
                            // Grey out past days in the current month
                            dayElement.style.backgroundColor = '#f0f0f0';
                            dayElement.style.color = '#ccc';
                            dayElement.classList.add('unavailable');
                        } else if (dayData && dayData.open_time !== '00:00:00' && dayData.close_time !== '00:00:00') {
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

                                fetchAvailableTimes(venueId, dateFormatted)
                                    .then(availableTimes => {
                                        // Directly call showContactForm with the available times
                                        if (availableTimes.length > 0) {
                                            console.log('Date Before call: ', dateFormatted);
                                            showContactForm(availableTimes, dateFormatted);
                                        } else {
                                            alert('No available times for this date.');
                                        }
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
        document.body.style.cursor = 'wait';
        updateCalendar(venueId);        
        document.body.style.cursor = 'default';
        
        function fetchAvailableTimes(venueId, date, uniqueId = null) {
            const url = `/wp-content/plugins/LEANWI-Book-A-Room/php/frontend/get-available-times.php?venue_id=${venueId}&date=${date}`;
            currentDetailsDate = date;

            // Append unique_id if it exists
            const finalUrl = uniqueId ? `${url}&unique_id=${encodeURIComponent(uniqueId)}` : url;
            console.log("Final URL: ", finalUrl);

            return fetch(finalUrl)
                .then(response => response.json())
                .catch(error => {
                    console.error('Error fetching available times:', error);
                    return []; // Return an empty array on error
                });
        }
        
        let contactFormContainer; // Declare this in a broader scope
        function showContactForm(availableTimes, day, showingExistingRecord = false) {
            contactFormContainer = document.querySelector('#contact-form-container');
            const timeSelectDiv = document.querySelector('#time-select');
            const dayInput = document.querySelector('#day');
            const availableTimesHeading = document.getElementById('available-times-heading');
            const totalCostDisplay = document.getElementById('total-cost'); // Add reference to total cost display
            const slotCost = parseFloat(document.getElementById('venue-slot-cost').textContent);
        
            if (!contactFormContainer || !timeSelectDiv || !dayInput || !totalCostDisplay || isNaN(slotCost)) {
                console.error('One or more elements were not found in the DOM or slot cost is invalid.');
                return;
            }

            // Clear previous options
            timeSelectDiv.innerHTML = '';
        
            // Split the 'YYYY-MM-DD' string and create a date in the local time zone
            const [year, month, date] = day.split('-');
            const selectedDate = new Date(year, month - 1, date); // Month is 0-indexed in JavaScript
            
            const formattedDate = selectedDate.toLocaleDateString('en-US', {
                month: 'long',
                day: 'numeric',
                year: 'numeric'
            });
            availableTimesHeading.textContent = `Available Times for ${formattedDate}`;

            // Array to store selected times for updating the dataset
            let selectedTimes = [];
        
            // Populate with buttons for available times
            availableTimes.forEach((time, index) => {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'time-button';
                button.textContent = time.start;  // Display start time
                
                // Check if the time slot is booked
                if (time.booked && !time.is_booked_for_unique_id) {
                    button.disabled = true;  // Disable the button
                    button.classList.add('booked');  // Add a class for styling booked times
                }

                // Automatically select booked times for the unique_id if showingExistingRecord is true
                if (showingExistingRecord && time.is_booked_for_unique_id) {
                    button.classList.add('selected');
                    selectedTimes.push(time.start);  // Add to selected times array
                }


                // Add functionality to handle time selection for available (non-booked) times
                if (!time.booked || time.is_booked_for_unique_id) {
                    button.addEventListener('click', () => {
                        const selectedTimes = Array.from(timeSelectDiv.querySelectorAll('.selected')).map(btn => btn.textContent);
                        const currentSelectionIndex = getTimeIndex(button.textContent, availableTimes);

                        // Check if the clicked time is already selected
                        if (button.classList.contains('selected')) {
                            // Deselect the clicked time and all times after it
                            const buttons = Array.from(timeSelectDiv.querySelectorAll('button'));

                            // Loop through all buttons starting from the clicked time to the end
                            for (let i = currentSelectionIndex; i < buttons.length; i++) {
                                buttons[i].classList.remove('selected');
                            }

                        } else {
                            // If the time is not selected, proceed with the normal selection logic

                            if (selectedTimes.length > 0) {
                                const lastSelectedTime = selectedTimes[selectedTimes.length - 1];
                                const lastSelectedIndex = getTimeIndex(lastSelectedTime, availableTimes);

                                // Check if the selected time is continuous
                                const isAdjacent = Math.abs(currentSelectionIndex - lastSelectedIndex) === 1;

                                if (!isAdjacent) {
                                    // Check for intermediate booked slots
                                    const minIndex = Math.min(currentSelectionIndex, lastSelectedIndex);
                                    const maxIndex = Math.max(currentSelectionIndex, lastSelectedIndex);

                                    let canSelect = true;
                                    for (let i = minIndex + 1; i < maxIndex; i++) {
                                        if (availableTimes[i].booked) {
                                            canSelect = false;
                                            break;
                                        }
                                    }

                                    if (canSelect) {
                                        // Automatically select all intermediate slots
                                        for (let i = minIndex; i <= maxIndex; i++) {
                                            const intermediateButton = timeSelectDiv.querySelectorAll('button')[i];
                                            intermediateButton.classList.add('selected');
                                        }
                                    } else {
                                        alert('A booking must use continuous times without a booked slot in between.');
                                        return;
                                    }
                                } else {
                                    // Toggle the clicked button's selection state
                                    button.classList.add('selected');
                                }
                            } else {
                                // If no times are selected, just select this one
                                button.classList.add('selected');
                            }
                        }

                        // Update selected times in the container's data attribute
                        const newSelectedTimes = Array.from(timeSelectDiv.querySelectorAll('.selected')).map(btn => btn.textContent);
                        contactFormContainer.dataset.selectedTimes = newSelectedTimes.join(',');

                        // Calculate the total cost
                        const selectedCount = newSelectedTimes.length; // Get the number of selected times
                        const totalCost = (selectedCount * slotCost).toFixed(2); // Calculate total cost
                        totalCostDisplay.textContent = totalCost; // Update the total cost display
                        
                    });
                }
                
                timeSelectDiv.appendChild(button);
            });
        
            dayInput.value = day;
            
            //If we're displaying an existing record we need to show the total cost before we leave
            if(showingExistingRecord){
                // Calculate the total cost
                const selectedTimes = Array.from(timeSelectDiv.querySelectorAll('.selected')).map(btn => btn.textContent);
                contactFormContainer.dataset.selectedTimes = selectedTimes.join(',');
                const selectedCount = selectedTimes.length; // Get the number of selected times
                const totalCost = (selectedCount * slotCost).toFixed(2); // Calculate total cost
                totalCostDisplay.textContent = totalCost; // Update the total cost display
            }
            // Show the contact form
            contactFormContainer.style.display = 'block';
        }

        // Function to get time index based on the button's time (start time)
        function getTimeIndex(timeString, availableTimes) {
            return availableTimes.findIndex(time => time.start === timeString);
        }        

        document.querySelector('#booking-form').addEventListener('submit', function (event) {
            event.preventDefault();
            let formData = new FormData(this);
        
            // Add the venue_id from hidden div
            formData.append('venue_id', document.getElementById('venue_id').value);
            formData.append('email_text', document.getElementById('venue-email-text').value);
            formData.append('total_cost', document.getElementById('total-cost').textContent); 
            formData.append('venue_name', document.getElementById('venue-name').textContent);

            if(existingRecord){
                formData.append('unique_id', document.getElementById('unique_id').value);
                console.log('unique_id passed: ', document.getElementById('unique_id').value);
            }

            // Add the current page URL to formData
            formData.append('page_url', window.location.href);

            // Handle multiple selected times from the data attribute
            const selectedTimes = contactFormContainer.dataset.selectedTimes ? contactFormContainer.dataset.selectedTimes.split(',') : [];
        
            if (selectedTimes.length === 0) {
                alert('Please select at least one time slot.');
                return;
            }
        
            var maxSlots = parseInt(document.getElementById('venue-max-slots').value, 10);
            // Check if selected times exceed the maximum booking slots
            if (selectedTimes.length > maxSlots) {
                alert(`We only allow you to book ${maxSlots} timeslots in one booking.`);
                return;
            }

            const currentDateFormatted = returnCurrentDate();
            formData.append('current_time', currentDateFormatted);

            // Get the start time and end time from the selected times
            const startTime = selectedTimes[0];
            const endTime = selectedTimes[selectedTimes.length - 1];

            // Construct 'YYYY-MM-DD HH:MM:SS' format by appending ':00'
            const formattedStartTime = `${currentDetailsDate} ${startTime}:00`;
            const formattedEndTime = `${currentDetailsDate} ${endTime}:00`;

            // Append the start time and end time to formData
            formData.append('start_time', formattedStartTime);
            formData.append('end_time', formattedEndTime);
            console.log('start_time:', formattedStartTime, 'end_time:', formattedEndTime, 'current_time:',currentDateFormatted)
            
            // Append plugin settings to formData
            formData.append('minutes_interval', bookingSettings.minutesInterval);
            formData.append('admin_email_address', bookingSettings.adminEmailAddress);
            formData.append('send_admin_email', bookingSettings.sendAdminEmail);
            
            fetch('/wp-content/plugins/LEANWI-Book-A-Room/php/frontend/submit-booking.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())  // Changed to .text() for debugging
            .then(data => {
                console.log('Response Data:', data);  // Log the raw response for inspection
                try {
                    const jsonData = JSON.parse(data);  // Try to parse the JSON if possible
                    if (jsonData.success) {
                        alert(jsonData.message);
                        document.querySelector('#contact-form-container').style.display = 'none';
                    } else {
                        alert('Error: ' + jsonData.message);
                    }
                } catch (e) {
                    console.error('Error parsing JSON:', e);
                    console.error('Raw response:', data);
                }
            })
            .catch(error => console.error('Error submitting booking:', error));
        });
        
        /*****************************************************************************************
         * FUNCTIONALITY FOR DISPLAYING AN EXISTING BOOKING
         *****************************************************************************************/
        let existingRecord = false;
        document.querySelector('#retrieve-booking').addEventListener('submit', function (event) {
            event.preventDefault();
            //Get the unique_id from the input and pass it to the fetch-booking file
            let formData = new FormData(this);
            existingRecord = true;
            
            // Add the venue_id from hidden div
            formData.append('venue_id', document.getElementById('venue_id').value);

            console.log('unique_id: ', formData.get('unique_id'), ' venue_id: ', formData.get('venue_id'))
            
            // Show "wait" cursor
            document.body.style.cursor = 'wait';  // Change cursor to wait
            // Disable the submit button to prevent multiple clicks
            const submitButton = document.querySelector('.find-button[type="submit"]');
            submitButton.disabled = true;
            submitButton.style.cursor = 'wait';

            fetch('/wp-content/plugins/LEANWI-Book-A-Room/php/frontend/fetch-booking.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                console.log('Fetch booking Response Data:', data);  // Log the raw response for inspection

                // Show "default" cursor
                document.body.style.cursor = 'default'; 
                submitButton.disabled = false;
                submitButton.style.cursor = 'default';

                if (data.success) {
                    const booking = data.data[0]; // Assuming single booking for now

                    const startDate = new Date(booking.start_time); 
                    const dateFormatted = formatDate_YYYY_MM_DD(startDate);
                    console.log('Formatted date:', dateFormatted);

                    document.body.style.cursor = 'wait';
                    // Call the function with the venueId, date, and uniqueId
                    fetchAvailableTimes(booking.venue_id , dateFormatted, booking.unique_id)
                    .then(availableTimes => {
                        // Handle the available times as needed
                        console.log('Available times:', availableTimes);
                        // Directly call showContactForm with the available times
                        if (availableTimes.length > 0) {
                            showContactForm(availableTimes, dateFormatted, existingRecord);
                        } else {
                            alert('No available times for this date.');
                        }
                    });
                    document.body.style.cursor = 'default';

                    // Populate form fields with the booking data
                    document.getElementById('name').value = booking.name;
                    document.getElementById('email').value = booking.email;
                    document.getElementById('phone').value = booking.phone;
                    document.getElementById('participants').value = booking.number_of_participants;
                    document.getElementById('notes').value = booking.booking_notes;
                    document.getElementById('category').value = booking.category_id;
                    document.getElementById('audience').value = booking.audience_id;

                    // Show the booking form
                    document.getElementById('contact-form-container').style.display = 'block';

                    // Highlight the day in the calendar based on the start_time
                    highlightSelectedDay(booking.start_time);

                } else {
                    alert(data.error);
                }
            })
            .catch(error => {
                // Show "default" cursor
                document.body.style.cursor = 'default';
                submitButton.disabled = false;
                submitButton.style.cursor = 'default';
                console.error('Error fetching booking:', error);
                alert('An error occurred fetching the booking. Please try again.');
            });
        });

        function highlightSelectedDay(startTime) {
            const bookingDate = new Date(startTime);
            const bookingDay = bookingDate.getDate();
            const bookingMonth = bookingDate.getMonth();
            const bookingYear = bookingDate.getFullYear();
        
            // Check if the booking month and year match the currently displayed month and year
            if (currentMonth.getFullYear() === bookingYear && currentMonth.getMonth() === bookingMonth) {
                // Current month matches booking month, highlight the day directly
                highlightDayInCalendar(bookingDay);
            } else {
                // Navigate to the booking month
                currentMonth.setFullYear(bookingYear);
                currentMonth.setMonth(bookingMonth);
                updateCalendar(venueId); // Refresh the calendar with the new month
        
                // Update the calendar and highlight after it's rendered
                updateCalendar(venueId, () => {
                    highlightDayInCalendar(bookingDay); // Highlight the correct day
                });
            }
        }
        
        function highlightDayInCalendar(bookingDay) {
            // Find the day element in the calendar
            const dayElements = document.querySelectorAll('.calendar-day');
            
            dayElements.forEach(dayElement => {
                const day = parseInt(dayElement.textContent, 10);
        
                // Check if the day matches the booking date
                if (day === bookingDay) {
                    dayElement.classList.add('highlighted'); // Highlight the matched day
                    dayElement.scrollIntoView({ behavior: 'smooth', block: 'center' }); // Scroll into view
                }
            });
        }
   
        // Function to format date as 'YYYY-MM-DD'
        function formatDate_YYYY_MM_DD(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0'); // Months are 0-based
            const day = String(date.getDate()).padStart(2, '0');

            return `${year}-${month}-${day}`;
        }

        function returnCurrentDate(){ // Format: 'YYYY-MM-DD HH:MM:SS'
            const currentDate = new Date();
            const year = currentDate.getFullYear();
            const month = String(currentDate.getMonth() + 1).padStart(2, '0'); // Months are zero-indexed, so add 1
            const day = String(currentDate.getDate()).padStart(2, '0');
            const hours = String(currentDate.getHours()).padStart(2, '0');
            const minutes = String(currentDate.getMinutes()).padStart(2, '0');
            const seconds = String(currentDate.getSeconds()).padStart(2, '0');

            // Format: 'YYYY-MM-DD HH:MM:SS'
            return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
        }

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
                fetch(`/wp-content/plugins/LEANWI-Book-A-Room/php/frontend/delete-booking.php`, {
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
                        // Clear the input field or redirect the user
                        document.getElementById('unique_id').value = '';
                        // Don't show the contact form
                        contactFormContainer.style.display = 'none';
                        document.getElementById('total-cost').textContent = '0.00';

                    } else {
                        alert('Failed to delete booking: ' + data.message);
                    }
                })
                .catch(error => {
                    // Show "default" cursor
                    document.body.style.cursor = 'default';
                    submitButton.disabled = false;
                    submitButton.style.cursor = 'default';
                    console.error('Error deleting booking:', error);
                    alert('An error occurred while deleting the booking. Please try again.');
                });
            }
        });

});
