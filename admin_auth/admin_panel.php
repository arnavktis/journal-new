<?php
require '../config.php';
$ADMIN = require_admin($DB);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Journal Portal</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-blue:#002147;
            --secondary-blue:#003366;
            --gold:#D4AF37;
            --dark:#1f2937;
            --border:#e5e7eb;
            --bg:#f8fafc;
            --white:#fff;
        }
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:Poppins,sans-serif;background:var(--bg);color:var(--dark);}
        .dashboard-container{display:flex;min-height:100vh;}

        .sidebar{
            width:280px;
            background:linear-gradient(180deg,var(--primary-blue),var(--secondary-blue));
            color:#fff;
            position:fixed;
            height:100vh;
            overflow-y:auto;
            padding-bottom:20px;
        }
        .sidebar-header{padding:32px 24px;border-bottom:1px solid rgba(255,255,255,.1);}
        .admin-info{padding:20px 24px;background:rgba(255,255,255,.1);}
        .nav-menu a{
            display:flex;gap:14px;
            padding:14px 24px;
            color:#fff;text-decoration:none;
        }
        .nav-menu a:hover{background:rgba(255,255,255,.1);}

        .main-content{
            margin-left:280px;
            flex:1;
            padding:40px;
        }

        .cards-grid{
            display:grid;
            grid-template-columns:repeat(auto-fit,minmax(260px,1fr));
            gap:24px;
            margin-top:30px;
        }
        .card{
            background:white;
            padding:26px;
            border-radius:14px;
            border:1px solid var(--border);
            text-decoration:none;
            color:inherit;
            transition:.2s;
        }
        .card:hover{
            box-shadow:0 6px 20px rgba(0,0,0,.08);
            transform:translateY(-2px);
        }
        .card h3{
            margin-bottom:12px;
            font-size:20px;
            font-weight:600;
        }
        .card p{
            font-size:14px;
            color:#555;
        }
    </style>
</head>

<body>

<div class="dashboard-container">

    <?php include "sidebar.php"; ?>

    <main class="main-content">
        <h2>Welcome back, <?= esc($ADMIN['name']) ?> 👋</h2>
        <p>Manage your journal portal content</p>

        <div class="cards-grid">

            <a href="subjects_list.php" class="card">
                <h3>Manage Subjects</h3>
                <p>View / Edit / Delete subjects</p>
            </a>

            <a href="add_subject.php" class="card">
                <h3>Add Subject</h3>
                <p>Create a new subject</p>
            </a>

            <a href="upload_issue.php" class="card">
                <h3>Upload Issue</h3>
                <p>Create a new journal issue</p>
            </a>

            <a href="issues_list.php" class="card">
                <h3>Manage Issues</h3>
                <p>View & manage all issues</p>
            </a>

            <a href="upload_article.php" class="card">
                <h3>Upload Article</h3>
                <p>Add an article to an issue</p>
            </a>

            <a href="articles_list.php" class="card">
                <h3>Manage Articles</h3>
                <p>Edit or delete articles</p>
            </a>

            <a href="pricing.php" class="card">
                <h3>Pricing</h3>
                <p>Manage journal pricing</p>
            </a>

        </div>
    </main>

</div>

</body>
</html>
