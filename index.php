<?php
session_start();
// You can add any PHP code or include files here in the future, for example, user session management
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Crop Connect</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/index.css">
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="logo"><a href="index.php">CC</a></div>
        <ul class="nav-links">
            <li><a href="view/home.php">Home</a></li>
            <li><a href="view/forum.php">Forum</a></li>
            <li><a href="view/profile.php">Profile</a></li>
        </ul>
        <div class="login">
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="actions/logout.php">Logout</a>
            <?php else: ?>
                <a href="view/login.php">Login</a>
            <?php endif; ?>
        </div>
    </nav>

    <main class="landing-page">
        <div class="description-container">
            <div class="description">
                <h1>Welcome to Crop Connect</h1>
                <p>A platform dedicated to helping farmers share agricultural knowledge, learn skills, and 
                    connect with fellow farmers. Join us and contribute to a growing network of farmers who are dedicated to building a sustainable future 
                    through shared knowledge and collaboration.</p>
            </div>
            <div class="signup-button-container">
                <form action="view/signup.php" method="GET">
                    <button type="submit" class="signup-button">Sign Up</button>
                </form>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 Crop Connect. All rights reserved. <a href="#">Privacy Policy</a> | <a href="#">Terms of Service</a></p>
    </footer>
</body>
</html>
