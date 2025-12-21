<?php
declare(strict_types=1);
mb_internal_encoding('UTF-8');

ini_set('log_errors','1');
ini_set('error_log', __DIR__.'/php_error.log');
header('Content-Type: application/json; charset=utf-8');

require __DIR__ . '/config.php'; // DB_*, FTP_*, PUBLIC_BASE_URL, MAX_FILE_BYTES, SMTP_HOST, SMTP_USER, SMTP_PASS, SMTP_FROM, SMTP_NAME

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require __DIR__ . '/phpmailer/Exception.php';
require __DIR__ . '/phpmailer/PHPMailer.php';
require __DIR__ . '/phpmailer/SMTP.php';

// --- hard error handlers (return JSON, log file) ---
set_error_handler(function($no,$str,$file,$line){
  error_log("[continuum] PHP#$no $str @$file:$line");
});
set_exception_handler(function($e){
  error_log("[continuum] UNCAUGHT ".$e->getMessage());
  http_response_code(500);
  echo json_encode(['ok'=>false,'error'=>'server_error','detail'=>$e->getMessage()], JSON_UNESCAPED_SLASHES);
  exit;
});

// --- tiny helpers ---
function jdie(int $code, array $data){ http_response_code($code); echo json_encode($data, JSON_UNESCAPED_SLASHES); exit; }
function logline(string $msg){ error_log('[continuum] '.$msg); @file_put_contents(__DIR__.'/mail_debug.log','['.date('c')."] $msg\n", FILE_APPEND); }

// --- method ---
if ($_SERVER['REQUEST_METHOD'] !== 'POST') jdie(405, ['ok'=>false,'error'=>'Method not allowed']);

// --- input ---
$fields = [
  'fullname'         => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
  'email'            => FILTER_SANITIZE_EMAIL,
  'affiliation'      => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
  'subject'          => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
  'article_synopsis' => FILTER_UNSAFE_RAW,
];
$input = filter_input_array(INPUT_POST, $fields) ?: [];
if (empty($input['fullname']) || empty($input['email']) || empty($input['subject']) || empty($input['article_synopsis'])) {
  jdie(422, ['ok'=>false,'error'=>'Missing required fields']);
}

// --- DB ---
try {
  $pdo = new PDO(
    'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4',
    DB_USER, DB_PASS,
    [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]
  );
} catch (Throwable $e) {
  logline('DB connect fail: '.$e->getMessage());
  jdie(500, ['ok'=>false,'error'=>'DB connection failed']);
}

// --- FTP helpers ---
function ftp_ensure_path($ftp, $path){
  $parts = array_filter(explode('/', trim($path,'/')));
  $cur = '/';
  foreach ($parts as $p) { $cur .= $p; @ftp_mkdir($ftp,$cur); @ftp_chdir($ftp,$cur); $cur.='/'; }
  @ftp_chdir($ftp,'/');
}

// --- file upload → FTP ---
if (!isset($_FILES['manuscript']) || $_FILES['manuscript']['error'] !== UPLOAD_ERR_OK) {
  jdie(422, ['ok'=>false,'error'=>'Manuscript file is required']);
}
$f = $_FILES['manuscript'];
if ($f['size'] > MAX_FILE_BYTES) jdie(413, ['ok'=>false,'error'=>'File too large']);

$allowed = defined('ALLOWED_MIME') ? ALLOWED_MIME : [
  'application/pdf',
  'application/msword',
  'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
];

$tmpPath = $f['tmp_name'];
$mime = function_exists('mime_content_type') ? mime_content_type($tmpPath) : ($f['type'] ?? 'application/octet-stream');
if (!in_array($mime, $allowed, true)) jdie(415, ['ok'=>false,'error'=>'Unsupported file type']);

$orig = preg_replace('/[^\w.\-]+/u','_', $f['name']);
$ext  = pathinfo($orig, PATHINFO_EXTENSION) ?: 'bin';
$base = pathinfo($orig, PATHINFO_FILENAME);
$remoteName = sprintf('%s_%s.%s', $base, bin2hex(random_bytes(6)), $ext);

$ftp = (defined('FTP_SSL') && FTP_SSL) ? @ftp_ssl_connect(FTP_HOST, 21, 20) : @ftp_connect(FTP_HOST, 21, 20);
if (!$ftp) jdie(500, ['ok'=>false,'error'=>'FTP connect failed']);
if (!@ftp_login($ftp, FTP_USER, FTP_PASS)) { ftp_close($ftp); jdie(500, ['ok'=>false,'error'=>'FTP auth failed']); }
if (defined('FTP_PASSIVE') && FTP_PASSIVE) ftp_pasv($ftp, true);

ftp_ensure_path($ftp, FTP_REMOTE_DIR);
$remotePath = rtrim(FTP_REMOTE_DIR,'/').'/'.$remoteName;
if (!@ftp_put($ftp, $remotePath, $tmpPath, FTP_BINARY)) { ftp_close($ftp); jdie(500, ['ok'=>false,'error'=>'FTP upload failed']); }
ftp_close($ftp);

$publicUrl = rtrim(PUBLIC_BASE_URL,'/').'/'.rawurlencode($remoteName);

// --- DB insert ---
try {
  $stmt = $pdo->prepare("
    INSERT INTO submissions
      (fullname, email, affiliation, subject, article_synopsis, manuscript_file, manuscript_path)
    VALUES
      (:fullname,:email,:affiliation,:subject,:article_synopsis,:manuscript_file,:manuscript_path)
  ");
  $stmt->execute([
    ':fullname'         => $input['fullname'],
    ':email'            => $input['email'],
    ':affiliation'      => $input['affiliation'] ?? null,
    ':subject'          => $input['subject'],
    ':article_synopsis' => $input['article_synopsis'] ?? null,
    ':manuscript_file'  => $remoteName,
    ':manuscript_path'  => $remotePath,
  ]);
  $insertId = (int)$pdo->lastInsertId();
} catch (Throwable $e) {
  logline('DB insert fail: '.$e->getMessage());
  jdie(500, ['ok'=>false,'error'=>'Insert failed']);
}

// --- PHPMailer boot + fallback (587 STARTTLS → 465 SMTPS) ---
function bootMailer(string $host, string $user, string $pass, int $port, string $secure): PHPMailer {
  $m = new PHPMailer(true);
  $m->isSMTP();
  $m->Host        = $host;
  $m->SMTPAuth    = true;
  $m->Username    = $user;
  $m->Password    = $pass;
  $m->Port        = $port; // 587 or 465
  $m->SMTPSecure  = $secure; // PHPMailer::ENCRYPTION_STARTTLS or ::ENCRYPTION_SMTPS
  $m->SMTPAutoTLS = true;
  $m->Timeout     = 10;
  $m->CharSet     = 'UTF-8';

  $from = defined('SMTP_FROM') ? SMTP_FROM : $user;
  $name = defined('SMTP_NAME') ? SMTP_NAME : 'Journal';
  $m->setFrom($from, $name);
  $m->addReplyTo($from, $name);

  $m->SMTPDebug   = SMTP::DEBUG_SERVER;
  $m->Debugoutput = function($str,$lvl){ logline("SMTP[$lvl] $str"); };

  return $m;
}
function trySend(callable $builder): bool {
  $host = SMTP_HOST; $user = SMTP_USER; $pass = SMTP_PASS;

  // prefer 587 STARTTLS
  try {
    $m = bootMailer($host,$user,$pass,587, PHPMailer::ENCRYPTION_STARTTLS);
    $builder($m);
    if ($m->send()) return true;
  } catch (Throwable $e) { logline('587 error: '.$e->getMessage()); }

  // fallback 465 SMTPS
  try {
    $m = bootMailer($host,$user,$pass,465, PHPMailer::ENCRYPTION_SMTPS);
    $builder($m);
    return (bool)$m->send();
  } catch (Throwable $e) { logline('465 error: '.$e->getMessage()); return false; }
}

// --- mail bodies ---
$aff = $input['affiliation'] ?? '';
$syn = nl2br(htmlspecialchars($input['article_synopsis'] ?? '', ENT_QUOTES|ENT_SUBSTITUTE, 'UTF-8'));
$adminBody = "<h3>New manuscript submission</h3>
<p><b>Name:</b> ".htmlspecialchars($input['fullname'])."</p>
<p><b>Email:</b> ".htmlspecialchars($input['email'])."</p>
<p><b>Affiliation:</b> ".htmlspecialchars($aff)."</p>
<p><b>Subject:</b> ".htmlspecialchars($input['subject'])."</p>
<p><b>Article Synopsis:</b><br>{$syn}</p>";

// UPDATED USER BODY
$userBody = "<p>Dear ".htmlspecialchars($input['fullname']).",</p>
<p>We have received your submission: <b>".htmlspecialchars($input['subject'])."</b>.</p>
<p>Next step: please download, complete, and sign the attached <b>Copyright Transfer Agreement Form</b>.</p>
<p>After completing the form, email the signed copy to <a href=\"mailto:thecontinuum@phindia.com\">thecontinuum@phindia.com</a>.</p>
<p>If you have already submitted this form, you may ignore this request.</p>
<p>&mdash; The Continuum Editorial Team</p>";

// --- send admin (attach from memory) ---
$adminSent = trySend(function(PHPMailer $m) use ($adminBody,$tmpPath,$orig){
  $m->addAddress('lalita@phindia.com','Admin');
  $m->addAddress('thecontinuum@phindia.com','Continuum');
  $m->addAddress('marketing@phindia.com','Admin');
  $m->isHTML(true);
  $m->Subject = 'New Submission Received';
  $m->Body    = $adminBody;
  if (is_readable($tmpPath)) {
    $m->addStringAttachment((string)file_get_contents($tmpPath), $orig);
  }
});

// --- send user (attach Copyright_Transfer_Agreement_Form.pdf) ---
$userSent = trySend(function(PHPMailer $m) use ($input,$userBody){
  $m->addAddress($input['email'] ?? '', $input['fullname'] ?? '');
  $m->isHTML(true);
  $m->Subject = 'Submission Acknowledgement & Copyright Transfer Form';
  $m->Body    = $userBody;

  // attach static copyright transfer form
  $pdfPath = __DIR__ . '/Copyright_Transfer_Agreement_Form.pdf';
  if (is_readable($pdfPath)) {
    $m->addAttachment($pdfPath, 'Copyright_Transfer_Agreement_Form.pdf');
  } else {
    logline('Copyright transfer form not readable: '.$pdfPath);
  }
});

// --- response ---
echo json_encode([
  'ok'   => true,
  'id'   => $insertId,
  'file' => [
    'name' => $remoteName,
    'path' => $remotePath,
    'url'  => $publicUrl,
    'mime' => $mime,
    'size' => (int)$f['size'],
  ],
  'mail' => ['admin'=>$adminSent,'user'=>$userSent]
], JSON_UNESCAPED_SLASHES);
