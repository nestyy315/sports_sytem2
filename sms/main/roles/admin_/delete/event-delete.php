
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
    exit();
}

require_once __DIR__ . '/../../../database/database.class.php';

$conn = (new Database())->connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['event_id']) && !empty($_POST['event_id'])) {
        $event_id = intval($_POST['event_id']);

        try {
            // First, delete the event image file if it exists
            $imageQuery = $conn->prepare("SELECT event_image FROM events WHERE event_id = :event_id");
            $imageQuery->bindParam(':event_id', $event_id, PDO::PARAM_INT);
            $imageQuery->execute();
            $event = $imageQuery->fetch(PDO::FETCH_ASSOC);

            if ($event && !empty($event['event_image'])) {
                $imagePath = __DIR__ . '/../../../../uploads/events/' . $event['event_image'];
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            // Then delete the event from the database
            $query = $conn->prepare("DELETE FROM events WHERE event_id = :event_id");
            $query->bindParam(':event_id', $event_id, PDO::PARAM_INT);

            if ($query->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'Event deleted successfully.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to delete event.']);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Error deleting event: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid event ID provided.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>
