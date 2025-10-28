<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require_once __DIR__ . '/../vendor/autoload.php';
session_start();
require_once __DIR__ . '/../config/dbconfig.inc.php';
include_once './assets/css/css_paths.php';

$token = $_GET['token'] ?? '';
$valid_token = false;

if ($token) {
    // Validate token
    $stmt = $conn->prepare("
        SELECT pr.user_id, u.user_name 
        FROM password_resets pr 
        LEFT JOIN admin_user u ON pr.user_id = u.id 
        WHERE pr.token = ? AND pr.expires_at > NOW() AND pr.used = 0
    ");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $valid_token = true;
        $user_data = $result->fetch_assoc();
    }
}

$page_title = "Reset Password";
$page_css = [CSS_ADMIN_LOGIN];
require_once '../includes/header.php';

// Display any errors
if (isset($_SESSION['errors'])) {
    echo '<div class="error-message">';
    foreach ($_SESSION['errors'] as $error) {
        echo '<p>' . htmlspecialchars($error) . '</p>';
    }
    echo '</div>';
    unset($_SESSION['errors']);
}

if (!$valid_token): ?>
    <div class="auth-container">
        <div class="header">
            <h1>Invalid Reset Link</h1>
        </div>
        <p>This password reset link is invalid or has expired.</p>
        <a href="forgot-password.php" class="btn btn-primary">Request New Link</a>
    </div>
<?php else: ?>
    <div class="auth-container">
        <p>Hello <strong><?= htmlspecialchars($user_data['user_name']) ?></strong>, enter your new password below.</p>
        <div class="header">
            <h1>Reset Your Password</h1>
        </div>
        
        <form action="../src/controllers/reset_password_process.php" method="POST">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
            
            <div class="input-group">
                <label for="new_password">New Password:</label>
                <input type="password" name="new_password" id="new_password" required 
                       minlength="8" placeholder="Minimum 8 characters">
            </div>
            
            <div class="input-group">
                <label for="confirm_password">Confirm New Password:</label>
                <input type="password" name="confirm_password" id="confirm_password" required>
            </div>
            
            <div class="input-group">
                <button type="submit" class="btn btn-primary">Reset Password</button>
            </div>
        </form>
    </div>
<?php endif; ?>
