document.addEventListener("DOMContentLoaded", function () {
    const venueId = document.getElementById('venue_id').value;

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

    document.body.style.cursor = 'wait'; // Set cursor before fetch starts
    fetch(`/wp-content/plugins/LEANWI-Book-A-Room/php/frontend/get-venue-details.php?venue_id=${venueId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(venue => {
            document.getElementById('venue-name').textContent = escapeHtml(venue.name);
            document.getElementById('venue-capacity').textContent = escapeHtml(venue.capacity);
            document.getElementById('venue-description').textContent = escapeHtml(venue.description);
            document.getElementById('venue-location').textContent = escapeHtml(venue.location);
            document.getElementById('venue-image').src = venue.image_url;
            document.getElementById('venue-extra-text').textContent = escapeHtml(venue.extra_text);
            document.getElementById('venue-email-text').value = escapeHtml(venue.email_text);
            document.getElementById('venue-max-slots').value = venue.max_slots;
            document.getElementById('venue-slot-cost').textContent = escapeHtml(venue.slot_cost); // Update the displayed cost

           updateCalendar(venueId);
        })
        .catch(error => console.error('Error fetching venue details:', error))
        .finally(() => {
            document.body.style.cursor = 'default'; // Reset cursor after fetch completes
        });

    //Fetch categories and audiences
    document.body.style.cursor = 'wait'; // Set cursor before fetch starts
    fetch('/wp-content/plugins/LEANWI-Book-A-Room/php/frontend/get-categories-audiences.php')
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
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
        .catch(error => console.error('Error fetching categories and audiences:', error))
        .finally(() => {
            document.body.style.cursor = 'default'; // Reset cursor after fetch completes
        });

        let selectedDayElement = null; // Store the selected day element for later
        function updateCalendar(venueId, callback, bookingData = null) {
            document.body.style.cursor = 'wait';
            // Fetch the available days for the venue
            fetch(`/wp-content/plugins/LEANWI-Book-A-Room/php/frontend/get-available-days.php?venue_id=${encodeURIComponent(venueId)}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.json();
                })
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
        
                    // Create the calendar grid
                    const calendarGrid = createCalendarGrid();
                    addWeekdayHeaders(calendarGrid);
                    addEmptyStartCells(calendarGrid, firstDay);
        
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
                                            document.body.style.cursor = 'wait';
                                            showContactForm(availableTimes, dateFormatted);
                                            document.body.style.cursor = 'default';
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
                .catch(error => console.error('Error fetching available days:', error))
                .finally(() => {
                    document.body.style.cursor = 'default'; // Reset cursor after fetch completes
                });
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
                
                updateCalendar(venueId);
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
                updateCalendar(venueId);
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
        
        updateCalendar(venueId);
        
        function fetchAvailableTimes(venueId, date, uniqueId = null) {
            currentDetailsDate = date;

            const params = new URLSearchParams({ venue_id: venueId, date: date });
            if (uniqueId) params.append('unique_id', uniqueId);
            const finalUrl = `/wp-content/plugins/LEANWI-Book-A-Room/php/frontend/get-available-times.php?${params}`;
            console.log("Final URL: ", finalUrl);

            document.body.style.cursor = 'wait'; // Set cursor before fetch starts
            return fetch(finalUrl)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.json();
                })
                .catch(error => {
                    console.error('Error fetching available times:', error);
                    return []; // Return an empty array on error
                })
                .finally(() => {
                    document.body.style.cursor = 'default'; // Reset cursor after fetch completes
                });
        }
        
        let contactFormContainer;

        function showContactForm(availableTimes, day, showingExistingRecord = false) {
            contactFormContainer = document.querySelector('#contact-form-container');
            const timeSelectDiv = document.querySelector('#time-select');
            const dayInput = document.querySelector('#day');
            const availableTimesHeading = document.getElementById('available-times-heading');
            const totalCostDisplay = document.getElementById('total-cost');
            const slotCost = parseFloat(document.getElementById('venue-slot-cost').textContent);
            
            if (!contactFormContainer || !timeSelectDiv || !dayInput || !totalCostDisplay || isNaN(slotCost)) {
                console.error('One or more elements were not found in the DOM or slot cost is invalid.');
                return;
            }

            timeSelectDiv.innerHTML = ''; // Clear previous options
            const formattedDate = new Date(day).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
            availableTimesHeading.textContent = `Available Times for ${formattedDate}`;

            // Populate time buttons
            availableTimes.forEach((time, index) => {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'time-button';
                button.textContent = time.start;

                if (time.booked && !time.is_booked_for_unique_id) {
                    button.disabled = true;
                    button.classList.add('booked');
                }

                if (showingExistingRecord && time.is_booked_for_unique_id) {
                    button.classList.add('selected');
                }

                button.addEventListener('click', () => handleTimeSelection(button, index));
                timeSelectDiv.appendChild(button);
            });

            dayInput.value = day;
            
            if (showingExistingRecord) {
                updateTotalCost();
            }

            contactFormContainer.style.display = 'block';

            function handleTimeSelection(button, index) {
                const isSelected = button.classList.contains('selected');

                if (isSelected) {
                    deselectFollowingButtons(index);
                } else {
                    const selectedButtons = Array.from(timeSelectDiv.querySelectorAll('.selected'));
                    const lastSelectedIndex = selectedButtons.length ? getTimeIndex(selectedButtons[selectedButtons.length - 1].textContent, availableTimes) : -1;

                    if (lastSelectedIndex >= 0 && !isContinuousSelection(index, lastSelectedIndex) && !hasIntermediateBookedSlots(Math.min(index, lastSelectedIndex), Math.max(index, lastSelectedIndex))) {
                        alert('A booking must use continuous times without a booked or empty slot in between.');
                        return;
                    }

                    button.classList.add('selected');
                }

                updateTotalCost();
            }

            function deselectFollowingButtons(index) {
                const buttons = Array.from(timeSelectDiv.querySelectorAll('button'));
                for (let i = index; i < buttons.length; i++) {
                    buttons[i].classList.remove('selected');
                }
            }

            function updateTotalCost() {
                const selectedTimes = Array.from(timeSelectDiv.querySelectorAll('.selected')).map(btn => btn.textContent);
                totalCostDisplay.textContent = (selectedTimes.length * slotCost).toFixed(2);
                contactFormContainer.dataset.selectedTimes = selectedTimes.join(',');
            }

            function isContinuousSelection(currentIndex, lastIndex) {
                return Math.abs(currentIndex - lastIndex) === 1;
            }
            
            function hasIntermediateBookedSlots(start, end) {
                for (let i = start + 1; i < end; i++) {
                    if (availableTimes[i].booked) return true;
                }
                return false;
            }
            
        }

        // Function to get time index based on the button's time (start time)
        function getTimeIndex(timeString, availableTimes) {
            return availableTimes.findIndex(time => time.start === timeString);
        }        

        document.querySelector('#booking-form').addEventListener('submit', function (event) {
            event.preventDefault();
        
            const formData = new FormData(this);
            const contactFormContainer = document.getElementById('contact-form-container');
            const selectedTimes = contactFormContainer.dataset.selectedTimes ? contactFormContainer.dataset.selectedTimes.split(',') : [];
            
            // Add the nonce
            formData.append('submit_booking_nonce', document.querySelector('#submit_booking_nonce').value);

            // Append required data to formData
            formData.append('venue_id', document.getElementById('venue_id').value);
            formData.append('email_text', document.getElementById('venue-email-text').value);
            formData.append('total_cost', document.getElementById('total-cost').textContent);
            formData.append('venue_name', document.getElementById('venue-name').textContent);
            formData.append('page_url', window.location.href);
        
            if (existingRecord) {
                const uniqueId = document.getElementById('unique_id').value;
                formData.append('unique_id', uniqueId);
                console.log('Unique ID:', uniqueId);
            }
        
            // Ensure at least one time slot is selected
            if (selectedTimes.length === 0) {
                alert('Please select at least one time slot.');
                return;
            }
        
            // Check against maximum allowed booking slots
            const maxSlots = parseInt(document.getElementById('venue-max-slots').value, 10);
            if (selectedTimes.length > maxSlots) {
                alert(`You can book a maximum of ${maxSlots} timeslots per booking.`);
                return;
            }
        
            // Format and append start and end times
            const currentDateFormatted = returnCurrentDate();
            const startTime = `${currentDetailsDate} ${selectedTimes[0]}:00`;
            const endTime = `${currentDetailsDate} ${selectedTimes[selectedTimes.length - 1]}:00`;
            formData.append('start_time', startTime);
            formData.append('end_time', endTime);
            formData.append('current_time', currentDateFormatted);
        
            // Add plugin settings
            formData.append('minutes_interval', bookingSettings.minutesInterval);
            formData.append('admin_email_address', bookingSettings.adminEmailAddress);
            formData.append('send_admin_email', bookingSettings.sendAdminEmail);
        
            document.body.style.cursor = 'wait'; // Set cursor before fetch starts
            fetch('/wp-content/plugins/LEANWI-Book-A-Room/php/frontend/submit-booking.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Network error: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    contactFormContainer.style.display = 'none';
                } else {
                    console.error('Submit Error:', data.message);
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error submitting booking:', error.message);
                alert('An error occurred while submitting your booking: ' + error.message);
            })
            .finally(() => {
                document.body.style.cursor = 'default'; // Reset cursor after fetch completes
            });
        });        
        
        /*****************************************************************************************
         * FUNCTIONALITY FOR DISPLAYING AN EXISTING BOOKING
         *****************************************************************************************/
        let existingRecord = false;
        const retrieveBookingForm = document.querySelector('#retrieve-booking');
        const findButton = retrieveBookingForm.querySelector('.find-button[type="submit"]');

        retrieveBookingForm.addEventListener('submit', function (event) {
            event.preventDefault();
            existingRecord = true;
            contactFormContainer = document.getElementById('contact-form-container');

            // Prepare form data
            const formData = new FormData(this);
            formData.append('venue_id', document.getElementById('venue_id').value);
            // Add the nonce
            formData.append('fetch_booking_nonce', document.querySelector('#fetch_booking_nonce').value);

            // Change cursor and disable submit button to prevent multiple clicks
            document.body.style.cursor = 'wait';
            findButton.disabled = true;
            findButton.style.cursor = 'wait';
            fetch('/wp-content/plugins/LEANWI-Book-A-Room/php/frontend/fetch-booking.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    const booking = data.data[0]; // Assuming single booking for now

                    // Populate form fields and display booking information
                    populateBookingFormFields(booking);
                    
                    // Fetch available times and show the contact form if times are available
                    handleAvailableTimes(booking);
                } else {
                    alert(data.error || 'Booking retrieval unsuccessful.');
                }
            })
            .catch(error => {
                console.error('Error fetching booking:', error);
                alert('An error occurred while fetching the booking. Please try again.');
            })
            .finally(() => {
                document.body.style.cursor = 'default'; // Reset cursor after fetch completes
                findButton.disabled = false;
                findButton.style.cursor = 'default';
            });
        });

        // Helper functions
        function populateBookingFormFields(booking) {
            // Populate form fields with booking data
            document.getElementById('name').value = booking.name;
            document.getElementById('email').value = booking.email;
            document.getElementById('phone').value = booking.phone;
            document.getElementById('participants').value = booking.number_of_participants;
            document.getElementById('notes').value = booking.booking_notes;
            document.getElementById('category').value = booking.category_id;
            document.getElementById('audience').value = booking.audience_id;

            // Display the booking form container
            contactFormContainer.style.display = 'block';

            // Highlight selected day on the calendar
            highlightSelectedDay(booking.start_time);
        }

        function handleAvailableTimes(booking) {
            const dateFormatted = formatDate_YYYY_MM_DD(new Date(booking.start_time));
            
            document.body.style.cursor = 'wait'; // Set cursor before fetch starts
            // Fetch available times and call showContactForm if there are times available
            fetchAvailableTimes(booking.venue_id, dateFormatted, booking.unique_id)
                .then(availableTimes => {
                    if (availableTimes.length > 0) {
                        document.body.style.cursor = 'wait';
                        showContactForm(availableTimes, dateFormatted, existingRecord);
                        document.body.style.cursor = 'default';
                    } else {
                        alert('No available times for this date.');
                    }
                })
                .catch(error => {
                    console.error('Error fetching available times:', error);
                    alert('An error occurred while fetching available times.');
                })
                .finally(() => {
                    document.body.style.cursor = 'default'; // Reset cursor after fetch completes
                });
        }


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

                updateCalendar(venueId, () => {
                    highlightDayInCalendar(bookingDay); // Highlight the correct day AFTER the calendar month has been loaded
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
   
        function escapeHtml(html) {
            const text = document.createElement("textarea");
            text.textContent = html;
            return text.innerHTML;
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

        const deleteBookingButton = document.querySelector('#delete-booking');
        const uniqueIdInput = document.getElementById('unique_id');

        deleteBookingButton.addEventListener('click', handleDeleteBooking);

        function handleDeleteBooking() {
            const uniqueId = uniqueIdInput.value;
            const { adminEmailAddress, sendAdminEmail } = bookingSettings;
            contactFormContainer = document.getElementById('contact-form-container');
            const deleteNonce = document.querySelector('#delete_booking_nonce').value; // Retrieve delete nonce

            if (confirm(`Are you sure you want to delete the booking with ID: ${uniqueId}?`)) {
                // Update cursor and button state to "wait"
                setLoadingState(true);
                // Make a fetch call to delete the booking
                fetch(`/wp-content/plugins/LEANWI-Book-A-Room/php/frontend/delete-booking.php`, {
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
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        alert('Booking deleted successfully.');
                        resetBookingForm();
                    } else {
                        alert('Failed to delete booking: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error deleting booking:', error);
                    alert('An error occurred while deleting the booking. Please try again.');
                })
                .finally(() => {
                    setLoadingState(false);
                });
            }
        }

        // Helper function to set the loading state
        function setLoadingState(isLoading) {
            document.body.style.cursor = isLoading ? 'wait' : 'default';
            deleteBookingButton.disabled = isLoading;
            deleteBookingButton.style.cursor = isLoading ? 'wait' : 'default';
        }

        // Helper function to reset the booking form after deletion
        function resetBookingForm() {
            uniqueIdInput.value = '';
            contactFormContainer.style.display = 'none';
            document.getElementById('total-cost').textContent = '0.00';
        }


});
