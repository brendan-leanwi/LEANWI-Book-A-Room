document.addEventListener("DOMContentLoaded", function () {
    // Get the user-defined colors from bookingSettings
    const highlightedButtonBgColor = bookingSettings.highlightedButtonBgColor || '#ffe0b3';
    const highlightedButtonBorderColor = bookingSettings.highlightedButtonBorderColor || '#ff9800';
    const highlightedButtonTextColor = bookingSettings.highlightedButtonTextColor || '#000000';

    // Update the CSS variables in the :root selector
    document.documentElement.style.setProperty('--highlighted-button-bg', highlightedButtonBgColor);
    document.documentElement.style.setProperty('--highlighted-button-border', highlightedButtonBorderColor);
    document.documentElement.style.setProperty('--highlighted-button-text', highlightedButtonTextColor);

    //Fetch categories even if we're not showing them to users as we might need to save their current value
    document.body.style.cursor = 'wait'; // Set cursor before fetch starts
    fetch('/wp-content/plugins/LEANWI-Book-A-Room/php/frontend/get-categories.php')
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            // Populate Category dropdown
            const categorySelect = document.getElementById('category');
            data.forEach(category => {
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
    document.body.style.cursor = 'wait'; // Set cursor before fetch starts
    fetch('/wp-content/plugins/LEANWI-Book-A-Room/php/frontend/get-audiences.php')
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            // Populate Audience dropdown
            const audienceSelect = document.getElementById('audience');
            data.forEach(audience => {
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
});

/*********************************************************************************************
 * Add a recurring booking functionality
 *********************************************************************************************/
document.getElementById('recurring-choices').addEventListener('submit', function(event) {
    event.preventDefault();

    // Set the display of the recurrence details container to block
    const detailsContainer = document.getElementById('recurrence-details-container');
    if (detailsContainer) {
        detailsContainer.style.display = 'block';
        // Scroll to it smoothly
        setTimeout(() => {
            const offset = 200; // Adjust this value based on your layout (increase if scrolling too far)
            const elementPosition = detailsContainer.getBoundingClientRect().top + window.scrollY;
            window.scrollTo({
                top: elementPosition - offset,
                behavior: 'smooth'
            });
        }, 100); // Delay to let the UI update before scrolling
    }

});

/*********************************************************************************************
 * Delete a recurring booking functionality
 *********************************************************************************************/
document.getElementById('delete_booking').addEventListener('click', function () {
    // Add the functionality here
    if (confirm('Are you sure you want to delete this recurrence and all future bookings?')) {
        const recurrenceId = document.getElementById('recurrence_id').value;
        deleteRecurrenceAndBookings(recurrenceId);
    } 
});

function deleteRecurrenceAndBookings(recurrenceId) {
    
    if (!recurrenceId) {
        alert('Invalid recurrence ID');
        return;
    }

    const nonce = document.querySelector('#delete_recurrence_nonce').value;
    console.log("Delete JSON:", JSON.stringify({ recurrence_id: recurrenceId, delete_recurrence_nonce: nonce }));

    fetch('/wp-content/plugins/LEANWI-Book-A-Room/php/frontend/staff/delete-recurrence.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ recurrence_id: recurrenceId, delete_recurrence_nonce: nonce }),
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Recurrence and bookings successfully deleted.');
                // Optionally, refresh the page or update the UI
            } else {
                alert('Error: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });

}

/*********************************************************************************************
 * Find existing recurring bookings functionality
 *********************************************************************************************/
document.getElementById('retrieve-recurrence').addEventListener('click', function (event) {
    event.preventDefault();
    
    document.body.style.cursor = 'wait';
    fetch('/wp-content/plugins/LEANWI-Book-A-Room/php/frontend/staff/get-recurrences.php')
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(recurrences => {
            const tableBody = document.getElementById('recurrenceTableBody');
            tableBody.innerHTML = ''; // Clear existing rows

            recurrences.forEach(recurrence => {
                const row = document.createElement('tr');
                const { venue_name, recurrence_type, recurrence_interval, recurrence_end_date, start_time, end_time, organization, recurrence_name, recurrence_id } = recurrence;

                row.appendChild(createTableCell(venue_name));
                row.appendChild(createTableCell(recurrence_type));
                row.appendChild(createTableCell(recurrence_interval));
                row.appendChild(createTableCell(recurrence_end_date));
                row.appendChild(createTableCell(start_time));
                row.appendChild(createTableCell(end_time));
                row.appendChild(createTableCell(organization || 'N/A'));
                row.appendChild(createTableCell(recurrence_name));

                // Add action cell
                const actionCell = document.createElement('td');
                const viewLink = document.createElement('a');
                viewLink.textContent = 'View';
                viewLink.href = '#';
                viewLink.dataset.recurrenceId = recurrence_id; // Pass the recurrence_id
                viewLink.addEventListener('click', function (e) {
                    e.preventDefault();
                    handleViewAction(this.dataset.recurrenceId);
                });

                actionCell.appendChild(viewLink);
                row.appendChild(actionCell);

                tableBody.appendChild(row);
            });

            // Show the container
            document.getElementById('existing-recurrence-container').style.display = 'block';
        })
        .catch(error => {
            console.error('Error fetching recurrences:', error);
        })
        .finally(() => {
            document.body.style.cursor = 'default'; // Reset cursor after fetch completes
        });
});

function createTableCell(content) {
    const cell = document.createElement('td');
    cell.textContent = content;
    return cell;
}

// Handle "View" action
function handleViewAction(recurrenceId) {
    // Set cursor to indicate loading
    document.body.style.cursor = 'wait';

    // Fetch recurrence details
    fetch(`/wp-content/plugins/LEANWI-Book-A-Room/php/frontend/staff/get-recurrence-details.php?recurrence_id=${recurrenceId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`Error fetching recurrence details: ${response.status}`);
            }
            return response.json();
        })
        .then(recurrence => {
            // Get the form element
            const form = document.getElementById('recurrence-details-form');
        
            // Populate form fields with the fetched data
            form.recurrence_id.value = recurrence.recurrence_id || 0;
            form.venue_id.value = recurrence.venue_id || "";
            form.recurrence_type.value = recurrence.recurrence_type || "";
            form.start_time.value = convertTo12HourFormat(recurrence.start_time);
            form.end_time.value = convertTo12HourFormat(recurrence.end_time);
            form.recurrence_interval.value = recurrence.recurrence_interval || "1";
            form.recurrence_day_of_week.value = recurrence.recurrence_day_of_week || "";
            form.recurrence_start_date.value = recurrence.recurrence_start_date || "";
            form.recurrence_end_date.value = recurrence.recurrence_end_date || "";
            form.recurrence_week_of_month.value = recurrence.recurrence_week_of_month || "";
            form.name.value = recurrence.name || "";
            form.organization.value = recurrence.organization || "";
            form.email.value = recurrence.email || "";
            form.phone.value = recurrence.phone || "";
            form.participants.value = recurrence.number_of_participants || "";
            form.notes.value = recurrence.booking_notes || "";
            form.category.value = recurrence.category_id || 1;
            form.audience.value = recurrence.audience_id || 1;

            //Display appropriate name on the Save button
            form.book_button.textContent = 'Resave Recurrence and change Future Bookings';
        
            // Display the delete button
            const deleteButton = document.getElementById('delete_booking');
            if (deleteButton) {
                deleteButton.style.display = 'block';
            }

            // Show the form container
            const detailsContainer = document.getElementById('recurrence-details-container');
            if (detailsContainer) {
                detailsContainer.style.display = 'block';
                
                // Scroll to the container smoothly
                setTimeout(() => {
                    const offset = 200; // Adjust based on your layout
                    const elementPosition = detailsContainer.getBoundingClientRect().top + window.scrollY;
                    window.scrollTo({
                        top: elementPosition - offset,
                        behavior: 'smooth'
                    });
                }, 100); // Delay for UI updates before scrolling
            }
        })
        .catch(error => {
            console.error('Error loading recurrence details:', error);
            alert('Unable to load recurrence details. Please try again.');
        })
        .finally(() => {
            // Reset cursor
            document.body.style.cursor = 'default';
        });
}

/*********************************************************************************************
 * Save a recurring booking functionality
 *********************************************************************************************/
document.querySelector('#recurrence-details-form').addEventListener('submit', function (event) {
    event.preventDefault();
    const formData = new FormData(this);

    if (!validateRecurrenceDates()) {
        return; // Stop form submission if validation fails
    }

    // If we have a recurrence Id we are updating so delete the old recurrence before adding the latest one.
    const recurrenceId = document.getElementById('recurrence_id').value;
    if (recurrenceId > 0) {
        deleteRecurrenceAndBookings(recurrenceId);
    }

    // Execute reCAPTCHA if enabled
    if (bookingSettings.enableRecaptcha) {
        grecaptcha.execute(bookingSettings.recaptchaSiteKey, { action: 'submit' })
        .then(function(token) {
            // Append the reCAPTCHA token to the form
            formData.append('g-recaptcha-response', token);  // !!! Not implemented in php file yet !!!
            // Call the fetch function after appending the token
            submitForm(formData);
        });
    } else {
        // If reCAPTCHA is not enabled, submit the form directly
        submitForm(formData);
    }
});

function validateRecurrenceDates() {
    const startDateInput = document.getElementById('recurrence_start_date');
    const endDateInput = document.getElementById('recurrence_end_date');

    const startDate = getLocalDate(startDateInput.value);
    const endDate = getLocalDate(endDateInput.value);
    const today = new Date();
    
    // Reset time to midnight for today to only compare dates
    today.setHours(0, 0, 0, 0);
    if (startDate < today) {
        alert('Recurrence Start Date cannot be in the past.');
        return false;
    }

    if (endDate < today) {
        alert('Recurrence End Date cannot be in the past.');
        return false;
    }

    if (startDate > endDate) {
        alert('Recurrence Start Date must be before the End Date.');
        return false;
    }

    return true;
}

// Helper function to parse the date input and adjust for local time
function getLocalDate(dateString) {
    const [year, month, day] = dateString.split('-').map(Number);
    return new Date(year, month - 1, day); // Month is 0-indexed in JavaScript
}

function handleSubmit() {
    if (!validateRecurrenceDates()) {
        return; // Stop form submission if validation fails
    }

    // Proceed with form submission
    const formData = new FormData();
    submitForm(formData);
}


/**
 * Converts a 12-hour time string (e.g., "5:00 AM") into 24-hour format (e.g., "05:00").
 * @param {string} time - A time string in 12-hour format.
 * @returns {string} - The time string in 24-hour format.
 */
function formatTo24Hour(time) {
    const [hoursAndMinutes, period] = time.split(' '); // Split into "5:00" and "AM/PM"
    let [hours, minutes] = hoursAndMinutes.split(':').map(Number); // Split into [5, 0]

    if (period === 'PM' && hours !== 12) {
        hours += 12; // Convert PM times to 24-hour format
    } else if (period === 'AM' && hours === 12) {
        hours = 0; // Convert 12 AM to 0
    }

    // Pad hours and minutes with leading zeros if necessary
    return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}`;
}

function convertTo12HourFormat(time24) {
    if (!time24) return ""; // Return empty if no value
    const [hour, minute] = time24.split(":");
    let period = "AM";
    let hour12 = parseInt(hour, 10);

    if (hour12 >= 12) {
        period = "PM";
        if (hour12 > 12) hour12 -= 12;
    } else if (hour12 === 0) {
        hour12 = 12; // Midnight case
    }

    return `${hour12}:${minute} ${period}`;
}

function submitForm(formData) {
    const recurrenceFormContainer = document.getElementById('recurrence-details-container');
    
    // Add the nonce
    formData.append('submit_recurrence_nonce', document.querySelector('#submit_recurrence_nonce').value);

    // Append required data to formData
    formData.append('venue_id', document.getElementById('venue_id').value);

    const startTime = formatTo24Hour(document.getElementById('start_time').value); // Convert start time
    const endTime = formatTo24Hour(document.getElementById('end_time').value); // Convert end time

    console.log('Display time: ', document.getElementById('start_time').value, 'returned time:',startTime);
    formData.append('start_time', startTime);
    formData.append('end_time', endTime);

    // Add plugin settings
    formData.append('minutes_interval', bookingSettings.minutesInterval);

    document.body.style.cursor = 'wait'; // Set cursor before fetch starts
    fetch('/wp-content/plugins/LEANWI-Book-A-Room/php/frontend/staff/add-recurring-bookings.php', {
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
            const messageContainer = document.getElementById('booking-message');
            messageContainer.textContent = data.message;
            messageContainer.style.display = 'block'; // Show the message div
            recurrenceFormContainer.style.display = 'none';
        } else {
            console.error('Submit Error:', data.message);
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error submitting recurrence booking:', error.message);
        alert('An error occurred while submitting your recurrence: ' + error.message);
    })
    .finally(() => {
        document.body.style.cursor = 'default'; // Reset cursor after fetch completes
    });
    
}