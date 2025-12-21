<?php
require_once 'config.php';

$subjectId = (int)($_GET['id'] ?? 0);
if (!$subjectId) die("Invalid subject");

// Subject
$subjectStmt = $DB->prepare("SELECT id, name FROM subjects WHERE id = ?");
$subjectStmt->execute([$subjectId]);
$subject = $subjectStmt->fetch(PDO::FETCH_ASSOC);
if (!$subject) die("Subject not found");

// Issues
$issuesStmt = $DB->prepare("
    SELECT id, title, price
    FROM issues
    WHERE subject_id = ?
    ORDER BY published_at DESC
");
$issuesStmt->execute([$subjectId]);
$issues = $issuesStmt->fetchAll(PDO::FETCH_ASSOC);

// Articles stmt
$articlesStmt = $DB->prepare("
    SELECT title
    FROM articles
    WHERE issue_id = ?
    ORDER BY id ASC
    LIMIT 5
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($subject['name']) ?> | Issues</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">

<style>
:root {
    --primary: #374785;
    --primary-dark: #2c3768;
    --bg-light: #f6f6f6;
    --accent: #4A90E2;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Poppins', sans-serif;
    background: var(--bg-light);
    color: #111;
    line-height: 1.6;
}

a {
    text-decoration: none;
    color: inherit;
}

/* NAVBAR */
.navbar {
    position: fixed;
    width: 100%;
    top: 0;
    background: var(--primary);
    z-index: 1000;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.nav-container {
    max-width: 1200px;
    margin: auto;
    padding: 14px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.nav-brand {
    display: flex;
    align-items: center;
    gap: 15px;
}

.nav-brand img {
    height: 40px;
}

.nav-menu {
    display: flex;
    gap: 10px;
}

.nav-menu a {
    color: #fff;
    padding: 8px 16px;
    border-radius: 6px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.nav-menu a:hover {
    background: rgba(255,255,255,0.1);
}

/* HERO */
.hero {
    padding: 160px 20px 70px;
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    color: #fff;
    text-align: center;
}

.hero-title {
    font-size: 46px;
    font-family: 'Playfair Display', serif;
    margin-bottom: 12px;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.hero-subtitle {
    font-size: 18px;
    opacity: 0.9;
}

.back-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: #fff;
    margin-bottom: 20px;
    padding: 8px 16px;
    background: rgba(255,255,255,0.15);
    border-radius: 8px;
    transition: all 0.3s ease;
}

.back-link:hover {
    background: rgba(255,255,255,0.25);
}

/* CONTENT */
.content-section {
    padding: 80px 20px;
}

.container {
    max-width: 1200px;
    margin: auto;
}

/* ISSUE LIST */
.issues-list {
    background: #fff;
    padding: 40px 50px;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    max-width: 800px;
    margin: 40px auto;
}

.issues-list ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.issues-list li {
    padding: 15px 0;
    border-bottom: 1px solid #f0f0f0;
}

.issues-list li:last-child {
    border-bottom: none;
}

.issue-link {
    display: flex;
    align-items: center;
    gap: 15px;
    color: #5a9;
    font-size: 18px;
    font-weight: 500;
    text-transform: uppercase;
    text-decoration: underline;
    transition: all 0.3s ease;
}

.issue-link:hover {
    color: #478;
    padding-left: 10px;
}

.issue-link i {
    font-size: 12px;
    color: #999;
}

.empty-state {
    background: #fff;
    padding: 60px;
    border-radius: 20px;
    text-align: center;
    box-shadow: 0 10px 40px rgba(0,0,0,0.08);
}

.empty-state i {
    font-size: 64px;
    color: #ddd;
    margin-bottom: 20px;
}

.empty-state h3 {
    font-size: 24px;
    color: #666;
    margin-bottom: 10px;
}

.empty-state p {
    color: #999;
}

/* FOOTER */
.footer {
    background: var(--primary);
    color: #fff;
    padding: 40px 20px;
    text-align: center;
    margin-top: 80px;
}

.footer-text {
    opacity: 0.9;
    font-size: 14px;
}

/* RESPONSIVE */
@media (max-width: 768px) {
    .hero-title {
        font-size: 32px;
    }
    
    .issue-grid {
        grid-template-columns: 1fr;
    }
    
    .nav-menu {
        display: none;
    }
}
</style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar">
    <div class="nav-container">
        <div class="nav-brand">
            <img src="images/PHI_White.png" alt="PHI Logo">
            <img src="images/continuum-logo-white.png" alt="The Continuum Logo">
        </div>
        <div class="nav-menu">
            <a href="index.php">Home</a>
            <a href="reviewers.html">Editorial Board</a>
            <a href="all-issues.php">Issues</a>
        </div>
    </div>
</nav>

<!-- HERO -->
<section class="hero">
    <a href="all-issues.php" class="back-link">
        <i class="fas fa-arrow-left"></i>
        Back to All Subjects
    </a>
    <h1 class="hero-title"><?= htmlspecialchars($subject['name']) ?></h1>
    <p class="hero-subtitle">All Issues in This Subject</p>
</section>

<!-- ISSUES -->
<section class="content-section">
<div class="container">

<?php if (empty($issues)): ?>
    <div class="empty-state" data-aos="fade-up">
        <i class="fas fa-inbox"></i>
        <h3>No Issues Yet</h3>
        <p>Issues for this subject will be published soon.</p>
    </div>
<?php else: ?>
    <div class="issues-list" data-aos="fade-up">
        <ul>
        <?php foreach ($issues as $i): ?>
            <li>
                <a href="issue.php?id=<?= $i['id'] ?>" class="issue-link">
                    <i class="fas fa-angle-double-right"></i>
                    <?= htmlspecialchars($i['title']) ?>
                </a>
            </li>
        <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
</div>
</section>

<!-- FOOTER -->
<footer class="footer">
    <div class="container">
        <p class="footer-text">&copy; 2025 PHI Learning Pvt Ltd. All rights reserved.</p>
    </div>
</footer>

<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script>
    AOS.init({
        duration: 600,
        once: true
    });
</script>

</body>
</html>
