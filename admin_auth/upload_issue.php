<?php
require '../config.php';
$ADMIN = require_admin($DB);

// Get subjects for dropdown
$subjects = $DB->query("SELECT id, name FROM subjects ORDER BY name ASC")->fetchAll();

$err = $ok = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $subject_id   = intval($_POST['subject_id']);
    $title        = trim($_POST['title']);
    $price        = floatval($_POST['price']);
    $published_at = $_POST['published_at'];
    $file         = $_FILES['issue_file'];

    if (!$subject_id || !$title || $price <= 0 || !$published_at) {
        $err = "All fields are required.";
    }

    // Handle PDF upload
    $issue_filename = null;

    if (!$err && $file['size'] > 0) {

        $mime = mime_content_type($file['tmp_name']);

        if ($mime !== "application/pdf") {
            $err = "Issue file must be a PDF.";
        } else {

            $ext = "pdf";
            $issue_filename = uniqid() . "." . $ext;

            $dir = __DIR__ . "/../uploads/issues/";

            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }

            $target = $dir . $issue_filename;

            if (!move_uploaded_file($file['tmp_name'], $target)) {
                $err = "Failed to upload file.";
            }
        }
    }

    if (!$err) {
        $stmt = $DB->prepare("
            INSERT INTO issues (subject_id, title, price, issue_file, published_at)
            VALUES (?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $subject_id,
            $title,
            $price,
            $issue_filename,
            $published_at
        ]);

        $ok = "Issue uploaded successfully!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Create Issue</title>

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

    .box{
        background:#fff;padding:40px;border-radius:16px;border:1px solid var(--border);max-width:700px;
    }
    label{font-weight:600;display:block;margin-top:18px;}
    input,select{
        width:100%;padding:12px;margin-top:6px;border-radius:8px;border:1px solid var(--border);
    }
    button{
        margin-top:30px;width:100%;padding:14px;background:var(--primary-blue);
        color:#fff;border:none;border-radius:10px;font-weight:600;
    }
    .err{background:#fee;padding:12px;border-radius:8px;color:#b00;margin-bottom:20px;}
    .ok{background:#d1fae5;padding:12px;border-radius:8px;color:#065f46;margin-bottom:20px;}
</style>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>

<div class="dashboard-container">

    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <h2>Create Issue</h2>

        <div class="box">

            <?php if ($err): ?>
                <div class="err"><?= esc($err) ?></div>
            <?php endif; ?>

            <?php if ($ok): ?>
                <div class="ok"><?= esc($ok) ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">

                <label>Subject *</label>
                <select name="subject_id" required>
                    <option value="">-- Select Subject --</option>
                    <?php foreach ($subjects as $s): ?>
                        <option value="<?= $s['id'] ?>"><?= esc($s['name']) ?></option>
                    <?php endforeach; ?>
                </select>

                <label>Issue Title *</label>
                <input type="text" name="title" required>

                <label>Price *</label>
                <input type="number" name="price" step="0.01" required>

                <label>Published At *</label>
                <input type="date" name="published_at" required>

                <label>Issue PDF *</label>
                <input type="file" name="issue_file" accept="application/pdf" required>

                <button>Upload Issue</button>
            </form>

        </div>
    </main>
</div>

</body>
</html>
