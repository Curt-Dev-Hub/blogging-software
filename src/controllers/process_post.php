<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

$post_id = filter_input(INPUT_POST, 'post_id', FILTER_VALIDATE_INT);
$title = trim(filter_input(INPUT_POST, 'title', FILTER_UNSAFE_RAW));
$summary = trim(filter_input(INPUT_POST, 'summary', FILTER_UNSAFE_RAW));
$markdown_content = htmlspecialchars_decode(filter_input(INPUT_POST, 'content', FILTER_UNSAFE_RAW));
$action = filter_input(INPUT_POST, 'action', FILTER_UNSAFE_RAW) ?? 'publish'; // Default to 'publish' if not set

error_log("Raw title: " . $title);


$is_draft = ($action == 'draft') ? 1 : 0; 

$Parsedown = new Parsedown();
$html_content = $Parsedown->text($markdown_content);
error_log("DB content: " . $html_content);

// validation
$errors = [];
if(empty($title)) $errors[] = "Please be sure to add title";
if(empty($summary)) $errors[] = "Please be sure to add a summary";
if(empty($markdown_content)) $errors[] = "Please be sure to add your content in markdown format";


if (!empty($errors)) 
{
    $_SESSION['errors'] = $errors;
    header("Location:" . ROOT_DIR . "editor/post/post.php");
    exit;
}

$user = $_SESSION['user_id'];

try {
    if ($post_id)
    {
        // update existing draft
        $stmt = $conn->prepare("
            UPDATE posts SET
            title = ?,
            summary = ?,
            content = ?,
            markdown_content = ?,
            is_draft = ?,
            last_updated = NOW()
            WHERE id = ? AND user_id = ?
        ");

        //! Want To Add in Further Validation of Title, Summary and Content Before Binding

        $stmt->bind_param("ssssiii",
            $title,
            $summary,
            $html_content,
            $markdown_content,
            $is_draft,
            $post_id,
            $_SESSION['user_id']
        );
    } else {
        // Create new post / draft - 25/06/2025
        $stmt = $conn->prepare("INSERT INTO posts
        (user_id, title, summary, content, markdown_content, is_draft)
        VALUES (?,?,?,?,?,?)");
        
        $stmt->bind_param("issssi", $_SESSION['user_id'], $title, $summary, $html_content, $markdown_content, $is_draft);
    }    
    if($stmt->execute())
    {
        $_SESSION['success_message'] = $is_draft ? 'Draft saved successfully!' : 'Post saved successfully!';
        header('Location: ' . ($is_draft ? '/blogging-software/editor/post/my-draft-posts.php' 
        : '/blogging-software/public/view-posts.php'));
        exit;
    } else {
        throw new Exception($stmt->error);
    }   
    } catch (Exception $e) {
        error_log("Save failed: " . $e->getMessage());
        $_SESSION['errors'] = ['Failed to save: ' . $e->getMessage()];
        header("Location: ../../editor/post/post.php?draft_id=$post_id");
        exit;
    }
    
    
