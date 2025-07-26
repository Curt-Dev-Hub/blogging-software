<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

header('Content-Type: text/html; charset=UTF-8');
session_start();

if(isset($_SESSION['success_message'])) {
    echo "<div class='success-message'>".htmlspecialchars($_SESSION['success_message'])."</div>";
    unset($_SESSION['success_message']);
}
if(isset($_SESSION['errors'])) {
    echo "<div class='error-message'>".htmlspecialchars(is_array($_SESSION['errors']) ? implode('<br>', $_SESSION['errors']) : $_SESSION['errors'])."</div>";
    unset($_SESSION['errors']);
}

require_once './assets/css/css_paths.php';
include_once '../src/controllers/fetch_posts.php';

$page_title = "Your Blog Posts";
$page_css = [
    CSS_VIEW_POSTS,
    CSS_HEADER
];
include_once '../includes/header.php';


if(!isset($_SESSION['user_id'])) {
    header('Location: /blogging-software/public/admin-login.php');
    exit;
}
$user_id = $_SESSION['user_id'];

$numPosts = numberOfPosts($user_id);


if($numPosts == 0)
{
    echo "<div class='no-posts-message'>";
    echo "<p class='error-text'>You have no posts to view</p>";
    echo "<a href='../editor/post/post.php' class='btn btn-primary'>Create Your First Post</a>";
    echo "</div>";
} else {
    $posts = fetchPosts($user_id);
    // New section - 06/07/2025
    echo "<div class='post-list-header'>";
    echo "<h2>Your Posts <span class='badge'>{$numPosts}</span></h2>";
    echo "<p class='success-text'>You have $numPosts posts to view</p>"; // original-may need to be removed
    echo "<a href='../editor/post/post.php' class='btn btn-new-post'>+ New Post</a>";
    echo "</div>";
    
    // Fetch and display placeholder posts here
    echo "<div class='post-list'>";
    
    foreach($posts as $post)
    {
        $is_draft = $post['is_draft'] ?? 0; //Draft status checking
        
        echo "<div class='post" . ($is_draft ? ' draft-post' : '') . "'>";
        if($is_draft)
        {
            echo "<p class='draft-text'>Post is a draft.</p>";
        }
        echo "<a class='post-link' href='your-post.php?id=" . htmlspecialchars($post['id']) . "'>";
        echo "<img class='doc-icon' src='./assets/images/text-document-svgrepo-com.svg' alt='Post'>";
        echo "<h2>" . htmlspecialchars($post['title']) . "</h2>";
        echo "</a>";
        echo "<div class='btn-group'>";
        echo "<a href='../editor/post/post.php?id={$post['id']}' class='btn btn-primary'>Edit</a>";
        echo "<button onclick='confirmDelete({$post['id']})' class='btn btn-danger'>Delete</button>";
        echo "</div>";
        echo "</div>";
    }
    echo "</div>";
}

?>

<script> 

function confirmDelete(postId) {
    if(confirm('Are you sure you want to permanently delete this post?')) {
        fetch(`/blogging-software/src/controllers/delete_posts.php?id=${postId}`, {
            credentials:"same-origin",
            redirect: "manual" // to handle redirects manually
        })
            .then(response => {
                if(response.status === 302 || response.ok) {
                    //Success - either got redirect or OK status
                    window.location.reload();
                } else {
                    throw new Error('HTTP error - status: ${response.status}');
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

