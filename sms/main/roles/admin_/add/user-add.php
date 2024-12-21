<?php
require_once __DIR__ . '/../../../database/database.class.php';

$conn = (new Database())->connect();
$response = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $role = trim($_POST['role']);

    // Basic validation
    if (empty($username) || empty($password) || empty($first_name) || empty($last_name) || empty($email) || empty($role)) {
        $response = ['status' => 'error', 'message' => 'All fields are required.'];
        echo json_encode($response);
        exit();
    }

    // Basic email validation (checks for "@")
    if (!str_contains($email, '@')) {
        $response = ['status' => 'error', 'message' => 'Invalid email address. It must contain "@".'];
        echo json_encode($response);
        exit();
    }

    try {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $conn->prepare("
            INSERT INTO users (username, password, first_name, last_name, email, role, datetime_sign_up)
            VALUES (:username, :password, :first_name, :last_name, :email, :role, NOW())
        ");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':last_name', $last_name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':role', $role);

        if ($stmt->execute()) {
            $response = ['status' => 'success', 'message' => 'User added successfully.'];
        } else {
            $response = ['status' => 'error', 'message' => 'Failed to add user.'];
        }
    } catch (PDOException $e) {
        $response = ['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()];
    }
}

echo json_encode($response);
