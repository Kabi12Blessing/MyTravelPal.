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
    $preference_id = $_POST['preference_id'];
    $message_text = $_POST['message'];
    $sender_id = $_SESSION['user_id'];
    
    // Fetch the receiver's user_id based on preference_id
    $receiver_id = null;
    $sql = "SELECT user_id FROM Travel_Preferences WHERE preference_id = :preference_id";
    try {
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':preference_id', $preference_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $receiver_id = $result['user_id'];
        }
    } catch (PDOException $e) {
        echo 'Query failed: ' . $e->getMessage();
        exit();
    }

    if ($receiver_id) {
        $sql = "INSERT INTO Messages (sender_id, receiver_id, message_text, preference_id) VALUES (:sender_id, :receiver_id, :message_text, :preference_id)";
        try {
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':sender_id', $sender_id);
            $stmt->bindParam(':receiver_id', $receiver_id);
            $stmt->bindParam(':message_text', $message_text);
            $stmt->bindParam(':preference_id', $preference_id);
            $stmt->execute();
            // Redirect with success flag
            header('Location: ../view/pages/trip_details.php?preference_id=' . $preference_id . '&message=success');
            exit();
        } catch (PDOException $e) {
            echo 'Query failed: ' . $e->getMessage();
            exit();
        }
    } else {
        echo 'No user found for the given preference ID.';
        exit();
    }
}
?>
