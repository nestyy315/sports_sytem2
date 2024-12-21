<?php
require_once '../functions/user.class.php';
session_start();

$signupErr = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirm-password']);
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $role = 'student'; // Default role is student

    // Validation for passwords
    if ($password !== $confirmPassword) {
        $signupErr = 'Passwords do not match.';
    } elseif (strlen($password) < 8) {
        $signupErr = 'Password must be at least 8 characters long.';
    } else {
        $user = new User();
        if ($user->fetch($username)) {
            $signupErr = 'Username already exists. Please choose another.';
        } else {
            // Add first_name and last_name to the signup process
            if ($user->signup($username, $password, $role, $email, $first_name, $last_name)) {
                header("Location: login.php");
                exit();
            } else {
                $signupErr = 'Error during registration. Please try again.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
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
                    <h2 class="text-center mb-4">Sign Up</h2>

                    <?php if ($signupErr): ?>
                        <div class="alert alert-danger text-center"><?= htmlspecialchars($signupErr) ?></div>
                    <?php endif; ?>
                    <form action="signup.php" method="post" id="signupForm">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" >
                        </div>
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" >
                        </div>
                        <div class="mb-3">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" >
                        </div>
                        <div class="mb-3">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" >
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" >
                        </div>
                        <div class="mb-3">
                            <label for="confirm-password" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="confirm-password" name="confirm-password" >
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-sage-green">Sign Up</button>
                        </div>
                        <div class="mt-3 text-center">
                            <a href="login.php">Already have an account? Log in</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
