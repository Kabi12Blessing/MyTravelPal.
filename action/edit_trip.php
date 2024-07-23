<?php
require '../settings/connection.php'; // Include your database configuration here

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['trip_id'])) {
    $trip_id = $_GET['trip_id'];
    // Fetch trip details from the database
    $stmt = $conn->prepare("SELECT * FROM trips WHERE id = :id");
    $stmt->bindParam(':id', $trip_id, PDO::PARAM_INT);
    $stmt->execute();
    $trip = $stmt->fetch();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['trip_id'])) {
    $trip_id = $_POST['trip_id'];
    $destination = $_POST['destination'];
    $origin = $_POST['origin'];
    $departure_date = $_POST['departure_date'];
    $return_date = $_POST['return_date'];
    $description = $_POST['description'];
    $budget = $_POST['budget'];
    $travelers = $_POST['travelers'];
    $accommodation = $_POST['accommodation'];
    $has_extra_space = $_POST['has_extra_space'];
    $needs_space = $_POST['needs_space'];
    $preferred_gender = $_POST['preferred_gender'];

    // Update trip details in the database
    $stmt = $conn->prepare("UPDATE trips SET destination=:destination, origin=:origin, departure_date=:departure_date, return_date=:return_date, description=:description, budget=:budget, travelers=:travelers, accommodation=:accommodation, has_extra_space=:has_extra_space, needs_space=:needs_space, preferred_gender=:preferred_gender WHERE id=:id");
    $stmt->bindParam(':destination', $destination);
    $stmt->bindParam(':origin', $origin);
    $stmt->bindParam(':departure_date', $departure_date);
    $stmt->bindParam(':return_date', $return_date);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':budget', $budget);
    $stmt->bindParam(':travelers', $travelers);
    $stmt->bindParam(':accommodation', $accommodation);
    $stmt->bindParam(':has_extra_space', $has_extra_space);
    $stmt->bindParam(':needs_space', $needs_space);
    $stmt->bindParam(':preferred_gender', $preferred_gender);
    $stmt->bindParam(':id', $trip_id, PDO::PARAM_INT);
    $stmt->execute();

    header("Location: ../view/pages/upcoming_trips.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Trip</title>
</head>
<body>
    <h2>Edit Trip</h2>
    <form action="edit_trip.php" method="post">
        <input type="hidden" name="trip_id" value="<?= htmlspecialchars($trip['id']) ?>">
        <label>Destination: <input type="text" name="destination" value="<?= htmlspecialchars($trip['destination']) ?>" required></label><br>
        <label>Origin: <input type="text" name="origin" value="<?= htmlspecialchars($trip['origin']) ?>" required></label><br>
        <label>Departure Date: <input type="date" name="departure_date" value="<?= htmlspecialchars($trip['departure_date']) ?>" required></label><br>
        <label>Return Date: <input type="date" name="return_date" value="<?= htmlspecialchars($trip['return_date']) ?>" required></label><br>
        <label>Description: <textarea name="description" required><?= htmlspecialchars($trip['description']) ?></textarea></label><br>
        <label>Budget: <input type="number" name="budget" value="<?= htmlspecialchars($trip['budget']) ?>" required></label><br>
        <label>Travelers: <input type="number" name="travelers" value="<?= htmlspecialchars($trip['travelers']) ?>" required></label><br>
        <label>Accommodation: <input type="text" name="accommodation" value="<?= htmlspecialchars($trip['accommodation']) ?>" required></label><br>
        <label>Has Extra Space: <input type="text" name="has_extra_space" value="<?= htmlspecialchars($trip['has_extra_space']) ?>" required></label><br>
        <label>Needs Space: <input type="text" name="needs_space" value="<?= htmlspecialchars($trip['needs_space']) ?>" required></label><br>
        <label>Preferred Gender: <input type="text" name="preferred_gender" value="<?= htmlspecialchars($trip['preferred_gender']) ?>" required></label><br>
        <button type="submit">Save</button>
    </form>
</body>
</html>
