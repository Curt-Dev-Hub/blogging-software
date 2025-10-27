<!-- <?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);

// require_once __DIR__ . '/../vendor/autoload.php';
// session_start();
// require_once __DIR__ . '/../config/dbconfig.inc.php';

// $token = $_GET['token'] ?? '';
// $valid_token = false;

// if ($token) {
//     // Validate token
//     $stmt = $conn->prepare("
//         SELECT pr.user_id, u.user_name 
//         FROM password_resets pr 
//         JOIN admin_user u ON pr.user_id = u.id 
//         WHERE pr.token = ? AND pr.expires_at > NOW() AND pr.used = 0
//     ");
//     $stmt->bind_param("s", $token);
//     $stmt->execute();
//     $result = $stmt->get_result();
    
//     if ($result->num_rows > 0) {
//         $valid_token = true;
//         $user_data = $result->fetch_assoc();
//     }
// }

// $page_title = "Reset Password";
// require_once '../includes/header.php';

if (!$valid_token): ?>
    <div class="auth-container">
        <h2>Invalid Reset Link</h2>
        <p>This password reset link is invalid or has expired.</p>
        <a href="forgot-password.php" class="btn btn-primary">Request New Link</a>
    </div>
<?php else: ?>
    <div class="auth-container">
        <h2>Reset Your Password</h2>
        <p>Hello <strong><?= htmlspecialchars($user_data['user_name']) ?></strong>, enter your new password below.</p>
        <form action="../src/controllers/reset_password_process.php" method="POST">  ** Add extra security here
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
            
            <div class="form-group">
                <label for="new_password">New Password:</label>
                <input type="password" name="new_password" id="new_password" required 
                       minlength="8" placeholder="Minimum 8 characters">
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm New Password:</label>
                <input type="password" name="confirm_password" id="confirm_password" required>
            </div>
            
            <button type="submit" class="btn btn-primary">Reset Password</button>
        </form>
    </div>
<?php endif; ?> -->



<!-- --------------------------------------------------------------------------- -->


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
$debug_info = '';

// DEBUG: Check what's actually in the database
if ($token) {
    $debug_stmt = $conn->prepare("
        SELECT pr.user_id, pr.token, pr.created_at, pr.expires_at, pr.used, u.user_name
        FROM password_resets pr 
        LEFT JOIN admin_user u ON pr.user_id = u.id 
        WHERE pr.token = ?
    ");
    $debug_stmt->bind_param("s", $token);
    $debug_stmt->execute();
    $debug_result = $debug_stmt->get_result();
    
    if ($debug_result->num_rows > 0) {
        $debug_data = $debug_result->fetch_assoc();
        $debug_info = "Found token in DB:<br>";
        $debug_info .= "User ID: " . $debug_data['user_id'] . "<br>";
        $debug_info .= "Token: " . $debug_data['token'] . "<br>";
        $debug_info .= "Created: " . $debug_data['created_at'] . "<br>";
        $debug_info .= "Expires: " . $debug_data['expires_at'] . "<br>";
        $debug_info .= "Used: " . $debug_data['used'] . "<br>";
        $debug_info .= "Username: " . ($debug_data['user_name'] ?? 'NOT FOUND') . "<br>";
        $debug_info .= "Current time: " . date('Y-m-d H:i:s') . "<br>";
        
        // Check if token is valid using created_at + 2 hours
        $created_time = strtotime($debug_data['created_at']);
        $current_time = time();
        $expiry_time = $created_time + (2 * 60 * 60); // 2 hours
        
        $debug_info .= "Token created: " . date('Y-m-d H:i:s', $created_time) . "<br>";
        $debug_info .= "Should expire: " . date('Y-m-d H:i:s', $expiry_time) . "<br>";
        $debug_info .= "Is expired: " . ($current_time > $expiry_time ? 'YES' : 'NO') . "<br>";
        $debug_info .= "Is used: " . ($debug_data['used'] ? 'YES' : 'NO') . "<br>";
        
        // Final validation
        if (!$debug_data['used'] && $current_time <= $expiry_time && !empty($debug_data['user_name'])) {
            $valid_token = true;
            $user_data = ['user_name' => $debug_data['user_name']];
            $debug_info .= "VALIDATION: PASSED<br>";
        } else {
            $debug_info .= "VALIDATION FAILED - Reason: ";
            if ($debug_data['used']) $debug_info .= "Token already used. ";
            if ($current_time > $expiry_time) $debug_info .= "Token expired. ";
            if (empty($debug_data['user_name'])) $debug_info .= "User not found. ";
            $debug_info .= "<br>";
        }
    } else {
        $debug_info = "Token not found in database at all.";
    }
}

$page_title = "Reset Password";
$page_css = [CSS_ADMIN_LOGIN];
require_once '../includes/header.php';

// Display debug info
echo "<div style='background: #f0f0f0; padding: 10px; margin: 10px; border: 1px solid #ccc;'>";
echo "<strong>Debug Info:</strong><br>" . $debug_info;
echo "</div>";

if (!$valid_token): ?>
    <div class="auth-container">
        <div class="header">
            <h1>Invalid Reset Link</h1>
        </div>
        <p>This password reset link is invalid or has expired.</p>
        <a href="forgot-password.php" class="btn btn-primary">Request New Link</a>
    </div>
<?php else: ?>
    <?php
        // display any errors
        //! Error showing above form - Need to update styling on these - 24/10/2025
        if (isset($_SESSION['errors'])) 
        {
            echo '<div class="error-message">';
            foreach ($_SESSION['errors'] as $error) {
                echo '<p>' . htmlspecialchars($error) . '</p>';
            }
            echo '</div>';
            unset($_SESSION['errors']);
        }
    ?>
    <div class="auth-container">
        <div class="class">
            <h1>Reset Your Password</h1>
        </div>
        <p>Hello <strong><?= htmlspecialchars($user_data['user_name']) ?></strong>, enter your new password below.</p>
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