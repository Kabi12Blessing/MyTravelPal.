<?php
require '../settings/connection.php'; // Make sure this path is correct for your setup

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['preference_id'])) {
    $preference_id = $_GET['preference_id'];

    try {
        // Fetch trip details from the database
        $stmt = $conn->prepare("SELECT * FROM Travel_Preferences WHERE preference_id = :id");
        $stmt->bindParam(':id', $preference_id, PDO::PARAM_INT);
        $stmt->execute();
        $trip = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($trip) {
            // Send trip data as JSON
            echo json_encode($trip);
        } else {
            // If no trip is found, send an empty JSON object
            echo json_encode([]);
        }
    } catch (PDOException $e) {
        // If there's an error, send a JSON with an error message
        echo json_encode(['error' => 'Query failed: ' . $e->getMessage()]);
    }
} else {
    // If the request method is not GET or preference_id is not set, send an error message
    echo json_encode(['error' => 'Invalid request']);
}
?>
