<?php 
/**
 * Logout Process Handler
 * Handles user logout with proper session cleanup and security logging
 */

session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);


function logout()
{
    // check user was actually logged in
    $was_logged_in = isset($_SESSION['user_id']) || isset($_SESSION['user_name']);
    
    // Log attempts to logout without being logged in (potential issue or attack)
    if (!$was_logged_in) 
    {
        error_log(
            "Logout attempt without active session - " .
            "IP=" . ($_SERVER['REMOTE_ADDR'] ?? 'unknown') . 
            ", timestamp=" . date('Y-m-d H:i:s') .
            ", user_agent=" . ($_SERVER['HTTP_USER_AGENT'] ?? 'unknown')
        );
    }
    
    // storing username and user ID before destroying session
    $previous_user = $_SESSION['user_name'] ?? null;
    $user_id = $_SESSION['user_id'] ?? null;
    
    // log successful logout action for monitoring
    if ($user_id) 
    {
        error_log(
            "User logout: user_id={$user_id}, username=" . 
            ($previous_user ?? 'unknown') . 
            ", IP=" . ($_SERVER['REMOTE_ADDR'] ?? 'unknown') .
            ", timestamp=" . date('Y-m-d H:i:s')
        );
    }
    
    // determine if automatic logout (timeout) or user-initiated
    $is_auto_logout = isset($_GET['timeout']) || !isset($_GET['logout']);
    
    $_SESSION = [];
    
    // delete the session cookie from browser
    if (ini_get("session.use_cookies")) 
    {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 3600,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }
    
    session_destroy();
    
    // start new clean session for logout success page
    session_start();
    session_regenerate_id(true);
    
    // Store minimal data needed for success page display
    if ($previous_user) {
        $_SESSION['previous_user'] = $previous_user;
    }
    
    if ($is_auto_logout) {
        $_SESSION['is_auto_logout'] = true;
    }
    
    // Flag that temporary session should be destroyed after display
    $_SESSION['cleanup_session'] = true;
    
    // Redirect to logout success page
    header('Location: ../../public/logout-success.php');
    exit;
}

logout();