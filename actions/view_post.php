<?php
session_start();
include 'db_connection.php';

// Get the post ID from the URL
$postId = $_GET['post_id'];

// Fetch the post data from the database
$query = "SELECT * FROM posts WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $postId);
$stmt->execute();
$result = $stmt->get_result();
$post = $result->fetch_assoc();

if (!$post) {
    echo "Post not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Post - Crop Connect</title>
    <link rel="stylesheet" href="../assets/css/post.css">
    <style>
        /* General styles for the View Post page */
        body {
            font-family: Arial, sans-serif;
            background-color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .post-container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 30px;
            max-width: 800px;
            width: 90%;
            text-align: center;
        }
        h2 {
            color: #333;
            font-size: 2rem;
            margin-bottom: 20px;
        }
        p {
            margin: 20px 0;
            line-height: 1.6;
            font-size: 1.1rem;
            color: #555;
        }
        img {
            max-width: 100%;
            height: auto;
            display: block;
            margin: 20px auto;
            border-radius: 5px;
        }
        .back-to-profile {
            display: inline-block;
            background-color: #32a852;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            margin-top: 20px;
        }
        .back-to-profile:hover {
            background-color: #28a745;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="post-container">
            <h2><?php echo htmlspecialchars($post['title']); ?></h2>
            <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
            <?php if ($post['image_url']) { ?>
                <img src="<?php echo htmlspecialchars($post['image_url']); ?>" alt="Post Image">
            <?php } ?>
            <a href="../view/home.php" class="back-to-profile">Home</a>
            <a href="../view/profile.php" class="back-to-profile">Profile</a>
        </div>
    </div>
</body>
</html>