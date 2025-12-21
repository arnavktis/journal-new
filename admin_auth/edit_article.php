<?php
require '../config.php';
$ADMIN = require_admin($DB);

$id = intval($_GET['id'] ?? 0);
if (!$id) die("Invalid article ID");

// fetch article
$stmt = $DB->prepare("SELECT * FROM articles WHERE id=?");
$stmt->execute([$id]);
$article = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$article) die("Article not found");

// fetch issues
$issues = $DB->query("SELECT id, title FROM issues ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

// fetch authors
$stmt = $DB->prepare("
    SELECT au.name 
    FROM article_authors aa
    JOIN authors au ON au.id = aa.author_id
    WHERE aa.article_id = ?
    ORDER BY aa.order_no
");
$stmt->execute([$id]);
$existing_authors = $stmt->fetchAll(PDO::FETCH_COLUMN);

$err = $ok = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $issue_id = intval($_POST['issue_id']);
    $title    = trim($_POST['title']);
    $abstract = trim($_POST['abstract']);
    $authors  = $_POST['authors'] ?? [];

    if (!$issue_id || !$title || !$abstract) {
        $err = "All fields are required.";
    }

    if (!$err) {

        // update article (NO FILENAME)
        $stmt = $DB->prepare("
            UPDATE articles SET 
                issue_id=?,
                title=?,
                abstract=?
            WHERE id=?
        ");
        $stmt->execute([
            $issue_id,
            $title,
            $abstract,
            $id
        ]);

        // delete old authors
        $DB->prepare("DELETE FROM article_authors WHERE article_id=?")->execute([$id]);

        // insert authors
        $order = 1;
        foreach ($authors as $name) {
            $name = trim($name);
            if ($name === '') continue;

            // check existing
            $stmtA = $DB->prepare("SELECT id FROM authors WHERE name=? LIMIT 1");
            $stmtA->execute([$name]);
            $fetch = $stmtA->fetch(PDO::FETCH_ASSOC);

            if ($fetch) {
                $author_id = $fetch['id'];
            } else {
                $DB->prepare("INSERT INTO authors (name) VALUES (?)")->execute([$name]);
                $author_id = $DB->lastInsertId();
            }

            $DB->prepare("
                INSERT INTO article_authors(article_id, author_id, order_no)
                VALUES (?, ?, ?)
            ")->execute([$id, $author_id, $order++]);
        }

        $ok = "Article updated successfully!";
    }
}
?>


<!DOCTYPE html>
<html>
<head>
<title>Edit Article</title>

<style>
:root {
    --primary-blue:#002147;
    --secondary-blue:#003366;
    --dark:#1f2937;
    --border:#e5e7eb;
    --bg:#f8fafc;
}

/* RESET */
*{margin:0;padding:0;box-sizing:border-box}

body{
    font-family:Poppins,sans-serif;
    background:var(--bg);
    color:var(--dark);
}

/* LAYOUT */
.dashboard-container{
    display:flex;
    min-height:100vh;
}

/* SIDEBAR */
.sidebar{
    width:280px;
    background:linear-gradient(180deg,var(--primary-blue),var(--secondary-blue));
    color:#fff;
    position:fixed;
    height:100vh;
    overflow-y:auto;
}

.sidebar-header{
    padding:32px 24px;
    border-bottom:1px solid rgba(255,255,255,.15);
}

.admin-info{
    padding:20px 24px;
    background:rgba(255,255,255,.1);
    border-bottom:1px solid rgba(255,255,255,.1);
}

.nav-menu a{
    display:flex;
    padding:14px 24px;
    gap:12px;
    font-size:15px;
    text-decoration:none;
    color:#fff;
    border-bottom:1px solid rgba(255,255,255,.05);
}
.nav-menu a:hover{
    background:rgba(255,255,255,.1);
}

/* MAIN */
.main-content{
    margin-left:280px;
    flex:1;
    padding:40px;
}

h2{
    margin-bottom:20px;
    font-weight:600;
}

/* CARD */
.card{
    background:#fff;
    padding:30px;
    border-radius:16px;
    border:1px solid var(--border);
    max-width:750px;
}

/* FORM CONTROLS */
label{
    display:block;
    margin-top:18px;
    font-weight:600;
    font-size:15px;
}

input, textarea, select{
    width:100%;
    padding:12px;
    margin-top:6px;
    border-radius:8px;
    background:white;
    border:1px solid var(--border);
    font-size:14px;
}

textarea{
    height:150px;
    resize:none;
}

/* BUTTON */
button{
    margin-top:30px;
    width:100%;
    padding:14px;
    background:var(--primary-blue);
    color:white;
    border:none;
    border-radius:10px;
    font-size:16px;
    font-weight:600;
    cursor:pointer;
}
button:hover{
    background:var(--secondary-blue);
}

/* ALERTS */
.err{
    background:#fee;
    padding:12px;
    border-radius:8px;
    color:#b00;
    margin-bottom:20px;
    border:1px solid #f5c2c7;
}
.ok{
    background:#d1fae5;
    padding:12px;
    border-radius:8px;
    color:#065f46;
    margin-bottom:20px;
    border:1px solid #a7f3d0;
}

/* AUTHOR CONTROLS */
.add-author{
    margin-top:12px;
    cursor:pointer;
    color:var(--primary-blue);
    font-weight:600;
    font-size:14px;
}

.remove{
    color:#c00;
    cursor:pointer;
    margin-left:10px;
    font-size:13px;
}

</style>

<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap">
</head>

<body>
<div class="dashboard-container">

<?php include 'sidebar.php'; ?>

<main class="main-content">

<h2>Edit Article</h2>

<div class="card">

<?php if ($err): ?><div class="err"><?=esc($err)?></div><?php endif; ?>
<?php if ($ok): ?><div class="ok"><?=esc($ok)?></div><?php endif; ?>

<form method="POST" enctype="multipart/form-data">

    <label>Assign Issue *</label>
    <select name="issue_id" required>
        <?php foreach($issues as $i): ?>
            <option value="<?= $i['id'] ?>" <?= $i['id'] == $article['issue_id'] ? 'selected' : '' ?>>
                <?= esc($i['title']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Article Title *</label>
    <input type="text" name="title" value="<?= esc($article['title']) ?>" required>

    <label>Abstract *</label>
    <textarea name="abstract"><?= esc($article['abstract']) ?></textarea>

    <label>Authors *</label>
    <div id="authors-container">
        <?php foreach($existing_authors as $a): ?>
        <div>
            <input type="text" name="authors[]" value="<?= esc($a) ?>">
            <span class="remove-author" onclick="this.parentElement.remove()">Remove</span>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="add-author" onclick="addAuthor()">+ Add Author</div>

    <label>Replace Article File (optional)</label>
    <?php if ($article['filename']): ?>
        <a href="<?= PUBLIC_BASE_URL.'/articles/'.$article['filename'] ?>" target="_blank">
            Download current file
        </a>
    <?php endif; ?>
    <input type="file" name="article_file">

    <button>Save Changes</button>

</form>

</div>
</main>
</div>

<script>
function addAuthor() {
    const div = document.createElement("div");
    div.innerHTML = `
        <input type="text" name="authors[]" placeholder="Author Name">
        <span class="remove-author" onclick="this.parentElement.remove()">Remove</span>
    `;
    document.getElementById("authors-container").appendChild(div);
}
</script>

</body>
</html>
