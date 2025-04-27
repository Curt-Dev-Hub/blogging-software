<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

session_start();

if(!isset($_SESSION['user_id']))
{
    header('Location: admin-login.php');
    exit;
}

require_once './assets/css/css_paths.php';
include_once '../src/controllers/fetch_posts.php';

$page_title = 'Post | View';
$page_css = [
    CSS_VIEW_POST
];

include_once '../includes/header.php';

// 1. Validate the post ID
$post_id = $_GET['id'] ?? null;
if (!$post_id || !is_numeric($post_id)) {
    echo "<p class='error-text'>Invalid post ID.</p>";
    exit;
}

// 2. Fetch the post from the database
$user_id = $_SESSION['user_id'];
$page_title = "View Post";
$post = getSinglePost($post_id, $user_id);


if (!$post) {
    die("Post not found or access denied");
}
?>
    <div class="post-container">
        <h1><?= htmlspecialchars($post['title']) ?></h1>
        <h2><?= htmlspecialchars($post['summary']) ?></h2>
        <p><strong>Created on:</strong> <?= htmlspecialchars($post['created_at']) ?></p>
        <p><strong>Last updated:</strong> <?= htmlspecialchars($post['last_updated']) ?></p>
        <div class="post-content">
            <?= $post['content'] ?> 
        </div>
        <a href="view-posts.php" class="back-link">← Back to Posts</a>
    </div>
</body>
</html>