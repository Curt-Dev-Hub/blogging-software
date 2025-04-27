<?php 
define('ROOT_DIR', dirname(dirname(__DIR__)));

require_once ROOT_DIR . '/config/dbconfig.inc.php';
require_once ROOT_DIR . '/config/headers.inc.php';


if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    $errors = [];

    //Retrieve + sanitize form data 
    $entered_username = trim(htmlspecialchars($_POST['username']));
    $entered_password = $_POST['password_1'];

    if (empty($entered_username))
    {
        $errors['username'] = "Please enter a username.";
    }
    if (empty($entered_password))
    {
        $errors['password_1'] = "Please enter a password.";
    }

    // If there are validation errors, redirect back with errors
    if (!empty($errors)) {
        header("Location: ../../public/admin-login.php?errors=" . urlencode(json_encode($errors)));
        exit();
    }

    // Prepare SQL statement to check user credentials
    $stmt = $conn->prepare("SELECT * FROM admin_user WHERE user_name = ?");
    $stmt->bind_param("s", $entered_username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if user exists and password matches
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        
        if (password_verify($entered_password, $row['user_password'])) {
            session_start();
            
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_name'] = $row['user_name'];
            $_SESSION['logged_in'] = true;

            // regenerate session id - security measure
            session_regenerate_id(true);

            header("Location: ../../public/admin-dashboard.php");
            exit();
        } else {
            $errors = [
                'password_1' => "Invalid username or password"
            ];
            header("Location: ../../public/admin-login.php?errors=" . urlencode(json_encode($errors)));
            exit();
        }
    } else {
        $errors = [
            'username' => "Invalid username or password"
        ];
        header("Location: ../../public/admin-login.php?errors=" . urlencode(json_encode($errors)));
        exit();
    }
        $stmt->close();
} else {
    header("Location: ../../public/admin-login.php");
    exit();
}



