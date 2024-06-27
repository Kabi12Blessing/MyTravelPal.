<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include database connection file
require_once '../settings/connection.php';

// Function to sanitize user input
function sanitize_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if email and password are set
    if (isset($_POST["email"]) && isset($_POST["password"])) {
        // Get form data
        $email = sanitize_input($_POST["email"]);
        $password = sanitize_input($_POST["password"]);

        // Validate form data
        if (empty($email) || empty($password)) {
            die("Email and password are required.");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            die("Invalid email format.");
        }

        // Prepare and execute query to fetch user data
        $stmt = $conn->prepare("SELECT user_id, username, email, password_hash FROM Users WHERE email = :email");
        $stmt->bindValue(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() === 1) {
            // Fetch the user data
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verify the password
            if (password_verify($password, $user['password_hash'])) {
                // Password is correct, start a session and store user data
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];

                // Redirect to the user dashboard or another page
                header("Location: ../view/pages/HomePage.php");
                exit();
            } else {
                die("Invalid password.");
            }
        } else {
            die("No user found with this email.");
        }
    } else {
        die("Email and password are required.");
    }
}
?>

