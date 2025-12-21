<?php
session_start();
require_once 'config.php';

$slug = trim($_GET['slug'] ?? '');

if ($slug === '') {
    die("Invalid issue slug");
}

$stmt = $DB->prepare("SELECT * FROM issues WHERE slug = ? LIMIT 1");
$stmt->execute([$slug]);
$issue = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$issue) {
    die("Issue not found");
}

$stmt = $DB->prepare("
    SELECT id, title, summary, filename 
    FROM articles 
    WHERE issue_id = ?
");
$stmt->execute([$issue['id']]);
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

$paid = $_SESSION['paid_issues'][$issue['id']] ?? false;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?=esc($issue['title'])?></title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">

<style>
/* ===== THEME ===== */
:root {
    --primary: #374785;
    --primary-dark: #2c3768;
    --bg: #f5f6f8;
}

/* ===== GLOBAL ===== */
body {
    margin: 0;
    font-family: 'Poppins', sans-serif;
    background: var(--bg);
    color: #111;
}

a { text-decoration: none; }

/* ===== ISSUE HEADER ===== */
.issue-header {
    background: linear-gradient(180deg, var(--primary), var(--primary-dark));
    color: #fff;
    padding: 80px 20px 55px;
}

.issue-header-inner {
    max-width: 820px;
    margin: auto;
}

.issue-header h1 {
    font-family: 'Playfair Display', serif;
    font-size: 38px;
    margin-bottom: 6px;
}

.issue-meta {
    font-size: 15px;
    opacity: 0.9;
}

/* ===== CONTENT ===== */
.content {
    max-width: 820px;
    margin: -30px auto 80px;
    padding: 0 20px;
}

/* ===== SECTION HEADER ===== */
.section-header {
    margin: 35px 0 22px;
}

.section-header h2 {
    font-family: 'Playfair Display', serif;
    font-size: 24px;
    margin-bottom: 4px;
}

.section-header p {
    font-size: 14px;
    color: #666;
}

/* ===== ARTICLE LIST ===== */
.article-card {
    background: #fff;
    padding: 26px 30px;
    margin-bottom: 20px;
    border-radius: 12px;
    border-left: 4px solid var(--primary);
    box-shadow: 0 6px 18px rgba(0,0,0,.06);
}

.article-card h3 {
    font-family: 'Playfair Display', serif;
    font-size: 20px;
    margin-bottom: 10px;
    color: #1f2d5a;
}

.article-card p {
    font-size: 15px;
    line-height: 1.65;
    color: #444;
    margin-bottom: 16px;
}

/* ===== ACTIONS ===== */
.article-actions {
    display: flex;
    gap: 12px;
}

.btn {
    padding: 9px 16px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    border: none;
    cursor: pointer;
}

.btn-primary {
    background: var(--primary);
    color: #fff;
}

.btn-primary:hover {
    background: var(--primary-dark);
}

.btn-secondary {
    background: #e8e8e8;
    color: #333;
}

.btn-secondary:hover {
    background: #dcdcdc;
}

/* ===== EMPTY STATE ===== */
.empty {
    background: #fff;
    padding: 40px;
    border-radius: 12px;
    text-align: center;
    color: #666;
    box-shadow: 0 6px 18px rgba(0,0,0,.06);
}
</style>
</head>

<body>

<!-- ISSUE HEADER -->
<header class="issue-header">
    <div class="issue-header-inner">
        <h1><?=esc($issue['title'])?></h1>
        <div class="issue-meta">
            <?=esc($issue['year'])?> • ₹<?=number_format($issue['price'],2)?>
        </div>
    </div>
</header>

<!-- CONTENT -->
<main class="content">

<?php if ($articles): ?>
    <div class="section-header">
        <h2>Articles in this Issue</h2>
        <p><?=count($articles)?> peer-reviewed article(s)</p>
    </div>
<?php endif; ?>

<?php if (!$articles): ?>
    <div class="empty">
        No articles published yet.
    </div>
<?php endif; ?>

<?php foreach ($articles as $a): ?>
<div class="article-card">
    <h3><?=esc($a['title'])?></h3>
    <p><?=esc($a['summary'])?></p>

    <div class="article-actions">
        <?php if ($paid): ?>
            <a class="btn btn-primary"
               href="manuscripts/articles/<?=esc($a['filename'])?>" download>
                Download PDF
            </a>
        <?php else: ?>
            <button class="btn btn-secondary"
                    onclick="unlockIssue(<?= $issue['id'] ?>)">
                Unlock to Download
            </button>
        <?php endif; ?>
    </div>
</div>
<?php endforeach; ?>

</main>

<script>
function unlockIssue(id){
    window.location = 'unlock.php?issue_id=' + id;
}
</script>

</body>
</html>
