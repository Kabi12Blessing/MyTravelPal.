<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../settings/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $origin_country_id = $_POST['origin_country'];
    $destination_country_id = $_POST['destination_country'];
    $travel_date = $_POST['departure'];
    $return_date = $_POST['return'];
    $budget = $_POST['budget'];
    $has_extra_space = $_POST['space'] === 'has_extra_space' ? 1 : 0;
    $needs_space = $_POST['space'] === 'needs_extra_space' ? 1 : 0;
    $description = $_POST['description'];
    $number_of_travelers = $_POST['travelers'];
    $accommodation_type = $_POST['accommodation'];
    $preferred_gender = $_POST['gender'];

    // Handle file upload
    $target_dir = __DIR__ . "/../uploads/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $image_paths = [];
    $uploadOk = 1;
    $allowed_extensions = ["jpg", "jpeg", "png", "gif"];

    foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
        $file_name = basename($_FILES["images"]["name"][$key]);
        $target_file = $target_dir . $file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if image file is an actual image or fake image
        $check = getimagesize($tmp_name);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            echo "File is not an image.";
            $uploadOk = 0;
        }

        // Check file size
        if ($_FILES["images"]["size"][$key] > 500000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        // Allow certain file formats
        if (!in_array($imageFileType, $allowed_extensions)) {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
        } else {
            if (move_uploaded_file($tmp_name, $target_file)) {
                $image_paths[] = $target_file;
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
    }

    // Convert image paths array to JSON for storage
    $image_paths_json = json_encode($image_paths);

    // Prepare SQL statement
    $sql = "INSERT INTO Travel_Preferences 
            (user_id, origin_country_id, destination_country_id, travel_date, return_date, budget, has_extra_space, needs_space, description, number_of_travelers, accommodation_type, preferences, image_path)
            VALUES 
            (:user_id, :origin_country_id, :destination_country_id, :travel_date, :return_date, :budget, :has_extra_space, :needs_space, :description, :number_of_travelers, :accommodation_type, :preferred_gender, :image_path)";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':origin_country_id', $origin_country_id);
    $stmt->bindParam(':destination_country_id', $destination_country_id);
    $stmt->bindParam(':travel_date', $travel_date);
    $stmt->bindParam(':return_date', $return_date);
    $stmt->bindParam(':budget', $budget);
    $stmt->bindParam(':has_extra_space', $has_extra_space);
    $stmt->bindParam(':needs_space', $needs_space);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':number_of_travelers', $number_of_travelers);
    $stmt->bindParam(':accommodation_type', $accommodation_type);
    $stmt->bindParam(':preferred_gender', $preferred_gender);
    $stmt->bindParam(':image_path', $image_paths_json);

    // Execute the statement
    if ($stmt->execute()) {
        header("Location: ../view/pages/dashboard.php");
        exit();
    } else {
        echo "Error: " . $stmt->errorInfo()[2];
    }
}
?>
