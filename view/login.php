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
    <title>Login - Crop Connect</title>
    <link rel="stylesheet" href="../assets/css/login.css">
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
                    <h1>Login</h1>
                </div>
                <form id="login-form" method="POST" action="../actions/login_process.php">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="signup-button">Login</button>
                    </div>
                    <div class="register-link">
                        <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
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
