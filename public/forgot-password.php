<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

include_once './assets/css/css_paths.php';
require_once __DIR__ . '/../vendor/autoload.php';
$page_title = "Forgot Password";
$page_css = [CSS_ADMIN_LOGIN];
require_once '../includes/header.php';

?>

<!--Check extra security needed here or on backend -->
<div class="auth-container">
    <div class="header">
        <h1>Reset Your Password</h1>
    </div>
    <form action="<?= htmlspecialchars('../src/controllers/forgot_password_process.php');?>" method="POST">
        <div class="input-group">
            <label for="email">Email Address:</label> 
            <input type="email" name="email" id="email" required 
                   placeholder="Enter your registered email"> 
        </div>
        <div class="input-group">
            <button type="submit" class="btn btn-primary">Send Reset Link</button>
        </div>
    </form>
    <p class="auth-link">
        <a class="form-nav-links" href="admin-login.php">Back to Login</a>
    </p>
</div>