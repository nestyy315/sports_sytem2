<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SESSION['role'] !== 'student') {
    header('Location: login.php');
    exit();
}

require_once __DIR__ . '/../../database/database.class.php';
$conn = (new Database())->connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $input = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'];
    $sport_id = $input['sport_id'];
    $student_id = $_SESSION['user_id'];

    if (empty($sport_id)) {
        echo json_encode(['success' => false, 'message' => 'Sport ID is required.']);
        exit();
    }

    try {
        if ($action === 'register_tryout') {
            $query = $conn->prepare("INSERT INTO tryouts (student_id, sport_id) VALUES (:student_id, :sport_id)");
            $query->bindParam(':student_id', $student_id);
            $query->bindParam(':sport_id', $sport_id);

            if ($query->execute()) {
                echo json_encode(['success' => true, 'message' => 'Successfully registered for tryout.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to register for tryout.']);
            }
        }

        if ($action === 'cancel_tryout') {
            $query = $conn->prepare("DELETE FROM tryouts WHERE student_id = :student_id AND sport_id = :sport_id");
            $query->bindParam(':student_id', $student_id);
            $query->bindParam(':sport_id', $sport_id);

            if ($query->execute()) {
                echo json_encode(['success' => true, 'message' => 'Successfully cancelled the tryout registration.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to cancel the tryout registration.']);
            }
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
}
?>
