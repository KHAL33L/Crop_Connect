<?php
session_start();
// If user is already logged in, redirect to home page
if(isset($_SESSION['user_id'])) {
    header("Location: home.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Crop Connect</title>
    <link rel="stylesheet" href="../assets/css/signup.css">
    <script src="../assets/js/signup_validation.js" defer></script>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="logo"><a href="../index.php">CC</a></div>
        <ul class="nav-links">
            <li><a href="home.php">Home</a></li>
            <li><a href="forum.php">Forum</a></li>
            <li><a href="profile.php">Profile</a></li>
        </ul>
        <div class="login">
            <a href="login.php">Login</a>
        </div>
    </nav>

    <div class="container">
        <main class="landing-page">
            <div class="description-container">
                <div class="description">
                    <h1>Create Your Account</h1>
                </div>
                <form id="signup-form" method="POST" action="../actions/signup_process.php">
                    <div class="form-group">
                        <label for="first-name">First Name</label>
                        <input type="text" id="first-name" name="first-name" required>
                    </div>
                    <div class="form-group">
                        <label for="last-name">Last Name</label>
                        <input type="text" id="last-name" name="last-name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                        <small>Password must be at least 8 characters long, contain at least one uppercase letter and one special character.</small>
                    </div>
                    <div class="form-group">
                        <label for="repeat-password">Repeat Password</label>
                        <input type="password" id="repeat-password" name="repeat-password" required>
                        <small>Re-enter your password for confirmation.</small>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="signup-button">Sign Up</button>
                    </div>
                    <div class="register-link">
                        <p>Already have an account? <a href="login.php">Login</a></p>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <footer>
        <p>&copy; 2024 Crop Connect. All rights reserved. <a href="#">Privacy Policy</a> | <a href="#">Terms of Service</a></p>
    </footer>
</body>
</html>
