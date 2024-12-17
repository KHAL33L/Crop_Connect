<?php
session_start();
include '../actions/db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You must be logged in to edit a topic.'); window.location.href = 'signup.php';</script>";
    exit();
}

// Check if topic ID is provided
if (!isset($_GET['topic_id'])) {
    header("Location: profile.php");
    exit();
}

$topicId = $_GET['topic_id'];
$userId = $_SESSION['user_id'];

// Fetch topic details and verify ownership
$query = "SELECT * FROM forumtopics WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $topicId, $userId);
$stmt->execute();
$result = $stmt->get_result();
$topic = $result->fetch_assoc();

if (!$topic) {
    echo "<script>alert('You do not have permission to edit it.'); window.location.href = 'profile.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Topic</title>
    <link rel="stylesheet" href="../assets/css/profile.css">
    <style>
        .edit-topic-container {
            background-color: #2a2a2a;
            padding: 20px;
            border-radius: 8px;
            margin: 20px auto;
            max-width: 800px;
            color: #fff;
        }

        .edit-form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .form-group label {
            color: #7db72f;
            font-size: 16px;
        }

        .form-group input,
        .form-group textarea {
            padding: 10px;
            border-radius: 4px;
            background-color: #333;
            border: 1px solid #444;
            color: #fff;
        }

        .form-group textarea {
            min-height: 200px;
            resize: vertical;
        }

        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .button-group button {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        .save-button {
            background-color: #7db72f;
            color: white;
        }

        .save-button:hover {
            background-color: #6a9c28;
        }

        .cancel-button {
            background-color: #dc3545;
            color: white;
        }

        .cancel-button:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="edit-topic-container">
            <h1>Edit Topic</h1>
            <form class="edit-form" action="../actions/update_topic.php" method="POST">
                <input type="hidden" name="topic_id" value="<?php echo $topicId; ?>">
                
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($topic['title']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="description">Content</label>
                    <textarea id="description" name="description" required><?php echo htmlspecialchars($topic['description']); ?></textarea>
                </div>

                <div class="button-group">
                    <button type="submit" class="save-button">Save Changes</button>
                    <button type="button" class="cancel-button" onclick="window.location.href='profile.php'">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
