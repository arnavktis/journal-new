<?php
require_once 'config.php';

$issueId = (int)($_GET['id'] ?? 0);
if (!$issueId) {
    die("Invalid issue");
}

/* ================= ISSUE ================= */
$issueStmt = $DB->prepare("
    SELECT id, title, price, published_at
    FROM issues
    WHERE id = ?
    LIMIT 1
");
$issueStmt->execute([$issueId]);
$issue = $issueStmt->fetch(PDO::FETCH_ASSOC);

if (!$issue) {
    die("Issue not found");
}

/* ================= ARTICLES ================= */
$articlesStmt = $DB->prepare("
    SELECT 
        a.id,
        a.title,
        a.abstract,
        GROUP_CONCAT(au.name SEPARATOR ', ') AS authors
    FROM articles a
    JOIN article_authors aa ON aa.article_id = a.id
    JOIN authors au ON au.id = aa.author_id
    WHERE a.issue_id = ?
    GROUP BY a.id
    ORDER BY a.id ASC
");
$articlesStmt->execute([$issueId]);
$articles = $articlesStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($issue['title']) ?></title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">

<style>
:root {
    --primary: #374785;
    --primary-dark: #2c3768;
    --bg: #f5f6f8;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Poppins', sans-serif;
    background: var(--bg);
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

.issue-header {
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    color: #fff;
    padding: 160px 20px 70px;
    text-align: center;
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

.issue-header h1 {
    font-family: 'Playfair Display', serif;
    font-size: 42px;
    margin-bottom: 12px;
}

.issue-meta {
    font-size: 16px;
    opacity: 0.9;
}

.content {
    max-width: 900px;
    margin: -30px auto 80px;
    padding: 0 20px;
}

.section-header {
    margin: 50px 0 30px;
    text-align: center;
}

.section-header h2 {
    font-family: 'Playfair Display', serif;
    font-size: 32px;
    margin-bottom: 8px;
    color: var(--primary);
}

.section-header p {
    font-size: 16px;
    color: #666;
}

.article-card {
    background: #fff;
    padding: 35px;
    margin-bottom: 30px;
    border-radius: 16px;
    border-left: 5px solid var(--primary);
    box-shadow: 0 8px 30px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
}

.article-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 40px rgba(0,0,0,0.12);
}

.article-card h3 {
    font-family: 'Playfair Display', serif;
    font-size: 24px;
    margin-bottom: 15px;
    color: var(--primary);
    font-weight: 600;
}

.article-authors {
    font-size: 15px;
    color: #555;
    margin-bottom: 18px;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 8px;
}

.article-authors i {
    color: var(--primary);
}

.article-card p {
    font-size: 15px;
    line-height: 1.7;
    color: #444;
    text-align: justify;
}

.empty {
    background: #fff;
    padding: 60px;
    border-radius: 16px;
    text-align: center;
    color: #666;
    box-shadow: 0 8px 30px rgba(0,0,0,0.08);
}

.empty i {
    font-size: 64px;
    color: #ddd;
    margin-bottom: 20px;
}

.empty h3 {
    font-size: 24px;
    margin-bottom: 10px;
}

.pay-box {
    margin-top: 50px;
    text-align: center;
    background: #fff;
    padding: 40px;
    border-radius: 16px;
    box-shadow: 0 8px 30px rgba(0,0,0,0.08);
}

.pay-box h3 {
    font-family: 'Playfair Display', serif;
    font-size: 28px;
    margin-bottom: 15px;
    color: var(--primary);
}

.pay-box p {
    font-size: 16px;
    color: #666;
    margin-bottom: 25px;
}

.pay-btn {
    background: var(--primary);
    color: #fff;
    padding: 16px 40px;
    font-size: 18px;
    font-weight: 600;
    border: none;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 12px;
}

.pay-btn:hover {
    background: var(--primary-dark);
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(55, 71, 133, 0.3);
}

.footer {
    background: var(--primary);
    color: #fff;
    padding: 40px 20px;
    text-align: center;
}

.footer-text {
    opacity: 0.9;
    font-size: 14px;
}

@media (max-width: 768px) {
    .issue-header h1 {
        font-size: 28px;
    }
    
    .article-card h3 {
        font-size: 20px;
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

<!-- HEADER -->
<header class="issue-header">
    <a href="javascript:history.back()" class="back-link">
        <i class="fas fa-arrow-left"></i>
        Back to Issues
    </a>
    <h1><?= htmlspecialchars($issue['title']) ?></h1>
    <div class="issue-meta">
        <?= $issue['published_at'] ? date('F Y', strtotime($issue['published_at'])) : '' ?>
        • ₹<?= number_format($issue['price'], 2) ?>
    </div>
</header>

<!-- CONTENT -->
<main class="content">

<?php if (!$articles): ?>
    <div class="empty" data-aos="fade-up">
        <i class="fas fa-file-alt"></i>
        <h3>No Articles Yet</h3>
        <p>Articles for this issue will be available soon.</p>
    </div>
<?php else: ?>
    <div class="section-header" data-aos="fade-up">
        <h2>Articles in This Issue</h2>
        <p><?= count($articles) ?> peer-reviewed research article<?= count($articles) != 1 ? 's' : '' ?></p>
    </div>
    
    <?php foreach ($articles as $a): ?>
    <div class="article-card" data-aos="fade-up">
        <h3><?= htmlspecialchars($a['title']) ?></h3>
        <div class="article-authors">
            <i class="fas fa-user"></i>
            <?= htmlspecialchars($a['authors']) ?>
        </div>
        <p><strong>Abstract:</strong> <?= nl2br(htmlspecialchars($a['abstract'])) ?></p>
    </div>
    <?php endforeach; ?>
<?php endif; ?>

<!-- PAYMENT -->
<div class="pay-box" data-aos="fade-up">
    <h3>Get Full Access</h3>
    <p>Purchase this issue to read complete articles and download PDFs</p>
    <button class="pay-btn" onclick="window.location.href='unlock.php?issue_id=<?= $issueId ?>'">
        <i class="fas fa-lock"></i>
        Purchase Full Issue Access
    </button>
</div>

</main>

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
