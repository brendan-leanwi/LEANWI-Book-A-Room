document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.querySelector('input[name="booking_search"]');
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
