<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);


include_once 'public/assets/css/css_paths.php';
$page_title = " Home | Markdown Blogging Platform";
$page_css = [];

require_once __DIR__ . '/includes/header.php';
?>

<ul class="background">
    <li></li>
    <li></li>
    <li></li>
    <li></li>
    <li></li>
    <li></li>
    <li></li>
    <li></li>
    <li></li>
    <li></li>
</ul>

<main class="landing-page">
    <section class="hero">
        <h1>Welcome to Blog Central</h1>
        <p>Create, save, and edit blog posts using Markdown. Join our community of writers today!</p>
        <div class="cta-buttons">
            <a href="./public/registration.php" class="cta-button">Get Started</a>
            <a href="./public/admin-login.php" class="cta-button">Login</a>
        </div>
    </section>

    <section class="features">
        <h2>Features</h2>
        <div class="feature-cards">
            <div class="card">
                <h3>Write in Markdown</h3>
                <p>Use Markdown to create beautifully formatted blog posts.</p>
                <a href="./public/markdown-guide.html" class="feature-link">Learn More</a>
            </div>
            <div class="card">
                <h3>Manage Your Posts</h3>
                <p>Save drafts, edit posts, and publish when you're ready.</p>
                <a href="./public/admin-dashboard.php" class="feature-link">Go to Dashboard</a>
            </div>
            <div class="card">
                <h3>Join the Community</h3>
                <p>Register today and start sharing your ideas with the world.</p>
                <a href="./public/registration.php" class="feature-link">Sign Up</a>
            </div>
        </div>
    </section>
</main>
</body>
</html>

