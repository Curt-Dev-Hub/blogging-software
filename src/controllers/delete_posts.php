<?php 

// Debugging during development
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

require_once __DIR__ . '/../../config/dbconfig.inc.php';

if(!isset($_SESSION['user_id']))
{
    header('Location: /public/admin-login');
    exit;
}

$post_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if(!$post_id)
{
    $_SESSION['errors'] = 'This post does not exist - Invalid post ID';
    header('Location: /public/view-posts.php');
    exit;
}

try {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM posts WHERE id = ? AND user_id = ?");
    $stmt->bind_param('ii', $post_id, $_SESSION['user_id']);
    
    if($stmt->execute())
    {
        if($stmt->affected_rows > 0)
        {
            $_SESSION['success_message'] = "Post deleted successfully";
        } else {
        $_SESSION['errors'] = ['Post not found or access denied'];
        }
    } else {
        throw new Exception($stmt->error);
    }   
} catch (Exception $e) {
    error_log("Delete post error: " . $e->getMessage());
    $_SESSION['errors'] = ['Error deleting post'];
}

header('Location: /blogging-software/public/my-posts.php');
exit;
