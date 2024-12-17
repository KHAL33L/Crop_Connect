<?php
session_start();
include 'db_connection.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../view/login.php");
    exit();
}

// Get the post ID from the URL
$postId = $_GET['post_id'];
$userId = $_SESSION['user_id'];

// Fetch the post data from the database
$query = "SELECT * FROM posts WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $postId, $userId);
$stmt->execute();
$result = $stmt->get_result();
$post = $result->fetch_assoc();

if (!$post) {
    echo "<script>alert('You do not have permission to edit it.'); window.location.href = '../view/profile.php';</script>";
    exit();
}

// Update post data after form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $imagePath = $post['image_url'];  // Keep the original image if none is uploaded

    if (!empty($_FILES['image']['name'])) {
        $targetDir = "uploads/";
        $imagePath = $targetDir . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
    }

    // Update the post in the database
    $updateQuery = "UPDATE posts SET title = ?, content = ?, image_url = ? WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("sssi", $title, $content, $imagePath, $postId);
    $stmt->execute();

    // Redirect to the profile page
    header("Location: ../view/profile.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Post - Crop Connect</title>
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
            max-width: 1200px;
            margin: 80px auto 20px;
            padding: 20px;
            min-width: 800px;
        }

        .edit-post-container {
            background-color: var(--light-grey);
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 1000px;
            margin: 0 auto;
        }

        h2 {
            color: #81d01a;
            margin-bottom: 30px;
            font-size: 28px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 10px;
            color: #fff;
            font-size: 16px;
        }

        input[type="text"],
        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--medium-grey);
            border-radius: 5px;
            background-color: var(--medium-grey);
            color: #fff;
            font-size: 16px;
        }

        textarea {
            min-height: 300px;
            resize: vertical;
        }

        .button-group {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            justify-content: flex-start;
        }

        button {
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
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

        .current-image {
            margin: 25px 0;
        }

        .current-image img {
            max-width: 100%;
            border-radius: 5px;
            margin-top: 10px;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #81d01a;
            text-decoration: none;
            transition: color 0.3s;
            font-size: 16px;
        }

        .back-link:hover {
            color: #6a9c28;
        }

        input[type="file"] {
            background-color: var(--medium-grey);
            color: #fff;
            padding: 10px;
            border-radius: 5px;
            width: 100%;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="edit-post-container">
            <h2>Edit Post</h2>
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="content">Content</label>
                    <textarea id="content" name="content" required><?php echo htmlspecialchars($post['content']); ?></textarea>
                </div>

                <?php if (!empty($post['image_url'])): ?>
                    <div class="current-image">
                        <label>Current Image</label>
                        <img src="<?php echo htmlspecialchars($post['image_url']); ?>" alt="Current Post Image">
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="image">New Image (optional)</label>
                    <input type="file" id="image" name="image" accept="image/*">
                </div>

                <div class="button-group">
                    <button type="submit">Save Changes</button>
                    <button type="button" class="cancel-btn" onclick="window.location.href='../view/profile.php'">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
