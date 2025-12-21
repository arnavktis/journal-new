<?php
require '../config.php';
$ADMIN = require_admin($DB);

$err = $ok = "";

/* Fetch issues */
$issues = $DB->query("
    SELECT issues.id, issues.title, subjects.name AS subject_name
    FROM issues
    JOIN subjects ON subjects.id = issues.subject_id
    ORDER BY issues.id DESC
")->fetchAll(PDO::FETCH_ASSOC);

/* Fetch authors */
$authors_list = $DB->query("SELECT id, name FROM authors ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $issue_id = intval($_POST['issue_id']);
    $title    = trim($_POST['title']);
    $abstract = trim($_POST['abstract']);
    $authors  = $_POST['authors'] ?? [];

    if (!$issue_id || !$title || !$abstract || empty($authors)) {
        $err = "All fields including authors are required.";
    }

    if (!$err) {

        // Insert article
        $stmt = $DB->prepare("INSERT INTO articles (issue_id, title, abstract) VALUES (?, ?, ?)");
        $stmt->execute([$issue_id, $title, $abstract]);

        $article_id = $DB->lastInsertId();

        // Insert authors mapping
        $order = 1;

        foreach ($authors as $a) {

            // Select dropdown = numeric = existing author
            if (ctype_digit($a)) {
                $author_id = intval($a);

            } else { 
                // Input text = new author name
                $name = trim($a);
                if ($name == "") continue;

                $DB->prepare("INSERT INTO authors(name) VALUES(?)")->execute([$name]);
                $author_id = $DB->lastInsertId();
            }

            $DB->prepare("
                INSERT INTO article_authors (article_id, author_id, order_no)
                VALUES (?, ?, ?)
            ")->execute([$article_id, $author_id, $order++]);
        }

        $ok = "Article uploaded successfully!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Upload Article</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

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


</head>
<body>
<div class="dashboard-container">

<?php include 'sidebar.php'; ?>

<main class="main-content">

<h2>Upload Article</h2>

<div class="card">

<?php if($err): ?><div class="err"><?=esc($err)?></div><?php endif; ?>
<?php if($ok): ?><div class="ok"><?=esc($ok)?></div><?php endif; ?>

<form method="POST">

    <!-- ISSUE -->
    <label>Issue *</label>
    <select name="issue_id" required>
        <option value="">Select Issue</option>
        <?php foreach($issues as $i): ?>
            <option value="<?= $i['id'] ?>">
                <?= esc($i['subject_name']) ?> — <?= esc($i['title']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <!-- TITLE -->
    <label>Article Title *</label>
    <input type="text" name="title" required>

    <!-- ABSTRACT -->
    <label>Abstract *</label>
    <textarea name="abstract" required></textarea>

    <!-- AUTHORS -->
    <label>Authors *</label>

    <div id="authors">
        <div>

            <select name="authors[]" style="width:80%;display:inline-block;">
                <option value="">Select existing author</option>
                <?php foreach($authors_list as $a): ?>
                    <option value="<?= $a['id'] ?>"><?= esc($a['name']) ?></option>
                <?php endforeach; ?>
            </select>

            <input type="text" name="authors[]" placeholder="Or add new author" 
                   style="width:80%;margin-top:10px;">
        </div>
    </div>

    <div class="add-author" onclick="addAuthor()">+ Add More Authors</div>

    <!-- SUBMIT -->
    <button>Upload Article</button>

</form>

</div>
</main>
</div>

<script>
function addAuthor() {
    const d = document.createElement('div');
    d.innerHTML = `
        <select name="authors[]" style="width:80%;display:inline-block;">
            <option value="">Select existing author</option>
            <?php foreach($authors_list as $a): ?>
                <option value="<?= $a['id'] ?>"><?= esc($a['name']) ?></option>
            <?php endforeach; ?>
        </select>

        <input type="text" name="authors[]" placeholder="Or add new author" 
               style="width:80%;margin-top:10px;">

        <span class="remove" onclick="this.parentElement.remove()">Remove</span>
    `;
    document.getElementById("authors").appendChild(d);
}
</script>

</body>
</html>
