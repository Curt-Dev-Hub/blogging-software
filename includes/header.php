<?php
if (session_status() === PHP_SESSION_NONE) 
{
    session_start([
        'cookie_httponly' => true,
        'cookie_secure' => isset($_SERVER['HTTPS']),
        'use_strict_mode' => true
    ]);
} 
elseif(session_status() === PHP_SESSION_DISABLED)
{
    error_log("Sessions are disabled");
}


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
    <link rel="shortcut icon" href="/blogging-software/public/assets/images/blogging-software-ico.ico" type="image/x-icon"/>
    <title><?= htmlspecialchars($page_title ?? 'Blog Central') ?></title>
    <meta name="description" content="A platform to create and share blogs using markdown.">
    <script src="https://kit.fontawesome.com/4e5953b453.js" crossorigin="anonymous"></script>
    <meta name="keywords" content="blog, markdown, create, share">
    <meta name="author" content="Curt King">
    <link rel="stylesheet" href="<?= "/blogging-software/fonts/roboto.css" ?>">
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
                <i class="fa-solid fa-pencil"></i>
            </div>

            <!-- mobile menu -->
            <button class="mobile-menu-button" aria-label="Toggle menu" id="mobileMenuButton">
                <span></span>
                <span></span>
                <span></span>
            </button>

            <ul class="nav-links" id="navLinks">
                <div class="logo">
                <a href="<?= base_url('/') ?>">Blog Central</a>
                    <i class="fa-solid fa-pencil"></i>
                </div>
                <?php if($is_logged_in): ?>
                    <li><a class="form-nav-links" href="<?= base_url('/src/controllers/logout-process.php?logout=true') ?>">Logout</a></li>
                    <li><a class="form-nav-links" href="<?= base_url('/editor/post/post.php') ?>" class="new-post-btn">
                        <i class="fa-solid fa-plus"></i> New Post
                    </a></li>
                <?php else: ?>
                    <li><a class="form-nav-links" href="<?= base_url('/public/admin-login.php') ?>">Login</a></li>
                    <li><a class="form-nav-links" href="<?= base_url('/public/registration.php') ?>">Register</a></li>
                <?php endif; ?>
                
                <li><a class="form-nav-links" href="<?= base_url('/public/admin-dashboard.php') ?>">
                    <?= $is_logged_in ? 'My Dashboard' : 'Demo' ?>
                </a></li>
                
                <li><a class="form-nav-links" href="<?= base_url('/public/markdown-guide.html') ?>" target="_blank">
                    <i class="icon-help"></i> Markdown Guide
                </a></li>
            </ul>
        </nav>
    </header>

    <script>
        // mobile menu functionality
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.getElementById('mobileMenuButton');
            const navLinks = document.getElementById('navLinks');
            const overlay = document.getElementById('mobileMenuOverlay');
                
            function toggleMobileMenu() {
                mobileMenuButton.classList.toggle('active');
                navLinks.classList.toggle('active');
                overlay.classList.toggle('active');
                
                // prevent body scroll when menu is open
                if (navLinks.classList.contains('active')) {
                    document.body.style.overflow = 'hidden';
                } else {
                    document.body.style.overflow = '';
                }
            }
        
            function closeMobileMenu() {
                mobileMenuButton.classList.remove('active');
                navLinks.classList.remove('active');
                overlay.classList.remove('active');
                document.body.style.overflow = '';
            }
            
            mobileMenuButton.addEventListener('click', toggleMobileMenu);
            overlay.addEventListener('click', closeMobileMenu);
            
            // close menu when clicking on a nav link
            const navLinksItems = navLinks.querySelectorAll('a');
            navLinksItems.forEach(link => {
                link.addEventListener('click', closeMobileMenu);
            });
            
            // close menu on window resize if open
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

