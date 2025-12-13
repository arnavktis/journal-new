<?php
/**
 * Logout Handler for The Continuum Journal Admin System
 * Properly destroys admin sessions and redirects to login
 */

session_start();

// Include auth class for proper session cleanup
require_once 'auth.php';

// Destroy the session using the AdminAuth class
$auth = new AdminAuth();
$auth->handle_request(); // This will handle the logout action

// If accessed directly without action parameter, default to logout
if (!isset($_GET['action']) && !isset($_POST['action'])) {
    // Set logout message
    $_SESSION['login_success'] = 'You have been logged out successfully.';
    
    // Clear all session data
    $_SESSION = array();
    
    // Destroy the session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Destroy the session
    session_destroy();
    
    // Redirect to login page
    header('Location: admin_login.php');
    exit;
}
?>