<?php
require_once __DIR__ . '/../../../database/database.class.php';

$conn = (new Database())->connect();

try {
    $query = $conn->prepare("SELECT user_id, username, first_name, last_name, datetime_sign_up, datetime_last_online, role FROM users");
    $query->execute();
    $users = $query->fetchAll(PDO::FETCH_ASSOC);

    if (count($users) > 0) {
        foreach ($users as $user) {
            // Sanitize output to prevent XSS
            $username = htmlspecialchars($user['username']);
            $role = htmlspecialchars($user['role']);
            $firstName = htmlspecialchars($user['first_name']);
            $lastName = htmlspecialchars($user['last_name']);
            $signUp = htmlspecialchars($user['datetime_sign_up']);
            $lastOnline = htmlspecialchars($user['datetime_last_online']);

            echo "
                <tr>
                    <td class='username'>{$username}</td>
                    <td class='role'>{$role}</td>
                    <td>{$firstName}</td>
                    <td>{$lastName}</td>
                    <td>{$signUp}</td>
                    <td>{$lastOnline}</td>
                    <td>
                        <button class='btn btn-warning edit-user' data-id='{$user['user_id']}' data-username='{$username}'>Edit</button>
                        <button class='btn btn-danger delete-user' data-id='{$user['user_id']}' data-username='{$username}'>Delete</button>
                    </td>
                </tr>
            ";
        }
    } else {
        echo "<tr><td colspan='7'>No users found.</td></tr>";
    }
} catch (Exception $e) {
    echo "<tr><td colspan='7'>Error: {$e->getMessage()}</td></tr>";
}
?>
