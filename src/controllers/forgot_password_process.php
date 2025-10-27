<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require_once __DIR__ . '/../../vendor/autoload.php';
session_start();

require_once __DIR__ . '/../../config/dbconfig.inc.php';
require_once __DIR__ .'/../../config/email.config.php';
require_once __DIR__ . '/../../includes/EmailHelper.php';

if($_SERVER['REQUEST_METHOD'] === 'POST')
{
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    $stmt = $conn->prepare("SELECT id, user_name, email FROM admin_user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Generate secure token
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime(RESET_TOKEN_EXPIRY));
        
        // Invalidate any existing tokens for this user
        $stmt = $conn->prepare("UPDATE password_resets SET used = 1 WHERE user_id = ? AND used = 0");
        $stmt->bind_param("i", $user['id']);
        $stmt->execute();
        
        // Store new token
        $stmt = $conn->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user['id'], $token, $expires);
        
        if ($stmt->execute()) {
            // Send email
            if (EmailHelper::sendPasswordResetEmail($user['email'], $user['user_name'], $token)) {
                $_SESSION['success_message'] = "Password reset link sent to your email!";
            } else {
                $_SESSION['errors'] = ["Failed to send email. Please try again."];
            }
        }
    }
    
    // Always show success message (security best practice)
    $_SESSION['success_message'] = "If that email exists in our system, a reset link has been sent.";
    header('Location: /blogging-software/public/admin-login.php');
    exit;
}



//! NOTES: No success message shown when email successfully sent
//! URL for password resets - http://localhost/blogging-software/public/reset-password.php?token=703f3f7c36aa5306678e132c5c3d57fc478472f28b324e7c211373773809bede