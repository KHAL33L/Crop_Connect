<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
include('db_connection.php');

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form inputs
    $first_name = mysqli_real_escape_string($conn, $_POST['first-name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last-name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $repeat_password = mysqli_real_escape_string($conn, $_POST['repeat-password']);
    $role = 2;

    // Check if passwords match
    if ($password !== $repeat_password) {
        echo "Passwords do not match.";
        exit();
    }

    // Hash the password before storing it
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if email already exists in the database
    $email_check_query = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
    $result = mysqli_query($conn, $email_check_query);
    $user = mysqli_fetch_assoc($result);

    if ($user) {
        echo "Email is already registered.";
        exit();
    }

    // Prepare the SQL query to insert the user data
    $insert_query = "INSERT INTO users (first_name, last_name, email, password, role) 
                     VALUES ('$first_name', '$last_name', '$email', '$hashed_password', '$role')";

    // Execute the query
    if (mysqli_query($conn, $insert_query)) {
        // Redirect to the login page after successful signup
        echo "<script>alert('Signup successful.'); window.location.href = '../view/login.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
