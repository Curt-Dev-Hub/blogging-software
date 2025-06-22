<!DOCTYPE html>
<?php 
session_start(); 


if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) 
{
    header('Location: admin-login.php');
    exit;
} 

require_once '../../src/controllers/fetch_posts.php';

if (isset($_SESSION['errors'])) 
{
    echo  "<p class='error-text'>Your form submission had the following errors:</p>";
    foreach ($_SESSION['errors'] as $error) {
        echo '<div class="error"> - '
         . $error . 
        '</div>';
    }
    unset($_SESSION['errors']);
}

// want to pre-fill the form if editing a draft - working on this 
$draft = [];
if (isset($_GET['draft_id'])) 
{
    $draft = getDraftById($_GET['draft_id'], $_SESSION['user_id']);
    if (!$draft)
    {
        $_SESSION['errors'] = ["Draft not found or access denied"];
        header("Location: my-draft-posts.php");
        exit;
    }
}

?>


<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="post.css">
    <link rel="stylesheet" href="<?php echo "../../fonts/roboto.css" ?>">
    <title>Create New Post</title>
</head>
<body>
    <h1>Create New Post</h1>
    
    <form method="POST" action="<?php echo htmlspecialchars('../../src/controllers/process_post.php');?>">
        <label for="title">Title:</label>
        <input type="text" name="title" id="title" required>

        <label for="summary">Summary:</label>
        <textarea name="summary" id="summary" required></textarea>

        <p>The main body content below should be written in markdown to be most effective, you can learn more about 
            markdown on the <a href="" target="_blank">DILLINGER</a> website or if you prefer 
            <a href="../../public/markdown-guide.html" target="_blank">view this cheatsheet</a>, or you can try 
            out this live markdown editor <a href="https://markdownlivepreview.com/" target="_blank">here</a>.
        </p>   

        <label for="content">Content (<b>Please Use Markdown</b>):</label>
        <textarea name="content" id="content" required></textarea>

        <div class="button-wrap">
            <button type="submit" name="action" value="publish">Create Post</button>
            <button type="submit" name="action" value="draft">Save Progress As Draft</button>
            <input type="hidden" name="post_id" value="<?= $draft["id"] ?? '' ?>">
        </div>
    </form>
</body>
</html>

