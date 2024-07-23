<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TravelPal</title>
    <link rel="stylesheet" type="text/css" href="/view/css/styles.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            scroll-behavior: smooth;
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
            background-color: rgba(0, 0, 0, 0.3);
        }
        .header .logo {
            font-size: 24px;
            font-weight: bold;
            color: white;
        }
        .header .login, .header .find-traveler {
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
            margin-right: 10px;
            cursor: pointer;
        }
        .hero {
            height: 100vh;
            background-image: url('https://rawcdn.githack.com/BlessingLeslie/TravelPalImages/f3fa5a30650b6390c7e41a26ff7550836e3a6f24/DALL%C2%B7E%202024-06-11%2000.17.43%20-%20A%20highly%20realistic%20airport%20scene%20featuring%20two%20Black%20people.%20One%20person%20is%20sharing%20an%20item%20with%20another%20traveler%20who%20has%20extra%20luggage.%20The%20traveler%20i.webp');
            background-size: cover;
            background-position: center;
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: white;
        }
        .hero-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
        }
        .hero-content {
            position: relative;
            z-index: 1;
        }
        .hero h1 {
            font-size: 48px;
            margin-bottom: 20px;
        }
        .hero p {
            font-size: 24px;
            margin-bottom: 20px;
        }
        .search-box {
            display: flex;
            justify-content: center;
        }
        .search-box input[type="text"] {
            padding: 10px;
            font-size: 15px;
            border: none;
            border-radius: 5px 0 0 5px;
            outline: none;
            width: 300px;
        }
        .search-box button {
            padding: 10px;
            font-size: 16px;
            border: none;
            border-radius: 0 5px 5px 0;
            cursor: pointer;
            background-color: #007BFF;
            color: white;
        }
        .search-box button:hover {
            background-color: #0056b3;
        }
        .arrow-down {
            font-size: 32px;
            color: white;
            margin-top: 20px;
            animation: bounce 2s infinite;
            cursor: pointer;
        }
        @keyframes bounce {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-10px);
            }
        }
        .content {
            padding: 100px 50px;
            background-color: #f5f5f5;
            text-align: center;
        }
        .section {
            max-width: 1200px;
            margin: 0 auto;
            padding: 50px;
            border-radius: 10px;
            margin-bottom: 50px;
        }
        .section h2 {
            font-size: 36px;
            margin-bottom: 20px;
            color: #333;
        }
        .section p {
            font-size: 18px;
            color: #555;
            line-height: 1.6;
        }
        .stats {
            display: flex;
            justify-content: space-between;
            text-align: center;
        }
        .stat {
            flex: 1;
            margin: 0 10px;
            padding: 20px;
            border-radius: 10px;
            background-color: #f1f1f1;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .stat i {
            font-size: 50px;
            color: #007BFF;
            margin-bottom: 10px;
        }
        .stat h3 {
            font-size: 36px;
            margin: 10px 0;
        }
        .stat p {
            font-size: 18px;
            color: #555;
        }
        .intro {
            display: flex;
            align-items: center;
            text-align: left;
        }
        .intro img {
            width: 50%;
            border-radius: 10px;
            margin-right: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .reasons {
            display: flex;
            justify-content: space-between;
            text-align: center;
        }
        .reason {
            flex: 1;
            margin: 0 10px;
            padding: 20px;
            border-radius: 10px;
            background-color: #f1f1f1;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }
        .reason:hover {
            transform: translateY(-10px);
        }
        .reason i {
            font-size: 50px;
            color: #007BFF;
            margin-bottom: 10px;
        }
        .reason h3 {
            font-size: 24px;
            margin: 10px 0;
        }
        .testimonials {
            padding: 50px 20px;
            background-color: #ffffff;
            text-align: center;
        }
        .testimonial {
            max-width: 800px;
            margin: 0 auto;
            margin-bottom: 30px;
            padding: 20px;
            background-color: #f1f1f1;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .testimonial p {
            font-size: 18px;
            color: #555;
            line-height: 1.6;
        }
        .testimonial h3 {
            margin-top: 10px;
            font-size: 20px;
            color: #333;
        }
        .map-container {
            height: 500px;
            width: 100%;
            margin-bottom: 50px;
            border-radius: 10px;
            overflow: hidden;
        }
        .footer {
            background-color: #333;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .footer a {
            color: #007BFF;
            text-decoration: none;
        }
        .footer a:hover {
            text-decoration: underline;
        }
        .faq {
            padding: 50px 20px;
            background-color: #f9f9f9;
        }
        .faq h2 {
            font-size: 36px;
            margin-bottom: 20px;
            color: #333;
        }
        .faq-item {
            margin-bottom: 20px;
        }
        .faq-item h3 {
            font-size: 24px;
            color: #007BFF;
        }
        .faq-item p {
            font-size: 18px;
            color: #555;
            line-height: 1.6;
        }

        /* Modal Styles */
        .modal {
            display: none; /* Hidden by default */
            position: fixed;
            z-index: 1001; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto; /* Enable scroll if needed */
            background-color: rgb(0,0,0); /* Fallback color */
            background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto; /* 15% from the top and centered */
            padding: 20px;
            border: 1px solid #888;
            width: 80%; /* Could be more or less, depending on screen size */
            max-width: 400px; /* Ensure a max width */
            border-radius: 10px;
        }

        .modal-header, .modal-footer {
            padding: 10px;
            text-align: center;
        }

        .modal-header {
            background-color: #333;
            color: white;
        }

        .modal-footer {
            background-color: #f1f1f1;
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

        .modal-body input[type="text"],
        .modal-body input[type="password"],
        .modal-body input[type="email"] {
            width: 100%;
            padding: 10px;
            margin: 5px 0 10px 0;
            display: inline-block;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .modal-body button {
            background-color: #007BFF;
            color: white;
            padding: 14px 20px;
            margin: 8px 0;
            border: none;
            cursor: pointer;
            width: 100%;
            border-radius: 5px;
        }

        .modal-body button:hover {
            background-color: #0056b3;
        }

        .modal-body .social-login {
            display: flex;
            justify-content: space-between;
        }

        .modal-body .social-login button {
            width: 48%;
            background-color: #3b5998; /* Facebook color */
            color: white;
        }

        .modal-body .social-login button.google {
            background-color: #db4a39; /* Google color */
        }

        .modal-body .social-login button:hover {
            opacity: 0.8;
        }

        .modal-body label {
            display: flex;
            align-items: center;
        }

        .modal-body input[type="checkbox"] {
            margin-right: 10px;
        }

        .modal-footer a {
            color: #007BFF;
            text-decoration: none;
        }

        .modal-footer a:hover {
            text-decoration: underline;
        }
    </style>
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
</head>
<body>
    <div class="header">
        <div class="logo">TravelPal</div>
        <button class="login">Login</button>
        <button class="find-traveler">Find a traveler</button>
    </div>
    <div class="hero">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <h1>Welcome to TravelPal</h1>
            <p>Connect with fellow travelers, share extra luggage space, and make new friends on your journeys.</p>
            <div class="search-box">
                <input type="text" placeholder="Where do you want to send your package?">
                <button>Search</button>
            </div>
            <div id="arrow-down" class="arrow-down">&#x2193;</div>
        </div>
    </div>
    <div id="content" class="content">
        <div class="section about">
            <h2>About TravelPal</h2>
            <p>TravelPal connects you with fellow travelers who have extra luggage space, allowing you to send packages easily and securely. Our community is built on trust, and every traveler is verified to ensure a safe experience.</p>
        </div>
        <div class="section stats">
            <div class="stat">
                <i class="fas fa-suitcase"></i>
                <h3>1000k+</</h3>
                <p>Travel matches made</p>
            </div>
            <div class="stat">
                <i class="fas fa-map-marker-alt"></i>
                <h3>5k+</h3>
                <p>Shipping Destinations</p>
            </div>
            <div class="stat">
                <i class="fas fa-shield-alt"></i>
                <h3>4.0</h3>
                <p>Trust score</p>
            </div>
        </div>
        <div class="section intro">
            <img src="https://rawcdn.githack.com/BlessingLeslie/TravelPalImages/f3fa5a30650b6390c7e41a26ff7550836e3a6f24/DALL%C2%B7E%202024-06-11%2000.18.38%20-%20A%20highly%20realistic%20airport%20scene%20featuring%20two%20Black%20people.%20One%20person%20is%20sharing%20an%20item%20with%20another%20traveler%20who%20has%20extra%20luggage.%20The%20traveler%20i.webp" alt="Travelers">
            <div>
                <h2>Real & Verified Travelers!</h2>
                <p>Find Travelers to transport your packages on TravelPal - the best P2P Travel shipping Website out there. Every process is tracked, organized, and verified. Find a traveler to ship your colis today!</p>
            </div>
        </div>
        <div class="section why-join-us">
            <h2>Why Join Us?</h2>
            <div class="reasons">
                <div class="reason">
                    <i class="fas fa-users"></i>
                    <h3>Connect with Travelers</h3>
                    <p>Connect with a global community of travelers</p>
                </div>
                <div class="reason">
                    <i class="fas fa-plane"></i>
                    <h3>Explore Destinations</h3>
                    <p>Explore new destinations with ease</p>
                </div>
                <div class="reason">
                    <i class="fas fa-shield-alt"></i>
                    <h3>Secure Platform</h3>
                    <p>Secure and trusted platform</p>
                </div>
            </div>
        </div>
        <div class="section testimonials">
            <h2>What Our Users Say</h2>
            <div class="testimonial">
                <p>"TravelPal made it so easy to send a package to my friend across the country. The traveler I connected with was friendly and reliable. Highly recommend!"</p>
                <h3>- Jane Doe</h3>
            </div>
            <div class="testimonial">
                <p>"As a frequent traveler, I love being able to help others by carrying their packages. TravelPal has made my journeys more enjoyable and meaningful."</p>
                <h3>- John Smith</h3>
            </div>
            <div class="testimonial">
                <p>"The verification process gave me peace of mind. Knowing that I can track my package every step of the way is a game-changer."</p>
                <h3>- Sarah Lee</h3>
            </div>
        </div>
        <div class="section map-container">
            <div id="map"></div>
        </div>
        <div class="section faq">
            <h2>Frequently Asked Questions</h2>
            <div class="faq-item">
                <h3>How does TravelPal ensure the safety of my package?</h3>
                <p>Every traveler is verified through our comprehensive vetting process, and packages are tracked from pick-up to delivery.</p>
            </div>
            <div class="faq-item">
                <h3>What happens if my package is lost?</h3>
                <p>We have a dedicated support team to assist with any issues, including lost packages. Our tracking system also minimizes the risk of loss.</p>
            </div>
            <div class="faq-item">
                <h3>How can I become a verified traveler?</h3>
                <p>Sign up on our platform and complete the verification process, which includes identity checks and travel history reviews.</p>
            </div>
        </div>
    </div>
    <div class="footer">
        <p>&copy; 2024 TravelPal. All rights reserved. | <a href="/view/privacy-policy.php">Privacy Policy</a> | <a href="/view/terms-of-service.php">Terms of Service</a></p>
    </div>
<!-- Login Modal -->
<div id="loginModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <span class="close">&times;</span>
            <h2>TravelPal Login</h2>
        </div>
        <div class="modal-body">
            <form action="/MyTravelPal/action/login_user_action.php" method="POST">
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">LOGIN</button>
                <label>
                    <input type="checkbox" checked="checked"> Remember me on this device
                </label>
            </form>
            <!-- Not necessary for now -->
            <!-- <div class="social-login">
                <button type="button" class="facebook">Login with Facebook</button>
                <button type="button" class="google">Login with Google</button>
            </div> -->
            <div class="register-link">
                <p>Don't have an account? <a href="javascript:void(0)" onclick="openRegisterModal()">Register here</a></p>
            </div>
        </div>
        <div class="modal-footer">
            <a href="#">Forgot your password or username?</a>
        </div>
    </div>
</div>


    <!-- Register Modal -->
    <div id="registerModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <span class="close">&times;</span>
                <h2>TravelPal Register</h2>
            </div>
            <div class="modal-body">
                <form action="/MyTravelPal/action/register_user_action.php" method="POST">
                    <input type="text" name="username" placeholder="Username" required>
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                    <button type="submit">REGISTER</button>
                    <label>
                        <input type="checkbox" required> I agree to the <a style="margin-left:5px;" href="#">Terms and Conditions</a>
                    </label>
                </form>
                <!-- Not necessary for now -->
                <!-- <div class="social-login">
                    <button type="button" class="facebook">Register with Facebook</button>
                    <button type="button" class="google">Register with Google</button>
                </div> -->
                <div class="login-link">
                    <p>Already have an account? <a href="javascript:void(0)" onclick="openLoginModal()">Login here</a></p>
                </div>
            </div>
            <div class="modal-footer">
                <a href="#">Need help?</a>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('arrow-down').addEventListener('click', function() {
            document.getElementById('content').scrollIntoView({ behavior: 'smooth', block: 'start' });
        });

        // Initialize Leaflet map
        var map = L.map('map').setView([51.505, -0.09], 2);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Get the modal elements
        var loginModal = document.getElementById("loginModal");
        var registerModal = document.getElementById("registerModal");

        // Get the button elements
        var loginBtn = document.querySelector(".login");
        var findTravelerBtn = document.querySelector(".find-traveler");

        // Get the <span> elements that close the modals
        var closeElements = document.getElementsByClassName("close");

        // When the user clicks the button, open the login modal
        loginBtn.onclick = function() {
            loginModal.style.display = "block";
        }

        // When the user clicks the button, open the register modal
        findTravelerBtn.onclick = function() {
            registerModal.style.display = "block";
        }

        // When the user clicks on <span> (x), close the modals
        for (var i = 0; i < closeElements.length; i++) {
            closeElements[i].onclick = function() {
                loginModal.style.display = "none";
                registerModal.style.display = "none";
            }
        }

        // When the user clicks anywhere outside of the modals, close them
        window.onclick = function(event) {
            if (event.target == loginModal) {
                loginModal.style.display = "none";
            } else if (event.target == registerModal) {
                registerModal.style.display = "none";
            }
        }

        // Function to open the login modal from the register modal
        function openLoginModal() {
            registerModal.style.display = "none";
            loginModal.style.display = "block";
        }

        // Function to open the register modal from the login modal
        function openRegisterModal() {
            loginModal.style.display = "none";
            registerModal.style.display = "block";
        }
    </script>
</body>
</html>
