<?php
require_once __DIR__ . '/../../../database/database.class.php';

$conn = (new Database())->connect();

try {
    $query = $conn->prepare("SELECT event_id, event_name, event_start_date, event_end_date, event_location, event_image, event_description, school_year, published FROM events");
    $query->execute();
    $events = $query->fetchAll(PDO::FETCH_ASSOC);

    if (count($events) > 0) {
        echo "<div class='row row-cols-1 row-cols-md-3 g-4'>"; // Bootstrap grid system
        foreach ($events as $event) {
            $eventName = htmlspecialchars($event['event_name']);
            $startDate = htmlspecialchars($event['event_start_date']);
            $endDate = htmlspecialchars($event['event_end_date']);
            $location = htmlspecialchars($event['event_location']);
            $description = htmlspecialchars($event['event_description']);
            $schoolYear = htmlspecialchars($event['school_year']);
            $published = htmlspecialchars($event['published']);
            
            // Check if image exists; use placeholder if not
            $imagePath = $event['event_image'] ? '/new/sports_system/sms/main/' . htmlspecialchars($event['event_image']) : 'path/to/placeholder/image.jpg';
            // Debugging: Log the image path
            error_log("Image path for event {$eventName}: {$imagePath}");

            echo "
            <div class='col'>
                <div class='card h-100 shadow-sm event-card'>
                    <img src='{$imagePath}' class='card-img-top' alt='{$eventName}' style='height: 200px; object-fit: cover;'>
                    <div class='card-body'>
                        <h5 class='card-title text-center event-name'>{$eventName}</h5>
                        <p class='card-text'>{$description}</p>
                        <p class='card-text'><small class='text-muted'>Location: {$location}</small></p>
                        <p class='card-text'><small class='text-muted'>Start Date: {$startDate}</small></p>
                        <p class='card-text'><small class='text-muted'>End Date: {$endDate}</small></p>
                        <p class='card-text'><small class='text-muted'>School Year: {$schoolYear}</small></p>
                        <p class='card-text'><small class='text-muted'>Published: {$published}</small></p>
                        <div class='d-flex justify-content-between'>
                            <button class='btn btn-warning edit-event' data-id='{$event['event_id']}' data-name='{$eventName}'>Edit</button>
                            <button class='btn btn-danger delete-event' data-id='{$event['event_id']}' data-name='{$eventName}'>Delete</button>
                        </div>
                    </div>
                </div>
            </div>
            ";
        }
        echo "</div>"; // End of grid row
    } else {
        echo "<p>No events found.</p>";
    }
} catch (Exception $e) {
    echo "<p>Error: {$e->getMessage()}</p>";
}
