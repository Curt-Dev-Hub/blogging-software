<?php 

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

include_once './assets/css/css_paths.php';

$page_title = "Admin | Login";
$page_css = [
    CSS_ADMIN_LOGIN
];

require_once '../includes/header.php';
?>  
    <div class="header">
        <h1>Enter Your Admin Login Details</h1>
    </div>

    <?php
    // Check for errors
    if (isset($_GET['errors'])) {
        $errors = urldecode($_GET['errors']);
        // Try to decode as JSON first
        $decoded_errors = json_decode($errors, true);

        if ($decoded_errors && is_array($decoded_errors)) {
            // If it's a JSON object with specific field errors
            $username_err = $decoded_errors['username'] ?? '';
            $password_err = $decoded_errors['password_1'] ?? '';
        } else {
            // If it's just a string message
            $general_error = htmlspecialchars($errors);
        }
    }

    $username_value = isset($_GET['username']) ? htmlspecialchars($_GET['username']) : '';
    ?>


    <?php if (!empty($general_error)): ?>
        <div class="error-message"><?= $general_error ?></div>
    <?php endif; ?>
    <form method="post" action="<?= htmlspecialchars('../src/controllers/login_process.php'); ?>">
        <div class="input-group">
            <label>Username</label>
            <input type="text"
                name="username"
                value="<?= $username_value ?>"
                class=<?= !empty($username_err) ? 'input-error' : '' ?>
                required>
            <?php if (!empty($username_err)) : ?>
                <div class="error-message"><?= htmlspecialchars($username_err) ?></div>
                <?php endif; ?>
                </div>
                <div class="input-group">
                    <label>Password</label>
                    <input type="password"
                        name="password_1"
                        class="<?= !empty($password_err) ? 'input-error' : '' ?>"
                        required>
                    <?php if (!empty($password_err)): ?>
                        <div class="error-message"><?= htmlspecialchars($password_err) ?></div>
                    <?php endif; ?>
                </div>
                <div class="input-group">
                    <button type="submit" class="btn" name="login_btn">Login Now</button>
                </div>
                <p>Not yet a member? <a href="./registration.php">Register Here</a></p>
    </form>
</body>
</html>