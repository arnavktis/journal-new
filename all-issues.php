<?php
session_start();
require_once 'config.php';

$issues = $DB->query("
    SELECT id, title, year, price, slug, preview_filename
    FROM issues
    ORDER BY year DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>The Continuum | All Issues</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">

<style>
/* ===== THEME ===== */
:root {
    --primary: #374785;
    --primary-dark: #2c3768;
    --bg-light: #f6f6f6;
}

/* ===== GLOBAL ===== */
body {
    margin: 0;
    font-family: 'Poppins', sans-serif;
    background: var(--bg-light);
    color: #111;
}

a { text-decoration: none; }

/* ===== NAVBAR ===== */
.navbar {
    position: fixed;
    width: 100%;
    top: 0;
    background: var(--primary);
    z-index: 1000;
}

.nav-container {
    max-width: 1200px;
    margin: auto;
    padding: 14px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.nav-brand img {
    height: 40px;
    margin-right: 10px;
}

.nav-menu a {
    color: #fff;
    margin-left: 20px;
    font-weight: 500;
}

.nav-menu a.active {
    border-bottom: 2px solid #fff;
}

/* ===== HERO ===== */
.hero {
    padding: 160px 20px 90px;
    background: linear-gradient(to bottom, var(--primary), var(--primary-dark));
    color: #fff;
    text-align: center;
}

.hero-title {
    font-size: 46px;
    font-family: 'Playfair Display', serif;
}

.hero-subtitle {
    font-size: 18px;
    opacity: 0.9;
    margin-top: 10px;
}

/* ===== CONTENT ===== */
.about-section {
    padding: 80px 20px;
}

.container {
    max-width: 1200px;
    margin: auto;
}

/* ===== GRID ===== */
.issues-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 28px;
}

/* ===== CARD ===== */
.issue-card {
    background: #fff;
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,.08);
    transition: all .3s ease;
    position: relative;
}

.issue-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 18px 40px rgba(0,0,0,.12);
}

/* ===== PRICE BADGE ===== */
.issue-status {
    position: absolute;
    top: 16px;
    right: 16px;
    background: var(--primary);
    color: #fff;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 600;
    z-index: 2;
}

/* ===== IMAGE ===== */
.issue-card-image {
    height: 260px;
    background: #eee;
    overflow: hidden;
}

.issue-card-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform .4s ease;
}

.issue-card:hover img {
    transform: scale(1.06);
}

/* ===== CONTENT ===== */
.issue-card-content {
    padding: 20px;
    text-align: center;
}

.issue-card-content h3 {
    font-family: 'Playfair Display', serif;
    font-size: 18px;
    margin-bottom: 16px;
    color: #111;
}

/* ===== BUTTONS ===== */
.issue-card-actions {
    display: flex;
    justify-content: center;
    gap: 12px;
}

.btn {
    padding: 10px 16px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    border: none;
}

.btn-secondary {
    background: #f1f1f1;
    color: #333;
}

.btn-secondary:hover {
    background: #e4e4e4;
}

.btn-primary {
    background: var(--primary);
    color: #fff;
}

.btn-primary:hover {
    background: var(--primary-dark);
}

/* ===== MODAL ===== */
.modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.6);
    z-index: 9999;
    backdrop-filter: blur(4px);
}

.modal-box {
    background: #fff;
    max-width: 420px;
    margin: 12% auto;
    padding: 32px;
    border-radius: 18px;
    text-align: center;
    box-shadow: 0 25px 60px rgba(0,0,0,.25);
}

.modal-box h3 {
    font-size: 22px;
    color: var(--primary);
}

.modal-box p {
    color: #555;
    margin: 12px 0 24px;
}

.modal-close {
    background: none;
    border: none;
    margin-top: 16px;
    color: #777;
    cursor: pointer;
}
</style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar">
    <div class="nav-container">
        <div class="nav-brand">
            <img src="images/PHI_White.png">
            <img src="images/continuum-logo-white.png">
        </div>
        <div class="nav-menu">
            <a href="index.html">Home</a>
            <a href="reviewers.html">Editorial Board</a>
            <a href="all-issues.php" class="active">Issues</a>
        </div>
    </div>
</nav>

<!-- HERO -->
<section class="hero">
    <h1 class="hero-title">All Published Issues</h1>
    <p class="hero-subtitle">Explore peer-reviewed academic issues of The Continuum.</p>
</section>

<!-- ISSUES -->
<section class="about-section">
<div class="container">
<div class="issues-grid">

<?php foreach ($issues as $i): ?>
<div class="issue-card" data-aos="fade-up">
    <div class="issue-status">₹<?=number_format($i['price'],2)?></div>

    <div class="issue-card-image">
        <?php if($i['preview_filename']): ?>
            <img src="manuscripts/previews/<?=esc($i['preview_filename'])?>">
        <?php else: ?>
            <img src="images/cover.png">
        <?php endif; ?>
    </div>

    <div class="issue-card-content">
        <h3><?=esc($i['title'])?> (<?=esc($i['year'])?>)</h3>

        <div class="issue-card-actions">
            <a href="issue.php?slug=<?=esc($i['slug'])?>" class="btn btn-secondary">
                <i class="fas fa-eye"></i> Preview
            </a>

            <button class="btn btn-primary" onclick="openPay(<?= $i['id'] ?>)">
                <i class="fas fa-lock"></i> Full Access
            </button>
        </div>
    </div>
</div>
<?php endforeach; ?>

</div>
</div>
</section>

<!-- PAYMENT MODAL -->
<div id="payModal" class="modal-overlay">
    <div class="modal-box">
        <h3>Secure Payment</h3>
        <p>This is a demo payment gateway.</p>

        <button class="btn btn-primary" style="width:100%" onclick="payNow()">
            <i class="fas fa-lock"></i> Pay Now
        </button>

        <button class="modal-close" onclick="closePay()">Cancel</button>
    </div>
</div>

<script>
let selectedIssue = null;

function openPay(id){
    selectedIssue = id;
    document.getElementById('payModal').style.display = 'block';
}

function closePay(){
    document.getElementById('payModal').style.display = 'none';
}

function payNow(){
    window.location = 'unlock.php?issue_id=' + selectedIssue;
}
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script>AOS.init();</script>

</body>
</html>
