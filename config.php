<?php
// ---------------------------------------------
// SESSION
// ---------------------------------------------
session_start();

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

// ---------------------------------------------
// UTILITY
// ---------------------------------------------
function esc($s) {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}
