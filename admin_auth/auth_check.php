<?php
function require_admin(PDO $DB) {

    if (!isset($_COOKIE['admin_auth']) || strlen($_COOKIE['admin_auth']) < 20) {
        header("Location: admin_login.php");
        exit;
    }

    $hash = hash('sha256', $_COOKIE['admin_auth']);

    $stmt = $DB->prepare("
        SELECT id,name 
        FROM admins
        WHERE auth_token_hash=?
          AND auth_token_expires > NOW()
        LIMIT 1
    ");
    $stmt->execute([$hash]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$admin) {
        setcookie('admin_auth','',time()-3600,'/');
        header("Location: admin_login.php");
        exit;
    }

    return $admin;
}