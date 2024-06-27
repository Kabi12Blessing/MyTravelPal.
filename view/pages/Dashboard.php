<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TravelPal Dashboard</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Roboto', sans-serif;
            scroll-behavior: smooth;
            background-color: #f5f7fa;
            color: #333;
        }
        .dark-mode {
            background-color: #121212;
            color: #f5f7fa;
        }
        .dark-mode .header,
        .dark-mode .sidebar,
        .dark-mode .section,
        .dark-mode .footer {
            background-color: #121212;
            color: #f5f7fa;
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
            color: #f5f7fa;
        }
        .dark-mode .sidebar a:hover {
            background-color: #1e1e1e;
        }
        .dark-mode .card {
            background-color: #1e1e1e;
        }
        .dark-mode .card .btn {
            background-color: #007BFF;
        }
        .dark-mode .card .btn:hover {
            background-color: #0056b3;
        }
        .dark-mode .card p, .dark-mode .card h3 {
            color: #f5f7fa;
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
            background-color: #007BFF;
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
            background-color: #007BFF;
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
        }
        .section {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 50px;
        }
        .dark-mode .section {
            background: #1e1e1e;
        }
        .section h2 {
            font-size: 28px;
            margin-bottom: 20px;
            color: #333;
        }
        .dark-mode .section h2 {
            color: #f5f7fa;
        }
        .section p {
            font-size: 18px;
            color: #666;
        }
        .dark-mode .section p {
            color: #f5f7fa;
        }
        .dashboard-section {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 20px;
        }
        .card {
            flex: 1 1 calc(50% - 20px);
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
            transition: transform 0.3s;
        }
        .dark-mode .card {
            background-color: #1e1e1e;
        }
        .card:hover {
            transform: translateY(-10px);
        }
        .card h3 {
            font-size: 24px;
            margin-bottom: 15px;
            color: #333;
        }
        .dark-mode .card h3 {
            color: #f5f7fa;
        }
        .card p {
            font-size: 16px;
            color: #555;
        }
        .dark-mode .card p {
            color: #f5f7fa;
        }
        .card .btn {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            background-color: #007BFF;
            color: white;
            text-decoration: none;
        }
        .card .btn:hover {
            background-color: #0056b3;
        }
        .footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 20px 0;
        }
        .footer a {
            color: #007BFF;
            text-decoration: none;
        }
        .footer a:hover {
            text-decoration: underline;
        }
        .hidden {
            display: none;
        }
        .profile-picture {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 20px;
        }
        .dark-mode .profile-picture {
            border: 2px solid #f5f7fa;
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
                <button class="create-trip" onclick="toggleTripForm()">Create New Trip</button>
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
            <h2>Welcome to your Dashboard</h2>
            <p>Here you can manage all your travel plans, view messages, update your profile, and adjust your settings.</p>
        </div>
        <div class="section">
            <h2>Upcoming Trips <button onclick="toggleSection('upcomingTrips')">Toggle</button></h2>
            <div id="upcomingTrips" class="dashboard-section">
                <div class="card">
                    <h3>Trip to New York</h3>
                    <p>Departure: July 10, 2024</p>
                    <p>Status: Confirmed</p>
                    <button class="btn">Edit</button>
                    <button class="btn">Delete</button>
                    <button class="btn">Find a Travel Match</button>
                </div>
                <div class="card">
                    <h3>Trip to Paris</h3>
                    <p>Departure: August 5, 2024</p>
                    <p>Status: Pending</p>
                    <button class="btn">Edit</button>
                    <button class="btn">Delete</button>
                    <button class="btn">Find a Travel Match</button>
                </div>
            </div>
        </div>
        <div class="section">
            <h2>Recent Messages <button onclick="toggleSection('recentMessages')">Toggle</button></h2>
            <div id="recentMessages" class="dashboard-section">
                <div class="card">
                    <h3>From: John Doe</h3>
                    <p>"Can you carry an extra bag for me?"</p>
                    <p>Date: June 25, 2024</p>
                    <button class="btn">Approve</button>
                    <button class="btn">Decline</button>
                    <button class="btn">Delete</button>
                </div>
                <div class="card">
                    <h3>From: Jane Smith</h3>
                    <p>"Looking forward to our trip together!"</p>
                    <p>Date: June 20, 2024</p>
                    <button class="btn">Approve</button>
                    <button class="btn">Decline</button>
                    <button class="btn">Delete</button>
                </div>
            </div>
        </div>
        <div class="section">
            <h2>Profile Overview <button class="btn" style="float:right;" onclick="editProfile()">Edit</button></h2>
            <div class="dashboard-section">
                <div class="card">
                    <h3>Your Profile</h3>
                    <?php if (file_exists('profile.jpg')): ?>
                        <img src="profile.jpg" alt="Profile Picture" class="profile-picture">
                    <?php else: ?>
                        <form action="upload_profile_picture.php" method="post" enctype="multipart/form-data">
                            <input type="file" name="profile_picture" accept="image/*">
                            <button type="submit" class="btn">Upload Picture</button>
                        </form>
                    <?php endif; ?>
                    <p>Name: <?php echo htmlspecialchars($_SESSION['username']); ?></p>
                    <p>Email: user@example.com</p>
                    <p>Member since: January 2024</p>
                </div>
                <div class="card">
                    <h3>Account Settings</h3>
                    <p><a href="settings.php">Update your settings</a></p>
                </div>
            </div>
        </div>
    </div>
    <div id="tripForm" class="section hidden">
        <h2>Create a New Trip</h2>
        <form>
            <label for="destination">Destination:</label>
            <input type="text" id="destination" name="destination" required>
            <label for="departure">Departure Date:</label>
            <input type="date" id="departure" name="departure" required>
            <label for="return">Return Date:</label>
            <input type="date" id="return" name="return" required>
            <label for="description">Description:</label>
            <textarea id="description" name="description" required></textarea>
            <label for="images">Upload Images:</label>
            <input type="file" id="images" name="images" multiple required>
            <button type="submit">Create Trip</button>
        </form>
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
        }

        function editProfile() {
            // Implement the edit profile functionality here
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
        }
    </script>
</body>
</html>

