<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Including database connection file
require_once '../settings/connection.php';

// Function to sanitize user input
function sanitize_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if username, email, password, and confirm_password are set
    if (isset($_POST["username"]) && isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["confirm_password"])) {
        // Get form data
        $username = sanitize_input($_POST["username"]);
        $email = sanitize_input($_POST["email"]);
        $password = sanitize_input($_POST["password"]);
        $confirm_password = sanitize_input($_POST["confirm_password"]);

        // Validate form data
        if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
            header("Location: ../login/register_view.php?error=" . urlencode("All fields are required."));
            exit();
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header("Location: ../login/register_view.php?error=" . urlencode("Invalid email format."));
            exit();
        }

        if ($password !== $confirm_password) {
            header("Location: ../login/register_view.php?error=" . urlencode("Passwords do not match."));
            exit();
        }

        // Hash the password
        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        // Prepare and execute query to insert user data
        $stmt = $conn->prepare("INSERT INTO Users (username, email, password_hash) VALUES (:username, :email, :password_hash)");
        $stmt->bindValue(':username', $username);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':password_hash', $password_hash);

        if ($stmt->execute()) {
            // Registration successful, redirect to login page with success message
            header("Location: ../login/login_view.php?success=" . urlencode("Registration successful. Please log in."));
            exit();
        } else {
            header("Location: ../login/register_view.php?error=" . urlencode("Registration failed. Please try again."));
            exit();
        }
    } else {
        header("Location: ../login/register_view.php?error=" . urlencode("All fields are required."));
        exit();
    }
}
?>
