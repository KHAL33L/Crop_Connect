<?php
session_start();
include 'db_connection.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../view/login.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $userId = $_SESSION['user_id'];
    $created_at = date('Y-m-d H:i:s');

    // Insert the topic into the database
    $query = "INSERT INTO forumtopics (title, description, user_id, created_at) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssis", $title, $description, $userId, $created_at);
    
    if ($stmt->execute()) {
        header("Location: ../view/forum.php");
        exit();
    } else {
        $error = "Error creating topic. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Topic - Crop Connect</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <style>
        :root {
            --dark-grey: #333;
            --medium-grey: #2d2d2d;
            --light-grey: #1a1a1a;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: var(--dark-grey);
            color: #fff;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 80px auto 20px;
            padding: 20px;
        }

        .create-topic-container {
            background-color: var(--light-grey);
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #81d01a;
            margin-bottom: 30px;
            font-size: 24px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #fff;
            font-size: 16px;
        }

        input[type="text"],
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--medium-grey);
            border-radius: 5px;
            background-color: var(--medium-grey);
            color: #fff;
            font-size: 14px;
        }

        textarea {
            min-height: 200px;
            resize: vertical;
        }

        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s;
        }

        button[type="submit"] {
            background-color: #81d01a;
            color: #fff;
        }

        button[type="submit"]:hover {
            background-color: #6a9c28;
        }

        .cancel-btn {
            background-color: #dc3545;
            color: #fff;
        }

        .cancel-btn:hover {
            background-color: #c82333;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #81d01a;
            text-decoration: none;
            transition: color 0.3s;
        }

        .back-link:hover {
            color: #6a9c28;
        }

        .error-message {
            background-color: #dc3545;
            color: #fff;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="logo"><a href="../index.php">CC</a></div>
        <ul class="nav-links">
            <li><a href="../view/home.php">Home</a></li>
            <li><a href="../view/forum.php">Forum</a></li>
            <li><a href="../view/profile.php">Profile</a></li>
        </ul>
        <div class="login">
            <a href="../actions/logout.php">Logout</a>
        </div>
    </nav>

    <div class="container">
        <div class="create-topic-container">
            <h2>New Topic</h2>
            <?php if (isset($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form action="" method="POST">
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" id="title" name="title" required>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" required></textarea>
                </div>

                <div class="button-group">
                    <button type="submit">Create Topic</button>
                    <button type="button" class="cancel-btn" onclick="window.location.href='../view/forum.php'">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
