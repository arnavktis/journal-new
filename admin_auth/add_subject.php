<?php
require '../config.php';
$ADMIN = require_admin($DB);

if($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name']);
    $file = $_FILES['cover_image'];

    if($name === '') die("Name required");

    $filename = null;

    if($file['size'] > 0) {
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid().".".$ext;
$target = __DIR__ . "/../uploads/subjects/" . $filename;

if (!move_uploaded_file($file['tmp_name'], $target)) {
    die("UPLOAD FAILED ❌<br>Target: $target");
}

    }

    $stmt = $DB->prepare("INSERT INTO subjects (name, cover_image) VALUES (?,?)");
    $stmt->execute([$name, $filename]);

    header("Location: subjects_list.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Subject</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-blue:#002147;--secondary-blue:#003366;
            --gold:#D4AF37;--dark:#1f2937;--light:#6b7280;
            --white:#fff;--bg:#f8fafc;--border:#e5e7eb;
        }

        body{font-family:Poppins,sans-serif;margin:0;background:var(--bg);color:var(--dark)}
        .dashboard-container{display:flex}

        .sidebar{width:280px;background:linear-gradient(180deg,var(--primary-blue),var(--secondary-blue));color:#fff;position:fixed;height:100vh;}
        .sidebar-header{padding:32px 24px;border-bottom:1px solid rgba(255,255,255,.1)}
        .admin-info{padding:20px 24px;background:rgba(255,255,255,.1);display:flex;gap:12px}
        .admin-avatar{width:48px;height:48px;border-radius:50%;background:linear-gradient(135deg,var(--gold),#f59e0b);
            display:flex;align-items:center;justify-content:center;font-weight:600}
        .nav-menu a{display:flex;gap:16px;padding:14px 24px;color:#fff;text-decoration:none}
        .nav-menu a:hover{background:rgba(255,255,255,.1)}

        .main-content{margin-left:280px;flex:1;padding:40px}

        .form-card{
            background:#fff;
            padding:30px;
            border-radius:16px;
            border:1px solid var(--border);
            max-width:600px;
        }

        label{display:block;margin-bottom:8px;font-weight:500}
        input[type=text],input[type=file]{
            width:100%;
            padding:12px;
            border:1px solid var(--border);
            border-radius:8px;
            margin-bottom:20px;
        }

        .btn{
            display:inline-block;
            background:var(--primary-blue);
            color:#fff;
            padding:12px 24px;
            border-radius:8px;
            border:none;
            cursor:pointer;
            font-weight:500;
        }
        .btn:hover{background:var(--secondary-blue)}
    </style>
</head>

<body>

<div class="dashboard-container">
    
    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <h2>Add Subject</h2>
        <p>Create a new journal subject</p>

        <form method="POST" enctype="multipart/form-data" class="form-card">

            <label>Name</label>
            <input type="text" name="name" required>

            <label>Cover Image</label>
            <input type="file" name="cover_image" accept="image/*">

            <button class="btn">Save</button>
        </form>
    </main>

</div>

</body>
</html>