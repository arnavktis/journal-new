<?php
require_once __DIR__ . '/../config.php';

/* AUTH */
$ADMIN = require_admin($DB);

/* Kill token */
$DB->prepare("UPDATE admins SET auth_token_hash=NULL WHERE id=?")
   ->execute([$ADMIN['id']]);

/* Delete cookie */
setcookie(
    'admin_auth',
    '',
    [
        'expires'  => time() - 3600,
        'path'     => '/',
        'secure'   => true,
        'httponly' => true,
        'samesite' => 'Lax'
    ]
);

header("Location: admin_login.php");
exit;
