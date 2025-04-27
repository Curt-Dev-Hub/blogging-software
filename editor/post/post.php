<!DOCTYPE html>
<?php 
session_start(); 


if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo "Please sign in";
    header('Location: admin-login.php');
    // redirect to admin login page 
    exit;
} 

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
            <button type="submit">Create Post</button>
            <button type="button">Save Progress As Draft</button>
        </div>
    </form>
</body>
</html>

