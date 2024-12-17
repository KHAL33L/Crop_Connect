<?php
$servername = "localhost"; // Change to your database host if necessary
$username = "ibrahim.dasuki"; // Your database username
$password = "Delorean12!"; // Your database password (leave empty if using default for local server)
$dbname = "webtech_fall2024_ibrahim_dasuki"; // Name of the database

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
