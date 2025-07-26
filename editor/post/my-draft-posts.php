<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();


if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: admin-login.php'); 
    exit;
} else {
    require_once '../../src/controllers/fetch_posts.php';
    $drafts = getDrafts($_SESSION['user_id']); // Fetching drafts for the logged-in user
}

include_once '../../public/assets/css/css_paths.php';
$page_title = "Editor | My Drafts";
$page_css = [
    CSS_MY_DRAFTS
];

require_once '../../includes/header.php';

?>
<div>
    <?php
    if (empty($drafts))
    {
        echo "<div class='no-drafts'>";
        echo "<h2> You have no drafts</h2>";
        echo "<p>Start writing by <a href='../post/post.php'>creating a new post</a></p>";
        echo "</div>";
    } else {
        echo "<h2 class='text-center'>My Drafts</h2>";
        echo "<div class='container'>";
        echo "<table class='table table-striped'>";
        echo "<thead><tr><th>Title</th><th>Date Created</th><th>Actions</th></tr></thead>";
        echo "<tbody>";

        foreach ($drafts as $draft) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($draft['title'], ENT_QUOTES, 'UTF-8') . "</td>";
            echo "<td>" . $draft['created_at'] . "</td>";
            echo "<td>";
            echo "<a href='../post/post.php?id=" . $draft['id'] . "' class='btn btn-primary'>Edit</a> ";
            echo "<button onclick='confirmDelete({$draft['id']})' class='btn btn-danger'>Delete</button>";
            echo "</td>";
            echo "</tr>";
        }

        echo "</tbody>";
        echo "</table>";
        echo "</div>";
    }
    ?>
</div>
<script>
function confirmDelete(postId) {
    if(confirm('Are you sure you want to permanently delete this post?')) {
        fetch(`/blogging-software/src/controllers/delete_posts.php?id=${postId}`, {
            credentials:"same-origin"
        })
            .then(response => {
                if(response.ok) {
                    window.location.reload();
                } else {
                    throw new Error('Delete Failed');
                }    
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to delete post. Please try again');
            });    
    }
}
</script>
</body>
</html>