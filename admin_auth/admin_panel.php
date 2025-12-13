<?php require_once __DIR__ . '/auth_check.php'; ?>
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
            --primary-blue: #002147;
            --secondary-blue: #003366;
            --tertiary-blue: #004080;
            --light-blue: #5d85b2;
            --continuum-gold: #D4AF37;
            --text-dark: #1f2937;
            --text-light: #6b7280;
            --bg-white: #ffffff;
            --bg-light: #f8fafc;
            --border-light: #e5e7eb;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: var(--bg-light);
            color: var(--text-dark);
            min-height: 100vh;
        }
        .dashboard-container { display: flex; min-height: 100vh; }
        .sidebar {
            width: 280px;
            background: linear-gradient(180deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
            color: white;
            padding: 0;
            position: fixed;
            height: 100vh;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            z-index: 100;
        }
        .sidebar-header {
            padding: 32px 24px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .sidebar-header h1 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 8px;
        }
        .sidebar-header p {
            font-size: 13px;
            opacity: 0.8;
            font-weight: 300;
        }
        .admin-info {
            padding: 20px 24px;
            background: rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .admin-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--continuum-gold), #f59e0b);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: 600;
            color: white;
        }
        .admin-details h3 {
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 2px;
        }
        .admin-details p {
            font-size: 12px;
            opacity: 0.8;
        }
        .nav-menu { padding: 24px 0; }
        .nav-menu a {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 14px 24px;
            color: white;
            text-decoration: none;
            font-size: 15px;
            font-weight: 500;
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }
        .nav-menu a:hover {
            background: rgba(255, 255, 255, 0.1);
            border-left-color: var(--continuum-gold);
        }
        .nav-menu a svg {
            width: 22px;
            height: 22px;
            fill: currentColor;
        }
        .nav-menu .logout-btn {
            margin-top: 24px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 24px;
        }
        .nav-menu .logout-btn a { color: #fecaca; }
        .nav-menu .logout-btn a:hover {
            background: rgba(254, 202, 202, 0.1);
            border-left-color: #fecaca;
        }
        .main-content {
            margin-left: 280px;
            flex: 1;
            padding: 40px;
        }
        .content-header { margin-bottom: 40px; }
        .content-header h2 {
            font-size: 32px;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 8px;
        }
        .content-header p {
            font-size: 16px;
            color: var(--text-light);
        }
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
        }
        .card {
            background: var(--bg-white);
            border-radius: 16px;
            padding: 28px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            border: 1px solid var(--border-light);
            text-decoration: none;
            color: inherit;
            display: block;
        }
        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        .card-icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }
        .card-icon svg {
            width: 28px;
            height: 28px;
            fill: white;
        }
        .card-icon.blue { background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue)); }
        .card-icon.purple { background: linear-gradient(135deg, #7c3aed, #a855f7); }
        .card-icon.green { background: linear-gradient(135deg, #059669, #10b981); }
        .card h3 {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 8px;
        }
        .card p {
            font-size: 14px;
            color: var(--text-light);
            line-height: 1.6;
        }
        .card-arrow {
            margin-top: 16px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 14px;
            font-weight: 500;
            color: var(--light-blue);
        }
        .card-arrow svg {
            width: 16px;
            height: 16px;
            fill: currentColor;
            transition: transform 0.3s ease;
        }
        .card:hover .card-arrow svg { transform: translateX(4px); }
        @media (max-width: 768px) {
            .sidebar { width: 100%; position: relative; height: auto; }
            .main-content { margin-left: 0; padding: 24px; }
            .cards-grid { grid-template-columns: 1fr; }
            .content-header h2 { font-size: 24px; }
        }
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
                <div class="admin-avatar"><?= strtoupper(substr(esc($_SESSION['admin_name']), 0, 1)) ?></div>
                <div class="admin-details">
                    <h3><?= esc($_SESSION['admin_name']) ?></h3>
                    <p>Administrator</p>
                </div>
            </div>
            <nav class="nav-menu">
                <a href="admin_panel.php">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
                    Dashboard
                </a>
                <a href="upload_article.php">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
                    Upload Article
                </a>
                <a href="upload_issue.php">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14z"/><path d="M7 10h2v7H7zm4-3h2v10h-2zm4 6h2v4h-2z"/></svg>
                    Upload Issue
                </a>
                <a href="pricing.php">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/></svg>
                    Pricing
                </a>
                <div class="logout-btn">
                    <a href="logout.php">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/></svg>
                        Logout
                    </a>
                </div>
            </nav>
        </aside>
        <main class="main-content">
            <div class="content-header">
                <h2>Welcome back, <?= esc($_SESSION['admin_name']) ?>! 👋</h2>
                <p>Manage your journal content from this dashboard</p>
            </div>
            <div class="cards-grid">
                <a href="upload_article.php" class="card">
                    <div class="card-icon blue">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
                    </div>
                    <h3>Upload Article</h3>
                    <p>Upload new articles in PDF, DOC, DOCX, or other supported formats</p>
                    <span class="card-arrow">Manage Articles <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z"/></svg></span>
                </a>
                <a href="upload_issue.php" class="card">
                    <div class="card-icon purple">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14z"/><path d="M7 10h2v7H7zm4-3h2v10h-2zm4 6h2v4h-2z"/></svg>
                    </div>
                    <h3>Upload Issue</h3>
                    <p>Publish new journal issues with volume and issue number tracking</p>
                    <span class="card-arrow">Manage Issues <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z"/></svg></span>
                </a>
                <a href="pricing.php" class="card">
                    <div class="card-icon green">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/></svg>
                    </div>
                    <h3>Pricing Management</h3>
                    <p>Set and update prices for articles, issues, and subscriptions</p>
                    <span class="card-arrow">Manage Pricing <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z"/></svg></span>
                </a>
            </div>
        </main>
    </div>
</body>
</html>
