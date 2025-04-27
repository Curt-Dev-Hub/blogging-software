<?php
session_start();


if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo "Please sign in";
    header('Location: admin-login.php');
    // redirect to admin login page 
    exit;
} else {
    // Otherwise if user is logged in, display dashboard content
echo "Hello, Guess What, You're only here because you are authenticated";
}





?>