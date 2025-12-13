<?php
declare(strict_types=1);
mb_internal_encoding('UTF-8');

// Enable error reporting for development
ini_set('log_errors', '1');
ini_set('error_log', __DIR__.'/admin_error.log');

require __DIR__ . '/config.php';

/**
 * Admin Authentication Handler for The Continuum Journal
 * Handles login, logout, session management, and security
 */

session_start();

class AdminAuth {
    private $pdo;
    private $max_login_attempts = 5;
    private $lockout_duration = 900; // 15 minutes
    private $session_lifetime = 3600; // 1 hour default
    private $remember_lifetime = 2592000; // 30 days
    
    public function __construct() {
        try {
            $this->pdo = new PDO(
                'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4',
                DB_USER, DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $e) {
            error_log('[AdminAuth] DB connection failed: ' . $e->getMessage());
            $this->redirect_with_error('Database connection failed. Please try again later.');
        }
    }
    
    /**
     * Handle authentication requests
     */
    public function handle_request() {
        $action = $_POST['action'] ?? $_GET['action'] ?? '';
        
        switch ($action) {
            case 'login':
                $this->handle_login();
                break;
            case 'logout':
                $this->handle_logout();
                break;
            case 'check_session':
                $this->check_session();
                break;
            default:
                $this->redirect_to_login();
        }
    }
    
    /**
     * Handle login process
     */
    private function handle_login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect_to_login();
            return;
        }
        
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);
        $ip_address = $this->get_client_ip();
        
        // Input validation
        if (empty($username) || empty($password)) {
            $this->redirect_with_error('Please enter both username and password.');
            return;
        }
        
        // Check for rate limiting
        if ($this->is_ip_locked($ip_address)) {
            $this->log_login_attempt($ip_address, $username, false);
            $this->redirect_with_error('Too many failed attempts. Please try again in 15 minutes.');
            return;
        }
        
        // Authenticate user
        $user = $this->authenticate_user($username, $password);
        
        if ($user) {
            $this->log_login_attempt($ip_address, $username, true);
            $this->create_session($user, $remember);
            $this->update_last_login($user['id']);
            header('Location: admin_dashboard.php');
            exit;
        } else {
            $this->log_login_attempt($ip_address, $username, false);
            $this->redirect_with_error('Invalid username or password.');
        }
    }
    
    /**
     * Authenticate user credentials
     */
    private function authenticate_user(string $username, string $password): ?array {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, username, email, password_hash, full_name, role, is_active 
                FROM admin_users 
                WHERE (username = ? OR email = ?) AND is_active = 1
            ");
            $stmt->execute([$username, $username]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password_hash'])) {
                return $user;
            }
            
            return null;
        } catch (PDOException $e) {
            error_log('[AdminAuth] Authentication query failed: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Create admin session
     */
    private function create_session(array $user, bool $remember = false): void {
        // Regenerate session ID for security
        session_regenerate_id(true);
        
        $session_id = session_id();
        $expires_at = $remember 
            ? date('Y-m-d H:i:s', time() + $this->remember_lifetime)
            : date('Y-m-d H:i:s', time() + $this->session_lifetime);
        
        // Store session in database
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO admin_sessions (id, user_id, ip_address, user_agent, expires_at)
                VALUES (?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE 
                    last_activity = NOW(),
                    expires_at = VALUES(expires_at)
            ");
            $stmt->execute([
                $session_id,
                $user['id'],
                $this->get_client_ip(),
                $_SERVER['HTTP_USER_AGENT'] ?? '',
                $expires_at
            ]);
        } catch (PDOException $e) {
            error_log('[AdminAuth] Session creation failed: ' . $e->getMessage());
        }
        
        // Set session variables
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['admin_username'] = $user['username'];
        $_SESSION['admin_email'] = $user['email'];
        $_SESSION['admin_full_name'] = $user['full_name'];
        $_SESSION['admin_role'] = $user['role'];
        $_SESSION['admin_login_time'] = time();
        
        // Set session cookie parameters
        if ($remember) {
            ini_set('session.cookie_lifetime', $this->remember_lifetime);
        }
    }
    
    /**
     * Check if current session is valid
     */
    public function check_session(): bool {
        if (!isset($_SESSION['admin_id'])) {
            return false;
        }
        
        $session_id = session_id();
        
        try {
            $stmt = $this->pdo->prepare("
                SELECT s.*, u.is_active 
                FROM admin_sessions s 
                JOIN admin_users u ON s.user_id = u.id 
                WHERE s.id = ? AND s.expires_at > NOW() AND u.is_active = 1
            ");
            $stmt->execute([$session_id]);
            $session = $stmt->fetch();
            
            if ($session) {
                // Update last activity
                $this->pdo->prepare("UPDATE admin_sessions SET last_activity = NOW() WHERE id = ?")
                         ->execute([$session_id]);
                return true;
            }
        } catch (PDOException $e) {
            error_log('[AdminAuth] Session check failed: ' . $e->getMessage());
        }
        
        // Invalid session, clean up
        $this->destroy_session();
        return false;
    }
    
    /**
     * Handle logout
     */
    private function handle_logout(): void {
        $this->destroy_session();
        $_SESSION['login_success'] = 'You have been logged out successfully.';
        $this->redirect_to_login();
    }
    
    /**
     * Destroy current session
     */
    private function destroy_session(): void {
        $session_id = session_id();
        
        // Remove from database
        try {
            $this->pdo->prepare("DELETE FROM admin_sessions WHERE id = ?")
                     ->execute([$session_id]);
        } catch (PDOException $e) {
            error_log('[AdminAuth] Session cleanup failed: ' . $e->getMessage());
        }
        
        // Clear session data
        $_SESSION = array();
        
        // Delete session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        session_destroy();
    }
    
    /**
     * Check if IP is locked due to failed attempts
     */
    private function is_ip_locked(string $ip): bool {
        try {
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as failed_attempts 
                FROM admin_login_attempts 
                WHERE ip_address = ? 
                AND success = 0 
                AND attempt_time > DATE_SUB(NOW(), INTERVAL ? SECOND)
            ");
            $stmt->execute([$ip, $this->lockout_duration]);
            $result = $stmt->fetch();
            
            return $result['failed_attempts'] >= $this->max_login_attempts;
        } catch (PDOException $e) {
            error_log('[AdminAuth] IP check failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Log login attempt
     */
    private function log_login_attempt(string $ip, string $username, bool $success): void {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO admin_login_attempts (ip_address, username, success, attempt_time)
                VALUES (?, ?, ?, NOW())
            ");
            $stmt->execute([$ip, $username, $success ? 1 : 0]);
        } catch (PDOException $e) {
            error_log('[AdminAuth] Login attempt logging failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Update user's last login timestamp
     */
    private function update_last_login(int $user_id): void {
        try {
            $this->pdo->prepare("UPDATE admin_users SET last_login = NOW() WHERE id = ?")
                     ->execute([$user_id]);
        } catch (PDOException $e) {
            error_log('[AdminAuth] Last login update failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Get client IP address
     */
    private function get_client_ip(): string {
        $ip_keys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];
        foreach ($ip_keys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                $ip = $_SERVER[$key];
                if (strpos($ip, ',') !== false) {
                    $ip = explode(',', $ip)[0];
                }
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
    
    /**
     * Redirect with error message
     */
    private function redirect_with_error(string $message): void {
        $_SESSION['login_error'] = $message;
        $this->redirect_to_login();
    }
    
    /**
     * Redirect to login page
     */
    private function redirect_to_login(): void {
        header('Location: admin_login.php');
        exit;
    }
    
    /**
     * Clean up expired sessions (call this periodically)
     */
    public function cleanup_expired_sessions(): void {
        try {
            $this->pdo->exec("DELETE FROM admin_sessions WHERE expires_at < NOW()");
            $this->pdo->exec("DELETE FROM admin_login_attempts WHERE attempt_time < DATE_SUB(NOW(), INTERVAL 24 HOUR)");
        } catch (PDOException $e) {
            error_log('[AdminAuth] Cleanup failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Require admin authentication (use in protected pages)
     */
    public static function require_admin(): array {
        $auth = new self();
        if (!$auth->check_session()) {
            $_SESSION['login_error'] = 'Please log in to access the admin area.';
            header('Location: admin_login.php');
            exit;
        }
        
        return [
            'id' => $_SESSION['admin_id'],
            'username' => $_SESSION['admin_username'],
            'email' => $_SESSION['admin_email'],
            'full_name' => $_SESSION['admin_full_name'],
            'role' => $_SESSION['admin_role']
        ];
    }
    
    /**
     * Check if user has specific role or higher
     */
    public static function has_role(string $required_role): bool {
        if (!isset($_SESSION['admin_role'])) {
            return false;
        }
        
        $role_hierarchy = [
            'editor' => 1,
            'admin' => 2,
            'super_admin' => 3
        ];
        
        $user_level = $role_hierarchy[$_SESSION['admin_role']] ?? 0;
        $required_level = $role_hierarchy[$required_role] ?? 0;
        
        return $user_level >= $required_level;
    }
}

// Handle the request if this file is accessed directly
if ($_SERVER['SCRIPT_NAME'] === '/auth.php' || basename($_SERVER['SCRIPT_NAME']) === 'auth.php') {
    $auth = new AdminAuth();
    $auth->cleanup_expired_sessions(); // Clean up old sessions
    $auth->handle_request();
}
?>