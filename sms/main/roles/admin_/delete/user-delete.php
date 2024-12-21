<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

require_once __DIR__ . '/../../../database/database.class.php';

$conn = (new Database())->connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['user_id']) && !empty($_POST['user_id'])) {
        $user_id = intval($_POST['user_id']);

        try {
            $query = $conn->prepare("DELETE FROM users WHERE user_id = :user_id");
            $query->bindParam(':user_id', $user_id, PDO::PARAM_INT);

            if ($query->execute()) {
                echo json_encode(['success' => true, 'message' => 'User deleted successfully.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete user.']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error deleting user: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid user ID provided.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
    