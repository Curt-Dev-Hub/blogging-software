<?php 
session_start();


include '../../config/dbconfig.inc.php';
include_once '../../config/headers.inc.php';
require '../../parsedown-1.7.4/Parsedown.php';


function sanitiseInput($data)
{
    return htmlspecialchars(stripslashes(trim($data)));
}

function decodeSpecialChars($data)
{
    return htmlspecialchars_decode($data);
}

// Retrieve form data 
$title = $_POST['title'];
$summary = $_POST['summary'];
$markdown_content = decodeSpecialChars($_POST['content']);

$Parsedown = new Parsedown();
$html_content = $Parsedown->text($markdown_content);


// Error Handling
$errors = [];

if(empty($_POST['title']))
{
    $errors[] = "Please be sure to add a title";
}

if(empty($_POST['summary']))
{
    $errors[] = "Please be sure to add a summary";
}

if(empty($_POST['content']))
{
    $errors[] = "Please be sure to add your content";
}

// set 'errors' session variable if errors are found 
if (!empty($errors)) 
{
    $_SESSION['errors'] = $errors;
    header("Location:" . ROOT_DIR . "editor/post/post.php");
    exit;
}


$user = $_SESSION['user_id'];

$is_draft_post = 'false';
                                            // TESTING
$stmt = $conn->prepare("INSERT INTO posts (user_id, title, summary, content, is_draft) VALUES (?,?,?,?,?)");
$stmt->bind_param("isssi", $user, $title, $summary, $html_content, $is_draft_post);

if($stmt->execute())
{
    $_SESSION['success_message'] = 'Post created successfully!';
    header('Location: ../../public/admin-dashboard.php'); // error
    exit;
} else {
    $_SESSION['errors'][] = 'Error creating post: ' . $stmt->error;
    header('Location: post.php');
    exit;
}


$conn->close();
exit;

