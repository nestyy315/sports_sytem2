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

// Fetch sports for the page
$query = $conn->prepare("SELECT s.sport_id, s.sport_name, s.sport_description, s.sport_date, s.sport_time, s.sport_location, e.event_name, CONCAT(f.first_name, ' ', f.last_name) AS facilitator_name, CONCAT(c.first_name, ' ', c.last_name) AS coach_name
    FROM sports s
    JOIN events e ON s.event_id = e.event_id
    JOIN users f ON s.user_id = f.user_id
    JOIN users c ON s.coach_user_id = c.user_id
    ORDER BY s.sport_name ASC");
$query->execute();
$sports = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard - Sports</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/SMS/css/style.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Available Sports</h2>

        <!-- Sports Table -->
        <div class="table-responsive">
            <table id="sports_table" class="table table-hover align-middle table-bordered rounded-3 overflow-hidden shadow">
                <thead class="table-primary">
                    <tr class="text-center">
                        <th>Sport Name</th>
                        <th>Event</th>
                        <th>Facilitator</th>
                        <th>Coach</th>
                        <th>Description</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Location</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sports as $sport): ?>
                        <tr>
                            <td class="sport-name"><?= htmlspecialchars($sport['sport_name'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="event-name"><?= htmlspecialchars($sport['event_name'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="facilitator-name"><?= htmlspecialchars($sport['facilitator_name'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="coach-name"><?= htmlspecialchars($sport['coach_name'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="sport-description"><?= htmlspecialchars($sport['sport_description'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="sport-date"><?= htmlspecialchars($sport['sport_date'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="sport-time"><?= htmlspecialchars($sport['sport_time'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="sport-location"><?= htmlspecialchars($sport['sport_location'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="text-center">
                                <button type="button" class="btn btn-success btn-sm register-tryout-btn px-3" data-sport-id="<?= $sport['sport_id'] ?>">Register</button>
                                <button type="button" class="btn btn-danger btn-sm cancel-tryout-btn px-3" data-sport-id="<?= $sport['sport_id'] ?>">Cancel</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelectorAll('.register-tryout-btn').forEach(button => {
            button.addEventListener('click', function () {
                const sportId = this.dataset.sportId;

                fetch('../main/roles/student_/register_tryout.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'register_tryout', sport_id: sportId }),
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        alert(result.message);
                        location.reload();
                    } else {
                        alert(result.message || 'Failed to register for tryout.');
                    }
                })
                .catch(error => console.error('Error:', error));
            });
        });

        document.querySelectorAll('.cancel-tryout-btn').forEach(button => {
            button.addEventListener('click', function () {
                const sportId = this.dataset.sportId;

                fetch('../main/roles/student_/register_tryout.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'cancel_tryout', sport_id: sportId }),
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        alert(result.message);
                        location.reload();
                    } else {
                        alert(result.message || 'Failed to cancel the tryout registration.');
                    }
                })
                .catch(error => console.error('Error:', error));
            });
        });
    </script>
</body>
</html>
