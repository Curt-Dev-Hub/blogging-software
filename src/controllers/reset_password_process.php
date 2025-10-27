<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require_once __DIR__ . '/../../vendor/autoload.php';
session_start();
require_once __DIR__ . '/../../config/dbconfig.inc.php';

function validatePassword($password) {
    if(strlen($password) < 8)
    {
        return "Password must be at least 8 characters long.";
    }
    if (!preg_match('/[A-Z]/', $password)) 
    {
        return "Password must contain at least one uppercase letter.";
    }
    if (!preg_match('/[a-z]/', $password)) 
    {
        return "Password must contain at least one lowercase letter.";
    }
    if (!preg_match('/[0-9]/', $password)) 
    {
        return "Password must contain at least one number.";
    }

    return null;
}

if($_SERVER["REQUEST_METHOD"] === 'POST')
{
    $token = $_POST['token'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    $errors = [];

    // validate token presence
    if(empty($token))
    {
        $errors[] = "Invalid reset token.";
    }

    // validate passwords
    if(empty($new_password))
    {
        $errors[] = "New password id required.";
    } else {
        $password_error = validatePassword($new_password);

        if($password_error)
        {
            $errors[] = $password_error;
        }
    }

    if(empty($confirm_password))
    {
        $errors[] = "Please confirm your new password";
    } elseif ($new_password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }
     
    // no validation errors - proceed with reset
    if(empty($errors))
    {
        try {
            // atomic operation - all requests successful or nothing goes through
            $conn->begin_transaction();

            // verify token validity and get user_id
            $stmt = $conn->prepare("
                SELECT pr.user_id, u.user_name FROM password_resets pr
                JOIN admin_user u ON pr.user_id = u.id
                WHERE pr.token = ?
                AND pr.created_at > DATE_SUB(NOW(), INTERVAL 2 HOUR)
                AND pr.used = 0");

            $stmt->bind_param("s", $token);
            $stmt->execute();
            $result = $stmt->get_result();

            if($result->num_rows === 0)
            {
                $errors[] = 'Invalid or expired reset token. Please request a new reset link.';
            } else {
                $user_data = $result->fetch_assoc();
                $user_id = $user_data['user_id'];

                // hash new password
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                // update user password
                $update_stmt = $conn->prepare("
                    UPDATE admin_user 
                    SET user_password = ?
                    WHERE id = ?"
                );

                $update_stmt->bind_param("si", $hashed_password, $user_id);
                $update_stmt->execute();

                if (!$update_stmt->execute()) 
                {
                    throw new Exception("Failed to update password: " . $update_stmt->error);
                }

                // Mark token as used
                $mark_used_stmt = $conn->prepare("UPDATE password_resets SET used = 1 WHERE token = ?");
                $mark_used_stmt->bind_param("s", $token);

                if (!$mark_used_stmt->execute()) 
                {
                throw new Exception("Failed to mark token as used: " . $mark_used_stmt->error);
                }    

                 // Commit transaction
                $conn->commit();

                // Log the password change (for security monitoring)
                error_log("Password reset successful for user_id: " . $user_id . " (username: " . $user_data['user_name'] . ")");

                // Set success message and redirect to login
                $_SESSION['success_message'] = "Password reset successfully! You can now login with your new password.";
                header('Location: /blogging-software/public/admin-login.php');
                exit;
            }

        } catch(Exception $e) {
            // Rollback transaction on error
            $conn->rollback();
            error_log("Password reset error: " . $e->getMessage());
            $errors[] = "An error occurred while resetting your password. Please try again.";
        }
    }

    // If we have errors, redirect back to reset form with token preserved
    if (!empty($errors)) 
    {
        $_SESSION['errors'] = $errors;
        header('Location: /blogging-software/public/reset-password.php?token=' . urlencode($token));

        exit;
    } else {
        // Not a POST request - redirect to forgot password
        header('Location: /blogging-software/public/forgot-password.php');
        exit;
    }    
}