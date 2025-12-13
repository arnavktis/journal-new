<?php
require_once __DIR__ . '/../config.php';

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $pw    = $_POST['password'] ?? '';

    $stmt = $DB->query("SELECT * FROM admins LIMIT 1");
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && $email === $admin['email'] && password_verify($pw, $admin['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['admin'] = true;
        $_SESSION['admin_name'] = $admin['name'];
        header("Location: admin_panel.php");
        exit;
    } else {
        $err = "Invalid email or password.";
    }
}
?>
<!doctype html><html><body style="font-family:sans-serif;max-width:400px;margin:40px auto;">
<h2>Admin Login</h2>
<?php if($err): ?><p style="color:red"><?=esc($err)?></p><?php endif; ?>
<form method="post">
  <input name="email" type="email" placeholder="Email" required style="width:100%;padding:8px">
  <input name="password" type="password" placeholder="Password" required style="width:100%;padding:8px;margin-top:8px">
  <button style="margin-top:12px;padding:8px 16px">Login</button>
</form>
</body></html>
