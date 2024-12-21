<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
    exit();
}

require_once __DIR__ . '/../../../database/database.class.php';

$conn = (new Database())->connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $username = trim($_POST['username']);
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $role = trim($_POST['role']);

    if (empty($username) || empty($first_name) || empty($last_name) || empty($email) || empty($role)) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
        exit();
    }


    try {
        $query = $conn->prepare("
            UPDATE users 
            SET username = :username, first_name = :first_name, last_name = :last_name, email = :email, role = :role 
            WHERE user_id = :user_id
        ");
        $query->bindParam(':username', $username);
        $query->bindParam(':first_name', $first_name);
        $query->bindParam(':last_name', $last_name);
        $query->bindParam(':email', $email);
        $query->bindParam(':role', $role);
        $query->bindParam(':user_id', $user_id, PDO::PARAM_INT);

        if ($query->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'User updated successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update user.']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
    }
}
?>
