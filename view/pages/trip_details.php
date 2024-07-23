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

// Fetch trip details from the database
$trip = null;
if (isset($_GET['preference_id'])) {
    $sql = "SELECT u.username, u.profile_picture, tp.*, c1.country_name AS origin_country, c2.country_name AS destination_country 
            FROM Users u 
            JOIN Travel_Preferences tp ON u.user_id = tp.user_id 
            JOIN Countries c1 ON tp.origin_country_id = c1.country_id 
            JOIN Countries c2 ON tp.destination_country_id = c2.country_id
            WHERE tp.preference_id = :preference_id";
    try {
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':preference_id', $_GET['preference_id']);
        $stmt->execute();
        $trip = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo 'Query failed: ' . $e->getMessage();
        exit();
    }
} else {
    echo 'No trip selected.';
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
    <title>Trip Details</title>
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
        .dark-mode .trip-info {
            background-color: var(--dark-card-bg-color);
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
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: var(--card-bg-color);
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            text-align: center;
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
            margin: 10px 0;
        }
        .dark-mode .section p {
            color: var(--dark-text-color);
        }
        .trip-header img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            display: block;
            margin: 0 auto;
        }
        .trip-header h2 {
            margin: 20px 0 10px;
            font-size: 32px;
            color: var(--primary-color);
        }
        .trip-details h3 {
            font-size: 24px;
            color: var(--primary-color);
            margin-bottom: 20px;
        }
        .trip-details p {
            margin: 10px 0;
            font-size: 18px;
        }
        .trip-details p span {
            font-weight: bold;
        }
        .trip-info {
            background: #f5f7fa;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
            text-align: left;
        }
        .trip-info p {
            margin: 10px 0;
        }
        .dark-mode .trip-info {
            background: var(--dark-card-bg-color);
            color: var(--dark-text-color);
        }
        .btn-request {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            background-color: var(--primary-color);
            color: white;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        .btn-request:hover {
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
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            border-radius: 10px;
            text-align: center;
        }
        .dark-mode .modal-content {
            background-color: var(--dark-card-bg-color);
            color: var(--dark-text-color);
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
        .modal input, .modal textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .dark-mode .modal input, .dark-mode .modal textarea {
            background-color: var(--dark-card-bg-color);
            color: var(--dark-text-color);
            border: 1px solid #444;
        }
        .modal .btn-confirm {
            background-color: var(--primary-color);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .modal .btn-confirm:hover {
            background-color: #0056b3;
        }
        .popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: var(--card-bg-color);
            border: 1px solid var(--primary-color);
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            z-index: 1000;
            text-align: center;
        }
        .popup.show {
            display: block;
        }
        .dark-mode .popup {
            background-color: var(--dark-card-bg-color);
            color: var(--dark-text-color);
        }
        .popup .btn-confirm {
            margin-top: 20px;
            background-color: var(--primary-color);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .popup .btn-confirm:hover {
            background-color: #0056b3;
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
                    <a href="/MyTravelPal/action/logout.php">Log Out</a>
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
            <h2 style="font-size: 28px; color: white; text-align: center; margin-bottom: 20px; font-weight: bold; background-color: #007BFF; padding: 10px 0; border-radius: 5px;">Trip Details</h2>

            <div class="trip-header">
                <?php 
                // Default avatar URL
                $defaultAvatar = 'https://rawcdn.githack.com/Kabi12Blessing/72892025_ChurningPrediction/c2e416446c7e05056259be4e948de42f070a8e6c/266033.png';
                // Convert absolute path to web-accessible relative path
                $profilePicturePath = empty($trip['profile_picture']) ? $defaultAvatar : convertPathToWeb($trip['profile_picture']);
                ?>
                <img src="<?= htmlspecialchars($profilePicturePath) ?>" alt="Profile Picture" class="profile-picture">
            </div>

            <div class="trip-details">
                <h3>Meet <?= htmlspecialchars($trip['username']) ?>, your potential travel companion</h3>
    
                <div class="trip-info">
                    <p><?= htmlspecialchars($trip['username']) ?> is planning a trip from <span><?= htmlspecialchars($trip['origin_country']) ?></span> to <span><?= htmlspecialchars($trip['destination_country']) ?></span>.</p>
                    <p>The departure is scheduled for <span><?= htmlspecialchars($trip['travel_date']) ?></span> and the return is set for <span><?= htmlspecialchars($trip['return_date']) ?></span>.</p>
                    <p><span><?= htmlspecialchars($trip['username']) ?></span> has a budget of <span><?= htmlspecialchars($trip['budget']) ?></span> dollars and prefers <span><?= htmlspecialchars($trip['accommodation_type']) ?></span> accommodation.</p>
                    <p><span><?= htmlspecialchars($trip['username']) ?></span> 
                        <?php if ($trip['has_extra_space'] == 1 && $trip['needs_space'] == 1): ?>
                            has extra space and needs space.
                        <?php elseif ($trip['has_extra_space'] == 1 && $trip['needs_space'] == 0): ?>
                            is looking for someone that has extra space.
                        <?php elseif ($trip['has_extra_space'] == 0 && $trip['needs_space'] == 1): ?>
                            has extra space and can help someone that needs space.
                        <?php else: ?>
                            does not have extra space and does not need space.
                        <?php endif; ?>
                    </p>
                    <p>Preferred travel companion's gender: <span><?= htmlspecialchars($trip['preferences']) ?></span></p>
                </div>
                <button class="btn-request" onclick="showModal()">Send a Request Match Message</button>
            </div>
        </div>
    </div>
    <div class="footer">
        <p>&copy; 2024 TravelPal. All rights reserved. | <a href="/view/privacy-policy.php">Privacy Policy</a> | <a href="/view/terms-of-service.php">Terms of Service</a></p>
    </div>

    <!-- Modal -->
    <div id="requestModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="hideModal()">&times;</span>
            <h2>Send a Request Match Message</h2>
            <form id="requestForm" action="/Travel_Pal/action/send_request.php" method="post">
                <input type="hidden" name="preference_id" value="<?= htmlspecialchars($trip['preference_id']) ?>">
                <!-- //it should be optional -->
                <textarea name="message" placeholder="Write anything you will like the <?= htmlspecialchars($trip['username']) ?> to know here..." required></textarea> 
                <button type="button" class="btn-confirm" onclick="confirmRequest()">Send Request</button>
            </form>
        </div>
    </div>

    <!-- Custom Confirmation Modal -->
    <div id="confirmModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="hideConfirmModal()">&times;</span>
            <h2>Confirm Request</h2>
            <p>Are you sure you want to send this request?</p>
            <button class="btn-confirm" onclick="submitRequest()">Yes, proceed</button>
            <button class="btn-confirm" onclick="hideConfirmModal()">Cancel</button>
        </div>
    </div>

    <!-- Success Popup -->
    <div id="successPopup" class="popup">
        <h2>Success!</h2>
        <p>Your message has been sent successfully.</p>
        <button class="btn-confirm" onclick="hideSuccessPopup()">OK</button>
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

        // Check if success message parameter is present in URL
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('message') && urlParams.get('message') === 'success') {
            showSuccessPopup();
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

    function showModal() {
        document.getElementById('requestModal').style.display = 'flex';
    }

    function hideModal() {
        document.getElementById('requestModal').style.display = 'none';
    }

    function confirmRequest() {
        document.getElementById('requestModal').style.display = 'none';
        document.getElementById('confirmModal').style.display = 'flex';
    }

    function hideConfirmModal() {
        document.getElementById('confirmModal').style.display = 'none';
        document.getElementById('requestModal').style.display = 'flex';
    }

    function submitRequest() {
        document.getElementById('confirmModal').style.display = 'none';
        document.getElementById('requestForm').submit();
    }

    function showSuccessPopup() {
        document.getElementById('successPopup').classList.add('show');
    }

    function hideSuccessPopup() {
        document.getElementById('successPopup').classList.remove('show');
        const urlParams = new URLSearchParams(window.location.search);
        const preferenceId = urlParams.get('preference_id');
        window.location.href = `trip_details.php?preference_id=${preferenceId}`;
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
        if (event.target == document.getElementById('requestModal')) {
            document.getElementById('requestModal').style.display = 'none';
        }
        if (event.target == document.getElementById('confirmModal')) {
            document.getElementById('confirmModal').style.display = 'none';
        }
        if (event.target == document.getElementById('successPopup')) {
            document.getElementById('successPopup').classList.remove('show');
        }
    }
</script>

</body>
</html>
