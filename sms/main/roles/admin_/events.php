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
    <title>Event Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card-header {
            background-color: #198754;
            color: white;
            font-weight: bold;
        }
        .card-body {
            font-size: 14px;
        }
        .card-img-top {
            object-fit: cover;
            height: 200px;
        }
        .btn-create-event {
            background-color: #198754;
            color: white;
        }
    </style>
</head>
<body>
<div class="container mt-4">
    <h2>Event Management</h2>
    <div class="d-flex flex-wrap align-items-center gap-3 mb-3">
        <input type="text" id="search_bar" class="form-control w-auto" placeholder="Search events..." onkeyup="searchEvent()">
        <button class="btn btn-primary px-4 shadow-sm" onclick="sortEvents()">Sort Alphabetically</button>
        <button class="btn btn-create-event btn-success" id="add-event">Create Event</button>
    </div>

    <div class="row" id="events_container">
        <!-- Populated via JavaScript -->
    </div>
</div>

<!-- Placeholder for dynamically loaded modals -->
<div class="modal-container"></div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Link to the external JavaScript file for events -->
<script src="../MAIN/roles/admin_/js/event.js"></script>

</body>
</html>
