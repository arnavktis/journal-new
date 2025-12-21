<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_error.log');

require_once __DIR__ . '/../config.php';
$ADMIN = require_admin($DB);


$id = intval($_GET['id'] ?? 0);
if (!$id) die("Invalid issue ID.");

$stmt = $DB->prepare("SELECT * FROM issues WHERE id=?");
$stmt->execute([$id]);
$issue = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$issue) die("Issue not found.");

$err = $ok = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $subject = trim($_POST['subject'] ?? '');
    $year    = intval($_POST['year'] ?? 0);
    $title   = trim($_POST['title'] ?? '');
    $issue_no= isset($_POST['issue_no']) && $_POST['issue_no'] !== ''
        ? intval($_POST['issue_no'])
        : null;
    $price   = floatval($_POST['price'] ?? 0);

    if (!$subject || !$year || !$title || $price <= 0) {
        $err = "Required fields missing.";
    }

    $preview_filename = $issue['preview_filename'];

    if (!$err && !empty($_FILES['preview']['name'])) {
        $pv = $_FILES['preview'];
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

    if (!$err) {
        $stmt = $DB->prepare("
            UPDATE issues SET
                subject=?,
                year=?,
                title=?,
                issue_no=?,
                price=?,
                preview_filename=?
            WHERE id=?
        ");

        $stmt->execute([
            $subject,
            $year,
            $title,
            $issue_no,
            $price,
            $preview_filename,
            $id
        ]);

        $ok = "Issue updated successfully.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Issue</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<style>
body {
    font-family:Poppins, Arial;
    background:#f6f7fb;
    padding:40px;
}
.box {
    max-width:800px;
    margin:auto;
    background:#fff;
    padding:40px;
    border-radius:14px;
}
h1 { margin-bottom:20px; }
label {
    font-weight:600;
    display:block;
    margin-top:18px;
}
input {
    width:100%;
    padding:12px;
    margin-top:6px;
    border-radius:8px;
    border:1px solid #ccc;
}
button {
    margin-top:30px;
    width:100%;
    padding:14px;
    background:#002147;
    color:#fff;
    border:none;
    border-radius:10px;
    font-size:16px;
    cursor:pointer;
}
.err {
    background:#fee;
    padding:12px;
    border-radius:8px;
    margin-bottom:15px;
    color:#b00;
}
.ok {
    background:#d1fae5;
    padding:12px;
    border-radius:8px;
    margin-bottom:15px;
    color:#065f46;
}
.back {
    display:inline-block;
    margin-bottom:20px;
    text-decoration:none;
    font-weight:600;
    color:#5d85b2;
}
.preview-link {
    font-size:14px;
    margin-top:6px;
    display:block;
}
</style>
</head>
<body>

<div class="box">

<a href="admin_panel.php" class="back">← Back to Dashboard</a>

<h1>Edit Issue</h1>

<?php if ($err): ?><div class="err"><?=esc($err)?></div><?php endif; ?>
<?php if ($ok): ?><div class="ok"><?=esc($ok)?></div><?php endif; ?>

<form method="post" enctype="multipart/form-data">

<label>Subject *</label>
<input type="text" name="subject" value="<?=esc($issue['subject'])?>" required>

<label>Year *</label>
<input type="number" name="year" value="<?=$issue['year']?>" required>

<label>Issue Title *</label>
<input type="text" name="title" value="<?=esc($issue['title'])?>" required>

<label>Issue Number (optional)</label>
<input type="number" name="issue_no" value="<?=esc($issue['issue_no'])?>">

<label>Price *</label>
<input type="text" name="price" value="<?=esc($issue['price'])?>" required>

<label>Preview Image (optional)</label>
<?php if ($issue['preview_filename']): ?>
<a class="preview-link" href="<?=PUBLIC_BASE_URL.'/previews/'.$issue['preview_filename']?>" target="_blank">
View current preview
</a>
<?php endif; ?>
<input type="file" name="preview" accept="image/png,image/jpeg">

<button>Save Changes</button>

</form>

</div>

</body>
</html>