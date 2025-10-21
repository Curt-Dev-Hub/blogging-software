<!DOCTYPE html>
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
session_start();


if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo "Please sign in";
    header('Location: admin-login.php');
    exit;
}

if (isset($_GET['registration']) && $_GET['registration'] === 'success') {
    echo "<div class='success-message'>Registration successful!</div>";
}

include_once './assets/css/css_paths.php';
$page_title = "Admin | Dashboard";
$page_css = [
    CSS_ADMIN_DASHBOARD
];

require_once '../includes/header.php';

?>
<div class="dashboard-container">
    <div class="dashboard-wrapper">
        <!-- Conditional Rendering for new users -->
        <div class="success-toast" style="display: none;" id="successToast">
            <i class="fas fa-check-circle"></i>
            Registration successful!
        </div>
        <!-- Header Section -->
        <div class="header-section">
            <h1 class="welcome-title">Welcome Back <?php echo $_SESSION['user_name'] ?>!</h1>
            <p class="welcome-subtitle">What would you like to do today?</p>
            <div class="user-badge">
                <div class="user-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <span>Admin User</span>
            </div>
        </div>

        <!-- new Action cards grid -->
        <div class="dashboard-grid">
            <div class="action-card" onclick="navigateTo('../editor/post/post.php')">
                <div class="card-icon create">
                    <i class="fas fa-plus"></i>
                </div>
                <h3 class="card-title">Create New Post</h3>
                <p class="card-description">Start writing a new blog post or article with our advanced editor</p>
            </div>

            <div class="action-card" onclick="navigateTo('../public/view-posts.php')">
                <div class="card-icon view">
                    <i class="fas fa-eye"></i>
                </div>
                <h3 class="card-title">View My Posts</h3>
                <p class="card-description">Browse and manage all your published posts and articles</p>
            </div>

            <div class="action-card" onclick="handleDeletePost()">
                <div class="card-icon delete">
                    <i class="fas fa-trash-alt"></i>
                </div>
                <h3 class="card-title">Delete Post</h3>
                <p class="card-description">Remove unwanted posts from your collection permanently</p>
            </div>

            <div class="action-card" onclick="navigateTo('../editor/post/my-draft-posts.php')">
                <div class="card-icon drafts">
                    <i class="fas fa-file-alt"></i>
                </div>
                <h3 class="card-title">Go to Drafts</h3>
                <p class="card-description">Continue working on your saved drafts and unpublished content</p>
            </div>
        </div>
        <!-- Previous Iteration ----------- -->
        <!-- <div class="choice-group">
            <button type="button" onclick="window.location.href = '../editor/post/post.php';">Create New Post</button>
            <button type="button" onclick="window.location.href = '../public/view-posts.php';">View My Posts</button>
            <button type="button">Delete Post</button>
            <button type="button" onclick="window.location.href = '../editor/post/my-draft-posts.php';">Go to Drafts</button>
        </div> -->

        <!-- logout section -->
        <div class="logout-section">
            <a href="../src/controllers/logout-process.php" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i>
                Logout
            </a>
        </div>
    </div>
</div>
<script>
        // Navigation function
        function navigateTo(url) {
            window.location.href = url;
        }

        // Delete post handler
        function handleDeletePost() {
            if (confirm('Are you sure you want to delete a post? This action cannot be undone.')) {
                // Add your delete post logic here
                alert('Delete post functionality would be implemented here');
            }
        }

        // Show success toast if registration was successful
        function showSuccessToast() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('registration') === 'success') {
                document.getElementById('successToast').style.display = 'flex';
                setTimeout(() => {
                    document.getElementById('successToast').style.display = 'none';
                }, 4000);
            }
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            showSuccessToast();
            
            // Add keyboard navigation
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    document.getElementById('successToast').style.display = 'none';
                }
            });
        });

        // Add subtle parallax effect
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const parallax = document.querySelector('.dashboard-container');
            const speed = scrolled * 0.5;
            parallax.style.transform = `translateY(${speed}px)`;
        });
    </script>
</body>
</html>