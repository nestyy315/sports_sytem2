
<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
    exit();
}

require_once __DIR__ . '/../../../database/database.class.php';

$conn = (new Database())->connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_id = $_POST['event_id'];
    $event_name = trim($_POST['event_name']);
    $event_start_date = trim($_POST['event_start_date']);
    $event_end_date = trim($_POST['event_end_date']);
    $event_location = trim($_POST['event_location']);
    $event_description = trim($_POST['event_description']);
    $school_year = trim($_POST['school_year']);

    if (empty($event_name) || empty($event_start_date) || empty($event_end_date) || empty($event_location) || empty($school_year)) {
        echo json_encode(['status' => 'error', 'message' => 'All required fields must be filled.']);
        exit();
    }

    try {
        $query = $conn->prepare("
            UPDATE events 
            SET event_name = :event_name, 
                event_start_date = :event_start_date, 
                event_end_date = :event_end_date, 
                event_location = :event_location, 
                event_description = :event_description, 
                school_year = :school_year 
            WHERE event_id = :event_id
        ");
        
        $query->bindParam(':event_name', $event_name);
        $query->bindParam(':event_start_date', $event_start_date);
        $query->bindParam(':event_end_date', $event_end_date);
        $query->bindParam(':event_location', $event_location);
        $query->bindParam(':event_description', $event_description);
        $query->bindParam(':school_year', $school_year);
        $query->bindParam(':event_id', $event_id, PDO::PARAM_INT);

        if ($query->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Event updated successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update event.']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
    }
}
?>
