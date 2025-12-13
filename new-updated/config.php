<?php
// --- DB ---
define('DB_HOST', 'vdb5.pit.pair.com');
define('DB_NAME', 'netsutra_journal');
define('DB_USER', 'netsutra_38');
define('DB_PASS', 'GPAQqEsPb9pUtQ2M');

// --- FTP (pair.com or your target) ---
define('FTP_HOST', 'netsutra.pairserver.com');
define('FTP_USER', 'netsutra_journal');
define('FTP_PASS', 'Arnav@2024');
define('FTP_SSL', false);          // set true if your FTP supports FTPS
define('FTP_PASSIVE', true);
define('FTP_REMOTE_DIR', '/public_html/journal.phindia.com/manuscripts'); // remote path
define('PUBLIC_BASE_URL', 'https://journal.phindia.com/manuscripts'); // where files become public
// --- SMTP ---
define('SMTP_HOST', 'netsutra.mail.pairserver.com');   // ✅ your mail server
define('SMTP_USER', 'thecontinuum@phindia.com');          // ✅ full mailbox
define('SMTP_PASS', 'AXH1234%$#');               // ✅ mailbox password
define('SMTP_FROM', 'thecontinuum@phindia.com');          // ✅ same as user (don’t change)
define('SMTP_NAME', 'The Continuum');                  // display name

// --- uploads ---
define('MAX_FILE_BYTES', 20 * 1024 * 1024); // 20MB
$ALLOWED_MIME = [
  'application/pdf','application/msword',
  'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
  'application/zip','image/png','image/jpeg'
];