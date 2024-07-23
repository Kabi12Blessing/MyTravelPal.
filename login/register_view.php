<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - TravelPal</title>
    <style>
        .modal {
            display: block; /* Visible by default */
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
            margin: 10% auto; /* Centered */
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
        .modal-body input[type="email"],
        .modal-body input[type="password"] {
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

        .login-link {
            margin-top: 20px;
            text-align: center;
        }

        .login-link a {
            color: #007BFF;
            text-decoration: none;
            font-weight: bold;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        .error-message {
            color: red;
            text-align: center;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div id="registerModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <span class="close">&times;</span>
                <h2>TravelPal Register</h2>
            </div>
            <div class="modal-body">
                <?php
                if (isset($_GET['error'])) {
                    echo '<div class="error-message">' . htmlspecialchars($_GET['error']) . '</div>';
                }
                ?>
                <form action="../action/register_user_action.php" method="post">
                    <input type="text" name="username" placeholder="Username" required>
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                    <button type="submit">REGISTER</button>
                    <label>
                        <input type="checkbox" required> I agree to the <a style="margin-left:5px;" href="#"> Terms and Conditions</a>
                    </label>
                </form>
                <!-- <div class="social-login">
                    <button type="button" class="facebook">Register with Facebook</button>
                    <button type="button" class="google">Register with Google</button>
                </div> -->
                <div class="login-link">
                    <p>Already have an account? <a href="login_view.php">Login here</a></p>
                </div>
            </div>
            <div class="modal-footer">
                <a href="#">Need help?</a>
            </div>
        </div>
    </div>
    <script>
        // Get the modal
        var modal = document.getElementById("registerModal");

        // Get the <span> element that closes the modal
        var span = document.getElementsByClassName("close")[0];

        // When the user clicks on <span> (x), close the modal
        span.onclick = function() {
            modal.style.display = "none";
        }

        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
</html>
