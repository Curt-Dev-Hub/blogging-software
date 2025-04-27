<?php 
session_start();

function logout()
{
    $_SESSION = []; 
    
    $params = session_get_cookie_params(); // update session cookie
    // session ID is replaced with a blank string 
    // expiry date is set to the past - this causes the browser to stop sending it
    setcookie('PHPSESSID', '', time() - 3600, $params['path'], $params['domain'], $params['secure'], $params['httponly']);

    session_destroy();
    if (!isset($_GET['logout'])) { //checking if logout parameter is present in query string of the current URL
        header('Location: ../../index.php');
    }
}

logout();





