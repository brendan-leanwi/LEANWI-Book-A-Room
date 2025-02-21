document.addEventListener("DOMContentLoaded", function () {
    const dateInput = document.getElementById("selected_date");

    dateInput.addEventListener("change", function () {
        // Submit the form when the date changes
        document.body.style.cursor = "wait"; // Change cursor to wait
        dateInput.form.submit();
    });

    document.getElementById("booking-date-selector").addEventListener("submit", function() {
        document.body.style.cursor = "wait"; // Change cursor to wait
    });

    document.getElementById("next-day-button").addEventListener("click", function() {
        var dateInput = document.getElementById("selected_date");
        var currentDate = new Date(dateInput.value);
        currentDate.setDate(currentDate.getDate() + 1); // Add one day
        dateInput.value = currentDate.toISOString().split("T")[0]; // Format as YYYY-MM-DD
        document.body.style.cursor = "wait"; // Change cursor to wait
        dateInput.form.submit(); // Submit the form automatically
    });

    // Reset cursor when the page loads
    window.addEventListener("load", function() {
        document.body.style.cursor = "default";
    });

});
