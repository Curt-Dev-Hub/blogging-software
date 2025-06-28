<?php

// This file can have multiple purposes: 
// 1. Can fetch the number of posts for a user and return the count, we can then render the matching number of file images on the view posts page
//2. Can fetch a specific post and return the html content from the database
// 3. Can fetch all posts for a user and return the html content from the database

require_once __DIR__ . '/../../config/dbconfig.inc.php';
require_once __DIR__ . '/../../config/headers.inc.php';

function numberOfPosts($user_id)
{
    global $conn;
    $stmt = $conn->prepare("SELECT COUNT(*) as post_count FROM posts WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['post_count'];
}

function fetchPosts($user_id)
{
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM posts WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $posts = [];
    while ($row = $result->fetch_assoc()) {
        $posts[] = $row;
    }
    return $posts;
}

function getPostTitles($user_id)
{
    global $conn;
    $stmt = $conn->prepare("SELECT title FROM posts WHERE user_id = ?");
    $stmt->bind_param(("i"), $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $titles = [];
    while ($row = $result->fetch_assoc()) {
        $titles[] = $row['title'];
    }
    return $titles;
}

function getSinglePost($post_id, $user_id)
{
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM posts WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $post_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        return null; // Post not found or access denied
    }
}

function getDrafts($user_id) 
{
    global $conn;
    $stmt = $conn->prepare("
        SELECT * FROM posts
        WHERE user_id = ? AND is_draft = 1
        ORDER BY last_updated DESC
    ");
    $stmt->execute([$user_id]);
    $result = $stmt->get_result();
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    return $rows;
}

function getDraftById($post_id, $user_id) {
    global $conn;
    
    $stmt = $conn->prepare("
        SELECT * FROM posts 
        WHERE id = ?
        AND user_id = ?
        AND is_draft = 1 
        LIMIT 1
    ");
    
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        return false;
    }
    
    $stmt->bind_param("ii", $post_id, $user_id);
    
    if (!$stmt->execute()) {
        error_log("Execution failed: " . $stmt->error);
        $stmt->close();
        return false;
    }
    
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    
    return $row;
}