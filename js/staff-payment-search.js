document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.querySelector('input[name="staff_search"]');
    const searchButton = document.querySelector('button[type="submit"]');

    // Example: Highlight search input on focus
    searchInput.addEventListener('focus', () => {
        searchInput.style.outline = '2px solid #0073aa';
    });

    searchInput.addEventListener('blur', () => {
        searchInput.style.outline = 'none';
    });

    // You can add more interactivity here, such as AJAX calls or validations
});

document.addEventListener("DOMContentLoaded", function () {
    // Add click event listener for "Mark Paid" and "Mark as Unpaid" links
    document.querySelectorAll('.toggle-paid-link').forEach(function (link) {
        link.addEventListener('click', function (event) {
            event.preventDefault(); // Prevent navigation
            const bookingId = this.getAttribute('data-booking-id');
            const newStatus = this.getAttribute('data-new-status'); // Get the new payment status
            const nonce = this.getAttribute('data-nonce'); // Get the nonce

            document.body.style.cursor = 'wait';

            // Make an AJAX request to the PHP file
            fetch('/wp-content/plugins/LEANWI-Book-A-Room/php/frontend/staff/staff-mark-payment.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ 
                    booking_id: bookingId,
                    new_status: newStatus, 
                    nonce: nonce // Include the nonce
                }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Refresh the page
                    location.reload();
                } else {
                    alert('Error updating payment status: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            })
            .finally(() => {
                document.body.style.cursor = 'default';
            });
        });
    });

    // Add click event listener for sending the feedback request links
    document.querySelectorAll('.send-feedback-request-link').forEach(function (link) {
        link.addEventListener('click', function (event) {
            event.preventDefault(); // Prevent navigation
            const bookingId = this.getAttribute('data-booking-id');
            const nonce = this.getAttribute('data-nonce'); // Get the nonce

            document.body.style.cursor = 'wait';

            // Make an AJAX request to the PHP file
            fetch('/wp-content/plugins/LEANWI-Book-A-Room/php/frontend/staff/staff-send-feedback-request-email.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ 
                    booking_id: bookingId,
                    nonce: nonce // Include the nonce
                }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Refresh the page
                    location.reload();
                } else {
                    alert('Error sending feedback request email: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            })
            .finally(() => {
                document.body.style.cursor = 'default';
            });
        });
    });
});