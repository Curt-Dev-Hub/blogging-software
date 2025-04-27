<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);

    include_once './assets/css/css_paths.php';

    $page_title = "Admin | Registration";
    $page_css = [
        CSS_ADMIN_LOGIN
    ];

    require_once '../includes/header.php';
?>

    <div class="header">
        <h1>Enter Your Registration Details</h1>
    </div>
    <?php
    if (isset($_GET['success'])) 
    {
        echo 'div class="success">' . htmlspecialchars($_GET['success']) . '</div>';
    }

    //error variables
    $username_err = $password_err = $confirm_password_err = '';

    //check for errors
    if (isset($_GET['errors'])) 
    {
        $errors = json_decode(urldecode($_GET['errors']), true);
        if ($errors) 
        {
            $username_err = $errors['username'] ?? '';
            $password_err = $errors['password'] ?? '';
            $confirm_password_err = $errors['confirm_password'] ?? '';
        }
    }

    //preserve username input if it was set
    $username_value = isset($_GET['username']) ? htmlspecialchars($_GET['username']) : '';
    ?>

    <form method="post" action="<?= htmlspecialchars('../src/controllers/register_process.php'); ?>">
        <div class="input-group">
            <label>Choose Your Username</label>
            <input type="text" 
                   name="username" 
                   value="<?= $username_value ?>" 
                   required
                   class="<?= !empty($username_err) ? 'input-error' : '' ?>">
                   <?php if (!empty($username_err)): ?>
                <div class="error-message"><?= htmlspecialchars($username_err) ?></div>
            <?php endif; ?>
        </div>
        <div class="input-group">
            <label> Choose Your Password</label>
            <input type="password" 
                   name="password_1" 
                   required
                   class="<?= !empty($password_err) ? 'input-error' : '' ?>">
            <?php if (!empty($password_err)): ?>
                <div class="error-message"><?= htmlspecialchars($password_err) ?></div>
            <?php endif; ?>       
        </div>
        <div class="input-group">
            <label>Confirm password</label>
            <input type="password" 
                   name="confirm_password" required
                   class="<?= !empty($confirm_password_err) ? 'input-error' : '' ?>">
            <?php if (!empty($confirm_password_err)): ?>
                <div class="error-message"><?= htmlspecialchars($confirm_password_err) ?></div>
            <?php endif; ?>        
        </div>
        <div class="input-group">
            <button type="submit" class="btn" name="register_btn">Register</button>
        </div>
        <p>Already a member? <a href="./admin-login.php">Login Here</a></p>
    </form>
</body>

</html>