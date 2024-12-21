<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . '../../database/database.class.php';
$conn = (new Database())->connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['skip'])) {
        // Redirect to the home page when Skip button is clicked
        header("Location: ../../SMS/index.php");
        exit();
    }

    $user_id = $_SESSION['user_id'];
    $sex = $_POST['sex'] ?? null;
    $course = $_POST['course'] ?? null;
    $section = $_POST['section'] ?? null;
    $birthday = $_POST['birthday'] ?? null;
    $address = $_POST['address'] ?? null;
    $contact_no = $_POST['contact_no'] ?? null;

    $cor = !empty($_FILES['cor']['tmp_name']) ? file_get_contents($_FILES['cor']['tmp_name']) : null;
    $id_image = !empty($_FILES['id_image']['tmp_name']) ? file_get_contents($_FILES['id_image']['tmp_name']) : null;
    $medcert = !empty($_FILES['medcert']['tmp_name']) ? file_get_contents($_FILES['medcert']['tmp_name']) : null;

    // Insert or update student profile information in the student table
    $query = $conn->prepare("INSERT INTO student (student_user_id, sex, course, section, birthday, address, contact_no, cor, id_image, medcert) 
        VALUES (:user_id, :sex, :course, :section, :birthday, :address, :contact_no, :cor, :id_image, :medcert) 
        ON DUPLICATE KEY UPDATE 
        sex = :sex, course = :course, section = :section, birthday = :birthday, address = :address, contact_no = :contact_no, 
        cor = :cor, id_image = :id_image, medcert = :medcert");

    $query->bindParam(':user_id', $user_id);
    $query->bindParam(':sex', $sex);
    $query->bindParam(':course', $course);
    $query->bindParam(':section', $section);
    $query->bindParam(':birthday', $birthday);
    $query->bindParam(':address', $address);
    $query->bindParam(':contact_no', $contact_no);
    $query->bindParam(':cor', $cor, PDO::PARAM_LOB);
    $query->bindParam(':id_image', $id_image, PDO::PARAM_LOB);
    $query->bindParam(':medcert', $medcert, PDO::PARAM_LOB);

    if ($query->execute()) {
        header("Location: ../../SMS/index.php");
        exit();
    } else {
        $error = "Error saving your profile. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Your Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card p-4" style="width: 100%; max-width: 600px;">
            <h2 class="text-center mb-4">Complete Your Profile</h2>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form method="post" action="" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="sex" class="form-label">Sex</label>
                    <select class="form-control" id="sex" name="sex" >
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="course" class="form-label">Course</label>
                    <input type="text" class="form-control" id="course" name="course" >
                </div>
                <div class="mb-3">
                    <label for="section" class="form-label">Section</label>
                    <input type="text" class="form-control" id="section" name="section" >
                </div>
                <div class="mb-3">
                    <label for="birthday" class="form-label">Birthday</label>
                    <input type="date" class="form-control" id="birthday" name="birthday" >
                </div>
                <div class="mb-3">
                    <label for="address" class="form-label">Address</label>
                    <textarea class="form-control" id="address" name="address" ></textarea>
                </div>
                <div class="mb-3">
                    <label for="contact_no" class="form-label">Contact Info</label>
                    <input type="text" class="form-control" id="contact_no" name="contact_no" >
                </div>
                <div class="mb-3">
                    <label for="cor" class="form-label">Certificate of Registration (COR)</label>
                    <input type="file" class="form-control" id="cor" name="cor" >
                </div>
                <div class="mb-3">
                    <label for="id_image" class="form-label">ID Image</label>
                    <input type="file" class="form-control" id="id_image" name="id_image" >
                </div>
                <div class="mb-3">
                    <label for="medcert" class="form-label">Medical Certificate</label>
                    <input type="file" class="form-control" id="medcert" name="medcert" >
                </div>
                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-primary">Save Profile</button>
                </div>
                <div class="d-grid">
                    <button type="submit" name="skip" class="btn btn-secondary">Skip</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
