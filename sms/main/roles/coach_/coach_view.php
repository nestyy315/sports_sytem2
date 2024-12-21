<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SESSION['role'] !== 'coach') {
    header('Location: login.php');
    exit();
}

require_once __DIR__ . '/../../database/database.class.php';
$conn = (new Database())->connect();
$coach_id = $_SESSION['user_id'];

// Fetch tryouts for the coach
$query = $conn->prepare("SELECT t.tryout_id, s.sport_name, e.event_name, CONCAT(u.first_name, ' ', u.last_name) AS student_name, u.email, u.contact_info, t.tryout_date
    FROM tryouts t
    JOIN sports s ON t.sport_id = s.sport_id
    JOIN events e ON s.event_id = e.event_id
    JOIN users u ON t.student_id = u.user_id
    WHERE s.coach_user_id = :coach_id
    ORDER BY t.tryout_date ASC");
$query->bindParam(':coach_id', $coach_id);
$query->execute();
$tryouts = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Coach Dashboard - Tryouts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/SMS/css/style.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Tryouts</h2>

        <!-- Tryouts Table -->
        <div class="table-responsive">
            <table id="tryouts_table" class="table table-hover align-middle table-bordered rounded-3 overflow-hidden shadow">
                <thead class="table-primary">
                    <tr class="text-center">
                        <th>Sport</th>
                        <th>Event</th>
                        <th>Student Name</th>
                        <th>Email</th>
                        <th>Contact Info</th>
                        <th>Tryout Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tryouts as $tryout): ?>
                        <tr>
                            <td class="sport-name"><?= htmlspecialchars($tryout['sport_name'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="event-name"><?= htmlspecialchars($tryout['event_name'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="student-name"><?= htmlspecialchars($tryout['student_name'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="student-email"><?= htmlspecialchars($tryout['email'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="contact-info"><?= htmlspecialchars($tryout['contact_info'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="tryout-date"><?= htmlspecialchars($tryout['tryout_date'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="text-center">
                                <button type="button" class="btn btn-warning btn-sm edit-tryout-btn px-3" data-tryout-id="<?= $tryout['tryout_id'] ?>">Edit</button>
                                <button type="button" class="btn btn-danger btn-sm cancel-tryout-btn px-3" data-tryout-id="<?= $tryout['tryout_id'] ?>">Cancel</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelectorAll('.cancel-tryout-btn').forEach(button => {
            button.addEventListener('click', function () {
                const tryoutId = this.dataset.tryoutId;

                fetch('../main/roles/coach_/cancel_tryout.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ tryout_id: tryoutId }),
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        alert(result.message);
                        location.reload();
                    } else {
                        alert(result.message || 'Failed to cancel tryout.');
                    }
                })
                .catch(error => console.error('Error:', error));
            });
        });

        document.querySelectorAll('.edit-tryout-btn').forEach(button => {
            button.addEventListener('click', function () {
                const tryoutId = this.dataset.tryoutId;

                fetch('../main/roles/coach_/edit_tryout.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ tryout_id: tryoutId }),
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        alert(result.message);
                        location.reload();
                    } else {
                        alert(result.message || 'Failed to edit tryout.');
                    }
                })
                .catch(error => console.error('Error:', error));
            });
        });
    </script>
</body>
</html>
