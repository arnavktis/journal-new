<?php
require_once __DIR__ . '/../config.php';
$ADMIN = require_admin($DB);


$id = intval($_GET['id'] ?? 0);
if (!$id) die("Invalid article ID.");

$stmt = $DB->prepare("SELECT * FROM articles WHERE id=?");
$stmt->execute([$id]);
$article = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$article) die("Article not found.");

/* Fetch issues */
$issues = $DB->query("
    SELECT id, subject, year, title
    FROM issues
    ORDER BY year DESC, id DESC
")->fetchAll(PDO::FETCH_ASSOC);

/* Fetch authors */
$stmt = $DB->prepare("
    SELECT au.name
    FROM article_authors aa
    JOIN authors au ON au.id = aa.author_id
    WHERE aa.article_id = ?
    ORDER BY aa.order_no
");
$stmt->execute([$id]);
$authors = $stmt->fetchAll(PDO::FETCH_ASSOC);

$err = ''; 
$ok  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $issue_id = intval($_POST['issue_id'] ?? 0);
    $title    = trim($_POST['title'] ?? '');
    $summary  = trim($_POST['summary'] ?? '');
    $new_authors = $_POST['authors'] ?? [];

    if (!$issue_id || $title === '') {
        $err = "Issue and Title are required.";
    }

    /* Fetch subject from issue */
    if (!$err) {
        $st = $DB->prepare("SELECT subject FROM issues WHERE id=?");
        $st->execute([$issue_id]);
        $issue = $st->fetch(PDO::FETCH_ASSOC);
        if (!$issue) {
            $err = "Invalid issue selected.";
        } else {
            $subject = $issue['subject'];
        }
    }

    /* Handle file replacement */
    $filename = $article['filename'];
    $original_name = $article['original_name'];

    if (!$err && !empty($_FILES['article_file']['name'])) {
        $file = $_FILES['article_file'];

        if ($file['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $filename = "article_" . date('Ymd_His') . "_" . bin2hex(random_bytes(4)) . ".$ext";
            $original_name = $file['name'];

            $dir = dirname(__DIR__).'/manuscripts/articles';
            if (!is_dir($dir)) mkdir($dir, 0755, true);

            move_uploaded_file($file['tmp_name'], "$dir/$filename");
        }
    }

    if (!$err) {

        $DB->prepare("
            UPDATE articles SET
                issue_id=?,
                subject=?,
                title=?,
                summary=?,
                filename=?,
                original_name=?
            WHERE id=?
        ")->execute([
            $issue_id,
            $subject,
            $title,
            $summary,
            $filename,
            $original_name,
            $id
        ]);

        /* Reset authors */
        $DB->prepare("DELETE FROM article_authors WHERE article_id=?")->execute([$id]);

        $order = 1;
        foreach ($new_authors as $name) {
            $name = trim($name);
            if ($name === '') continue;

            $st = $DB->prepare("SELECT id FROM authors WHERE name=? LIMIT 1");
            $st->execute([$name]);
            $a = $st->fetch(PDO::FETCH_ASSOC);

            if ($a) {
                $aid = $a['id'];
            } else {
                $DB->prepare("INSERT INTO authors(name) VALUES(?)")->execute([$name]);
                $aid = $DB->lastInsertId();
            }

            $DB->prepare("
                INSERT INTO article_authors (article_id, author_id, order_no)
                VALUES (?, ?, ?)
            ")->execute([$id, $aid, $order++]);
        }

        $ok = "Article updated successfully.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Article</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<style>
body { font-family:Poppins; background:#f8fafc; padding:40px; }
.container { max-width:900px; margin:auto; background:#fff; padding:40px; border-radius:16px; }
input, textarea, select {
    width:100%; padding:14px; margin-bottom:20px;
    border-radius:10px; border:1px solid #ccc;
}
textarea { height:140px; resize:none; }
.btn-save {
    padding:14px 18px; background:#003366;
    color:white; border:none; border-radius:10px;
    font-size:16px; cursor:pointer;
}
.alert-success { background:#d1fae5; padding:12px; border-radius:8px; margin-bottom:20px; }
.alert-error { background:#fee; padding:12px; border-radius:8px; margin-bottom:20px; }
.remove-author { color:#c00; cursor:pointer; margin-left:10px; }
.add-btn { background:#004080; color:white; padding:8px 14px; border-radius:8px; cursor:pointer; display:inline-block; }
.file-link { font-size:14px; margin-bottom:10px; display:block; }
</style>
</head>
<body>

<div class="container">

<h2>Edit Article</h2>

<?php if($err): ?><div class="alert-error"><?=esc($err)?></div><?php endif; ?>
<?php if($ok): ?><div class="alert-success"><?=esc($ok)?></div><?php endif; ?>

<form method="post" enctype="multipart/form-data">

<label>Assign to Issue *</label>
<select name="issue_id" required>
<?php foreach($issues as $i): ?>
<option value="<?=$i['id']?>" <?= $i['id']==$article['issue_id']?'selected':'' ?>>
<?=esc($i['subject'])?> — <?=esc($i['year'])?> — <?=esc($i['title'])?>
</option>
<?php endforeach; ?>
</select>

<label>Article Title *</label>
<input type="text" name="title" value="<?=esc($article['title'])?>" required>

<label>Summary *</label>
<textarea name="summary"><?=esc($article['summary'])?></textarea>

<label>Authors *</label>
<div id="authors-container">
<?php foreach($authors as $a): ?>
<div>
<input type="text" name="authors[]" value="<?=esc($a['name'])?>">
<span class="remove-author" onclick="this.parentElement.remove()">Remove</span>
</div>
<?php endforeach; ?>
</div>

<div class="add-btn" onclick="addAuthor()">+ Add Author</div>

<label style="margin-top:20px;">Replace Article File (optional)</label>
<a class="file-link" href="<?=PUBLIC_BASE_URL.'/articles/'.$article['filename']?>" target="_blank">Download current file</a>
<input type="file" name="article_file">

<button class="btn-save">Save Changes</button>

</form>

</div>

<script>
function addAuthor() {
    const d = document.createElement("div");
    d.innerHTML = `<input type="text" name="authors[]" placeholder="Author Name">
                   <span class="remove-author" onclick="this.parentElement.remove()">Remove</span>`;
    document.getElementById("authors-container").appendChild(d);
}
</script>

</body>
</html>