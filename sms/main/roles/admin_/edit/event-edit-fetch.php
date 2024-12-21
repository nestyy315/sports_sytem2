<?php
require_once __DIR__ . '/../../../database/database.class.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $conn = (new Database())->connect();
    $event_id = $_GET['id'];

    try {
        $query = $conn->prepare("SELECT * FROM events WHERE event_id = :event_id");
        $query->bindParam(':event_id', $event_id, PDO::PARAM_INT);
        $query->execute();
        $event = $query->fetch(PDO::FETCH_ASSOC);

        if ($event) {
            echo json_encode($event);
        } else {
            echo json_encode(['error' => 'Event not found.']);
        }
    } catch (Exception $e) {
        echo json_encode(['error' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Invalid request.']);
}
?>
