<?php
require_once '../../config/headers.inc.php';
require_once '../../config/dbconfig.inc.php';


function registerUser($username, $email, $password)
{
    global $conn;
    $sql = "INSERT INTO admin_user (user_name, email, user_password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt->bind_param("sss", $username, $email, $hashed_password);
    return $stmt->execute();
}

function sanitiseInput($data)
{
    return htmlspecialchars(stripslashes(trim($data)));
}

function validateEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

$username = $email = $password = $confirm_password = "";
// $username_err = $password_err = $confirm_password_err = ""; testing
$errors = []; // new


if($_SERVER["REQUEST_METHOD"] == "POST")
{
    // validate username
    // $errors = []; testing comm'd out 22/810/2025
    if(empty(trim($_POST["username"])))
    {
        $errors["username"] = "Please enter a username.";
    } else {
        // check for existing username
        $username = sanitiseInput($_POST["username"]);
        $sql = "SELECT id FROM admin_user WHERE user_name = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if($stmt->num_rows > 0)
        {
            $errors["username"] = "This username already exists.";
        }
        $stmt->close();
    }

    // validate email
    if (empty(trim($_POST["email"]))) 
    {
        $errors["email"] = "Please enter an email address";
    } else {
        $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
        if(!validateEmail($email))
        {
            $errors["email"] = "Please enter a valid email address";
        } else {
            // checking for existing email
            $sql = "SELECT id FROM admin_user WHERE email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows() > 0)
            {
                $errors["email"] = "This email is already registered";
            }
            $stmt->close();
        }
    }


    // Validate password

    // ---------------------------------------------------------------------------------
    if (empty(trim($_POST["password_1"]))) {
        $errors["password"] = "Please enter a password.";
    } elseif (strlen(trim($_POST["password_1"])) < 8) {
        $errors["password"] = "Password must have at least 8 characters.";
    } elseif (!preg_match('/[A-Z]/', $_POST["password_1"])) {
        $errors["password"] = "Password must contain at least one uppercase letter.";
    } elseif (!preg_match('/[a-z]/', $_POST["password_1"])) {
        $errors["password"] = "Password must contain at least one lowercase letter.";
    } elseif (!preg_match('/[0-9]/', $_POST["password_1"])) {
        $errors["password"] = "Password must contain at least one number.";
    }

     // Validate confirm password
    if (empty(trim($_POST["confirm_password"]))) {
        $errors["confirm_password"] = "Please confirm password.";
    } elseif (trim($_POST["password_1"]) !== trim($_POST["confirm_password"])) {
        $errors["confirm_password"] = "Passwords do not match.";
    }



    // ---------------------------------------------------------------------------------
    // if (empty(trim($_POST["password_1"]))) 
    // {
    //     $errors = [
    //         "password" => "Please enter a password."
    //     ];
    // } elseif (strlen(trim($_POST["password_1"])) < 6) {
    //     $errors = [  
    //       "password" => "Password must have at least 6 characters."
    //     ];
    // }

    // // Validate confirm password
    // if (empty(trim($_POST["confirm_password"]))) {
    //     $errors = [
    //         "confirm_password" => "Please confirm password."
    //     ] ;
    // } elseif (trim($_POST["password_1"]) !== trim($_POST["confirm_password"])) {
    //     $errors = [
    //         "confirm_password" => "Passwords do not match."
    //     ];
    // }

    // If no errors, move forward with registration
    if (empty($errors)) {
        if (registerUser($username, $email, $_POST["password_1"])) {
            session_start();
            $_SESSION['user_name'] = $username;
            $_SESSION['logged_in'] = true;
            // Successful registration
            //Todo: set user unique id as session variable for later use - may be done - TEST TEST TEST

            // get user id for session - newly added - 22/10/25
            $sql = "SELECT id FROM admin_user WHERE username = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            $_SESSION['user_id'] = $user['id'];

            // successful registration
            header("Location: ../../public/admin-dashboard.php?registration=success");
            exit();
        } else {
            $errors['general'] = "Registration failed. Please try again later.";
            header("Location: ../../public/registration.php?errors=" . urlencode(json_encode($errors)) . "&username=" . urlencode($username) . "&email=" . urlencode($email));
            exit();
        }
    } else {
        // Redirect back with errors, preserving inputs
        header("Location: ../../public/registration.php?errors=" . urlencode(json_encode($errors)) . "&username=" . urlencode($username) . "&email=" . urlencode($email));
        // header("Location: ../../public/registration.php?errors=" . urlencode(json_encode($errors)));
        exit();
    }
} else {
    // Not a POST request
    header("Location: ../../public/registration.php");
    exit();
}
