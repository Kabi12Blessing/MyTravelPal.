<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include database connection file
require_once '../settings/connection.php';

// Function to sanitize user input
function sanitize_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

<<<<<<< HEAD
session_start();

=======
>>>>>>> 8b205f118c2463e2af33ee587bdd228d5f878a58
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $username = sanitize_input($_POST["username"]);
    $email = sanitize_input($_POST["email"]);
    $password = sanitize_input($_POST["password"]);
    $confirm_password = sanitize_input($_POST["confirm_password"]);

    // Validate form data
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        die("All fields are required.");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format.");
    }

    if ($password !== $confirm_password) {
        die("Passwords do not match.");
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if the email or username already exists
    $stmt = $conn->prepare("SELECT user_id FROM Users WHERE email = :email OR username = :username");
    $stmt->bindValue(':email', $email);
    $stmt->bindValue(':username', $username);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        die("Email or username already registered.");
    }

    // Insert the new user into the database
    $stmt = $conn->prepare("INSERT INTO Users (username, email, password_hash) VALUES (:username, :email, :password_hash)");
    $stmt->bindValue(':username', $username);
    $stmt->bindValue(':email', $email);
    $stmt->bindValue(':password_hash', $hashed_password);

    if ($stmt->execute()) {
<<<<<<< HEAD
        // Set session variables
        $_SESSION['username'] = $username;
        $_SESSION['user_id'] = $conn->lastInsertId(); // Assuming you have an auto-increment ID column

        // Redirect to home page
=======
        echo "Registration successful!";
        // Redirect to login or another page
>>>>>>> 8b205f118c2463e2af33ee587bdd228d5f878a58
        header("Location: ../view/pages/HomePage.php");
        exit();
    } else {
        echo "Error: " . $stmt->errorInfo()[2];
    }
}
?>
