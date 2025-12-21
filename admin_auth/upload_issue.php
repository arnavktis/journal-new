<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../config.php';

/* AUTH — REQUIRED */
$ADMIN = require_admin($DB);

$err = $ok = '';

function make_slug($s) {
    $s = strtolower(trim($s));
    $s = preg_replace('/[^a-z0-9]+/', '-', $s);
    return trim($s, '-');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $subject  = trim($_POST['subject'] ?? '');
    $year     = intval($_POST['year'] ?? 0);
    $title    = trim($_POST['title'] ?? '');
    $issue_no = ($_POST['issue_no'] ?? '') !== '' ? intval($_POST['issue_no']) : null;
    $price    = floatval($_POST['price'] ?? 0);

    if (!$subject || !$year || !$title || $price <= 0) {
        $err = "Required fields missing.";
    }

    $preview_filename = null;

    if (!$err && !empty($_FILES['preview']['name'])) {
        $pv = $_FILES['preview'];

        if ($pv['error'] !== UPLOAD_ERR_OK) {
            $err = "Preview upload failed.";
        } else {
            $mime = mime_content_type($pv['tmp_name']);
            if (!in_array($mime, ['image/png','image/jpeg'])) {
                $err = "Preview must be PNG or JPG.";
            } else {
                $ext = pathinfo($pv['name'], PATHINFO_EXTENSION);
                $preview_filename = 'preview_' . bin2hex(random_bytes(4)) . '.' . $ext;
                $dir = dirname(__DIR__).'/manuscripts/previews';
                if (!is_dir($dir)) mkdir($dir,0755,true);
                move_uploaded_file($pv['tmp_name'], "$dir/$preview_filename");
            }
        }
    }

    if (!$err) {
        $slug = make_slug($title.'-'.$year);

        $stmt = $DB->prepare("
            INSERT INTO issues
            (subject, year, title, issue_no, price, slug, preview_filename)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $subject,
            $year,
            $title,
            $issue_no,
            $price,
            $slug,
            $preview_filename
        ]);

        $ok = "Issue created successfully.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Create Issue</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<style>
body { font-family:Poppins,Arial; background:#f6f7fb; padding:40px; }
.box { max-width:800px; margin:auto; background:#fff; padding:40px; border-radius:14px; }
label { font-weight:600; display:block; margin-top:18px; }
input { width:100%; padding:12px; margin-top:6px; border-radius:8px; border:1px solid #ccc; }
button { margin-top:30px; width:100%; padding:14px; background:#002147; color:#fff; border:none; border-radius:10px; }
.err { background:#fee; padding:12px; border-radius:8px; color:#b00; }
.ok { background:#d1fae5; padding:12px; border-radius:8px; color:#065f46; }
.back { display:inline-block; margin-bottom:20px; font-weight:600; color:#5d85b2; text-decoration:none; }
</style>
</head>
<body>

<div class="box">
<a href="admin_panel.php" class="back">← Back to Dashboard</a>

<h1>Create Issue</h1>

<?php if ($err): ?><div class="err"><?=esc($err)?></div><?php endif; ?>
<?php if ($ok): ?><div class="ok"><?=esc($ok)?></div><?php endif; ?>

<form method="post" enctype="multipart/form-data">

<label>Subject *</label>
<input type="text" name="subject" required>

<label>Year *</label>
<input type="number" name="year" required>

<label>Issue Title *</label>
<input type="text" name="title" required>

<label>Issue Number (optional)</label>
<input type="number" name="issue_no">

<label>Price *</label>
<input type="text" name="price" required>

<label>Preview Image (optional)</label>
<input type="file" name="preview" accept="image/png,image/jpeg">

<button>Create Issue</button>
</form>
</div>

</body>
</html>
