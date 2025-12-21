<?php
require '../config.php';
$ADMIN = require_admin($DB);

// Fetch issues + join subjects + article count
$sql = "
SELECT 
    issues.*,
    subjects.name AS subject_name,
    (SELECT COUNT(*) FROM articles WHERE issue_id = issues.id) AS article_count
FROM issues
LEFT JOIN subjects ON subjects.id = issues.subject_id
ORDER BY issues.id DESC
";

$issues = $DB->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
<title>Manage Issues</title>

<link rel="preconnect" href="https://fonts.googleapis.com">

<style>
    :root {
        --primary-blue:#002147;--secondary-blue:#003366;--light-blue:#5d85b2;
        --gold:#D4AF37;--dark:#1f2937;--light:#6b7280;
        --white:#fff;--bg:#f8fafc;--border:#e5e7eb;
    }
    *{margin:0;padding:0;box-sizing:border-box}
    body{font-family:Poppins,sans-serif;background:var(--bg);color:var(--dark)}
    .dashboard-container{display:flex;min-height:100vh}

    .sidebar{
        width:280px;background:linear-gradient(180deg,var(--primary-blue),var(--secondary-blue));
        color:#fff;position:fixed;height:100vh;overflow-y:auto;padding-bottom:20px;
    }
    .sidebar-header{padding:32px 24px;border-bottom:1px solid rgba(255,255,255,.1)}
    .admin-info{padding:20px 24px;background:rgba(255,255,255,.1)}
    .nav-menu a{display:flex;gap:16px;padding:14px 24px;color:#fff;text-decoration:none}
    .nav-menu a:hover{background:rgba(255,255,255,.1)}

    .main-content{margin-left:280px;flex:1;padding:40px}

    table{
        width:100%;background:#fff;border-collapse:collapse;
        border:1px solid var(--border);border-radius:12px;overflow:hidden;
    }
    th,td{padding:14px;border-bottom:1px solid var(--border);font-size:14px}
    th{background:#002147;color:#fff;text-align:left;}

    .btn-edit{background:#3b82f6;color:white;padding:6px 12px;text-decoration:none;border-radius:6px;}
    .btn-delete{background:#ef4444;color:white;padding:6px 12px;text-decoration:none;border-radius:6px;}
</style>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

</head>

<body>

<div class="dashboard-container">

<?php include 'sidebar.php'; ?>

<main class="main-content">

    <h2>Manage Issues</h2>

    <table>
        <tr>
            <th>ID</th>
            <th>Subject</th>
            <th>Title</th>
            <th>Price</th>
            <th>Published</th>
            <th>File</th>
            <th>Articles</th>
            <th>Actions</th>
        </tr>

        <?php if(!$issues): ?>
        <tr>
            <td colspan="8" style="text-align:center;padding:20px;color:#777;">No issues found.</td>
        </tr>
        <?php endif; ?>

        <?php foreach($issues as $i): ?>
        <tr>
            <td><?= $i['id'] ?></td>
            <td><?= esc($i['subject_name']) ?></td>
            <td><?= esc($i['title']) ?></td>
            <td>₹<?= esc($i['price']) ?></td>
            <td><?= esc($i['published_at']) ?></td>

            <td>
                <?php if($i['issue_file']): ?>
                <a href="../uploads/issues/<?= esc($i['issue_file']) ?>" target="_blank">PDF</a>
                <?php else: ?>
                —
                <?php endif; ?>
            </td>

            <td><?= $i['article_count'] ?></td>

            <td>
                <a class="btn-edit" href="edit_issue.php?id=<?= $i['id'] ?>">Edit</a>
                <a class="btn-delete" href="delete_issue.php?id=<?= $i['id'] ?>"
                    onclick="return confirm('Delete this issue?');">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>

    </table>

</main>
</div>

</body>
</html>
