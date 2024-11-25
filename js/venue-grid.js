document.addEventListener("DOMContentLoaded", function () {
    const dateInput = document.getElementById("selected_date");

    dateInput.addEventListener("change", function () {
        // Submit the form when the date changes
        document.getElementById("date-selector").submit();
    });
});
