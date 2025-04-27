<!DOCTYPE html>
<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
session_start();


if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) 
{
    echo "Please sign in";
    header('Location: admin-login.php');
    exit;
} 

if (isset($_GET['registration']) && $_GET['registration'] === 'success')
{
    echo "<div class='success-message'>Registration successful!</div>";
}

include_once './assets/css/css_paths.php';
$page_title = "Admin | Dashboard";
$page_css = [
    CSS_ADMIN_DASHBOARD
];

require_once '../includes/header.php';

?>
    <div class="dashboard-wrapper">
        <h1>What would you like to do <?php echo$_SESSION['user_name'] ?>?</h1>
        <div class="choice-group">
            <button type="button" onclick="window.location.href = '../editor/post/post.php';">Create New Post</button>
            <button type="button" onclick="window.location.href = '../public/view-posts.php';">View My Posts</button>
            <button type="button">Delete Post</button>
            <button type="button" onclick="window.location.href = './post/my-draft-posts.php';">Go to Drafts</button>
        </div>
        <a href="../src/controllers/logout-process.php">Logout</a>
    </div>
</body>
</html>