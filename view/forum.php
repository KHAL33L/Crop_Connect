<?php
session_start();
// If user is not logged in, redirect to login page
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include '../actions/db_connection.php';

// Get the user ID from session
$userId = $_SESSION['user_id'];

// Fetch all topics with user information, ordered by creation date (newest first)
$query = "SELECT ft.*, u.first_name, u.last_name, 
          (SELECT COUNT(*) FROM forumcomments fc WHERE fc.topic_id = ft.id) as comment_count 
          FROM forumtopics ft 
          JOIN users u ON ft.user_id = u.id 
          ORDER BY ft.created_at DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forum - Crop Connect</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="../assets/css/shared.css">
    <link rel="stylesheet" href="../assets/css/forum.css">
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
            <a href="../actions/logout.php">Logout</a>
        </div>
    </nav>

    <div class="container">
        <div class="forum-container">
            <button class="create-topic-btn" onclick="window.location.href='../actions/create_topic.php'">Create Topic</button>
            
            <?php if ($result->num_rows > 0): ?>
                <?php while($topic = $result->fetch_assoc()): ?>
                    <div class="topic-card" onclick="window.location.href='view_topic.php?topic_id=<?php echo $topic['id']; ?>'">
                        <div class="topic-header">
                            <h2 class="topic-title"><?php echo htmlspecialchars($topic['title']); ?></h2>
                            <div class="topic-meta">
                                Posted by <span class="topic-author"><?php echo htmlspecialchars($topic['first_name'] . ' ' . $topic['last_name']); ?></span>
                                on <?php echo date('F j, Y', strtotime($topic['created_at'])); ?>
                            </div>
                        </div>
                        <div class="topic-description">
                            <?php 
                            $description = $topic['description'];
                            if (strlen($description) > 300) {
                                $description = substr($description, 0, 300) . '...';
                            }
                            echo htmlspecialchars($description);
                            ?>
                        </div>
                        <div class="topic-footer">
                            <span class="comment-count"><?php echo $topic['comment_count']; ?> comments</span>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="color: #fff; text-align: center;">No topics have been created yet. Be the first to start a discussion!</p>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <p>&copy; 2024 Crop Connect. All rights reserved. <a href="#">Privacy Policy</a> | <a href="#">Terms of Service</a></p>
    </footer>
</body>
</html>