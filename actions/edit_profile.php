<?php
session_start();
include 'db_connection.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../view/login.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Fetch user data (first name, last name, email) from the database
$query = "SELECT first_name, last_name, email, password FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "User not found.";
    exit();
}

// Handle form submission for updating profile
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Profile fields
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $email = $_POST['email'];

    // Handle password change if provided
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    // Validate passwords
    if (!empty($newPassword)) {
        if ($newPassword !== $confirmPassword) {
            echo "Passwords do not match!";
            exit();
        }
        // Hash the new password
        $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
    } else {
        // If no new password is provided, use the existing password
        $newPasswordHash = $user['password'];
    }

    // Update the user's profile and password
    $updateQuery = "UPDATE users SET first_name = ?, last_name = ?, email = ?, password = ? WHERE id = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("ssssi", $firstName, $lastName, $email, $newPasswordHash, $userId);
    $updateStmt->execute();

    // Redirect to profile page after successful update
    header("Location: ../view/profile.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="../assets/css/profile.css">
</head>
<body>

    <div class="container">
        <div class="edit-profile-container">
            <h1>Edit Your Profile</h1>
            <form action="edit_profile.php" method="POST">
                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" name="first_name" id="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" name="last_name" id="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>

                <h3>Change Password</h3>
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" name="new_password" id="new_password">
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" name="confirm_password" id="confirm_password">
                </div>

                <div class="button-group">
                    <button type="submit">Save Changes</button>
                    <a href="../view/profile.php" class="cancel-button">Cancel</a>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
