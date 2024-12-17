<?php
// Include the database connection
include('db_connection.php');

// Start the session to handle user login state
session_start();

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize the form inputs
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Query to check if the user exists with the given email
    $query = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        // User exists, now verify the password
        $user = mysqli_fetch_assoc($result);
        $hashed_password = $user['password'];

        // Check if the password is correct
        if (password_verify($password, $hashed_password)) {
            // Password is correct, log the user in by storing user info in session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['last_name'] = $user['last_name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role']; // Store user role in session

            // Redirect to the profile or dashboard page
            header("Location: ../view/profile.php");
            exit();
        } else {
            // Incorrect password
            echo "<script>alert('Invalid email or password.'); window.location.href = '../view/login.php';</script>";
        }
    } else {
        // Email not found
        echo "<script>alert('No user found with that email.'); window.location.href = '../view/login.php';</script>";
    }
}
?>
