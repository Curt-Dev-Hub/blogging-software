<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

session_start();

if (!isset($_SESSION['logged_in'])) {
    header('Location: /blogging-software/public/admin-login.php');
    exit;
}

require_once __DIR__ . '/../../src/controllers/fetch_posts.php';

if (isset($_SESSION['errors'])) {
    $error_messages = $_SESSION['errors'];
    unset($_SESSION['errors']);
}

$draft = [];
$post_id = $_GET['draft_id'] ?? $_GET['id'] ?? null;
if ($post_id) {
    $draft = getPostById($post_id, $_SESSION['user_id']);
    if (!$draft) {
        $_SESSION['errors'] = ["Post not found or access denied"];
        header("Location: my-draft-posts.php");
        exit;
    }
}

include_once '../../public/assets/css/css_paths.php';

$page_title = isset($draft['id']) ? 'Edit Draft' : 'Create New Post';
$page_css = [ 
    CSS_POST_FORM,
    CSS_ROBOTO,
];

require_once __DIR__ . '/../../includes/header.php';
?>

<h1><?= $page_title ?></h1>

<form method="POST" action="<?= htmlspecialchars('/blogging-software/src/controllers/process_post.php') ?>">
     <label for="title">Title:</label>
        <input type="text" name="title" id="title" value="<?= htmlspecialchars($draft['title'] ?? '')?>" required>

        <label for="summary">Summary:</label>
        <textarea name="summary" id="summary" required><?= htmlspecialchars($draft['summary'] ?? '')?></textarea>

        <p>The main body content below should be written in markdown to be most effective, you can learn more about 
            markdown on the <a href="" target="_blank" class="doc-link">DILLINGER</a> website or if you prefer 
            <a href="../../public/markdown-guide.html" target="_blank" class="doc-link">view this cheatsheet</a>, or you can try 
            out this live markdown editor <a href="https://markdownlivepreview.com/" target="_blank" class="doc-link">here</a>.
        </p>   

        <label for="content">Content (<b>MUST Use Markdown</b>):</label>
        <textarea name="content" id="content" required><?=
         isset($draft['markdown_content']) ? htmlspecialchars($draft['markdown_content']) : '' 
        ?></textarea>

        <div class="button-wrap">
            <button type="submit" name="action" value="publish">Create Post</button>
            <button type="submit" name="action" value="draft">Save Progress As Draft</button>
            <input type="hidden" name="post_id" value="<?= $draft["id"] ?? '' ?>">
        </div>
</form>
</body>
</html>