<?php
require_once '../../config/headers.inc.php';
require_once '../../config/dbconfig.inc.php';


function registerUser($username, $password)
{
    global $conn;
    $sql = "INSERT INTO admin_user (user_name, user_password) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt->bind_param("ss", $username, $hashed_password);
    return $stmt->execute();
}

function sanitiseInput($data)
{
    return htmlspecialchars(stripslashes(trim($data)));
}

$username = $password = $confirm_password = "";
$username_err = $password_err = $confirm_password_err = "";


if($_SERVER["REQUEST_METHOD"] == "POST")
{
    $errors = [];
    if(empty(trim($_POST["username"])))
    {
        $errors = [
            "username" => "Please enter a username."
        ];
    } else {
        $username = sanitiseInput($_POST["username"]);
        $sql = "SELECT id FROM admin_user WHERE user_name = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if($stmt->num_rows > 0)
        {
            $errors = [
                "username" => "This username is already taken."
            ];
        }
        $stmt->close();
    }

    // Validate password
    if (empty(trim($_POST["password_1"]))) {
        $errors = [
            "password" => "Please enter a password."
        ];
    } elseif (strlen(trim($_POST["password_1"])) < 6) {
        $errors = [  
          "password" => "Password must have at least 6 characters."
        ];
    }

    // Validate confirm password
    if (empty(trim($_POST["confirm_password"]))) {
        $errors = [
            "confirm_password" => "Please confirm password."
        ] ;
    } elseif (trim($_POST["password_1"]) !== trim($_POST["confirm_password"])) {
        $errors = [
            "confirm_password" => "Passwords do not match."
        ];
    }

    // If no errors, proceed with registration
    if (empty($errors)) {
        if (registerUser($username, $_POST["password_1"])) {
            session_start();
            $_SESSION['user_name'] = $username;
            $_SESSION['logged_in'] = true;
            // Successful registration
            //Todo: set user unique id as session variable for later use
            header("Location: ../../public/admin-dashboard.php?registration=success");
            exit();
        } else {
            $errors['general'] = "Registration failed. Please try again later.";
            header("Location: ../../public/registration.php?errors=" . urlencode(json_encode($errors)));
            exit();
        }
    } else {
        // Redirect back with errors
        header("Location: ../../public/registration.php?errors=" . urlencode(json_encode($errors)));
        exit();
    }
} else {
    // Not a POST request
    header("Location: ../../public/registration.php");
    exit();
}
