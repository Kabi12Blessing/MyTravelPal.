<?php
require '../settings/connection.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log the entire POST array to the error log for debugging
error_log(print_r($_POST, true));

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['preference_id'])) {
    $preference_id = $_POST['preference_id'];
    $destination = isset($_POST['destination_country']) && is_numeric($_POST['destination_country']) ? (int)$_POST['destination_country'] : null;
    $origin = isset($_POST['origin_country']) && is_numeric($_POST['origin_country']) ? (int)$_POST['origin_country'] : null;
    $departure_date = $_POST['departure_date'] ?? ''; // Ensure this matches the form name attribute
    $return_date = $_POST['return_date'] ?? ''; // Ensure this matches the form name attribute
    $description = $_POST['description'] ?? '';
    $budget = $_POST['budget'] ?? '';
    $travelers = $_POST['number_of_travelers'] ?? '';
    $accommodation = $_POST['accommodation_type'] ?? '';
    $has_extra_space = isset($_POST['space']) && $_POST['space'] == 'has_extra_space' ? 1 : 0;
    $needs_space = isset($_POST['space']) && $_POST['space'] == 'needs_extra_space' ? 1 : 0;
    $preferred_gender = $_POST['gender'] ?? ''; // Ensure this matches the form name attribute

    // Validate the presence of all required fields
    if ($destination === null || $origin === null || empty($departure_date) || empty($return_date) || empty($budget) || empty($travelers)) {
        echo "All required fields must be filled out and valid.";
        exit();
    }

    try {
        $stmt = $conn->prepare("UPDATE Travel_Preferences SET 
            destination_country_id = :destination, 
            origin_country_id = :origin, 
            travel_date = :departure_date, 
            return_date = :return_date, 
            description = :description, 
            budget = :budget, 
            number_of_travelers = :travelers, 
            accommodation_type = :accommodation, 
            has_extra_space = :has_extra_space, 
            needs_space = :needs_space, 
            preferences = :preferred_gender 
            WHERE preference_id = :id");

        $stmt->bindParam(':destination', $destination, PDO::PARAM_INT);
        $stmt->bindParam(':origin', $origin, PDO::PARAM_INT);
        $stmt->bindParam(':departure_date', $departure_date);
        $stmt->bindParam(':return_date', $return_date);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':budget', $budget);
        $stmt->bindParam(':travelers', $travelers);
        $stmt->bindParam(':accommodation', $accommodation);
        $stmt->bindParam(':has_extra_space', $has_extra_space, PDO::PARAM_BOOL);
        $stmt->bindParam(':needs_space', $needs_space, PDO::PARAM_BOOL);
        $stmt->bindParam(':preferred_gender', $preferred_gender);
        $stmt->bindParam(':id', $preference_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo "success";
        } else {
            echo "Failed to update trip. Please check your data and try again.";
        }
    } catch (PDOException $e) {
        echo "Failed to update trip: " . $e->getMessage();
    }
} else {
    echo "Invalid request. Please provide the necessary information.";
}
?>
