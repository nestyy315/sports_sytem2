<?php
require_once __DIR__ . '/../../../database/database.class.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $conn = (new Database())->connect();
    $user_id = $_GET['id'];

    try {
        $query = $conn->prepare("SELECT * FROM users WHERE user_id = :user_id");
        $query->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $query->execute();
        $user = $query->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            echo json_encode($user);
        } else {
            echo json_encode(['error' => 'User not found.']);
        }
    } catch (Exception $e) {
        echo json_encode(['error' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Invalid request.']);
}
?>
