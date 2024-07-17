<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../../settings/connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

// Get the preference_id, sender_id, and receiver_id from the URL
$preference_id = isset($_GET['preference_id']) ? intval($_GET['preference_id']) : 0;
$receiver_id = isset($_GET['receiver_id']) ? intval($_GET['receiver_id']) : 0;
$sender_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;

// SQL query to fetch messages between the two users for the specified preference ID
$sql = "SELECT m.*, u.username AS sender_username, u.profile_picture AS sender_profile_pic
        FROM Messages m
        JOIN Users u ON m.sender_id = u.user_id
        WHERE m.preference_id = :preference_id
        ORDER BY m.sent_at ASC";

try {
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':preference_id', $preference_id, PDO::PARAM_INT);
    $stmt->execute();
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo 'Query failed: ' . $e->getMessage();
    exit();
}

// Function to convert absolute path to web-accessible relative path
function convertPathToWeb($absolutePath) {
    $documentRoot = '/Applications/XAMPP/xamppfiles/htdocs/';
    $baseUrl = '/'; // Adjust this to your base URL if necessary

    if (strpos($absolutePath, $documentRoot) === 0) {
        return $baseUrl . substr($absolutePath, strlen($documentRoot));
    }

    return $absolutePath; // Return as is if it doesn't match
}

// Handle new message submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $message_text = $_POST['message_text'];
    if (!empty($message_text)) {
        // Determine the correct sender and receiver IDs
        $new_sender_id = $sender_id;
        $new_receiver_id = $receiver_id;

        if (!empty($messages)) {
            $last_message = end($messages);
            if ($last_message['sender_id'] == $sender_id) {
                // Swap sender and receiver if the last message was sent by the current user
                $new_sender_id = $receiver_id;
                $new_receiver_id = $sender_id;
            } else {
                // Keep the original sender and receiver if the last message was sent by the other user
                $new_sender_id = $sender_id;
                $new_receiver_id = $receiver_id;
            }
        }

        // Debugging: Output the sender and receiver IDs before inserting
        echo '<pre>New Sender ID: ' . $new_sender_id . ', New Receiver ID: ' . $new_receiver_id . '</pre>';

        $sql = "INSERT INTO Messages (sender_id, receiver_id, message_text, preference_id) VALUES (:sender_id, :receiver_id, :message_text, :preference_id)";
        try {
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':sender_id', $new_sender_id, PDO::PARAM_INT);
            $stmt->bindValue(':receiver_id', $new_receiver_id, PDO::PARAM_INT);
            $stmt->bindValue(':message_text', $message_text, PDO::PARAM_STR);
            $stmt->bindValue(':preference_id', $preference_id, PDO::PARAM_INT);
            $stmt->execute();
            // Refresh the page to display the new message
            header("Location: message_thread.php?preference_id=$preference_id&receiver_id=$new_receiver_id");
            exit();
        } catch (PDOException $e) {
            echo 'Message sending failed: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Message Thread</title>
    <style>
        :root {
            --primary-color: #007BFF;
            --background-color: #f5f7fa;
            --dark-background-color: #121212;
            --text-color: #333;
            --dark-text-color: #f5f7fa;
            --card-bg-color: white;
            --dark-card-bg-color: #1e1e1e;
            --sender-bg-color: #e7f0fe;
            --receiver-bg-color: #dcf8c6;
        }
        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Roboto', sans-serif;
            background-color: var(--background-color);
            color: var(--text-color);
        }
        .dark-mode {
            background-color: var(--dark-background-color);
            color: var(--dark-text-color);
        }
        .dark-mode .header,
        .dark-mode .sidebar,
        .dark-mode .section,
        .dark-mode .footer {
            background-color: var(--dark-background-color);
            color: var(--dark-text-color);
        }
        .dark-mode .header .dashboard,
        .dark-mode .header .create-trip,
        .dark-mode .header .view-travelers,
        .dark-mode .header .dark-mode-toggle {
            background: rgba(255, 255, 255, 0.1);
        }
        .dark-mode .header .dashboard:hover,
        .dark-mode .header .create-trip:hover,
        .dark-mode .header .view-travelers:hover,
        .dark-mode .header .dark-mode-toggle:hover {
            background-color: rgba(255, 255, 255, 0.3);
        }
        .dark-mode .sidebar a {
            color: var(--dark-text-color);
        }
        .dark-mode .sidebar a:hover {
            background-color: #1e1e1e;
        }
        .dark-mode .card {
            background-color: var(--dark-card-bg-color);
        }
        .dark-mode .card .btn {
            background-color: var(--primary-color);
        }
        .dark-mode .card .btn:hover {
            background-color: #0056b3;
        }
        .dark-mode .card p, .dark-mode .card h3 {
            color: var(--dark-text-color);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            width: 100%;
            position: fixed;
            top: 0;
            z-index: 1000;
            background-color: rgba(0, 0, 0, 0.7);
        }
        .header .logo {
            font-size: 24px;
            font-weight: bold;
            color: white;
        }
        .header .user-menu {
            display: flex;
            align-items: center;
            position: relative;
        }
        .header .dashboard, .header .create-trip, .header .view-travelers, .header .dark-mode-toggle {
            color: white;
            font-weight: bold;
            text-decoration: none;
            font-size: 16px;
            cursor: pointer;
            margin-right: 10px;
            padding: 10px 20px;
            border: none;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 5px;
        }
        .header .dashboard:hover, .header .create-trip:hover, .header .view-travelers:hover, .header .dark-mode-toggle:hover {
            background-color: rgba(255, 255, 255, 0.3);
        }
        .header .username {
            padding: 10px 20px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
            cursor: pointer;
            position: relative;
        }
        .header .dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            background-color: #fff;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 1;
            display: none;
            border-radius: 5px;
            overflow: hidden;
            min-width: 150px;
        }
        .header .dropdown a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            transition: background-color 0.3s;
        }
        .header .dropdown a:hover {
            background-color: #f1f1f1;
        }
        .sidebar {
            position: fixed;
            top: 70px;
            left: 0;
            width: 250px;
            height: 100%;
            background-color: var(--primary-color);
            padding-top: 20px;
            color: white;
        }
        .sidebar a {
            padding: 15px;
            text-decoration: none;
            font-size: 18px;
            color: white;
            display: block;
            transition: background-color 0.3s;
        }
        .sidebar a:hover {
            background-color: #0056b3;
        }
        .main-content {
            margin-left: 250px;
            padding: 80px 20px 20px;
            transition: filter 0.3s;
            min-height: calc(100vh - 120px); /* Account for header and footer height */
        }
        .section {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background: var(--card-bg-color);
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .dark-mode .section {
            background: var(--dark-card-bg-color);
        }
        .section h2 {
            font-size: 28px;
            margin-bottom: 20px;
            color: var(--text-color);
        }
        .dark-mode .section h2 {
            color: var(--dark-text-color);
        }
        .message-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .message {
            border-radius: 10px;
            padding: 20px;
            display: flex;
            gap: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .message.sender {
            background: var(--sender-bg-color);
            align-self: flex-start;
        }
        .message.receiver {
            background: var(--receiver-bg-color);
            align-self: flex-end;
        }
        .message .profile-pic {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
        }
        .message-content {
            flex: 1;
        }
        .message .sender-name {
            font-weight: bold;
            color: var(--primary-color);
        }
        .message .message-text {
            margin-bottom: 10px;
            color: var(--text-color);
        }
        .message .timestamp {
            font-size: 12px;
            color: #999;
        }
        .message-input {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        .message-input textarea {
            flex: 1;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            resize: none;
        }
        .message-input button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            background-color: var(--primary-color);
            color: white;
            cursor: pointer;
        }
        .message-input button:hover {
            background-color: #0056b3;
        }
        .footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 20px 0;
            position: fixed;
            width: 100%;
            bottom: 0;
        }
        .footer a {
            color: var(--primary-color);
            text-decoration: none;
        }
        .footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">TravelPal</div>
        <?php if (isset($_SESSION['username'])): ?>
            <div class="user-menu">
                <a href="HomePage.php" class="dashboard">HomePage</a>
                <a href="Dashboard.php" class="dashboard">Dashboard</a>
                <button class="create-trip" onclick="toggleModal()">Create New Trip</button>
                <a href="view_travelers.php" class="view-travelers">View All Travelers</a>
                <button class="dark-mode-toggle" onclick="toggleDarkMode()">Toggle Dark Mode</button>
                <div class="username" onclick="toggleDropdown()">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></div>
                <div class="dropdown">
                    <a href="profile.php">Profile</a>
                    <a href="/Travel_Pal/action/logout.php">Log Out</a>
                </div>
            </div>
        <?php else: ?>
            <a href="../LandingPage.php"></a>
        <?php endif; ?>
    </div>
    <div class="sidebar">
        <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
        <a href="travel_plans.php"><i class="fas fa-plane"></i> Travel Plans</a>
        <a href="messages.php"><i class="fas fa-envelope"></i> Messages</a>
        <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
        <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
    </div>
    <div class="main-content">
        <div class="section">
            <h2>Message Thread</h2>
            <div class="message-container">
                <?php foreach ($messages as $message): ?>
                    <div class="message <?= ($message['sender_id'] == $sender_id) ? 'sender' : 'receiver' ?>">
                        <img src="<?= htmlspecialchars(convertPathToWeb($message['sender_profile_pic'] ?? 'default_profile_picture.jpg')) ?>" alt="Profile Picture" class="profile-pic">
                        <div class="message-content">
                            <span class="sender-name"><?= htmlspecialchars($message['sender_username']) ?></span>
                            <p class="message-text"><?= htmlspecialchars($message['message_text']) ?></p>
                            <span class="timestamp"><?= htmlspecialchars($message['sent_at']) ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <form class="message-input" method="POST" action="">
                <textarea name="message_text" rows="3" placeholder="Type your message here..."></textarea>
                <input type="hidden" name="receiver_id" value="<?= $receiver_id ?>">
                <button type="submit">Send</button>
            </form>
        </div>
    </div>
    <div class="footer">
        <p>&copy; 2024 TravelPal. All rights reserved. | <a href="/view/privacy-policy.php">Privacy Policy</a> | <a href="/view/terms-of-service.php">Terms of Service</a></p>
    </div>
    <script>
        function toggleDropdown() {
            var dropdown = document.querySelector('.dropdown');
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        }

        function toggleDarkMode() {
            document.body.classList.toggle('dark-mode');
            // Save dark mode preference in local storage
            if (document.body.classList.contains('dark-mode')) {
                localStorage.setItem('darkMode', 'enabled');
            } else {
                localStorage.setItem('darkMode', 'disabled');
            }
        }

        // Apply dark mode preference on page load
        document.addEventListener('DOMContentLoaded', (event) => {
            if (localStorage.getItem('darkMode') === 'enabled') {
                document.body.classList.add('dark-mode');
            }
        });

        // Close the dropdown if the user clicks outside of it
        window.onclick = function(event) {
            if (!event.target.matches('.username')) {
                var dropdowns = document.querySelectorAll('.dropdown');
                for (var i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.style.display === 'block') {
                        openDropdown.style.display = 'none';
                    }
                }
            }
        }
    </script>
</body>
</html>
