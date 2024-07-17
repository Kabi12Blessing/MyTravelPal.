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
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    
    // Validate inputs
    if (empty($username) || empty($email)) {
        header('Location: ../view/pages/Dashboard.php?error=empty_fields');
        exit();
    }

    // Update user information in the database
    $sql = "UPDATE Users SET username = :username, email = :email WHERE user_id = :user_id";
    try {
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':user_id', $_SESSION['user_id']);
        $stmt->execute();
        
        // Update session variables
        $_SESSION['username'] = $username;
        
        header('Location: ../view/pages/Dashboard.php?success=profile_updated');
        exit();
    } catch (PDOException $e) {
        echo 'Query failed: ' . $e->getMessage();
        exit();
    }
} else {
    header('Location: ../profile.php');
    exit();
}
?>
