<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '../../database/database.class.php';
$conn = (new Database())->connect();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $query = $conn->prepare("SELECT * FROM users WHERE username = :username");
    $query->bindParam(':username', $username);
    $query->execute();
    $user = $query->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['last_name'] = $user['last_name'];

        if ($user['role'] === 'student') {
            // Check if student record exists
            $query = $conn->prepare("SELECT * FROM student WHERE student_user_id = :user_id");
            $query->bindParam(':user_id', $user['user_id']);
            $query->execute();
            $student_profile = $query->fetch(PDO::FETCH_ASSOC);

            // Redirect to complete_profile.php if student profile does not exist or is incomplete
            if (!$student_profile || empty($student_profile['cor']) || empty($student_profile['id_image']) || empty($student_profile['medcert']) || empty($student_profile['sex']) || empty($student_profile['course']) || empty($student_profile['section']) || empty($student_profile['birthday']) || empty($student_profile['address']) || empty($student_profile['contact_no'])) {
                header("Location: complete_profile.php");
                exit();
            }
        }

        // Redirect to home page
        header("Location: ../../SMS/index.php");
        exit();
    } else {
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../../sms/css/login/signup.css">
    <style>
        .btn-sage-green {
            background-color: #193a19;
            color: white;
        }
        .btn-sage-green:hover {
            background-color: #132613;
            color: white;
        }
        body {
            background: #0a390a;
            background-size: 400% 400%;
            animation: gradientBackground 15s ease infinite;
            height: 100%;
            margin: 0;
        }

        @keyframes gradientBackground {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
    </style>
</head>
<body>
    <div class="container-fluid d-flex justify-content-center align-items-center vh-100">
        <div class="row w-100">
            <div class="col-md-6 d-flex flex-column justify-content-center align-items-center">
                <img src="final.png" alt="Logo" class="mb-4" style="max-width: 1000px; object-fit: contain;">
            </div>

            <div class="col-md-6 d-flex justify-content-center align-items-center">
                <div class="card shadow p-4" style="width: 100%; max-width: 400px;">
                    <h2 class="text-center mb-4">Login</h2>
                    <?php if ($error): ?>
                        <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>
                    <form method="post" action="">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-sage-green">Login</button>
                        </div>
                        <div class="mt-3 text-center">
                            <a href="signup.php">Don't have an account? Sign up</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
