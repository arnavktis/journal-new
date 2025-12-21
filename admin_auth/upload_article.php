<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../config.php';

/* AUTH */
$ADMIN = require_admin($DB);

$err = $ok = '';

/* Fetch issues */
$issues = $DB->query("
    SELECT id, subject, year, title
    FROM issues
    ORDER BY year DESC, id DESC
")->fetchAll(PDO::FETCH_ASSOC);

function make_slug($s) {
    $s = strtolower(trim($s));
    $s = iconv('utf-8','ascii//TRANSLIT',$s);
    $s = preg_replace('/[^a-z0-9]+/', '-', $s);
    return trim($s, '-');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $issue_id = intval($_POST['issue_id'] ?? 0);
    $title    = trim($_POST['title'] ?? '');
    $summary  = trim($_POST['summary'] ?? '');
    $authors  = $_POST['authors'] ?? [];

    if (!$issue_id || !$title) {
        $err = "Issue and Article Title are required.";
    }

    /* Fetch subject */
    if (!$err) {
        $stmt = $DB->prepare("SELECT subject FROM issues WHERE id=?");
        $stmt->execute([$issue_id]);
        $issue = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$issue) {
            $err = "Invalid issue selected.";
        } else {
            $subject = $issue['subject'];
        }
    }

    if (!$err && empty($_FILES['article']['name'])) {
        $err = "Article file is required.";
    }

    /* Upload file */
    if (!$err) {
        $f = $_FILES['article'];

        if ($f['error'] !== UPLOAD_ERR_OK) {
            $err = "File upload failed.";
        } else {
            $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
            $slug = make_slug($title);

            $filename = "article_" . date('Ymd_His') . "_" . bin2hex(random_bytes(4)) . ".$ext";

            $dir = dirname(__DIR__)."/manuscripts/articles";
            if (!is_dir($dir)) mkdir($dir,0755,true);

            if (!move_uploaded_file($f['tmp_name'], "$dir/$filename")) {
                $err = "Failed to save article file.";
            }
        }
    }

    /* Insert article */
    if (!$err) {

        $uploaded_by = $ADMIN['name'];

        $stmt = $DB->prepare("
            INSERT INTO articles
            (issue_id, subject, title, slug, summary, filename, original_name, uploaded_by)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $issue_id,
            $subject,
            $title,
            $slug,
            $summary,
            $filename,
            $_FILES['article']['name'],
            $uploaded_by
        ]);

        $article_id = $DB->lastInsertId();

        /* Authors */
        foreach ($authors as $i => $name) {
            $name = trim($name);
            if ($name === '') continue;

            $stmtA = $DB->prepare("SELECT id FROM authors WHERE name=? LIMIT 1");
            $stmtA->execute([$name]);
            $a = $stmtA->fetch(PDO::FETCH_ASSOC);

            if ($a) {
                $author_id = $a['id'];
            } else {
                $stmtA = $DB->prepare("INSERT INTO authors (name) VALUES (?)");
                $stmtA->execute([$name]);
                $author_id = $DB->lastInsertId();
            }

            $stmtAA = $DB->prepare("
                INSERT INTO article_authors (article_id, author_id, order_no)
                VALUES (?, ?, ?)
            ");
            $stmtAA->execute([$article_id, $author_id, $i + 1]);
        }

        $ok = "Article uploaded successfully.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Upload Article</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<style>
body { font-family:Poppins; background:#f8fafc; padding:40px; }
.container { max-width:900px; margin:auto; }
.card { background:#fff; padding:40px; border-radius:16px; }
.form-group { margin-bottom:22px; }
input, select, textarea {
    width:100%; padding:14px;
    border-radius:10px; border:1px solid #e5e7eb;
}
textarea { height:140px; resize:none; }
button {
    width:100%; padding:16px;
    background:#003366; color:#fff;
    border:none; border-radius:12px;
    font-size:16px;
}
.alert { padding:14px; border-radius:10px; margin-bottom:20px; }
.err { background:#fee; color:#b00; }
.ok { background:#d1fae5; color:#065f46; }
.add-author { margin-top:10px; cursor:pointer; color:#003366; font-weight:600; }
.remove { color:#c00; cursor:pointer; margin-left:10px; }
.back { display:inline-block; margin-bottom:20px; font-weight:600; color:#5d85b2; text-decoration:none; }
</style>
</head>
<body>

<div class="container">
<a href="admin_panel.php" class="back">← Back to Dashboard</a>

<h1>Upload Article</h1>

<?php if($err): ?><div class="alert err"><?=esc($err)?></div><?php endif; ?>
<?php if($ok): ?><div class="alert ok"><?=esc($ok)?></div><?php endif; ?>

<form method="post" enctype="multipart/form-data">
<div class="card">

<div class="form-group">
<label>Assign to Issue *</label>
<select name="issue_id" required>
<option value="">Select Issue</option>
<?php foreach($issues as $i): ?>
<option value="<?=$i['id']?>">
<?=esc($i['subject'])?> — <?=esc($i['year'])?> — <?=esc($i['title'])?>
</option>
<?php endforeach; ?>
</select>
</div>

<div class="form-group">
<label>Article Title *</label>
<input type="text" name="title" required>
</div>

<div class="form-group">
<label>Summary / Abstract *</label>
<textarea name="summary"></textarea>
</div>

<div class="form-group">
<label>Authors *</label>
<div id="authors">
<input type="text" name="authors[]" placeholder="Author name" required>
</div>
<div class="add-author" onclick="addAuthor()">+ Add Author</div>
</div>

<div class="form-group">
<label>Article File *</label>
<input type="file" name="article" required>
</div>

<button>Upload Article</button>

</div>
</form>
</div>

<script>
function addAuthor() {
    const d = document.createElement('div');
    d.innerHTML = `<input type="text" name="authors[]" placeholder="Author name">
                   <span class="remove" onclick="this.parentElement.remove()">Remove</span>`;
    document.getElementById('authors').appendChild(d);
}
</script>

</body>
</html>