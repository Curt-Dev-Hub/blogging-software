<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
header('Content-Type: text/html; charset=UTF-8');
session_start();

require_once './assets/css/css_paths.php';
include_once '../src/controllers/fetch_posts.php';
$page_title = "Your Blog Posts";
$page_css = [
    CSS_VIEW_POSTS,
];
include_once '../includes/header.php';

if(!isset($_SESSION['user_id'])) {
    header('Location: /blogging-software/public/admin-login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$numPosts = numberOfPosts($user_id);
?>

<div class="view-posts-container">
    <?php if(isset($_SESSION['success_message'])): ?>
        <div class='alert alert-success'>
            <i class="fa-solid fa-circle-check"></i>
            <?= htmlspecialchars($_SESSION['success_message']) ?>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if(isset($_SESSION['errors'])): ?>
        <div class='alert alert-error'>
            <i class="fa-solid fa-circle-exclamation"></i>
            <?= htmlspecialchars(is_array($_SESSION['errors']) ? implode('<br>', $_SESSION['errors']) : $_SESSION['errors']) ?>
        </div>
        <?php unset($_SESSION['errors']); ?>
    <?php endif; ?>

    <?php if($numPosts == 0): ?>
        <div class='empty-state'>
            <div class='empty-state-icon'>
                <i class="fa-solid fa-file-pen"></i>
            </div>
            <h2>No Posts Yet</h2>
            <p>Start your blogging journey by creating your first post</p>
            <a href='../editor/post/post.php' class='btn btn-primary btn-large'>
                <i class="fa-solid fa-plus"></i> Create Your First Post
            </a>
        </div>
    <?php else: 
        $posts = fetchPosts($user_id);
    ?>
        <div class='posts-header'>
            <div class='header-content'>
                <h1>Your Posts</h1>
                <span class='post-count'><?= $numPosts ?> <?= $numPosts === 1 ? 'Post' : 'Posts' ?></span>
            </div>
            <a href='../editor/post/post.php' class='btn btn-primary'>
                <i class="fa-solid fa-plus"></i> New Post
            </a>
        </div>

        <div class='posts-grid'>
            <?php foreach($posts as $post): 
                $is_draft = $post['is_draft'] ?? 0;
                $post_id = htmlspecialchars($post['id'], ENT_QUOTES, 'UTF-8');
                $post_title = htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8');
                $created_date = isset($post['created_at']) ? date('M d, Y', strtotime($post['created_at'])) : 'N/A';
            ?>
                <div class='post-card <?= $is_draft ? 'is-draft' : '' ?>'>
                    <?php if($is_draft): ?>
                        <div class='draft-badge'>
                            <i class="fa-solid fa-file-lines"></i> Draft
                        </div>
                    <?php else: ?>
                        <div class='published-badge'>
                            <i class="fa-solid fa-circle-check"></i> Published
                        </div>
                    <?php endif; ?>
                    
                    <a href='your-post.php?id=<?= $post_id ?>' class='post-card-link'>
                        <div class='post-card-icon'>
                            <i class="fa-solid fa-file-lines"></i>
                        </div>
                        <h3 class='post-card-title'><?= $post_title ?></h3>
                        <p class='post-card-date'>
                            <i class="fa-regular fa-calendar"></i> <?= $created_date ?>
                        </p>
                    </a>
                    
                    <div class='post-card-actions'>
                        <a href='../editor/post/post.php?id=<?= $post_id ?>' class='btn btn-secondary'>
                            <i class="fa-solid fa-pen"></i> Edit
                        </a>
                        <button onclick='confirmDelete(<?= $post_id ?>)' class='btn btn-danger'>
                            <i class="fa-solid fa-trash"></i> Delete
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script> 
function confirmDelete(postId) {
    if(confirm('Are you sure you want to permanently delete this post?')) {
        fetch(`/blogging-software/src/controllers/delete_posts.php?id=${postId}`, {
            credentials: "same-origin",
            redirect: "manual"
        })
        .then(response => {
            if(response.status === 302 || response.ok) {
                window.location.reload();
            } else {
                throw new Error(`HTTP error - status: ${response.status}`);
            }    
        })
        .catch(error => {
            console.error('Delete error:', error);
            alert('Failed to delete post. Please try again');
        });    
    }
}
</script>
</body>
</html>
