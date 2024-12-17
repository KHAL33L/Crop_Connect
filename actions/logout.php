<?php
// Start the session
session_start();

// Destroy the session to log the user out
session_unset();
session_destroy();

// Redirect the user to the home page (index.php)
header("Location: ../index.php");
exit();
?>