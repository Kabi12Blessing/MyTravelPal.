<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../../settings/connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch travelers from the database
$travelers = [];
$sql = "SELECT u.username, u.profile_picture, tp.*, c1.country_name AS origin_country, c2.country_name AS destination_country 
        FROM Users u 
        JOIN Travel_Preferences tp ON u.user_id = tp.user_id 
        JOIN Countries c1 ON tp.origin_country_id = c1.country_id 
        JOIN Countries c2 ON tp.destination_country_id = c2.country_id
        WHERE tp.user_id != :user_id";
try {
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $travelers[] = $row;
    }
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Travelers</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
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
            scroll-behavior: smooth;
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
            margin-bottom: 50px;
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
        .section p {
            font-size: 18px;
            color: #666;
        }
        .dark-mode .section p {
            color: var(--dark-text-color);
        }
        .dashboard-section {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: space-between;
        }

        .card {
            flex: 1 1 calc(25% - 20px); /* Adjust for 4 cards per row */
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            background-color: var(--card-bg-color);
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
            transition: transform 0.3s, box-shadow 0.3s;
            box-sizing: border-box;
            min-width: calc(25% - 20px);
            max-width: calc(25% - 20px);
            height: 400px; /* Set a fixed height */
            overflow: hidden; /* Hide overflow content */
        }

        .dark-mode .card {
            background-color: var(--dark-card-bg-color);
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .card-content {
            width: 100%;
            flex: 1; /* Allow the content to grow and take up available space */
            display: flex;
            flex-direction: column;
            justify-content: space-between; /* Distribute space evenly */
        }

        .card img {
            width: 100px; /* Set the width you prefer */
            height: 100px; /* Set the height you prefer */
            border-radius: 50%; /* Make the image circular */
            object-fit: cover; /* Ensure the image covers the area */
            margin-top: 10px;
        }

        .card h3 {
            font-size: 20px;
            margin-top: 10px;
            color: var(--text-color);
        }

        .dark-mode .card h3 {
            color: var(--dark-text-color);
        }

        .card p {
            font-size: 16px;
            color: #555;
        }

        .dark-mode .card p {
            color: var(--dark-text-color);
        }

        .card .btn {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            background-color: var(--primary-color);
            color: white;
            text-decoration: none;
            margin-right: 10px;
        }

        .card .btn:hover {
            background-color: #0056b3;
        }

        .card .view-more {
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: var(--primary-color);
            color: white;
            text-decoration: none;
            padding: 10px;
            border-radius: 0 0 10px 10px;
            font-weight: bold;
            font-size: 18px;
            width: 100%; /* Make it span the entire card width */
            text-align: center;
        }

        .card .view-more:hover {
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

        .profile-picture {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
        }
    </style>
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
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
        <h2 style="font-size: 28px; color: white; text-align: center; margin-bottom: 20px; font-weight: bold; background-color: #007BFF; padding: 10px 0; border-radius: 5px;">Discover Your Perfect Travel Companion</h2>



        <div class="dashboard-section">
    <?php foreach ($travelers as $traveler): ?>
        <div class="card">
            <div class="card-content">
                <?php 
                // Default avatar URL
                $defaultAvatar = 'https://rawcdn.githack.com/Kabi12Blessing/72892025_ChurningPrediction/c2e416446c7e05056259be4e948de42f070a8e6c/266033.png';
                // Convert absolute path to web-accessible relative path
                $profilePicturePath = empty($traveler['profile_picture']) ? $defaultAvatar : convertPathToWeb($traveler['profile_picture']);
                ?>
                <img src="<?= htmlspecialchars($profilePicturePath) ?>" alt="Profile Picture" class="profile-picture">
                <h3>I am <?= htmlspecialchars($traveler['username']) ?>, travelling</h3>
                <p>From: <?= htmlspecialchars($traveler['origin_country']) ?></p>
                <p>To: <?= htmlspecialchars($traveler['destination_country']) ?></p>
                <p>Departure: <?= htmlspecialchars($traveler['travel_date']) ?></p>
            </div>
            <a style="margin-left:-10px;" href="trip_details.php?preference_id=<?= $traveler['preference_id'] ?>" class="view-more">View More</a>
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

        function toggleTripForm() {
            var form = document.getElementById('tripForm');
            form.classList.toggle('hidden');
        }

        function toggleSection(sectionId) {
            var section = document.getElementById(sectionId);
            section.classList.toggle('hidden');
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

        function toggleModal() {
            var modal = document.getElementById('tripModal');
            var mainContent = document.querySelector('.main-content');
            if (modal.style.display === 'flex') {
                modal.style.display = 'none';
                mainContent.classList.remove('blur');
            } else {
                modal.style.display = 'flex';
                mainContent.classList.add('blur');
            }
        }

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

            // Close the modal if the user clicks outside of it
            if (event.target == document.getElementById('tripModal')) {
                document.getElementById('tripModal').style.display = 'none';
                document.querySelector('.main-content').classList.remove('blur');
            }
        }
    </script>
</body>
</html>
