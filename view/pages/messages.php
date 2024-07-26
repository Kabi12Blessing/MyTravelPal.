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

// Fetch last message from each conversation
$user_id = $_SESSION['user_id'];
$sql = "SELECT m.*, u.username AS sender_username, u.profile_picture AS sender_profile_pic, tp.origin_country_id, tp.destination_country_id, c1.country_name AS origin_country, c2.country_name AS destination_country
        FROM Messages m
        JOIN Users u ON m.sender_id = u.user_id
        JOIN Travel_Preferences tp ON m.preference_id = tp.preference_id
        JOIN Countries c1 ON tp.origin_country_id = c1.country_id
        JOIN Countries c2 ON tp.destination_country_id = c2.country_id
        INNER JOIN (
            SELECT MAX(message_id) AS last_message_id
            FROM Messages
            WHERE receiver_id = :user_id
            GROUP BY conversation_id
        ) lm ON m.message_id = lm.last_message_id
        ORDER BY m.sent_at DESC";

try {
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo 'Query failed: ' . $e->getMessage();
    exit();
}

// Function to convert absolute path to web-accessible relative path
function convertPathToWeb($absolutePath) {
    $documentRoot = '/Applications/XAMPP/xamppfiles/htdocs/';
    $baseUrl = '/'; 

    if (strpos($absolutePath, $documentRoot) === 0) {
        return $baseUrl . substr($absolutePath, strlen($documentRoot));
    }

    return $absolutePath; // Return as is if it doesn't match
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages</title>
    <style>
        :root {
            --primary-color: #007BFF;
            --background-color: #f5f7fa;
            --dark-background-color: #121212;
            --text-color: #333;
            --dark-text-color: #f5f7fa;
            --card-bg-color: white;
            --dark-card-bg-color: #1e1e1e;
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
            background: #e7f0fe;
            border-radius: 10px;
            padding: 20px;
            display: flex;
            gap: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
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
        .message .sender {
            font-weight: bold;
            color: var(--primary-color);
        }
        .message .trip-details {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
            margin-bottom: 10px;
        }
        .message .message-text {
            margin-bottom: 10px;
            color: var(--text-color);
        }
        .message .timestamp {
            font-size: 12px;
            color: #999;
        }
        .message .button-container {
            display: flex;
            gap: 10px;
        }
        .message .approve-button, .message .decline-button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        .message .approve-button {
            background-color: #28a745;
            color: white;
        }

        .message .accept-button {
        background-color: #007BFF; /* A shade of blue */
        color: white;
        }
        .message .accept-button:hover {
            background-color: #0056b3; /* Darker shade of blue for hover effect */
        }


        .message .decline-button {
            background-color: #dc3545;
            color: white;
        }
        .message .approve-button:hover {
            background-color: #218838;
        }
        .message .decline-button:hover {
            background-color: #c82333;
        }
        .footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 20px 0;
            position: relative;
            width: 100%;
            bottom: 0;
            margin-top: 50px;
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
        <h2 style="font-size: 28px; color: white; text-align: center; margin-bottom: 20px; font-weight: bold; background-color: #007BFF; padding: 10px 0; border-radius: 5px;">Messages</h2>
        <div class="message-container">
            <?php foreach ($messages as $message): ?>
                <div class="message">
                    <img src="<?= htmlspecialchars(convertPathToWeb($message['sender_profile_pic'] ?? 'default_profile_picture.jpg')) ?>" alt="Profile Picture" class="profile-pic">
                    <div class="message-content">
                        <span class="sender"><?= htmlspecialchars($message['sender_username']) ?></span>
                        <p class="trip-details">Your trip from <?= htmlspecialchars($message['origin_country']) ?> to <?= htmlspecialchars($message['destination_country']) ?></p>
                        <p class="message-text"><?= htmlspecialchars($message['sender_username']) ?> would like to have you as a travel companion for this trip.<br>
                            <span style="font-weight: bold;">What <?= htmlspecialchars($message['sender_username']) ?> would like you to know:</span><br>
                            <?= htmlspecialchars($message['message_text']) ?: 'No additional message provided.' ?>
                        </p>
                        <span class="timestamp"><?= htmlspecialchars($message['sent_at']) ?></span>
                        <div class="button-container">
                            <a href="message_thread.php?conversation_id=<?= htmlspecialchars($message['conversation_id']) ?>&preference_id=<?= htmlspecialchars($message['preference_id']) ?>&sender_id=<?= htmlspecialchars($message['sender_id']) ?>&receiver_id=<?= htmlspecialchars($user_id) ?>" class="approve-button">View Full Conversation</a>
                            <button class="accept-button">Accept Matching Request</button>
                            
                            <!-- Decline Button with Form -->
                            <form method="POST" action="../../../MyTravelPal/action/delete_conversation.php" style="display:inline;">
                                <input type="hidden" name="conversation_id" value="<?= htmlspecialchars($message['conversation_id']) ?>">
                                <input type="hidden" name="preference_id" value="<?= htmlspecialchars($message['preference_id']) ?>">
                                <button type="submit" class="decline-button" onclick="return confirm('Are you sure you want to decline and delete this conversation?')">Decline</button>
                            </form>

                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
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
