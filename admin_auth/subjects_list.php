<?php
require '../config.php';
$ADMIN = require_admin($DB);

$subjects = $DB->query("SELECT * FROM subjects ORDER BY id DESC")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Subjects</title>
    <link rel="stylesheet" href="admin_styles.css">
</head>

<style>
    :root {
        --primary-blue:#002147;--secondary-blue:#003366;--light-blue:#5d85b2;
        --gold:#D4AF37;--dark:#1f2937;--light:#6b7280;
        --white:#fff;--bg:#f8fafc;--border:#e5e7eb;
    }

    *{margin:0;padding:0;box-sizing:border-box}
    body{font-family:Poppins,sans-serif;background:var(--bg);color:var(--dark)}

    .dashboard-container{display:flex;min-height:100vh}

    /* SIDEBAR */
    .sidebar{
        width:280px;
        background:linear-gradient(180deg,var(--primary-blue),var(--secondary-blue));
        color:#fff;
        position:fixed;
        top:0;
        left:0;
        height:100vh;
        overflow-y:auto;
        padding-bottom:20px;
    }
    .sidebar-header{
        padding:32px 24px;
        border-bottom:1px solid rgba(255,255,255,.1);
    }
    .admin-info{
        padding:20px 24px;
        background:rgba(255,255,255,.1);
        display:flex;
        gap:12px;
    }
    .nav-menu a{
        display:flex;
        gap:16px;
        padding:14px 24px;
        color:#fff;
        text-decoration:none;
    }
    .nav-menu a:hover{background:rgba(255,255,255,.1)}

    /* SCROLLBAR */
    .sidebar::-webkit-scrollbar{width:6px;}
    .sidebar::-webkit-scrollbar-thumb{
        background:rgba(255,255,255,.3);
        border-radius:10px;
    }
    .sidebar::-webkit-scrollbar-track{
        background:rgba(255,255,255,.1);
    }

    /* CONTENT */
    .main-content{
        margin-left:280px;
        flex:1;
        padding:40px;
    }

    /* BUTTONS */
    .btn{
        background:var(--primary-blue);
        padding:10px 18px;
        border-radius:8px;
        color:#fff;
        text-decoration:none;
        font-weight:500;
    }
    .btn:hover{background:var(--secondary-blue)}

    .btn-small{
        background:#3b82f6;
        padding:6px 12px;
        border-radius:6px;
        color:#fff;
        font-size:14px;
        text-decoration:none;
    }
    .btn-danger-small{
        background:#dc2626;
        padding:6px 12px;
        border-radius:6px;
        color:#fff;
        font-size:14px;
        text-decoration:none;
    }

    /* TABLE */
    .styled-table{
        width:100%;
        border-collapse:collapse;
        background:#fff;
        border-radius:12px;
        overflow:hidden;
        border:1px solid var(--border);
    }
    .styled-table th, .styled-table td{
        padding:14px;
        border-bottom:1px solid var(--border);
    }
    .styled-table th{
        background:#f3f4f6;
        font-weight:600;
    }
</style>


<body>

<?php include 'sidebar.php'; ?>

<div class="main-content">
    <h2>Subjects</h2>
<br> <br>
    <a href="add_subject.php" class="btn">+ Add Subject</a>

    <table class="styled-table" style="margin-top:20px;">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Cover</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($subjects as $s): ?>
                <tr>
                    <td><?= $s['id'] ?></td>
                    <td><?= esc($s['name']) ?></td>
                    <td>
                        <?php if($s['cover_image']): ?>
                            <img src="../uploads/subjects/<?= esc($s['cover_image']) ?>" height="60">
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="edit_subject.php?id=<?= $s['id'] ?>" class="btn-small">Edit</a>
                        <a href="delete_subject.php?id=<?= $s['id'] ?>" class="btn-danger-small"
                            onclick="return confirm('Delete this subject?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</div>

</body>
</html>