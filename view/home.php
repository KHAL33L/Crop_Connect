<?php
session_start();
// If user is not logged in, redirect to login page
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include '../actions/db_connection.php';

// Get search parameters
$search = isset($_GET['search']) ? $_GET['search'] : '';
$timeFilter = isset($_GET['time_filter']) ? $_GET['time_filter'] : 'all';

// Base query
$query = "SELECT p.*, CONCAT(u.first_name, ' ', u.last_name) as author_name 
          FROM posts p 
          JOIN users u ON p.user_id = u.id 
          WHERE 1=1";
$params = [];
$types = "";

// Add search condition if search term is provided
if (!empty($search)) {
    $query .= " AND (p.title LIKE ? OR p.content LIKE ?)";
    $searchParam = "%$search%";
    $params[] = $searchParam;
    $params[] = $searchParam;
    $types .= "ss";
}

// Add time filter condition
$currentDate = date('Y-m-d H:i:s');
switch ($timeFilter) {
    case 'today':
        $query .= " AND DATE(p.created_at) = CURDATE()";
        break;
    case 'week':
        $query .= " AND p.created_at >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK)";
        break;
    case 'month':
        $query .= " AND p.created_at >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
        break;
}

// Order by creation date
$query .= " ORDER BY p.created_at DESC";

// Prepare and execute the query
$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Crop Connect</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="../assets/css/home.css">
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
        <div class="home-container">
            <!-- Filters Section -->
            <div class="filters-section">
                <form action="" method="GET" class="filter-form">
                    <div class="search-box">
                        <input type="text" name="search" placeholder="Search..." value="<?php echo htmlspecialchars($search); ?>">
                        <button type="submit"><i class="fas fa-search"></i></button>
                    </div>
                    
                    <div class="filter-options">
                        <select name="time_filter">
                            <option value="all" <?php echo $timeFilter === 'all' ? 'selected' : ''; ?>>All Time</option>
                            <option value="today" <?php echo $timeFilter === 'today' ? 'selected' : ''; ?>>Today</option>
                            <option value="week" <?php echo $timeFilter === 'week' ? 'selected' : ''; ?>>This Week</option>
                            <option value="month" <?php echo $timeFilter === 'month' ? 'selected' : ''; ?>>This Month</option>
                        </select>
                    </div>
                </form>
                <button class="create-post-btn" id="createPostBtn">Create Post</button>
            </div>

            <!-- Posts Section -->
            <div class="    posts-section">
                <?php if ($result->num_rows > 0): ?>
                    <?php while($post = $result->fetch_assoc()): ?>
                        <div class="post-card" onclick="window.location.href='../actions/view_post.php?post_id=<?php echo $post['id']; ?>'">
                            <div class="post-content">
                                <div class="post-header">
                                    <h2 class="post-title"><?php echo htmlspecialchars($post['title']); ?></h2>
                                    <div class="post-meta">
                                        <span>By <?php echo htmlspecialchars($post['author_name']); ?></span>
                                        <span><?php echo date('F j, Y', strtotime($post['created_at'])); ?></span>
                                    </div>
                                </div>
                                <p class="post-excerpt">
                                    <?php
                                    $content = htmlspecialchars($post['content']);
                                    echo strlen($content) > 200 ? substr($content, 0, 200) . '...' : $content;
                                    ?>
                                </p>
                            </div>
                            <?php if (!empty($post['image_url'])): ?>
                                <div class="post-image-indicator">
                                    <i class="fas fa-image"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="no-posts">No posts found. Be the first to create a post!</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal for Adding Posts -->
    <div id="createPostModal" class="modal">
        <div class="modal-content">
            <h3>Create a New Post</h3>
            <form action="../actions/add_post.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="postTitle">Title</label>
                    <input type="text" name="title" id="postTitle" required>
                </div>

                <div class="form-group">
                    <label for="postContent">Content</label>
                    <textarea name="content" id="postContent" required></textarea>
                </div>

                <!-- <div class="form-group">
                    <label for="postImage">Optional Image</label>
                    <input type="file" name="image" id="postImage" accept="image/*">
                </div> -->

                <div class="form-buttons">
                    <button type="submit" class="submit-btn">Create Post</button>
                    <button type="button" class="cancel-btn" id="closeModalBtn">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <footer>
        <p>&copy; 2024 Crop Connect. All rights reserved. <a href="#">Privacy Policy</a> | <a href="#">Terms of Service</a></p>
    </footer>

    <script>
        // Get modal elements
        const modal = document.getElementById('createPostModal');
        const createPostBtn = document.getElementById('createPostBtn');
        const closeModalBtn = document.getElementById('closeModalBtn');

        // Open modal when Create Post button is clicked
        createPostBtn.onclick = function() {
            modal.style.display = "flex";
            document.body.style.overflow = "hidden"; // Prevent scrolling when modal is open
        }

        // Close modal when Close button is clicked
        closeModalBtn.onclick = function() {
            modal.style.display = "none";
            document.body.style.overflow = "auto"; // Re-enable scrolling
        }

        // Close modal when clicking outside of it
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
                document.body.style.overflow = "auto"; // Re-enable scrolling
            }
        }
    </script>
</body>
</html>
