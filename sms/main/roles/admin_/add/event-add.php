<?php
require_once __DIR__ . '/../../../database/database.class.php';

$conn = (new Database())->connect();
$response = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Fetch data from the form
    $event_name = trim($_POST['event_name']);
    $event_start_date = trim($_POST['event_start_date']);
    $event_end_date = trim($_POST['event_end_date']);
    $event_location = trim($_POST['event_location']);
    $event_description = trim($_POST['event_description']);
    $school_year = trim($_POST['school_year']);
    $published = trim($_POST['published']);
    $imagePath = null;

    // Debugging: Log the contents of $_FILES and $_POST
    error_log("FILES array: " . print_r($_FILES, true));
    error_log("POST array: " . print_r($_POST, true));

    // Handle image upload
    if (isset($_FILES['event_image'])) {
        if ($_FILES['event_image']['error'] === 0) {
            $fileTmpPath = $_FILES['event_image']['tmp_name'];
            $fileName = uniqid() . '-' . basename($_FILES['event_image']['name']);
            $uploadDir = __DIR__ . '/../../../pictures/events/';
            $destPath = $uploadDir . $fileName;

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $imagePath = 'pictures/events/' . $fileName;

                if (file_exists($destPath)) {
                    error_log("File uploaded successfully to: {$destPath}");
                } else {
                    error_log("File not found at the expected location: {$destPath}");
                }
            } else {
                error_log("Error uploading image: Could not move the file.");
                $response = ['status' => 'error', 'message' => 'Error uploading image.'];
                echo json_encode($response);
                exit();
            }
        } else {
            $fileError = $_FILES['event_image']['error'];
            error_log("File upload error code: {$fileError}");
            $response = ['status' => 'error', 'message' => 'File upload error: ' . $fileError];
            echo json_encode($response);
            exit();
        }
    } else {
        error_log("No file was uploaded.");
        $response = ['status' => 'error', 'message' => 'No image was uploaded.'];
        echo json_encode($response);
        exit();
    }

    // Validate fields
    if (empty($event_name) || empty($event_start_date) || empty($event_end_date) || empty($event_location) || empty($event_description) || empty($school_year) || empty($published)) {
        error_log("Validation error: All fields are required.");
        $response = ['status' => 'error', 'message' => 'All fields are required.'];
        echo json_encode($response);
        exit();
    }

    try {
        // Insert event into the database
        $stmt = $conn->prepare("INSERT INTO events (event_name, event_start_date, event_end_date, event_location, event_image, event_description, school_year, published)
                                VALUES (:event_name, :event_start_date, :event_end_date, :event_location, :event_image, :event_description, :school_year, :published)");
        $stmt->bindParam(':event_name', $event_name);
        $stmt->bindParam(':event_start_date', $event_start_date);
        $stmt->bindParam(':event_end_date', $event_end_date);
        $stmt->bindParam(':event_location', $event_location);
        $stmt->bindParam(':event_image', $imagePath);
        $stmt->bindParam(':event_description', $event_description);
        $stmt->bindParam(':school_year', $school_year);
        $stmt->bindParam(':published', $published);

        if ($stmt->execute()) {
            error_log("Event added successfully: " . json_encode($response));
            $response = ['status' => 'success', 'message' => 'Event added successfully.'];
        } else {
            error_log("Database error: Failed to add event.");
            $response = ['status' => 'error', 'message' => 'Failed to add event.'];
        }
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        $response = ['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()];
    }
} else {
    error_log("Request method was not POST.");
}

echo json_encode($response);
?>
