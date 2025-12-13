<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - The Continuum Journal</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="admin-styles.css">
    <style>
        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-container {
            background: white;
            padding: 2.5rem;
            border-radius: 12px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            margin: 1rem;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .login-header h1 {
            color: #2d3748;
            font-size: 1.8rem;
            font-weight: 600;
            margin: 0 0 0.5rem 0;
        }
        
        .login-header p {
            color: #718096;
            font-size: 0.9rem;
            margin: 0;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #374151;
            font-weight: 500;
            font-size: 0.9rem;
        }
        
        .form-group input {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.2s;
            box-sizing: border-box;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .login-btn {
            width: 100%;
            padding: 0.75rem;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s;
            margin-bottom: 1rem;
        }
        
        .login-btn:hover {
            background: #5a67d8;
        }
        
        .login-btn:disabled {
            background: #a0aec0;
            cursor: not-allowed;
        }
        
        .alert {
            padding: 0.75rem;
            border-radius: 6px;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }
        
        .alert-error {
            background: #fed7d7;
            color: #c53030;
            border: 1px solid #feb2b2;
        }
        
        .alert-success {
            background: #c6f6d5;
            color: #276749;
            border: 1px solid #9ae6b4;
        }
        
        .remember-me {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .remember-me input {
            margin-right: 0.5rem;
        }
        
        .remember-me label {
            margin: 0;
            font-size: 0.9rem;
            color: #4a5568;
        }
        
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid #ffffff;
            border-radius: 50%;
            border-top-color: transparent;
            animation: spin 0.8s linear infinite;
            margin-right: 0.5rem;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        .footer-text {
            text-align: center;
            color: #718096;
            font-size: 0.8rem;
            margin-top: 1.5rem;
        }
        
        .security-note {
            background: #f7fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 0.75rem;
            margin-top: 1rem;
            font-size: 0.8rem;
            color: #4a5568;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>Admin Portal</h1>
            <p>The Continuum Journal Management System</p>
        </div>
        
        <?php
        session_start();
        
        // Check if already logged in
        if (isset($_SESSION['admin_id'])) {
            header('Location: admin_dashboard.php');
            exit;
        }
        
        // Display messages
        if (isset($_SESSION['login_error'])) {
            echo '<div class="alert alert-error">' . htmlspecialchars($_SESSION['login_error']) . '</div>';
            unset($_SESSION['login_error']);
        }
        
        if (isset($_SESSION['login_success'])) {
            echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['login_success']) . '</div>';
            unset($_SESSION['login_success']);
        }
        ?>
        
        <form id="loginForm" method="POST" action="auth.php">
            <input type="hidden" name="action" value="login">
            
            <div class="form-group">
                <label for="username">Username or Email</label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    required 
                    autocomplete="username"
                    placeholder="Enter your username or email"
                    value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                >
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    required 
                    autocomplete="current-password"
                    placeholder="Enter your password"
                >
            </div>
            
            <div class="remember-me">
                <input type="checkbox" id="remember" name="remember" value="1">
                <label for="remember">Remember me for 30 days</label>
            </div>
            
            <button type="submit" class="login-btn" id="loginBtn">
                <span id="loginText">Sign In</span>
            </button>
        </form>
        
        <div class="security-note">
            <strong>Security Notice:</strong> This is a secure admin area. All login attempts are monitored and logged. 
            After 5 failed attempts, your IP will be temporarily blocked.
        </div>
        
        <div class="footer-text">
            © <?php echo date('Y'); ?> The Continuum Journal. All rights reserved.
        </div>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const btn = document.getElementById('loginBtn');
            const text = document.getElementById('loginText');
            
            btn.disabled = true;
            text.innerHTML = '<span class="loading"></span>Signing in...';
            
            // Re-enable button after 10 seconds as fallback
            setTimeout(() => {
                btn.disabled = false;
                text.textContent = 'Sign In';
            }, 10000);
        });
        
        // Auto-focus username field
        document.getElementById('username').focus();
        
        // Handle Enter key in password field
        document.getElementById('password').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                document.getElementById('loginForm').submit();
            }
        });
    </script>
</body>
</html>