<?php
require_once __DIR__ . '/../config.php';

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $pw    = $_POST['password'] ?? '';

    $stmt = $DB->prepare("SELECT id,name,password_hash FROM admins WHERE email=? LIMIT 1");
    $stmt->execute([$email]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && password_verify($pw, $admin['password_hash'])) {

        $token = bin2hex(random_bytes(32));

        $hash = hash('sha256', $token);

        $DB->prepare("
            UPDATE admins 
            SET auth_token_hash=?, auth_token_expires=DATE_ADD(NOW(), INTERVAL 7 DAY)
            WHERE id=?
        ")->execute([$hash, $admin['id']]);

        setcookie(
            'admin_auth',
            $token,
            [
                'expires'  => time() + 604800,
                'path'     => '/',
                'secure'   => true,
                'httponly' => true,
                'samesite' => 'Lax'
            ]
        );

        header("Location: admin_panel.php");
        exit;
    }

    $err = "Invalid credentials";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Journal Portal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #002147;
            --secondary-blue: #003366;
            --tertiary-blue: #004080;
            --light-blue: #5d85b2;
            --continuum-gold: #D4AF37;
            --text-dark: #1f2937;
            --text-light: #6b7280;
            --bg-white: #ffffff;
            --bg-light: #f8fafc;
            --border-light: #e5e7eb;
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 50%, var(--tertiary-blue) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .login-container {
            background: var(--bg-white);
            border-radius: 20px;
            box-shadow: var(--shadow-xl);
            max-width: 460px;
            width: 100%;
            overflow: hidden;
            animation: slideUp 0.5s ease-out;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .login-header {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
            padding: 40px 30px;
            text-align: center;
            color: white;
        }
        .login-header h1 {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 8px;
        }
        .login-header p {
            font-size: 14px;
            opacity: 0.9;
            font-weight: 300;
        }
        .icon-wrapper {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            margin-bottom: 16px;
        }
        .icon-wrapper svg {
            width: 32px;
            height: 32px;
            fill: white;
        }
        .login-body { padding: 40px 30px; }
        .alert {
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 24px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
            background-color: #fee;
            color: #c00;
            border: 1px solid #fcc;
        }
        .alert::before { content: "⚠"; font-size: 18px; }
        .form-group { margin-bottom: 24px; }
        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: var(--text-dark);
            margin-bottom: 8px;
        }
        .form-group input {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid var(--border-light);
            border-radius: 10px;
            font-size: 15px;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s ease;
            background: var(--bg-white);
        }
        .form-group input:focus {
            outline: none;
            border-color: var(--light-blue);
            box-shadow: 0 0 0 4px rgba(93, 133, 178, 0.1);
        }
        .form-group input::placeholder { color: #9ca3af; }
        .btn-login {
            width: 100%;
            padding: 14px 24px;
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        .btn-login:active { transform: translateY(0); }
        .login-footer {
            text-align: center;
            padding: 20px 30px 30px;
            color: var(--text-light);
            font-size: 13px;
        }
        @media (max-width: 480px) {
            .login-container { border-radius: 16px; }
            .login-header { padding: 32px 24px; }
            .login-header h1 { font-size: 24px; }
            .login-body { padding: 32px 24px; }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <div class="icon-wrapper">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/>
                </svg>
            </div>
            <h1>Admin Portal</h1>
            <p>Sign in to manage your journal</p>
        </div>
        <div class="login-body">
            <?php if($err): ?>
                <div class="alert"><?=esc($err)?></div>
            <?php endif; ?>
            <form method="post">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input id="email" name="email" type="email" placeholder="admin@journal.com" required autocomplete="email">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input id="password" name="password" type="password" placeholder="Enter your password" required autocomplete="current-password">
                </div>
                <button type="submit" class="btn-login">Sign In</button>
            </form>
        </div>
        <div class="login-footer">
            <p>&copy; 2025 Journal Admin Portal. All rights reserved.</p>
        </div>
    </div>
</body>
</html>