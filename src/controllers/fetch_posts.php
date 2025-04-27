<?php

// This file can have multiple purposes: 
// 1. Can fetch the number of posts for a user and return the count, we can then render the matching number of file images on the view posts page
//2. Can fetch a specific post and return the html content from the database
// 3. Can fetch all posts for a user and return the html content from the database

require_once '../config/dbconfig.inc.php';
require_once '../config/headers.inc.php';


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