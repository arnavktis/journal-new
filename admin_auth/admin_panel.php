<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../config.php';
$ADMIN = require_admin($DB); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Journal Portal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-blue:#002147;--secondary-blue:#003366;--light-blue:#5d85b2;
            --gold:#D4AF37;--dark:#1f2937;--light:#6b7280;
            --white:#fff;--bg:#f8fafc;--border:#e5e7eb;
        }
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:Poppins,sans-serif;background:var(--bg);color:var(--dark)}
        .dashboard-container{display:flex;min-height:100vh}
        .sidebar{width:280px;background:linear-gradient(180deg,var(--primary-blue),var(--secondary-blue));color:#fff;position:fixed;height:100vh}
        .sidebar-header{padding:32px 24px;border-bottom:1px solid rgba(255,255,255,.1)}
        .admin-info{padding:20px 24px;background:rgba(255,255,255,.1);display:flex;gap:12px}
        .admin-avatar{width:48px;height:48px;border-radius:50%;background:linear-gradient(135deg,var(--gold),#f59e0b);
            display:flex;align-items:center;justify-content:center;font-weight:600}
        .nav-menu a{display:flex;gap:16px;padding:14px 24px;color:#fff;text-decoration:none}
        .nav-menu a:hover{background:rgba(255,255,255,.1)}
        .main-content{margin-left:280px;flex:1;padding:40px}
        .cards-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:24px}
        .card{background:#fff;border-radius:16px;padding:28px;border:1px solid var(--border);text-decoration:none;color:inherit}
    </style>
</head>
<body>
<div class="dashboard-container">
    <aside class="sidebar">
        <div class="sidebar-header">
            <h1>Journal Portal</h1>
            <p>Admin Dashboard</p>
        </div>

        <div class="admin-info">
            <div class="admin-avatar">
                <?= strtoupper(substr(esc($ADMIN['name']),0,1)) ?>
            </div>
            <div>
                <h3><?= esc($ADMIN['name']) ?></h3>
                <p>Administrator</p>
            </div>
        </div>

        <nav class="nav-menu">
            <a href="admin_panel.php">Dashboard</a>
            <a href="upload_article.php">Upload Article</a>
            <a href="upload_issue.php">Upload Issue</a>
            <a href="issues_list.php">Manage Issues</a>
            <a href="articles_list.php">Manage Articles</a>
            <a href="pricing.php">Pricing</a>
            <a href="logout.php" style="color:#fecaca">Logout</a>
        </nav>
    </aside>

    <main class="main-content">
        <h2>Welcome back, <?= esc($ADMIN['name']) ?> 👋</h2>
        <p>Manage your journal content</p>

        <div class="cards-grid" style="margin-top:30px">
            <a href="upload_article.php" class="card">
                <h3>Upload Article</h3>
                <p>Add new articles</p>
            </a>
            <a href="upload_issue.php" class="card">
                <h3>Upload Issue</h3>
                <p>Create journal issues</p>
            </a>
            <a href="pricing.php" class="card">
                <h3>Pricing</h3>
                <p>Manage pricing</p>
            </a>
        </div>
    </main>
</div>
</body>
</html>