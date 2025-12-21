<?php
session_start();
require_once 'config.php';

/**
 * Fetch subjects which have at least 1 issue
 */
$subjects = $DB->query("
    SELECT 
        s.id,
        s.name,
        s.cover_image,
        COUNT(i.id) AS issue_count
    FROM subjects s
    JOIN issues i ON i.subject_id = s.id
    GROUP BY s.id
    HAVING issue_count > 0
    ORDER BY s.name ASC
")->fetchAll(PDO::FETCH_ASSOC);

/**
 * Prepared statement to fetch FIRST 5 issues of a subject
 */
$issueStmt = $DB->prepare("
    SELECT id, title
    FROM issues
    WHERE subject_id = ?
    ORDER BY id ASC
    LIMIT 5
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>The Continuum | All Issues</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
:root {
    --primary:#374785;
    --primary-dark:#2c3768;
    --bg:#f5f6fa;
}

body {
    margin:0;
    font-family:'Poppins',sans-serif;
    background:var(--bg);
}

a { text-decoration:none; }

.navbar {
    position:fixed;
    top:0;
    width:100%;
    background:var(--primary);
    z-index:1000;
}

.nav-container {
    max-width:1200px;
    margin:auto;
    padding:14px 20px;
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.nav-brand img {
    height:38px;
    margin-right:10px;
}

.nav-menu a {
    color:#fff;
    margin-left:20px;
    font-weight:600;
}

.nav-menu a.active {
    border-bottom:2px solid #fff;
}

/* HERO */
.hero {
    padding:160px 20px 90px;
    background:linear-gradient(to bottom,var(--primary),var(--primary-dark));
    color:#fff;
    text-align:center;
}

.hero h1 {
    font-family:'Playfair Display',serif;
    font-size:46px;
}

.hero p {
    opacity:.9;
}

/* GRID */
.container {
    max-width:1200px;
    margin:auto;
    padding:80px 20px;
}

.subject-grid {
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(280px,1fr));
    gap:30px;
}

/* CARD */
.subject-card {
    background:#fff;
    border-radius:18px;
    overflow:hidden;
    box-shadow:0 10px 30px rgba(0,0,0,.08);
    transition:.3s ease;
}

.subject-card:hover {
    transform:translateY(-6px);
    box-shadow:0 20px 45px rgba(0,0,0,.12);
}

.subject-image {
    height:260px;
    background:#eee;
}

.subject-image img {
    width:100%;
    height:100%;
    object-fit:cover;
}

.subject-content {
    padding:22px;
    text-align:center;
}

.subject-content h3 {
    font-family:'Playfair Display',serif;
    font-size:20px;
    margin-bottom:16px;
    text-transform:uppercase;
}

/* ISSUE LIST */
.issue-list {
    list-style:none;
    padding:0;
    margin:0 0 18px;
}

.issue-list li {
    margin-bottom:8px;
}

.issue-list a {
    color:var(--primary);
    font-weight:600;
}

/* VIEW ALL */
.view-all {
    display:inline-block;
    margin-top:10px;
    color:var(--primary);
    font-weight:700;
    text-transform:uppercase;
}
</style>
</head>

<body>

<!-- NAV -->
<nav class="navbar">
    <div class="nav-container">
        <div class="nav-brand">
            <img src="images/PHI_White.png">
            <img src="images/continuum-logo-white.png">
        </div>
        <div class="nav-menu">
            <a href="index.php">Home</a>
            <a href="reviewers.html">Editorial Board</a>
            <a href="all-issues.php" class="active">Issues</a>
        </div>
    </div>
</nav>

<!-- HERO -->
<section class="hero">
    <h1>All Published Issues</h1>
    <p>Browse The Continuum by academic subject</p>
</section>

<!-- SUBJECT GRID -->
<section class="container">
<div class="subject-grid">

<?php foreach ($subjects as $s): ?>
<?php
    $issueStmt->execute([$s['id']]);
    $issues = $issueStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="subject-card">

    <!-- IMAGE -->
    <div class="subject-image">
        <img src="/uploads/subjects/<?= htmlspecialchars($s['cover_image']) ?>" alt="<?= htmlspecialchars($s['name']) ?>">
    </div>

    <!-- CONTENT -->
    <div class="subject-content">
        <h3><?= htmlspecialchars($s['name']) ?></h3>

        <!-- FIRST 5 ISSUES -->
        <ul class="issue-list">
            <?php foreach ($issues as $i): ?>
                <li>
                    <a href="issue.php?id=<?= $i['id'] ?>">
                        <?= htmlspecialchars($i['title']) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>

        <!-- VIEW ALL -->
        <a class="view-all" href="subject.php?id=<?= $s['id'] ?>">
            VIEW ALL <?= $s['issue_count'] ?> ISSUES
        </a>
    </div>

</div>
<?php endforeach; ?>

</div>
</section>

</body>
</html>
