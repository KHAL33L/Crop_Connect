<?php
session_start();
// If user is not logged in, redirect to login page
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include '../actions/db_connection.php';

// Get topic ID from URL
$topicId = isset($_GET['topic_id']) ? $_GET['topic_id'] : null;
if (!$topicId) {
    header("Location: forum.php");
    exit();
}

// Fetch topic with user information
$query = "SELECT ft.*, u.first_name, u.last_name 
          FROM forumtopics ft 
          JOIN users u ON ft.user_id = u.id 
          WHERE ft.id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $topicId);
$stmt->execute();
$result = $stmt->get_result();
$topic = $result->fetch_assoc();

if (!$topic) {
    header("Location: forum.php");
    exit();
}

// Fetch comments for this topic
$commentsQuery = "SELECT fc.*, u.first_name, u.last_name 
                 FROM forumcomments fc 
                 JOIN users u ON fc.user_id = u.id 
                 WHERE fc.topic_id = ? 
                 ORDER BY fc.created_at ASC";
$stmt = $conn->prepare($commentsQuery);
$stmt->bind_param("i", $topicId);
$stmt->execute();
$comments = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Topic - Crop Connect</title>
    <link rel="stylesheet" href="../assets/css/view_topic.css">
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
        <a href="forum.php" class="back-btn">Back to Forum</a>
        
        <div class="topic-container">
            <div class="topic-header">
                <h1 class="topic-title"><?php echo htmlspecialchars($topic['title']); ?></h1>
                <div class="topic-meta">
                    Posted by <span class="topic-author"><?php echo htmlspecialchars($topic['first_name'] . ' ' . $topic['last_name']); ?></span>
                    on <?php echo date('F j, Y', strtotime($topic['created_at'])); ?>
                </div>
            </div>
            <div class="topic-description">
                <?php echo nl2br(htmlspecialchars($topic['description'])); ?>
            </div>
            
            <?php if ($topic['user_id'] == $_SESSION['user_id']): ?>
                <div class="action-buttons">
                    <button onclick="window.location.href='edit_topic.php?topic_id=<?php echo $topic['id']; ?>'" class="submit-btn">Edit</button>
                    <form action="../actions/delete_topic.php" method="POST" style="display: inline;">
                        <input type="hidden" name="topic_id" value="<?php echo $topic['id']; ?>">
                        <button type="submit" class="submit-btn" onclick="return confirm('Are you sure you want to delete this topic?')">Delete</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>

        <div class="comments-section">
            <h2 class="comments-header">Comments</h2>
            <?php if ($comments->num_rows > 0): ?>
                <?php while($comment = $comments->fetch_assoc()): ?>
                    <div class="comment">
                        <div class="comment-meta">
                            <span class="topic-author"><?php echo htmlspecialchars($comment['first_name'] . ' ' . $comment['last_name']); ?></span>
                            on <?php echo date('F j, Y g:i A', strtotime($comment['created_at'])); ?>
                        </div>
                        <div class="comment-content">
                            <?php echo nl2br(htmlspecialchars($comment['content'])); ?>
                        </div>
                        <?php if ($comment['user_id'] == $_SESSION['user_id']): ?>
                            <div class="action-buttons">
                                <button onclick="editComment(<?php echo $comment['id']; ?>, '<?php echo addslashes($comment['content']); ?>')" class="submit-btn">Edit</button>
                                <form action="../actions/delete_comment.php" method="POST" style="display: inline;">
                                    <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
                                    <input type="hidden" name="topic_id" value="<?php echo $topicId; ?>">
                                    <button type="submit" class="submit-btn" onclick="return confirm('Are you sure you want to delete this comment?')">Delete</button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="comment-content">No comments yet. Be the first to comment!</p>
            <?php endif; ?>

            <form id="commentForm" class="comment-form" action="../actions/add_comment.php" method="POST">
                <h3>Add a Comment</h3>
                <div class="form-group">
                    <textarea name="content" rows="4" required placeholder="Write your comment here..."></textarea>
                </div>
                <input type="hidden" name="topic_id" value="<?php echo $topicId; ?>">
                <button type="submit" class="submit-btn">Post Comment</button>
            </form>
        </div>
    </div>


    <div id="editCommentModal" class="modal" style="display: none;">
        <div class="modal-content">
            <h3>Edit Comment</h3>
            <form action="../actions/update_comment.php" method="POST">
                <input type="hidden" name="comment_id" id="edit_comment_id">
                <input type="hidden" name="topic_id" value="<?php echo $topicId; ?>">
                <textarea name="content" id="edit_comment_content" rows="4" required></textarea>
                <div class="button-group">
                    <button type="submit">Save Changes</button>
                    <button type="button" onclick="closeEditModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function editComment(commentId, content) {
            document.getElementById('edit_comment_id').value = commentId;
            document.getElementById('edit_comment_content').value = content;
            document.getElementById('editCommentModal').style.display = 'flex';
        }

        function closeEditModal() {
            document.getElementById('editCommentModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target == document.getElementById('editCommentModal')) {
                closeEditModal();
            }
        }
    </script>

    <footer>
        <p>&copy; 2024 Crop Connect. All rights reserved. <a href="#">Privacy Policy</a> | <a href="#">Terms of Service</a></p>
    </footer>
</body>
</html>
