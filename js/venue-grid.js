document.addEventListener("DOMContentLoaded", function () {
    const dateInput = document.getElementById("selected_date");

    // Let's show in the cosole log whether the user is staff or not
    console.log("Is Booking Staff:", isBookingStaff);

    //Only allow users to see/add for dates maxMonths into th future as specified in our settings
    if (dateInput) {
        const today = new Date();
        const maxMonths = !isBookingStaff ? bookingSettings.maxMonths : 15;

        // Calculate the max selectable date
        const futureMonthNumber = today.getMonth() + parseInt(maxMonths);
        const futureYear = today.getFullYear() + Math.floor(futureMonthNumber / 12);
        const adjustedFutureMonthNumber = futureMonthNumber % 12;
        const maxDate = new Date(futureYear, adjustedFutureMonthNumber + 1, 0); // Last day of the allowed month

        // Format maxDate as YYYY-MM-DD
        const maxDateFormatted = maxDate.toISOString().split("T")[0];

        // Set the max attribute on the date input
        dateInput.setAttribute("max", maxDateFormatted);
    }

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
