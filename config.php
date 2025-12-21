<?php
// ---------------------------------------------
// SESSION
// ---------------------------------------------
ini_set('session.use_strict_mode', 1);

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '.phindia.com',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Lax',
]);

session_start();

if (!isset($_SESSION['initiated'])) {
    session_regenerate_id(true);
    $_SESSION['initiated'] = true;
}


// ---------------------------------------------
// DATABASE (Pair Networks)
// ---------------------------------------------
define('DB_HOST', 'vdb5.pit.pair.com');
define('DB_NAME', 'netsutra_journal');
define('DB_USER', 'netsutra_38');
define('DB_PASS', 'GPAQqEsPb9pUtQ2M');

try {
    $DB = new PDO(
        'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4',
        DB_USER, DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (Exception $e) {
    die("Database connection error.");
}

// ---------------------------------------------
// FTP (Pair Networks)
// ---------------------------------------------
define('FTP_HOST', 'netsutra.pairserver.com');
define('FTP_USER', 'netsutra_journal');
define('FTP_PASS', 'Arnav@2024');
define('FTP_SSL', false);     // set true if FTPS required
define('FTP_PASSIVE', true);
define('FTP_REMOTE_DIR', '/public_html/journal.phindia.com/manuscripts');

define('PUBLIC_BASE_URL', 'https://journal.phindia.com/manuscripts');

// ---------------------------------------------
// SMTP (Pair Networks Mailbox)
// ---------------------------------------------
define('SMTP_HOST', 'netsutra.mail.pairserver.com');
define('SMTP_USER', 'thecontinuum@phindia.com');
define('SMTP_PASS', 'AXH1234%$#');
define('SMTP_FROM', SMTP_USER);
define('SMTP_NAME', 'The Continuum');

// ---------------------------------------------
// FILE UPLOAD SETTINGS
// ---------------------------------------------
define('MAX_FILE_BYTES', 20 * 1024 * 1024); // 20MB

$ALLOWED_MIME = [
  'application/pdf',
  'application/msword',
  'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
  'application/zip',
  'image/png',
  'image/jpeg'
];

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

// ---------------------------------------------
// UTILITY
// ---------------------------------------------
function esc($s) {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}