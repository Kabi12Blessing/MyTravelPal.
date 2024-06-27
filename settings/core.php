<?php
// Start the session
session_start();

// Include the database connection
require 'connection.php';

// Define some global constants
define('BASE_URL', 'http://localhost/TRAVELPAL/');

// Function to sanitize user input
function sanitize($data) {
    return htmlspecialchars(strip_tags($data));
}

// Example function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Other global settings or functions can be added here

// Example: Error reporting settings
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
