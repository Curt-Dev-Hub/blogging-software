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
    CSS_HEADER
];
include_once '../includes/header.php';

$user_id = $_SESSION['user_id'] ?? null;
if(!$user_id)
{
    header('Location: admin-login.php');
    // could enter some code to display a message to the user that they need to login first
    exit;
}

$numPosts = numberOfPosts($user_id);


if($numPosts == 0)
{
    echo "<p class='error-text'>You have no posts to view</p>";
} else {
    $posts = fetchPosts($user_id);
    echo "<p class='success-text'>You have $numPosts posts to view</p>";
    // Fetch and display placeholder posts here
    echo "<div class='post-list'>";
    
    foreach($posts as $post)
    {
        echo "<div class='post'>";
        echo "<a class='post-link' href='your-post.php?id=" . htmlspecialchars($post['id']) . "'>";
        echo "<img class='doc-icon' src='./assets/images/text-document-svgrepo-com.svg' alt='Post'>";
        echo "<h2>" . htmlspecialchars($post['title']) . "</h2>";
        echo "</a>";
        echo "<div class='btn-group'>";
        echo "<a href='edit-post.php?id={$post['id']}' class='btn btn-primary'>Edit</a>";
        echo "<button onclick='confirmDelete({$post['id']})' class='btn btn-danger'>Delete</button>";
        echo "</div>";
        echo "</div>";
    }
    echo "</div>";
}

?>

<script>
function confirmDelete(postId) {
    if (confirm('Are you sure you want to delete this post?')) {
        window.location.href = '/blogging-software/src/controllers/delete_posts.php?id=' + postId;
    }
}
</script>
</body>
</html>

