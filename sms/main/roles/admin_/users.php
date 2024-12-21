<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .table-primary thead th {
            font-weight: bold;
            color: #1e90ff;
            text-align: center;
            background-color: #d1ecf1;
        }
        .btn-create-user {
            background-color: #198754;
            color: white;
        }
    </style>
</head>
<body>
<div class="container mt-4">
    <h2>User Management</h2>
    <div class="d-flex flex-wrap align-items-center gap-3 mb-3">
        <input type="text" id="search_bar" class="form-control w-auto" placeholder="Search usernames..." onkeyup="searchUser()">
        <select id="role_filter" class="form-select w-auto" onchange="filterRole()">
        <option value="">All Roles</option>
        <option value="student">Student</option>
        <option value="coach">Coach</option>
        <option value="facilitator">Facilitator</option>
        <option value="moderator">Moderator</option>
        <option value="admin">Admin</option>
        </select>

        <button class="btn btn-primary px-4 shadow-sm" onclick="sortTable()">Sort Alphabetically</button>
        <button class="btn btn-create-user btn-success" id="add-user">Create User</button>
    </div>

    <div class="table-responsive">
        <table id="users_table" class="table table-hover align-middle table-bordered rounded-3 overflow-hidden shadow">
            <thead class="table-primary">
                <tr class="text-center">
                    <th>Username</th>
                    <th>Role</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Sign Up</th>
                    <th>Last Online</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="users_table_body">
                <!-- Populated via JavaScript -->
            </tbody>
        </table>
    </div>
</div>

<!-- Placeholder for dynamically loaded modals -->
<div class="modal-container"></div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Link to the external JavaScript file -->
<script src="../MAIN/roles/admin_/js/user.js"></script>

</body>
</html>
