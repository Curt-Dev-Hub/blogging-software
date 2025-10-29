<?php
/**
 * Logout Success Page
 * Displays confirmation message after successful logout
 * Then destroys the temporary session used for displaying user info
 */

if (session_status() === PHP_SESSION_NONE) 
{
    session_start();
}

// data from session
$was_logged_in = isset($_SESSION['previous_user']);
$username = $_SESSION['previous_user'] ?? 'User';
$is_auto_logout = $_SESSION['is_auto_logout'] ?? false;
$should_cleanup = $_SESSION['cleanup_session'] ?? false;

$_SESSION = [];

// if logout process, destroy the temporary session completely
if ($should_cleanup) 
{
    // delete session cookie
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
}

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);


include_once './assets/css/css_paths.php';
$page_title = "Logged Out Successfully";
$page_css = [
    CSS_HEADER,
    CSS_ADMIN_LOGIN  // Reusing login styles for consistency
];

require_once '../includes/header.php';
?>

<div class="auth-container">
    <div class="logout-success">
        <div class="success-icon">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#4CAF50" stroke-width="2">
                <path d="M20 6L9 17l-5-5"/>
            </svg>
        </div>
        
        <h1>Successfully Logged Out</h1>
        
        <?php if ($was_logged_in): ?>
            <?php if ($is_auto_logout): ?>
                <p class="logout-message">
                    Your session has expired for security reasons. 
                    Goodbye, <strong><?= htmlspecialchars($username) ?></strong>!
                </p>
            <?php else: ?>
                <p class="logout-message">
                    Goodbye, <strong><?= htmlspecialchars($username) ?></strong>! 
                    You have been successfully logged out of your account.
                </p>
            <?php endif; ?>
        <?php else: ?>
            <p class="logout-message">You are now logged out.</p>
        <?php endif; ?>
        
        <div class="action-buttons">
            <a href="<?= base_url('/public/admin-login.php') ?>" class="btn btn-primary">
                🔐 Login Again
            </a>
            <a href="<?= base_url('/') ?>" class="btn btn-secondary">
                🏠 Return Home
            </a>
        </div>
        
        <div class="auto-redirect">
            <p>You will be automatically redirected to the home page in <span id="countdown">10</span> seconds...</p>
        </div>
    </div>
</div>

<script>
// auto-redirect functionality
let countdown = 10;
const countdownElement = document.getElementById('countdown');

const countdownInterval = setInterval(() => {
    countdown--;
    countdownElement.textContent = countdown;
    
    if (countdown <= 0) {
        clearInterval(countdownInterval);
        window.location.href = '<?= base_url('/') ?>';
    }
}, 1000);

// allow user to stop auto-redirect if they interact with the page
let userInteracted = false;

function stopRedirect() {
    if (!userInteracted) {
        userInteracted = true;
        clearInterval(countdownInterval);
        const redirectElement = document.querySelector('.auto-redirect');
        if (redirectElement) {
            redirectElement.style.opacity = '0';
            setTimeout(() => {
                redirectElement.style.display = 'none';
            }, 300);
        }
    }
}

document.addEventListener('click', stopRedirect);
document.addEventListener('keydown', stopRedirect);
</script>

<style>
.logout-success {
    text-align: center;
    max-width: 500px;
    margin: 0 auto;
    padding: 2rem;
}

.success-icon {
    margin-bottom: 1.5rem;
}

.success-icon svg {
    animation: checkmark 0.5s ease-in-out;
}

@keyframes checkmark {
    0% { 
        transform: scale(0); 
        opacity: 0; 
    }
    50% { 
        transform: scale(1.2); 
    }
    100% { 
        transform: scale(1); 
        opacity: 1; 
    }
}

.logout-success h1 {
    color: #2c3e50;
    margin-bottom: 1rem;
}

.logout-message {
    font-size: 1.2rem;
    margin-bottom: 2rem;
    color: #333;
    line-height: 1.6;
}

.action-buttons {
    display: flex;
    gap: 1rem;
    justify-content: center;
    margin: 2rem 0;
    flex-wrap: wrap;
}

.auto-redirect {
    margin-top: 2rem;
    padding: 1rem;
    background: #e3f2fd;
    border-radius: 4px;
    color: #1565C0;
    transition: opacity 0.3s ease;
}

.auto-redirect p {
    margin: 0;
}

.auto-redirect span {
    font-weight: bold;
    color: #0D47A1;
}

@media (max-width: 768px) {
    .logout-success {
        padding: 1.5rem;
    }
    
    .action-buttons {
        flex-direction: column;
        align-items: center;
    }
    
    .action-buttons .btn {
        width: 200px;
    }
    
    .logout-message {
        font-size: 1.1rem;
    }
}
</style>

</body>
</html>