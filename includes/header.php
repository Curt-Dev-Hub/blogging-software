<?php
require_once __DIR__ . '/../public/assets/css/css_paths.php';
require_once __DIR__ . '/helpers.php';
$page_css = $page_css ?? [];
//include_once './css/css_paths.php'; // works for index.php but causes error on view-posts.php 
// include_once '../css/css_paths.php'; causing error on index.php

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../public/assets/images/blogging-software-ico.ico" type="image/x-icon"/>
    <title><?= htmlspecialchars($page_title ?? 'Blog Central') ?></title>
    <meta name="description" content="A platform to create and share blogs using markdown.">
    <meta name="keywords" content="blog, markdown, create, share">
    <meta name="author" content="Curt King">
    <link rel="stylesheet" href="<?= "../fonts/roboto.css" ?>">
    <link rel="stylesheet" href="<?= CSS_HEADER ?>">
    <?php foreach ($page_css as $css_file): ?>
        <link rel="stylesheet" href="<?= htmlspecialchars($css_file) ?>">
    <?php endforeach; ?>
</head>
<body>
<div style="position:fixed;bottom:0;background:white;padding:10px;">
    Debug Info:<br>
    Current URL: <?= base_url('admin-dashboard.php') ?><br>
    Script Name: <?= $_SERVER['SCRIPT_NAME'] ?><br>
    Project Base: <?= rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/') ?>
</div>
    <header>
        <nav>
            <div class="logo">
                <a href="<?= base_url('/') ?>">Blog Central</a>
            </div>
            <ul class="nav-links">
                <li><a href="<?= base_url('/public/admin-login.php') ?>">Login</a></li>
                <li><a href="<?= base_url('/public/registration.php') ?>">Register</a></li>
                <li><a href="<?= base_url('/public/admin-dashboard.php') ?>">Dashboard</a></li>
                <li><a href="<?= base_url('/public/markdown-guide.html') ?>">Markdown Guide</a></li>
            </ul>
        </nav>
    </header>


    