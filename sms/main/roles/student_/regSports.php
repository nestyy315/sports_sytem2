<?php
ini_set('display_errors', 0);
ini_set('log_errors', 1); 
error_reporting(E_ALL);

require_once '../MAIN/database/database.class.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$conn = (new Database())->connect();

if (!isset($_SESSION['user_id'])) {
    die("Access denied. Please log in first.");
}

$student_id = $_SESSION['user_id'];

// Fetch user's tryout applications
$tryoutQuery = $conn->prepare("
    SELECT t.*, s.sport_name 
    FROM tryouts t 
    JOIN sports s ON t.sport_id = s.sport_id 
    WHERE t.student_id = :student_id
");
$tryoutQuery->bindParam(':student_id', $student_id);
$tryoutQuery->execute();
$userTryouts = $tryoutQuery->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Tryout Applications</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class="my-4">
        <h3>My Tryout Applications</h3>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Sport</th>
                        <th>Status</th>
                        <th>Application Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($userTryouts as $tryout): ?>
                        <tr>
                            <td><?= htmlspecialchars($tryout['sport_name']) ?></td>
                            <td>
                                <span class="badge bg-<?= $tryout['status'] === 'pending' ? 'warning' : 
                                                      ($tryout['status'] === 'approved' ? 'success' : 'danger') ?>">
                                    <?= ucfirst(htmlspecialchars($tryout['status'])) ?>
                                </span>
                            </td>
                            <td><?= date('F j, Y', strtotime($tryout['created_at'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
