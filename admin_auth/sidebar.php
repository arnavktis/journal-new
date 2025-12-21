<div class="sidebar">
    <div class="sidebar-header">
        <h1>Journal Portal</h1>
        <p>Admin Dashboard</p>
    </div>

    <div class="admin-info">
        <div>
            <h3><?= esc($ADMIN['name']) ?></h3>
            <p>Administrator</p>
        </div>
    </div>

    <nav class="nav-menu">

        <a href="admin_panel.php">Dashboard</a>

        <a href="subjects_list.php">Manage Subjects</a>
        <a href="add_subject.php">Add Subject</a>

        <a href="upload_issue.php">Upload Issue</a>
        <a href="issues_list.php">Manage Issues</a>

        <a href="upload_article.php">Upload Article</a>
        <a href="articles_list.php">Manage Articles</a>

        <a href="pricing.php">Pricing</a>

        <a href="logout.php" style="color:#fecaca">Logout</a>
    </nav>
</div>
