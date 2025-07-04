document.addEventListener("DOMContentLoaded", function () {
    const venueId = document.getElementById('venue_id').value;

    // Let's show in the cosole log whether the user is staff or not
    console.log("Is Booking Staff:", isBookingStaff);

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
        
        const retrieveBookingForm = document.querySelector('#retrieve-booking');
        if (retrieveBookingForm) {
            // Trigger the form submission after the calendar is fully updated
            console.log("updateCalendar hasbookingId");
            updateCalendar(venueId, () => {
                setTimeout(() => {
                    const submitEvent = new Event('submit', { bubbles: true, cancelable: true });
                    retrieveBookingForm.dispatchEvent(submitEvent);
                }, 0);
            });
        }
    }

    // Get the selected_date query parameter
    const passedDate = getQueryParam('selected_date');

    // Get the time_slot query parameter
    const passedTimeSlot = getQueryParam('time_slot');
    
    // Update the CSS variables in the :root selector
    document.documentElement.style.setProperty('--highlighted-button-bg', highlightedButtonBgColor);
    document.documentElement.style.setProperty('--highlighted-button-border', highlightedButtonBorderColor);
    document.documentElement.style.setProperty('--highlighted-button-text', highlightedButtonTextColor);

    let currentMonth = new Date(); // The month that is currently being displayed
    let maxMonths = !isBookingStaff ? bookingSettings.maxMonths : 15;
    let currentDetailsDate = new Date();

    document.body.style.cursor = 'wait'; // Set cursor before fetch starts
    fetch(`${bookingSettings.ajax_url}?action=leanwi_get_venue_details&venue_id=${venueId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(fetched_venue_data => {
            console.log("Venue fetched:", fetched_venue_data);

            if (!fetched_venue_data.success) {
                console.error("Venue error:", fetched_venue_data.data?.message || "Unknown error");
                return;
            }

            const venue = fetched_venue_data.data;
            // Check if the venue is restricted to staff-only booking.
            // Should only need this if someone is misdirected somehow.
            if (!isBookingStaff && venue.bookable_by_staff_only == 1) {
                alert("This venue is only bookable by staff members.");
                
                // Redirect back to the previous page
                window.history.back();
                return; // Stop further execution
            }

            // Check if the delete button should be hidden
            if (!isBookingStaff && venue.updated_by_staff_only == 1) {
                const deleteButton = document.getElementById('delete-booking');
                if (deleteButton) {
                    deleteButton.style.display = 'none';
                }
            }

            document.getElementById('venue-name').textContent = venue.name;
            document.getElementById('venue-capacity').textContent = venue.capacity;
            document.getElementById('venue-description').innerHTML = venue.description.replace(/\\'/g, "'").replace(/\r?\n/g, '<br>');

            document.getElementById('venue-extra-text').innerHTML = escapeHtml(venue.extra_text).replace(/\r?\n/g, '<br>');
            document.getElementById('venue-location').textContent = venue.location;
            document.getElementById('venue-image').src = venue.image_url;
            document.getElementById('venue-max-slots').value = venue.max_slots;
            document.getElementById('venue-slot-cost').textContent = parseFloat(venue.slot_cost).toFixed(2);
            document.getElementById('display-affirmations').value = venue.display_affirmations;
            document.getElementById('conditions-of-use-url').value = venue.conditions_of_use_url;
            document.getElementById('days_before_booking').value = venue.days_before_booking;
            document.getElementById('use_business_days_only').value = venue.use_business_days_only;
            document.getElementById('venue_admin_email').value = venue.venue_admin_email;
            document.getElementById('bookable_by_staff_only').value = venue.bookable_by_staff_only;
            document.getElementById('updated_by_staff_only').value = venue.updated_by_staff_only;

            let bookingNotesLabel = document.getElementById('booking_notes_label');
            bookingNotesLabel.textContent = venue.booking_notes_label && venue.booking_notes_label.trim() ? venue.booking_notes_label : "Booking Notes:";

            // Check if cost should be hidden
            if (bookingSettings.showZeroCost === 'no' && parseFloat(venue.slot_cost) === 0) {
                document.getElementById('cost-info').style.display = 'none'; // Hide the Cost line
            }

            // Check if passedDate and passedTimeSlot are available in the query parameters
            if (passedDate && passedTimeSlot) {
                const [year, month, day] = passedDate.split('-').map(Number);
                const targetDate = new Date(year, month - 1, day); // JavaScript months are 0-indexed
            
                currentMonth.setFullYear(targetDate.getFullYear());
                currentMonth.setMonth(targetDate.getMonth());
                updateCalendar(venueId, () => {
                    highlightDayInCalendar(day);
            
                    fetchAvailableTimes(venueId, passedDate).then(availableTimes => {
                        if (availableTimes.length > 0) {
                            showContactForm(availableTimes, passedDate);
            
                            // Highlight the passed time slot
                            const timeSelectDiv = document.querySelector('#time-select');
                            const contactFormContainer = document.getElementById('contact-form-container');
                            const timeButtons = timeSelectDiv.querySelectorAll('button');
            
                            timeButtons.forEach(button => {
                                if (button.dataset.timeValue === passedTimeSlot) {
                                    button.classList.add('selected');
                                    button.scrollIntoView({ behavior: 'smooth', block: 'center' }); 
                                    
                                    // Update the data-selected-times attribute
                                    const selectedTimes = contactFormContainer.dataset.selectedTimes
                                    ? contactFormContainer.dataset.selectedTimes.split(',')
                                    : [];
                                    if (!selectedTimes.includes(passedTimeSlot)) {
                                        selectedTimes.push(passedTimeSlot);
                                        contactFormContainer.dataset.selectedTimes = selectedTimes.join(',');
                                    }
                                }
                            });
                            updateTimeAllowedDisplay();
                            updateTimeLengthDisplay();
                        } else {
                            alert('No available times for this date.');
                        }
                    });
                });
            }
                        
            else {
                if (!bookingId) {
                    console.log("updateCalendar Normal pageload");
                    updateCalendar(venueId);
                    updateTimeAllowedDisplay();
                    updateTimeLengthDisplay();
                }
            }
        })
        .catch(error => console.error('Error fetching venue details:', error))
        .finally(() => {
            document.body.style.cursor = 'default'; // Reset cursor after fetch completes
        });

        //Fetch categories even if we're not showing them to users as we might need to save their current value
        document.body.style.cursor = 'wait'; // Set cursor before fetch starts
        fetch(`${bookingSettings.ajax_url}?action=leanwi_get_categories`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then(fetched_category_data => {

                if (!fetched_category_data.success) {
                    console.error("Categories error:", fetched_category_data.data?.message || "Unknown error");
                    return;
                }

                const categories = fetched_category_data.data;

                // Populate Category dropdown
                const categorySelect = document.getElementById('category');
                categories.forEach(category => {
                    let option = document.createElement('option');
                    option.value = category.category_id;
                    option.textContent = category.category_name;
                    categorySelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error fetching categories:', error))
            .finally(() => {
                document.body.style.cursor = 'default'; // Reset cursor after fetch completes
            });

        //Fetch audiences even if we're not showing them to users as we might need to save their current value
        fetch(`${bookingSettings.ajax_url}?action=leanwi_get_audiences`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then(fetched_audience_data => {

                if (!fetched_audience_data.success) {
                    console.error("Audiences error:", fetched_audience_data.data?.message || "Unknown error");
                    return;
                }

                const audiences = fetched_audience_data.data;

                // Populate Audience dropdown
                const audienceSelect = document.getElementById('audience');
                audiences.forEach(audience => {
                    let option = document.createElement('option');
                    option.value = audience.audience_id;
                    option.textContent = audience.audience_name;
                    audienceSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error fetching audiences:', error))
            .finally(() => {
                document.body.style.cursor = 'default'; // Reset cursor after fetch completes
            });

        let selectedDayElement = null; // Stores the selected day element for later
        function updateCalendar(venueId, callback) {
            document.body.style.cursor = 'wait';
            // Fetch the available days for the venue
            fetch(`${bookingSettings.ajax_url}?action=leanwi_get_available_days&venue_id=${venueId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(fetched_available_days_data => {

                    if (!fetched_available_days_data.success) {
                        console.error("Available Days error:", fetched_available_days_data.data?.message || "Unknown error");
                        return;
                    }

                    const availableDays = fetched_available_days_data.data;
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
                    announceCalendarStatus("Now showing " + monthName);
        
                    // First and last days of the current month
                    const firstDay = new Date(year, month, 1);
                    const lastDay = new Date(year, month + 1, 0);
        
                    // Create the calendar grid
                    const calendarGrid = createCalendarGrid();
                    addWeekdayHeaders(calendarGrid);
                    addEmptyStartCells(calendarGrid, firstDay);

                    const daysBeforeBooking = parseInt(document.getElementById('days_before_booking').value, 10) || 0;
                    const useBusinessDaysOnly = document.getElementById('use_business_days_only').value === '1';

                    const cutoffDate = calculateCutoffDate(today, daysBeforeBooking, useBusinessDaysOnly);


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
                        //if (!isBookingStaff && currentDateMidnight < todayMidnight && year === today.getFullYear() && month === today.getMonth()) {
                        if (!isBookingStaff && currentDateMidnight < cutoffDate) {
                        // Grey out past days in the current month
                            dayElement.style.backgroundColor = '#f0f0f0';
                            dayElement.style.color = '#ccc';
                            dayElement.classList.add('unavailable');
                            dayElement.setAttribute('aria-disabled', 'true');
                            dayElement.setAttribute('aria-hidden', 'true');
                            dayElement.setAttribute('tabindex', '-1');
                        } else if (dayData && dayData.open_time !== '00:00:00' && dayData.close_time !== '00:00:00') {
                            // Available day: make it clickable
                            dayElement.style.cursor = 'pointer';
                            dayElement.classList.add('available');
                            dayElement.setAttribute('tabindex', '0');
                            dayElement.setAttribute('role', 'button');
                            dayElement.setAttribute('aria-label', `${dayName}, ${monthName} ${day}, ${year}`);

                            dayElement.addEventListener('click', function () {
                                if (selectedDayElement) {
                                    selectedDayElement.classList.remove('highlighted'); // Remove highlight from previous selection
                                }
                                dayElement.classList.add('highlighted'); // Highlight the clicked day
                                selectedDayElement = dayElement; // Store the selected day

                                // Announce loading to screen readers
                                const statusRegion = document.getElementById('calendar-status');
                                if (statusRegion) {
                                    statusRegion.textContent = 'Loading available times...';
                                }

                                fetchAvailableTimes(venueId, dateFormatted)
                                    .then(availableTimes => {
                                        // Directly call showContactForm with the available times
                                        if (availableTimes.length > 0) {
                                            document.body.style.cursor = 'wait';
                                            showContactForm(availableTimes, dateFormatted);
                                            document.body.style.cursor = 'default';

                                            //Send the screen focus to the times
                                            setTimeout(() => {
                                                const skipToTimes = document.getElementById('skip-to-times');
                                                if (skipToTimes) {
                                                    skipToTimes.focus();
                                                }
                                            }, 0);
                                        } else {
                                            alert('No available times for this date.');
                                        }
                                    });
                            });

                            // Add ability for keyboard users to "click" day buttons also
                            dayElement.addEventListener('keydown', function (event) {
                                if (event.key === 'Enter' || event.key === ' ') {
                                    event.preventDefault();
                                    dayElement.click(); // Reuse existing click handler (above)
                                }
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
        
        function calculateCutoffDate(today, days, businessOnly) {
            const cutoff = new Date(today.getTime());
        
            let addedDays = 0;
            while (addedDays < days) {
                cutoff.setDate(cutoff.getDate() + 1);
        
                if (businessOnly) {
                    const day = cutoff.getDay();
                    if (day !== 0 && day !== 6) { // Skip Sunday (0) and Saturday (6)
                        addedDays++;
                    }
                } else {
                    addedDays++;
                }
            }
        
            cutoff.setHours(0, 0, 0, 0);
            return cutoff;
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
                currentMonth.setMonth(currentMonth.getMonth() - 1); 
                currentMonth.setDate(1); // Reset to the first day of the month
                announceCalendarStatus("Loading previous month");
                updateCalendar(venueId);
            }
        });
        
        // Ensure currentMonth always starts on the first day of the month to prevent day-based shifts
        currentMonth.setDate(1);

        // Handle the next month click event
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
            const maxMonthLimit = new Date(futureYear, futureMonthNumber, 1); // Ensure future month starts on the 1st

            // Only allow navigation if within the maxMonths limit
            if (currentMonth < maxMonthLimit) {
                currentMonth.setMonth(currentMonth.getMonth() + 1);
                currentMonth.setDate(1); // Reset to the first day to avoid jump issues
                announceCalendarStatus("Loading next month");
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

        function announceCalendarStatus(message) {
            const statusEl = document.getElementById('calendar-status');
            if (statusEl) {
                statusEl.textContent = ''; // Clear first to ensure screen readers pick up the update
                setTimeout(() => {
                    statusEl.textContent = message;
                }, 10); // Short delay allows screen readers to detect change
            }
        }

        
        function fetchAvailableTimes(venueId, date, uniqueId = null) {
            currentDetailsDate = date;

            const params = new URLSearchParams({ venue_id: venueId, date: date });
            if (uniqueId) params.append('unique_id', uniqueId);

            const finalUrl = `${bookingSettings.ajax_url}?action=leanwi_get_available_times&${params.toString()}`;

            document.body.style.cursor = 'wait';
            return fetch(finalUrl)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(result => {
                    if (!result.success) {
                        console.error("Server error:", result.data?.message || "Unknown error");
                        return [];
                    }
                    return result.data; // return the array of slot objects
                })
                .catch(error => {
                    console.error('Error fetching available times:', error);
                    return [];
                })
                .finally(() => {
                    document.body.style.cursor = 'default';
                });
        }

        function updateTimeLengthDisplay() {
            const selectedButtons = Array.from(document.querySelectorAll('.time-button.selected'));
            const timeLengthDiv = document.getElementById('time-length');

            if (selectedButtons.length > 0) {
                const times = selectedButtons
                    .map(btn => btn.dataset.timeValue)
                    .sort(); // Assumes time values are in HH:mm or HH:mm:ss

                const startTime = times[0];
                const lastTime = times[times.length - 1];
                const minutesPerSlot = parseInt(bookingSettings.minutesInterval, 10);
                const endTime = calculateEndTime(lastTime, minutesPerSlot);

                const totalMinutes = selectedButtons.length * minutesPerSlot;

                const readableStart = formatTimeTo12Hour(startTime);
                const readableEnd = formatTimeTo12Hour(endTime);

                const summaryText = `Your current booking is for ${totalMinutes} minute${totalMinutes !== 1 ? 's' : ''} from ${readableStart} until ${readableEnd}.`;

                timeLengthDiv.textContent = summaryText;
                timeLengthDiv.setAttribute('aria-live', 'polite'); // Ensure it's read when updated
                timeLengthDiv.setAttribute('aria-atomic', 'true');
            } else {
                timeLengthDiv.textContent = '';
                timeLengthDiv.removeAttribute('aria-live');
                timeLengthDiv.removeAttribute('aria-atomic');
            }
        }

        function focusBookingSummary() {
            const summary = document.getElementById('time-length');
            if (summary) {
                summary.focus();
                summary.setAttribute('aria-live', 'polite');
                summary.setAttribute('role', 'status');
            }
        }

        function updateTimeAllowedDisplay() {
            const timeAllowedDiv = document.getElementById('time-allowed');
            if (!isBookingStaff) {
                const maxSlots = parseInt(document.getElementById('venue-max-slots').value, 10);
                if (maxSlots < 100) {
                    timeAllowedDiv.textContent = `You can select ${maxSlots !== 1 ? ' up to ' : ''} ${maxSlots} ${maxSlots !== 1 ? ' consecutive ' : ''} timeslot${maxSlots !== 1 ? 's' : ''}.`;
                } else {
                    timeAllowedDiv.textContent = ''; // Clear for unlimited booking
                }
            } else {
                timeAllowedDiv.textContent = ''; // Clear for staff
            }
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
            // Assuming 'day' is in 'YYYY-MM-DD' format
            const [year, month, date] = day.split('-').map(Number);
            const localDate = new Date(year, month - 1, date); // month is 0-indexed
            const formattedDate = localDate.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });

            availableTimesHeading.textContent = `Available Times for ${formattedDate}`;

            // Populate time buttons
            availableTimes.forEach((time, index) => {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'time-button';
                
                // Set the button text to the formatted 12-hour time
                button.textContent = formatTimeTo12Hour(time.start);
                
                // Set the original time value as a data attribute
                button.dataset.timeValue = time.start; // Use the raw time value directly

                if (time.booked && !time.is_booked_for_unique_id) {
                    button.disabled = true;
                    button.classList.add('booked');
                }

                if (showingExistingRecord && time.is_booked_for_unique_id) {
                    button.classList.add('selected');
                }

                // Hover event to display time range
                button.addEventListener('mouseenter', () => {
                    const endTime = calculateEndTime(time.start, bookingSettings.minutesInterval);
                    button.title = `${formatTimeTo12Hour(time.start)} - ${formatTimeTo12Hour(endTime)}`;
                });

                button.addEventListener('keydown', (event) => {
                    if (event.key === 'Escape' || (event.ctrlKey && event.shiftKey && event.key === 'X')) {
                        focusBookingSummary();
                        event.preventDefault();
                    }
                });

                button.addEventListener('click', () => handleTimeSelection(button, index));
                timeSelectDiv.appendChild(button);
            });

            // Construct accessible text
            let availableCount = availableTimes.filter(t => !(t.booked && !t.is_booked_for_unique_id)).length;

            const timeStatus = document.getElementById('time-status');
            if (availableCount > 0) {
                timeStatus.textContent = `${availableCount} time ${availableCount === 1 ? 'slot is' : 'slots are'} available for ${formattedDate}. Use the Tab key to navigate available times. Hit the escape key once you are done selecting your times.`;
            } else {
                timeStatus.textContent = `No available time slots for ${formattedDate}.`;
            }


            dayInput.value = day;
            
            if (showingExistingRecord) {
                updateTotalCost();
                updateTimeLengthDisplay();
                updateTimeAllowedDisplay();
            }

            // Hide the "Submit Booking" button if not staff, venue is staff-only updatable, and it's an existing booking
            const updatedByStaffOnly = document.getElementById('updated_by_staff_only')?.value;
            const submitBookingButton = document.querySelector('.book-button');

            if (submitBookingButton && !isBookingStaff && updatedByStaffOnly == 1 && showingExistingRecord) {
                submitBookingButton.style.display = 'none';
            }


            //Show affirmations if is selected to do so for this venue
            const showAffirmations = parseInt(document.getElementById('display-affirmations').value, 10);

            if(showAffirmations === 1) {
                document.body.style.cursor = 'wait'; // Set cursor before fetch starts
                fetch(`${bookingSettings.ajax_url}?action=leanwi_get_affirmations`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! Status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(fetched_affirmations_data => {
                        if (!fetched_affirmations_data.success) {
                            console.error("Affirmations error:", fetched_affirmations_data.data?.message || "Unknown error");
                            return;
                        }

                        const affirmations = fetched_affirmations_data.data;
                        // Reference to the affirmations div
                        const affirmationsDiv = document.getElementById('affirmations');
            
                        if (affirmations.length > 0) {
                            // Clear previous content
                            affirmationsDiv.innerHTML = '';

                            // Create and add the heading
                            const heading = document.createElement('label');
                            heading.innerHTML = "<strong>Please affirm the following statements before completing your booking *<strong>";
                            heading.style.display = 'block';
                            heading.style.marginBottom = '10px'; // Optional: add spacing below the heading
                            affirmationsDiv.appendChild(heading);

                            // Create the table element
                            const table = document.createElement('table');
                            table.classList.add('affirmations-table');
                        
                            affirmations.forEach(affirmation => {
                                const row = document.createElement('tr');

                                // Checkbox cell
                                const checkboxCell = document.createElement('td');
                                checkboxCell.style.width = '5%';
                                const checkbox = document.createElement('input');
                                checkbox.type = 'checkbox';
                                checkbox.id = `affirmation-${affirmation.id}`;
                                checkbox.name = `affirmation-${affirmation.id}`;
                                checkbox.required = true;
                                checkbox.checked = existingRecord;
                                checkboxCell.appendChild(checkbox);

                                // Text cell with label
                                const textCell = document.createElement('td');
                                const checkboxLabel = document.createElement('label');
                                checkboxLabel.htmlFor = checkbox.id;
                                checkboxLabel.innerHTML = affirmation.affirmation;
                                textCell.appendChild(checkboxLabel);
                                textCell.style.paddingLeft = '10px';

                                // Append to row and table
                                row.appendChild(checkboxCell);
                                row.appendChild(textCell);
                                table.appendChild(row);
                            });
                        
                            // Clear previous content and add the table to the div
                            affirmationsDiv.appendChild(table);
                            affirmationsDiv.style.display = 'block';
                        }
                                                        
                        
                    })
                    .catch(error => console.error('Error fetching affirmations:', error))
                    .finally(() => {
                        document.body.style.cursor = 'default'; // Reset cursor after fetch completes
                    });
            }

            //Show conditions of use text and link if there is one assigned for this venue
            const conditionsOfUseUrl = document.getElementById('conditions-of-use-url').value;

            if (conditionsOfUseUrl) {
                // Reference to the conditions of use div
                const conditionsDiv = document.getElementById('conditions-of-use');

                // Clear previous content
                conditionsDiv.innerHTML = '';

                // Create a container for styling
                const container = document.createElement('div');
                container.style.border = '1px solid #ccc'; // Add a border
                container.style.padding = '15px'; // Add padding
                container.style.marginBottom = '15px'; // Margin below the container

                // Create the text paragraph
                const textParagraph = document.createElement('p');
                textParagraph.textContent = "In order to reserve a room, we require that you review and agree to the Meeting Room Conditions of Use. ";

                // Create the link to the PDF (make "here" the hyperlink)
                const link = document.createElement('a');
                link.href = conditionsOfUseUrl;
                link.textContent = "Navigate to the Conditions of Use document."; // hyperlink
                link.target = "_blank"; // Opens the link in a new tab
                link.style.fontWeight = 'bold'; 
                link.style.textDecoration = 'underline';
                textParagraph.appendChild(link); // Append the link to the paragraph

                // Create the table for checkbox and label
                const table = document.createElement('table');
                table.style.width = '100%'; // Make the table full width
                table.style.marginTop = '10px'; // Space above the table
                table.style.border = '0px'

                // Create a new row for the checkbox
                const row = document.createElement('tr');

                // Create the checkbox cell
                const checkboxCell = document.createElement('td');
                checkboxCell.style.width = '5%'; // Width for the checkbox
                const checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.id = 'conditions-accepted';
                checkbox.name = 'conditions-accepted';
                checkbox.checked = existingRecord;
                checkbox.required = true; // Set as required
                checkboxCell.appendChild(checkbox);

                // Create the text cell for the label
                const textCell = document.createElement('td');
                const checkboxLabel = document.createElement('label');
                checkboxLabel.htmlFor = checkbox.id;
                checkboxLabel.innerHTML = "<strong>* I have read and will follow the Meeting Room Conditions of Use.</strong>";
                textCell.appendChild(checkboxLabel);
                textCell.style.paddingLeft = '10px'; // Add padding for spacing

                // Append cells to the row
                row.appendChild(checkboxCell);
                row.appendChild(textCell);

                // Append the row to the table
                table.appendChild(row);

                // Append the text paragraph and the table to the container
                container.appendChild(textParagraph);
                container.appendChild(table);

                // Append the container to the conditions div
                conditionsDiv.appendChild(container);

                // Make the div visible
                conditionsDiv.style.display = 'block';
            }

            // Check if cost should be hidden
            if (bookingSettings.showZeroCost === 'no' && parseFloat(document.getElementById('venue-slot-cost').textContent) === 0) {
                document.getElementById('cost-container').style.display = 'none'; // Hide the entire cost container
            }

            contactFormContainer.style.display = 'block';

            function handleTimeSelection(button, index) {
                const isSelected = button.classList.contains('selected');
            
                if (isSelected) {
                    deselectFollowingButtons(index);
                } else {
                    const selectedButtons = Array.from(timeSelectDiv.querySelectorAll('.selected'));
                    const lastSelectedIndex = selectedButtons.length 
                            ? getTimeIndex(selectedButtons[selectedButtons.length - 1], availableTimes)
                            : null;
            
                    if (lastSelectedIndex !== null) {
                        const startIndex = Math.min(index, lastSelectedIndex);
                        const endIndex = Math.max(index, lastSelectedIndex);
            
                        // Check if the range has any intermediate booked slots
                        if (hasIntermediateBookedSlots(startIndex, endIndex)) {
                            alert('A booking must use continuous times without a booked or empty slot in between.');
                            return;
                        }
            
                        // Select all buttons in the range
                        selectRange(startIndex, endIndex);
                    } else {
                        button.classList.add('selected'); // First selection
                    }
                }
                
                updateTimeAllowedDisplay();
                updateTimeLengthDisplay();
                updateTotalCost();
            }
            
            function deselectFollowingButtons(index) {
                const buttons = Array.from(timeSelectDiv.querySelectorAll('button'));
                for (let i = index; i < buttons.length; i++) {
                    buttons[i].classList.remove('selected');
                }
            }
            
            function hasIntermediateBookedSlots(start, end) {
                for (let i = start + 1; i < end; i++) {
                    if (availableTimes[i].booked && !availableTimes[i].is_booked_for_unique_id) return true;
                }
                return false;
            }
            
            function selectRange(startIndex, endIndex) {
                const buttons = Array.from(timeSelectDiv.querySelectorAll('button'));
                for (let i = startIndex; i <= endIndex; i++) {
                    buttons[i].classList.add('selected');
                }
            }
            
            function updateTotalCost() {
                const selectedTimes = Array.from(timeSelectDiv.querySelectorAll('.selected')).map(btn => btn.textContent);
                totalCostDisplay.textContent = (selectedTimes.length * slotCost).toFixed(2);
                contactFormContainer.dataset.selectedTimes = selectedTimes.join(',');
            }
        }

        //Function to display start and end time on cursor hover
        function calculateEndTime(startTime, interval) {
            // Split start time into hours and minutes
            const intervalMinutes = Number(interval);
            const [hour, minute] = startTime.split(':').map(Number);

            // Calculate total minutes from the start time and add the interval
            let totalMinutes = (hour * 60) + minute + intervalMinutes;
        
            // Calculate end hour and end minute by converting total minutes back
            const endHour = Math.floor(totalMinutes / 60) % 24; // Wrap hours to 24-hour format
            const endMinute = totalMinutes % 60;
        
            // Ensure hours and minutes are formatted with leading zeros
            const formattedEndHour = String(endHour).padStart(2, '0');
            const formattedEndMinute = String(endMinute).padStart(2, '0');

            return `${formattedEndHour}:${formattedEndMinute}`;
        }
        
        // Function to format time to 12-hour format
        function formatTimeTo12Hour(time) {
            const [hour, minute] = time.split(':').map(Number);
            const period = hour >= 12 ? 'pm' : 'am';
            const formattedHour = hour % 12 || 12; // Convert to 12-hour format; use 12 for midnight and noon
            const formattedMinute = String(minute).padStart(2, '0'); // Optional: format minute with leading zero

            return `${formattedHour}:${formattedMinute} ${period}`;
        }
        
        // Function to get time index based on the button's time (start time)
        function getTimeIndex(button, availableTimes) {
            const timeValue = button.dataset.timeValue; // Get the time from the button's data attribute
            return availableTimes.findIndex(time => time.start === timeValue);
        }

        function formatTo24Hour(dateString) {
            // Try 12-hour format first
            let match = dateString.match(/^(\d{4}-\d{2}-\d{2}) (\d{1,2}):(\d{2}) (am|pm)$/i);
            if (match) {
                let [, datePart, hour, minute, period] = match;
                hour = parseInt(hour, 10);
                if (period.toLowerCase() === 'pm' && hour !== 12) hour += 12;
                if (period.toLowerCase() === 'am' && hour === 12) hour = 0;
                const hourStr = hour.toString().padStart(2, '0');
                return `${datePart} ${hourStr}:${minute}:00`;
            }

            // Try 24-hour format
            match = dateString.match(/^(\d{4}-\d{2}-\d{2}) (\d{2}):(\d{2})$/);
            if (match) {
                const [, datePart, hour, minute] = match;
                return `${datePart} ${hour}:${minute}:00`;
            }

            console.error("Unrecognized date format:", dateString);
            return '';
        }


        document.querySelector('#booking-form').addEventListener('submit', function (event) {
            event.preventDefault();

            // Get email and phone field values
            const email = document.querySelector('#email').value.trim();
            const phone = document.querySelector('#phone').value.trim();
            const recurrence_id = document.querySelector('#recurrence_id').value.trim();

            // Ensure at least one of the fields is filled if wasn't created from a recurrence
            if (!email && !phone && recurrence_id <= 0) {
                alert('Please provide at least an email address or phone number.');
                return;
            }

            const formData = new FormData(this);
        
            // Get the submit button and disable it
            const submitButton = this.querySelector('button[type="submit"]');
            submitButton.disabled = true; // Disable the button immediately to prevent further clicks

            // Execute reCAPTCHA if enabled
            if (bookingSettings.enableRecaptcha === 'yes') {
                grecaptcha.ready(function() {
                    grecaptcha.execute(bookingSettings.recaptchaSiteKey, { action: 'submit' })
                    .then(function(token) {
                        // Append the reCAPTCHA token to the form
                        formData.append('g-recaptcha-response', token);
                        // Call the fetch function after appending the token
                        submitForm(formData);
                    })
                    .catch(function (error) {
                        console.error('reCAPTCHA error:', error);
                        submitButton.disabled = false; // Re-enable the button in case of reCAPTCHA failure
                    });
                });
            } else {
                // If reCAPTCHA is not enabled, submit the form directly
                submitForm(formData);
            }
        
            function submitForm(formData) {
                const contactFormContainer = document.getElementById('contact-form-container');
                const selectedTimes = contactFormContainer.dataset.selectedTimes ? contactFormContainer.dataset.selectedTimes.split(',') : [];

                const submitButton = document.querySelector('.book-button');
                const bookingForm = document.getElementById('booking-form');
                const statusRegion = document.getElementById('booking-form-status');

                // Add the nonce
                formData.append('submit_booking_nonce', document.querySelector('#submit_booking_nonce').value);

                // Append required data
                formData.append('venue_id', document.getElementById('venue_id').value);
                formData.append('total_cost', document.getElementById('total-cost').textContent);
                formData.append('venue_name', document.getElementById('venue-name').textContent);
                formData.append('page_url', window.location.href);
                formData.append('days_before_booking', document.getElementById('days_before_booking').value);
                formData.append('use_business_days_only', document.getElementById('use_business_days_only').value);
                formData.append('venue_admin_email', document.getElementById('venue_admin_email').value);
                formData.append('updated_by_staff_only', document.getElementById('updated_by_staff_only').value);

                if (existingRecord) {
                    const uniqueId = document.getElementById('unique_id').value;
                    formData.append('unique_id', uniqueId);
                }

                if (selectedTimes.length === 0) {
                    alert('Please select at least one time slot.');
                    submitButton.disabled = false;
                    return;
                }

                if (!isBookingStaff) {
                    const maxSlots = parseInt(document.getElementById('venue-max-slots').value, 10);
                    if (selectedTimes.length > maxSlots) {
                        alert(`You can book a maximum of ${maxSlots} timeslots per booking.`);
                        submitButton.disabled = false;
                        return;
                    }
                }

                const currentDateFormatted = returnCurrentDate();
                const startTime = formatTo24Hour(`${currentDetailsDate} ${selectedTimes[0]}`);
                const endTime = formatTo24Hour(`${currentDetailsDate} ${selectedTimes[selectedTimes.length - 1]}`);

                if (startTime === '' || endTime === '') {
                    throw new Error(`Incorrect start and/or end date.`);
                }

                formData.append('start_time', startTime);
                formData.append('end_time', endTime);
                formData.append('current_time', currentDateFormatted);

                formData.append('minutes_interval', bookingSettings.minutesInterval);
                formData.append('admin_email_address', bookingSettings.adminEmailAddress);
                formData.append('send_admin_email', bookingSettings.sendAdminEmail);
                formData.append('is_booking_staff', isBookingStaff);

                if (statusRegion) {
                    statusRegion.textContent = '';
                    requestAnimationFrame(() => {
                        statusRegion.textContent = 'Submitting your booking. Please wait.';
                    });
                }

                document.body.style.cursor = 'wait';

                fetch(`${bookingSettings.ajax_url}?action=leanwi_submit_booking`, {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`Network error: ${response.status}`);
                    }
                    return response.json();
                })
                .then(submit_data => {
                    const data = submit_data.data; 

                    if (submit_data.success) {
                        const message = data.message + (data.unique_id ? `\n\nBooking Reference: ${data.unique_id}` : '');
                        if (statusRegion) {
                            statusRegion.textContent = 'Booking submitted successfully. ' + (data.unique_id ? `Reference: ${data.unique_id}.` : '');
                        }
                        alert(message);
                        location.href = location.pathname;
                    } else {
                        if (statusRegion) {
                            statusRegion.textContent = 'There was an issue submitting your booking. ' + data.message;
                        }
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error submitting booking:', error.message);
                    if (statusRegion) {
                        statusRegion.textContent = 'An error occurred while submitting your booking.';
                    }
                    alert('An error occurred while submitting your booking: ' + error.message);
                })
                .finally(() => {
                    document.body.style.cursor = 'default';
                    submitButton.disabled = false;
                });
            }
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
            fetch(`${bookingSettings.ajax_url}?action=leanwi_fetch_booking`, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then(fetch_data => {
                const data = fetch_data.data;
                if (fetch_data.success) {
                    const booking = data[0]; // Assuming single booking for now

                    // Populate form fields and display booking information
                    populateBookingFormFields(booking);
                    
                    // Fetch available times and show the contact form if times are available
                    handleAvailableTimes(booking);
                } else {
                    alert(data.error || 'Booking retrieval unsuccessful.');
                    document.getElementById('unique_id').value = '';
                    existingRecord = false;
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
            document.getElementById('organization').value = booking.organization;
            document.getElementById('email').value = booking.email;
            document.getElementById('phone').value = booking.phone;
            document.getElementById('physical_address').value = booking.physical_address;
            document.getElementById('participants').value = booking.number_of_participants;
            //document.getElementById('notes').value = booking.booking_notes;
            document.getElementById('notes').value = document.createElement("textarea").innerHTML = booking.booking_notes;
            document.getElementById('category').value = booking.category_id;
            document.getElementById('audience').value = booking.audience_id;
            document.getElementById('recurrence_id').value = booking.recurrence_id;

            // If it's a recurring booking, remove 'required' from physical_address
            const physicalAddressField = document.getElementById('physical_address');
            if (booking.recurrence_id > 0) {
                physicalAddressField.removeAttribute('required');
            }
            
            // Display the booking form container
            contactFormContainer.style.display = 'block';

            // Highlight selected day on the calendar
            console.log("HighlightSelectedDay populateBookingFormFields: ", booking.start_time);
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

                console.log("updateCalendar highlightSelectedDay");
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
                    if (selectedDayElement) {
                        selectedDayElement.classList.remove('highlighted'); // Remove highlight from previous selection
                    }
                    dayElement.classList.add('highlighted'); // Highlight the clicked day
                    selectedDayElement = dayElement; // Store the selected day
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
            const venueAdminEmail = document.getElementById('venue_admin_email')?.value || '';

            contactFormContainer = document.getElementById('contact-form-container');
            const deleteNonce = document.querySelector('#delete_booking_nonce').value; // Retrieve delete nonce

            if (confirm(`Are you sure you want to delete the booking with ID: ${uniqueId}?`)) {
                // Prompt for cancellation reason
                const cancellationReason = prompt("Please provide a reason for the cancellation (optional):");

                // Update cursor and button state to "wait"
                setLoadingState(true);
                // Make a fetch call to delete the booking
                fetch(`${bookingSettings.ajax_url}?action=leanwi_delete_booking`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ 
                        venue_id: document.getElementById('venue_id').value,
                        unique_id: uniqueId, 
                        admin_email_address: adminEmailAddress,
                        send_admin_email: sendAdminEmail,
                        venue_admin_email: venueAdminEmail,
                        delete_booking_nonce: deleteNonce, // Include the nonce
                        cancellation_reason: cancellationReason || '', // Pass the reason or an empty string if none provided
                        is_booking_staff: Boolean(isBookingStaff)
                    }),
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(delete_data => {
                    alert(delete_data.data.message);
                    // Refresh the page without parameters
                    location.href = location.pathname;
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

//  dynamically add a red asterisk * next to all required field labels in the booking-form
document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll("#booking-form input[required], #booking-form select[required], #booking-form textarea[required]").forEach((field) => {
        let label = document.querySelector(`#booking-form label[for="${field.id}"]`);
        if (label) {
            label.innerHTML += ' <span style="color: red;">*</span>';
        }
    });
});

// Accessibility functionality to announce activity on Booking retreival and deletion
document.addEventListener('DOMContentLoaded', function () {
    const retrieveForm = document.getElementById('retrieve-booking');
    const deleteButton = document.getElementById('delete-booking');
    const statusRegion = document.getElementById('booking-status');

    if (retrieveForm) {
        retrieveForm.addEventListener('submit', function () {
        statusRegion.textContent = 'Retrieving your booking, please wait.';
        });
    }

    if (deleteButton) {
        deleteButton.addEventListener('click', function () {
        statusRegion.textContent = 'Deleting your booking, please wait.';
        });
    }


    const timeSelectDiv = document.getElementById('time-select');

    // Add listener to each button *after they are created*
    timeSelectDiv.addEventListener('keydown', function (event) {
        const isButton = event.target.classList.contains('time-button');
        if (!isButton) return;

        if (event.key === 'Escape') {
            focusBookingSummary();
            event.preventDefault();
        }
    });
        
});
