<?php
require '../settings/connection.php'; // Include your database configuration here

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['trip_id'])) {
    $trip_id = $_GET['trip_id'];
    // Delete trip from the database
    $stmt = $conn->prepare("DELETE FROM trips WHERE id = :id");
    $stmt->bindParam(':id', $trip_id, PDO::PARAM_INT);
    $stmt->execute();

    header("Location: ../view/pages/upcoming_trips.php");
    exit();
}
?>
