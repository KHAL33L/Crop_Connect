<?php
session_start();
// If user is not logged in, redirect to login page
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include '../actions/db_connection.php';

// Get the user ID and role from session
$userId = $_SESSION['user_id'];
$userRole = $_SESSION['role'] ?? 0;

// Fetch user data (first name, last name, and email) from the database
$query = "SELECT first_name, last_name, email, role FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "User not found.";
    exit();
}

// Concatenate first name and last name to form the username
$username = $user['first_name'] . " " . $user['last_name'];
$email = $user['email'];

// Fetch posts and topics based on user role
if ($userRole == 1) {  // Super Admin
    // Fetch ALL posts
    $postQuery = "SELECT p.*, CONCAT(u.first_name, ' ', u.last_name) as author_name FROM posts p 
                  JOIN users u ON p.user_id = u.id 
                  ORDER BY p.created_at DESC";
    $stmt = $conn->prepare($postQuery);
    $stmt->execute();
    $postsResult = $stmt->get_result();

    // Fetch ALL topics
    $topicQuery = "SELECT ft.*, CONCAT(u.first_name, ' ', u.last_name) as author_name FROM forumtopics ft 
                   JOIN users u ON ft.user_id = u.id 
                   ORDER BY ft.created_at DESC";
    $stmt = $conn->prepare($topicQuery);
    $stmt->execute();
    $topicsResult = $stmt->get_result();

    // Fetch ALL users
    $usersQuery = "SELECT id, first_name, last_name, email, role FROM users WHERE id != ?";
    $stmt = $conn->prepare($usersQuery);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $usersResult = $stmt->get_result();

    // Fetch total statistics for analytics
    $statsQuery = "
        SELECT 
            (SELECT COUNT(*) FROM users) as total_users,
            (SELECT COUNT(*) FROM posts) as total_posts,
            (SELECT COUNT(*) FROM forumtopics) as total_topics
    ";
    $statsStmt = $conn->prepare($statsQuery);
    $statsStmt->execute();
    $statsResult = $statsStmt->get_result();
    $stats = $statsResult->fetch_assoc();
} else {
    // Regular user - fetch their own posts and topics
    $postQuery = "SELECT * FROM posts WHERE user_id = ? ORDER BY created_at DESC";
    $stmt = $conn->prepare($postQuery);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $postsResult = $stmt->get_result();

    $topicQuery = "SELECT * FROM forumtopics WHERE user_id = ? ORDER BY created_at DESC";
    $stmt = $conn->prepare($topicQuery);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $topicsResult = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Crop Connect</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="../assets/css/profile.css">
    <style>
        .admin-users-section {
            background-color: var(--light-grey);
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
        }

        .user-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
        }

        .user-card {
            background-color: var(--medium-grey);
            border-radius: 5px;
            padding: 15px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .user-actions {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        .view-btn, .delete-btn {
            padding: 5px 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-size: 12px;
        }

        .view-btn {
            background-color: #81d01a;
            color: white;
        }

        .delete-btn {
            background-color: #dc3545;
            color: white;
        }
    </style>
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
        <!-- Profile Details Section -->
        <div class="profile-container">
            <div class="profile-info">
                <div class="profile-details">
                    <h2>Welcome, <?php echo htmlspecialchars($username); ?></h2>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
                    <p><strong>Username:</strong> <?php echo htmlspecialchars($username); ?></p>
                    <p><strong>Role:</strong> <?php echo $userRole == 1 ? 'Admin' : 'User'; ?></p>
                    [ <a href="../actions/edit_profile.php">Edit Profile</a> ] 
                </div>
                
                <?php if ($userRole == 1): ?>
                    <div class="profile-analytics">
                        <h3>Quick Analytics</h3>
                        <div class="analytics-summary">
                            <div class="analytics-item">
                                <div class="analytics-text">
                                    <strong><?php echo $stats['total_users']; ?> <p> Users</p></strong>  
                                </div>
                            </div>
                            <div class="analytics-item">
                                <div class="analytics-text">
                                    <strong><?php echo $stats['total_posts']; ?> <p> Posts</p></strong> 
                                </div>
                            </div>
                            <div class="analytics-item">
                                <div class="analytics-text">
                                    <strong><?php echo $stats['total_topics']; ?> <p> Topics</p></strong> 
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="content-container">
            <!-- Left Section: Posts -->
            <div class="posts-section">
                <!-- User's Posts -->
                <div class="posts-container">
                    <h2><?php echo $userRole == 1 ? 'All Posts' : 'Your Posts'; ?></h2>
                    <?php while ($post = $postsResult->fetch_assoc()): ?>
                        <div class="post-item">
                            <h4>
                                <?php echo htmlspecialchars($post['title']); ?>
                                <?php if ($userRole == 1): ?>
                                    <small>(by <?php echo htmlspecialchars($post['author_name']); ?>)</small>
                                <?php endif; ?>
                            </h4>
                            <p>
                                <?php 
                                $snippet = htmlspecialchars($post['content']);
                                echo strlen($snippet) > 100 ? substr($snippet, 0, 160) . '...' : $snippet;
                                ?>
                            </p>
                            <?php if (!empty($post['image_path'])): ?>
                                <img src="<?php echo htmlspecialchars($post['image_path']); ?>" alt="Post Image">
                            <?php endif; ?>
                            <p class="post-date">Posted on: <?php echo date('M d, Y', strtotime($post['created_at'])); ?></p>
                            <div class="action-buttons">
                                <a href="../actions/view_post.php?post_id=<?php echo $post['id']; ?>">View</a>
                                <?php if ($userRole == 1 || $post['user_id'] == $userId): ?>
                                    <a href="../actions/edit_post.php?post_id=<?php echo $post['id']; ?>">Edit</a>
                                    <form action="../actions/delete_post.php" method="POST" style="display:inline;" onsubmit="return confirmDelete('post')">
                                        <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                        <button type="submit">Delete</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <!-- Right Section: Topics -->
            <div class="topics-section">
                <!-- User's Topics -->
                <div class="topics-container">
                    <h2><?php echo $userRole == 1 ? 'All Topics' : 'Your Topics'; ?></h2>
                    <?php while ($topic = $topicsResult->fetch_assoc()): ?>
                        <div class="topic-item">
                            <h4>
                                <?php echo htmlspecialchars($topic['title']); ?>
                                <?php if ($userRole == 1): ?>
                                    <small>(by <?php echo htmlspecialchars($topic['author_name']); ?>)</small>
                                <?php endif; ?>
                            </h4>
                            <p>
                                <?php 
                                $snippet = htmlspecialchars($topic['description']);
                                echo strlen($snippet) > 100 ? substr($snippet, 0, 120) . '...' : $snippet;
                                ?>
                            </p>
                            
                            <p class="topic-date">Created on: <?php echo date('M d, Y', strtotime($topic['created_at'])); ?></p>
                            <div class="action-buttons">
                                <a href="view_topic.php?topic_id=<?php echo $topic['id']; ?>">View</a>
                                <?php if ($userRole == 1 || $topic['user_id'] == $userId): ?>
                                    <a href="edit_topic.php?topic_id=<?php echo $topic['id']; ?>">Edit</a>
                                    <form action="../actions/delete_topic.php" method="POST" style="display:inline;" onsubmit="return confirmDelete('topic')">
                                        <input type="hidden" name="topic_id" value="<?php echo $topic['id']; ?>">
                                        <button type="submit">Delete</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <?php if ($userRole == 1): ?>
                <!-- Admin Users Section -->
                <div class="users-section">
                    <div class="topics-container">
                        <h2>All Users</h2>
                        <?php while ($user = $usersResult->fetch_assoc()): ?>
                            <div class="topic-item user-item">
                                <div class="user-details">
                                    <h4><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h4>
                                    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                                    <p><strong>Role:</strong> <?php echo $user['role'] == 1 ? 'Admin' : 'User'; ?></p>
                                </div>
                                
                                <div class="action-buttons">
                                    <button class="view-btn view-profile-btn" 
                                        data-user-id="<?php echo $user['id']; ?>"
                                        data-first-name="<?php echo htmlspecialchars($user['first_name']); ?>"
                                        data-last-name="<?php echo htmlspecialchars($user['last_name']); ?>"
                                        data-email="<?php echo htmlspecialchars($user['email']); ?>"
                                        data-role="<?php echo $user['role'] == 1 ? 'Admin' : 'User'; ?>">
                                        View Profile
                                    </button>
                                    <button class="delete-btn" type="submit" onclick="confirmDeleteUser(<?php echo $user['id']; ?>)">Delete User</button>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- User Profile Modal -->
        <div id="userProfileModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2 id="modalUserName"></h2>
                <div class="user-profile-details">
                    <p><strong>Email:</strong> <span id="modalUserEmail"></span></p>
                    <p><strong>Role:</strong> <span id="modalUserRole"></span></p>
                    <p><strong>Total Posts:</strong> <span id="modalUserTotalPosts"></span></p>
                    <p><strong>Total Topics:</strong> <span id="modalUserTotalTopics"></span></p>
                </div>
            </div>
        </div>

        <!-- Modals for Creating Posts and Topics (same as before) -->
        <div id="createPostModal" class="modal">
            <!-- Post Modal Content -->
        </div>

        <div id="createTopicModal" class="modal">
            <!-- Topic Modal Content -->
        </div>
    </div>

    <footer>
        <p>&copy; 2024 Crop Connect. All rights reserved. <a href="#">Privacy Policy</a> | <a href="#">Terms of Service</a></p>
    </footer>

    <script>
        function confirmDelete(type) {
            return confirm(`Are you sure you want to delete this ${type}?`);
        }

        function confirmDeleteUser(userId) {
            if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
                window.location.href = `../actions/delete_user.php?user_id=${userId}`;
            }
        }

        // User Profile Modal Functionality
        document.addEventListener('DOMContentLoaded', function() {
            const viewProfileButtons = document.querySelectorAll('.view-profile-btn');
            const modal = document.getElementById('userProfileModal');
            const closeModal = document.querySelector('#userProfileModal .close');

            viewProfileButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const userId = this.getAttribute('data-user-id');
                    const firstName = this.getAttribute('data-first-name');
                    const lastName = this.getAttribute('data-last-name');
                    const email = this.getAttribute('data-email');
                    const role = this.getAttribute('data-role');

                    // Fetch total posts and topics via AJAX
                    fetch(`../actions/get_user_stats.php?user_id=${userId}`)
                        .then(response => response.json())
                        .then(data => {
                            document.getElementById('modalUserName').textContent = `${firstName} ${lastName}`;
                            document.getElementById('modalUserEmail').textContent = email;
                            document.getElementById('modalUserRole').textContent = role;
                            document.getElementById('modalUserTotalPosts').textContent = data.total_posts;
                            document.getElementById('modalUserTotalTopics').textContent = data.total_topics;

                            modal.style.display = 'block';
                        })
                        .catch(error => {
                            console.error('Error fetching user stats:', error);
                            alert('Failed to load user statistics.');
                        });
                });
            });

            // Close modal when clicking the close button
            closeModal.addEventListener('click', function() {
                modal.style.display = 'none';
            });

            // Close modal when clicking outside of it
            window.addEventListener('click', function(event) {
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>
