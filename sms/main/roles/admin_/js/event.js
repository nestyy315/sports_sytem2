console.log("Event.js loaded");

$(document).ready(function () {
    const eventsContainer = $("#events_container");

    // Function to load events
    function loadEvents() {
        console.log("Loading events...");
        $.ajax({
            url: "../MAIN/roles/admin_/view/event-view.php", // Ensure this path is correct
            method: "GET",
            dataType: "html", // Expecting HTML response
            success: function (response) {
                console.log("Events loaded successfully");
                eventsContainer.html(response); // Populate the cards with response
                bindActions(); // Bind actions to buttons (edit, delete)
            },
            error: function (error) {
                console.error("Error loading events:", error.responseText);
            },
        });
    }

    function bindActions() {
        console.log("Binding actions...");

        // Bind Add Event Button
        $("#add-event").off("click").on("click", function () {
            addEvent();
        });

        // Bind Edit Event Button
        $(".edit-event").off("click").on("click", function () {
            const eventId = $(this).data("id");
            editEvent(eventId);
        });

        // Bind Delete Event Button
        $(".delete-event").off("click").on("click", function () {
            const eventId = $(this).data("id");
            const eventName = $(this).data("name");
            deleteEvent(eventId, eventName);
        });

        // Bind Search Input (Search Events)
        $("#search_bar").off("keyup").on("keyup", function () {
            searchEvent();
        });

        // Bind Sort Events Button
        $(".btn-primary").off("click").on("click", function () {
            sortEvents();
        });
    }

    // Function to show Add Event modal
    function addEvent() {
        console.log("Opening Add Event modal...");
        $.ajax({
            url: "../MAIN/roles/admin_/add/event-add-modal.html",
            method: "GET",
            success: function (view) {
                $(".modal-container").html(view);
                $("#addEventModal").modal("show");

                // Handle Add Event form submission
                $("#form-add-event").on("submit", function (e) {
                    e.preventDefault();
                    saveEvent();
                });
            },
            error: function () {
                alert("Error loading Add Event modal.");
            },
        });
    }

    // Function to save a new event
    function saveEvent() {
        const formData = new FormData($("#form-add-event")[0]);

        $.ajax({
            url: "../MAIN/roles/admin_/add/event-add.php",
            method: "POST",
            dataType: "json",
            data: formData,
            processData: false,  // Don't process the files
            contentType: false,  // Set content type to false as jQuery will tell the server its a query string request
            success: function (response) {
                if (response.status === "success") {
                    $("#addEventModal").modal("hide");
                    loadEvents(); // Reload the events list
                } else {
                    alert(response.message || "Error adding event.");
                }
            },
            error: function () {
                alert("Error saving event.");
            },
        });
    }

    // Function to show Edit Event modal
    function editEvent(eventId) {
        console.log("Opening Edit Event modal for eventId:", eventId);
        $.ajax({
            url: `../MAIN/roles/admin_/edit/event-edit-modal.html?id=${eventId}`,
            method: "GET",
            success: function (view) {
                $(".modal-container").html(view);
                $("#editEventModal").modal("show");

                // Populate event details
                $.ajax({
                    url: `../MAIN/roles/admin_/edit/event-edit-fetch.php?id=${eventId}`,
                    method: "GET",
                    dataType: "json",
                    success: function (event) {
                        $("#event_name").val(event.event_name);
                        $("#event_start_date").val(event.event_start_date);
                        $("#event_end_date").val(event.event_end_date);
                        $("#event_location").val(event.event_location);
                        $("#event_image").val(event.event_image);
                        $("#event_description").val(event.event_description);
                        $("#school_year").val(event.school_year);
                        $("#published").val(event.published);
                    },
                    error: function () {
                        alert("Error fetching event details.");
                    },
                });

                // Handle Edit Event form submission
                $("#form-edit-event").on("submit", function (e) {
                    e.preventDefault();
                    updateEvent(eventId);
                });
            },
            error: function () {
                alert("Error loading Edit Event modal.");
            },
        });
    }

    // Function to update an event
    function updateEvent(eventId) {
        const eventData = {
            event_id: eventId,
            event_name: $("#event_name").val(),
            event_start_date: $("#event_start_date").val(),
            event_end_date: $("#event_end_date").val(),
            event_location: $("#event_location").val(),
            event_image: $("#event_image").val(),
            event_description: $("#event_description").val(),
            school_year: $("#school_year").val(),
            published: $("#published").val(),
        };

        $.ajax({
            url: "../MAIN/roles/admin_/edit/event-edit.php",
            method: "POST",
            dataType: "json",
            data: eventData,
            success: function (response) {
                if (response.status === "success") {
                    $("#editEventModal").modal("hide");
                    loadEvents(); // Reload the events list
                } else {
                    alert(response.message || "Error updating event.");
                }
            },
            error: function () {
                alert("Error updating event.");
            },
        });
    }

    // Function to delete an event
    function deleteEvent(eventId, eventName) {
        if (confirm(`Are you sure you want to delete the event "${eventName}"?`)) {
            $.ajax({
                url: "../MAIN/roles/admin_/delete/event-delete.php",
                method: "POST",
                dataType: "json",
                data: { event_id: eventId },
                success: function (response) {
                    if (response.status === "success") {
                        alert("Event deleted successfully.");
                        loadEvents(); // Reload the events list to reflect the deletion
                    } else {
                        alert(response.message || "Error deleting event.");
                    }
                },
                error: function () {
                    alert("Error deleting event.");
                },
            });
        }
    }

    // Function to search events
    function searchEvent() {
        const searchQuery = document.getElementById('search_bar').value.toLowerCase();
        const cards = document.querySelectorAll('.event-card');

        cards.forEach(card => {
            const eventName = card.querySelector('.event-name').textContent.toLowerCase();
            if (eventName.includes(searchQuery)) {
                card.style.display = ''; // Show card if it matches the search query
            } else {
                card.style.display = 'none'; // Hide card if it does not match the search query
            }
        });
    }

    // Function to sort the events alphabetically by name
    function sortEvents() {
        const cards = Array.from(document.querySelectorAll('.event-card'));
        cards.sort((cardA, cardB) => {
            const nameA = cardA.querySelector('.event-name').textContent.toLowerCase();
            const nameB = cardB.querySelector('.event-name').textContent.toLowerCase();
            return nameA.localeCompare(nameB);
        });

        const container = $("#events_container");
        container.html('');
        cards.forEach(card => container.append(card)); // Re-append cards in sorted order
    }

    // Initial Load
    loadEvents();
});
