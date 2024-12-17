<?php
header('Content-Type: application/json');

// Include database connection
include 'db_connection.php';

// Check if user_id is provided
if (!isset($_GET['user_id'])) {
    echo json_encode(['error' => 'User ID is required']);
    exit();
}

$userId = intval($_GET['user_id']);

// Query to get total posts
$postQuery = "SELECT COUNT(*) as total_posts FROM posts WHERE user_id = ?";
$stmt = $conn->prepare($postQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$postResult = $stmt->get_result();
$postCount = $postResult->fetch_assoc()['total_posts'];

// Query to get total topics
$topicQuery = "SELECT COUNT(*) as total_topics FROM forumtopics WHERE user_id = ?";
$stmt = $conn->prepare($topicQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$topicResult = $stmt->get_result();
$topicCount = $topicResult->fetch_assoc()['total_topics'];

// Return JSON response
echo json_encode([
    'total_posts' => $postCount,
    'total_topics' => $topicCount
]);

$conn->close();
?>