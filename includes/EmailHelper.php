<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/email.config.php';

USE PHPMailer\PHPMailer\PHPMailer;
USE PHPMailer\PHPMailer\Exception;

class EmailHelper {
    public static function sendPasswordResetEmail($userEmail, $userName, $token) {
        $mail = new PHPMailer(true);
        
        try {
            // server settings
            $mail->isSMTP();
            $mail->Host       = SMTP_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = SMTP_USERNAME;
            $mail->Password   = SMTP_PASSWORD;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = SMTP_PORT;
            
            // recipients
            $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
            $mail->addAddress($userEmail, $userName);
            
            // content
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request - Blog Central';
            
            $resetLink = APP_BASE_URL . '/reset-password.php?token=' . $token;
            
            $mail->Body = "
                <h2>Password Reset Request</h2>
                <p>Hello $userName,</p>
                <p>You requested a password reset for your Blog Central account.</p>
                <p>Click the link below to reset your password (expires in 30 minutes):</p>
                <p><a href='$resetLink' style='background: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Reset Password</a></p>
                <p>Or copy this link: $resetLink</p>
                <p>If you didn't request this, please ignore this email.</p>
            ";
            
            $mail->AltBody = "Password Reset Link: $resetLink";
            
            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Email sending failed: " . $mail->ErrorInfo);
            return false;
        }
    }
}
?>