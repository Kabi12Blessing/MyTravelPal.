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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $originCountry = $_POST['origin_country'];
    $destinationCountry = $_POST['destination_country'];
    $departureDate = $_POST['departure'];
    $returnDate = $_POST['return'];
    $description = $_POST['description'];
    $budget = $_POST['budget'];
    $travelers = $_POST['travelers'];
    $accommodation = $_POST['accommodation'];
    $hasExtraSpace = isset($_POST['space']) && $_POST['space'] == 'has_extra_space' ? 1 : 0;
    $needsSpace = isset($_POST['space']) && $_POST['space'] == 'needs_extra_space' ? 1 : 0;
    $preferredGender = $_POST['gender'];

    // Validate inputs
    $errors = [];
    $today = date('Y-m-d');

    if (empty($originCountry) || empty($destinationCountry) || empty($departureDate) || empty($returnDate) || empty($travelers)) {
        $errors[] = 'All fields are required.';
    }
    if ($originCountry === $destinationCountry) {
        $errors[] = 'Origin and destination cannot be the same.';
    }
    if ($departureDate < $today) {
        $errors[] = 'The departure date cannot be in the past.';
    }
    if ($returnDate < $departureDate) {
        $errors[] = 'The return date cannot be before the departure date.';
    }
    if ($budget <= 0) {
        $errors[] = 'Budget must be a positive number.';
    }
    if ($travelers < 1) {
        $errors[] = 'There must be at least one traveler.';
    }

    if (!empty($errors)) {
        // Redirect back with errors
        header('Location: ../view/pages/Dashboard.php?error=' . urlencode(implode(', ', $errors)));
        exit();
    }

    // Insert trip data into the database
    $sql = "INSERT INTO Travel_Preferences (user_id, origin_country_id, destination_country_id, travel_date, return_date, description, budget, number_of_travelers, accommodation_type, has_extra_space, needs_space, preferences) 
            VALUES (:user_id, :origin, :destination, :departure_date, :return_date, :description, :budget, :travelers, :accommodation, :has_extra_space, :needs_space, :preferred_gender)";

    try {
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':user_id', $_SESSION['user_id']);
        $stmt->bindParam(':origin', $originCountry);
        $stmt->bindParam(':destination', $destinationCountry);
        $stmt->bindParam(':departure_date', $departureDate);
        $stmt->bindParam(':return_date', $returnDate);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':budget', $budget);
        $stmt->bindParam(':travelers', $travelers);
        $stmt->bindParam(':accommodation', $accommodation);
        $stmt->bindParam(':has_extra_space', $hasExtraSpace);
        $stmt->bindParam(':needs_space', $needsSpace);
        $stmt->bindParam(':preferred_gender', $preferredGender);
        $stmt->execute();
        
        header('Location: ../view/pages/Dashboard.php?success=trip_created');
        exit();
    } catch (PDOException $e) {
        error_log('Query failed: ' . $e->getMessage()); // Log the error message
        echo 'Error: ' . $e->getMessage(); // Optionally display the error
        exit();
    }
} else {
    header('Location: ../view/pages/Dashboard.php');
    exit();
}
?>
