<?php
session_start();

require_once __DIR__ . '/../public/assets/css/css_paths.php';
require_once __DIR__ . '/helpers.php';
$page_css = $page_css ?? [];

$is_logged_in = isset($_SESSION['user_id']) && $_SESSION['logged_in'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="./public/assets/images/blogging-software-ico.ico" type="image/x-icon"/>
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
    <div class="mobile-menu-overlay" id="mobileMenuOverlay"></div>
    <header>
        <nav aria-label="Main navigation">
            <div class="logo">
                <a href="<?= base_url('/') ?>">Blog Central</a>
            </div>

            <!-- mobile menu -->
            <button class="mobile-menu-button" aria-label="Toggle menu" id="mobileMenuButton">
                <span></span>
                <span></span>
                <span></span>
            </button>

            <ul class="nav-links" id="navLinks">
                <?php if($is_logged_in): ?>
                    <li><a href="<?= base_url('/src/controllers/logout-process.php') ?>">Logout</a></li>
                    <li><a href="<?= base_url('/editor/post/post.php') ?>" class="new-post-btn">
                        <i class="icon-plus"></i> New Post
                    </a></li>
                <?php else: ?>
                    <li><a href="<?= base_url('/public/admin-login.php') ?>">Login</a></li>
                    <li><a href="<?= base_url('/public/registration.php') ?>">Register</a></li>
                <?php endif; ?>
                
                <li><a href="<?= base_url('/public/admin-dashboard.php') ?>">
                    <?= $is_logged_in ? 'My Dashboard' : 'Demo' ?>
                </a></li>
                
                <li><a href="<?= base_url('/public/markdown-guide.html') ?>">
                    <i class="icon-help"></i> Markdown Guide
                </a></li>
            </ul>
        </nav>
    </header>

    <script>
        // Mobile menu functionality
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.getElementById('mobileMenuButton');
            const navLinks = document.getElementById('navLinks');
            const overlay = document.getElementById('mobileMenuOverlay');
            
            // Toggle mobile menu
            function toggleMobileMenu() {
                mobileMenuButton.classList.toggle('active');
                navLinks.classList.toggle('active');
                overlay.classList.toggle('active');
                
                // Prevent body scroll when menu is open
                if (navLinks.classList.contains('active')) {
                    document.body.style.overflow = 'hidden';
                } else {
                    document.body.style.overflow = '';
                }
            }
            
            // Close mobile menu
            function closeMobileMenu() {
                mobileMenuButton.classList.remove('active');
                navLinks.classList.remove('active');
                overlay.classList.remove('active');
                document.body.style.overflow = '';
            }
            
            // Event listeners
            mobileMenuButton.addEventListener('click', toggleMobileMenu);
            overlay.addEventListener('click', closeMobileMenu);
            
            // Close menu when clicking on a nav link
            const navLinksItems = navLinks.querySelectorAll('a');
            navLinksItems.forEach(link => {
                link.addEventListener('click', closeMobileMenu);
            });
            
            // Close menu on window resize if open
            window.addEventListener('resize', function() {
                if (window.innerWidth > 768 && navLinks.classList.contains('active')) {
                    closeMobileMenu();
                }
            });
            
            // Handle escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && navLinks.classList.contains('active')) {
                    closeMobileMenu();
                }
            });
        });
    </script>

