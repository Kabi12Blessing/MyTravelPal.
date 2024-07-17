<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require '../settings/connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profile_picture'])) {
    $userId = $_SESSION['user_id'];
    $uploadDir = __DIR__ . '/../uploads/profile_pictures/';
    
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $imageFileType = strtolower(pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION));
    $targetFile = $uploadDir . $userId . '.' . $imageFileType; // Save as user_id.file_extension

    // Validate the uploaded file
    $check = getimagesize($_FILES['profile_picture']['tmp_name']);
    if ($check === false) {
        die('File is not an image.');
    }

    if ($_FILES['profile_picture']['size'] > 5000000) {
        die('Sorry, your file is too large.');
    }

    $allowed_extensions = ["jpg", "jpeg", "png", "gif"];
    if (!in_array($imageFileType, $allowed_extensions)) {
        die('Sorry, only JPG, JPEG, PNG & GIF files are allowed.');
    }

    // Remove old file if it exists
    foreach (glob($uploadDir . $userId . '.*') as $file) {
        if (!unlink($file)) {
            die('Error removing old profile picture.');
        }
    }

    // Move the uploaded file to target location
    if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $targetFile)) {
        // Update database record
        $profilePicturePath = $targetFile;
        $updateSql = "UPDATE Users SET profile_picture = :profile_picture WHERE user_id = :user_id";
        try {
            $stmt = $conn->prepare($updateSql);
            $stmt->bindParam(':profile_picture', $profilePicturePath);
            $stmt->bindParam(':user_id', $userId);

            // Debugging before executing
            echo "Executing SQL: $updateSql <br>";
            echo "Profile Picture Path: $profilePicturePath <br>";
            echo "User ID: $userId <br>";

            if ($stmt->execute()) {
                // Confirm database update
                echo "Database updated successfully.<br>";

                // Redirect to the dashboard after successful upload
                header('Location: ../view/pages/Dashboard.php?success=profile_picture_updated');
                exit();
            } else {
                // Fetch error information
                $errorInfo = $stmt->errorInfo();
                echo 'Database update failed. Error: ' . $errorInfo[2] . "<br>";
                exit();
            }
        } catch (PDOException $e) {
            echo 'Query failed: ' . $e->getMessage();
            exit();
        }
    } else {
        $error = $_FILES['profile_picture']['error'];
        die("Sorry, there was an error uploading your file. Error code: $error");
    }
} else {
    die('Invalid request.');
}
?>
