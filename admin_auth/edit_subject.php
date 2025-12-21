<?php
require '../config.php';
$ADMIN = require_admin($DB);

$id = (int)$_GET['id'];
$subject = $DB->query("SELECT * FROM subjects WHERE id=$id")->fetch();

if(!$subject) die("Invalid subject");

if($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name']);
    $file = $_FILES['cover_image'];

    $filename = $subject['cover_image']; // keep old image

    if($file['size'] > 0) {

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename = uniqid() . "." . $ext;

        $target = __DIR__ . "/../uploads/subjects/" . $filename;

        if(!move_uploaded_file($file['tmp_name'], $target)){
            die("UPLOAD FAILED ❌<br>Target: $target");
        }
    }

    $stmt = $DB->prepare("UPDATE subjects SET name=?, cover_image=? WHERE id=?");
    $stmt->execute([$name, $filename, $id]);

    header("Location: subjects_list.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Subject</title>

    <!-- SAME CSS AS ADD + LIST -->
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
        .admin-info{padding:20px 24px;background:rgba(255,255,255,.1);display:flex;gap:12px}

        .nav-menu a{display:flex;gap:16px;padding:14px 24px;color:#fff;text-decoration:none}
        .nav-menu a:hover{background:rgba(255,255,255,.1)}

        .main-content{margin-left:280px;flex:1;padding:40px}

        .form-card{
            background:#fff;padding:30px;border-radius:16px;border:1px solid var(--border);max-width:600px;
        }

        label{display:block;margin-bottom:8px;font-weight:500}
        input[type=text],input[type=file]{
            width:100%;padding:12px;border:1px solid var(--border);border-radius:8px;margin-bottom:20px;
        }

        .btn{
            background:var(--primary-blue);color:#fff;padding:12px 24px;border-radius:8px;border:none;cursor:pointer;font-weight:500;
        }
        .btn:hover{background:var(--secondary-blue)}
    </style>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>

<div class="dashboard-container">

    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <h2>Edit Subject</h2>

        <form method="POST" enctype="multipart/form-data" class="form-card">

            <label>Name</label>
            <input type="text" name="name" value="<?= esc($subject['name']) ?>" required>

            <label>Current Cover</label><br>
            <?php if($subject['cover_image']): ?>
                <img src="../uploads/subjects/<?= esc($subject['cover_image']) ?>" height="100">
            <?php endif; ?>

            <br><br>

            <label>New Cover (optional)</label>
            <input type="file" name="cover_image" accept="image/*">

            <button class="btn">Update</button>
        </form>
    </div>

</div>

</body>
</html>
