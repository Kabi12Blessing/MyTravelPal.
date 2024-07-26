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
    $conversation_id = $_POST['conversation_id'];
    $preference_id = $_POST['preference_id'];

    // Ensure that the user has the right to delete these messages
    $user_id = $_SESSION['user_id'];

    $sql = "DELETE FROM Messages WHERE conversation_id = :conversation_id AND preference_id = :preference_id";
    try {
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':conversation_id', $conversation_id, PDO::PARAM_STR);
        $stmt->bindParam(':preference_id', $preference_id, PDO::PARAM_INT);
        $stmt->execute();
        
        // Redirect to messages page with success message
        header('Location: ../view/pages/messages.php?status=deleted');
        exit();
    } catch (PDOException $e) {
        echo 'Deletion failed: ' . $e->getMessage();
    }
}
?>
