<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/auth_check.php';

$err=''; $ok='';
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    if(empty($_FILES['issue'])) { $err='file required'; }
    else {
        $file = $_FILES['issue'];
        if($file['error'] !== UPLOAD_ERR_OK){ $err='upload error'; }
        elseif($file['size'] > 100 * 1024 * 1024){ $err='file too large (limit 100MB)'; }
        else {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($file['tmp_name']);
            global $ALLOWED_MIME;
            if(!in_array($mime, $ALLOWED_MIME)){ $err='invalid file type'; }
            else {
                $vol = intval($_POST['volume'] ?? 0);
                $ino = intval($_POST['issue_no'] ?? 0);
                $title = trim($_POST['title'] ?? "Vol{$vol}_Issue{$ino}");
                $slug = preg_replace('/[^a-z0-9]+/','-',strtolower(iconv('utf-8','ascii//TRANSLIT',$title)));
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $filename = date('Ymd') . "_vol{$vol}_iss{$ino}_" . bin2hex(random_bytes(5)) . ".{$ext}";

                $destDir = dirname(__DIR__) . '/manuscripts/issues';
                if(!is_dir($destDir)) mkdir($destDir,0755,true);
                $destPath = $destDir . '/' . $filename;

                if(!move_uploaded_file($file['tmp_name'], $destPath)){ $err='move failed'; }
                else {
                    $uploaded_by = $_SESSION['admin_name'] ?? 'admin';
                    $published_at = !empty($_POST['published_at']) ? $_POST['published_at'] : null;
                    $stmt = $DB->prepare('INSERT INTO issues (title,volume,issue_no,slug,filename,original_name,published_at,uploaded_by) VALUES (?,?,?,?,?,?,?,?)');
                    $stmt->execute([$title,$vol,$ino,$slug,$filename,$file['name'],$published_at,$uploaded_by]);
                    $ok = 'Issue uploaded: ' . $filename;
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Issue - Admin Portal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #002147;
            --secondary-blue: #003366;
            --light-blue: #5d85b2;
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
            padding: 40px 20px;
        }
        .container { max-width: 900px; margin: 0 auto; }
        .page-header { margin-bottom: 40px; }
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--light-blue);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 20px;
            transition: gap 0.3s ease;
        }
        .back-link:hover { gap: 12px; }
        .back-link svg { width: 16px; height: 16px; fill: currentColor; }
        .page-header h1 {
            font-size: 32px;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 8px;
        }
        .page-header p { font-size: 16px; color: var(--text-light); }
        .upload-card {
            background: var(--bg-white);
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            border: 1px solid var(--border-light);
        }
        .alert {
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 32px;
            font-size: 14px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }
        .alert-error {
            background-color: #fee;
            color: #c00;
            border: 1px solid #fcc;
        }
        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
        }
        .alert svg { width: 20px; height: 20px; flex-shrink: 0; margin-top: 2px; }
        .alert-content { flex: 1; }
        .alert-content strong { display: block; margin-bottom: 4px; }
        .view-link { color: inherit; text-decoration: underline; font-weight: 500; }
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 24px;
            margin-bottom: 28px;
        }
        .form-group { margin-bottom: 28px; }
        .form-group label {
            display: block;
            font-size: 15px;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 10px;
        }
        .form-group .optional-badge {
            font-size: 12px;
            font-weight: 400;
            color: #9ca3af;
            margin-left: 6px;
        }
        .form-group .helper-text {
            font-size: 13px;
            color: var(--text-light);
            margin-top: 6px;
        }
        .form-group input {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid var(--border-light);
            border-radius: 10px;
            font-size: 15px;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s ease;
            background: var(--bg-white);
        }
        .form-group input:focus {
            outline: none;
            border-color: var(--light-blue);
            box-shadow: 0 0 0 4px rgba(93, 133, 178, 0.1);
        }
        .file-upload-area {
            border: 2px dashed var(--border-light);
            border-radius: 12px;
            padding: 40px;
            text-align: center;
            background: var(--bg-light);
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .file-upload-area:hover {
            border-color: var(--light-blue);
            background: rgba(93, 133, 178, 0.05);
        }
        .file-upload-area.drag-over {
            border-color: var(--light-blue);
            background: rgba(93, 133, 178, 0.1);
        }
        .file-upload-icon {
            width: 64px;
            height: 64px;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, #7c3aed, #a855f7);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .file-upload-icon svg { width: 32px; height: 32px; fill: white; }
        .file-upload-area h3 {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 8px;
        }
        .file-upload-area p {
            font-size: 14px;
            color: var(--text-light);
            margin-bottom: 16px;
        }
        .file-upload-area input[type="file"] { display: none; }
        .file-types { font-size: 12px; color: #9ca3af; margin-top: 12px; }
        .btn-upload {
            width: 100%;
            padding: 16px 24px;
            background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .btn-upload:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        .btn-upload:active { transform: translateY(0); }
        .btn-upload svg { width: 20px; height: 20px; fill: currentColor; }
        .info-box {
            background: var(--bg-light);
            border-left: 4px solid #a855f7;
            padding: 20px;
            border-radius: 8px;
            margin-top: 32px;
        }
        .info-box h4 {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 12px;
        }
        .info-box ul {
            margin-left: 20px;
            font-size: 13px;
            color: #374151;
            line-height: 1.8;
        }
        @media (max-width: 640px) {
            body { padding: 24px 16px; }
            .upload-card { padding: 24px; }
            .page-header h1 { font-size: 24px; }
            .file-upload-area { padding: 32px 20px; }
            .form-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="page-header">
            <a href="admin_panel.php" class="back-link">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg>
                Back to Dashboard
            </a>
            <h1>Upload Journal Issue</h1>
            <p>Publish new journal issues with comprehensive metadata</p>
        </div>
        <div class="upload-card">
            <?php if($err): ?>
                <div class="alert alert-error">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                    <div class="alert-content">
                        <strong>Error</strong>
                        <?=esc($err)?>
                    </div>
                </div>
            <?php endif; ?>
            <?php if($ok): ?>
                <div class="alert alert-success">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                    <div class="alert-content">
                        <strong>Success!</strong>
                        <?=esc($ok)?> — <a href="<?= PUBLIC_BASE_URL . '/issues/' . rawurlencode(basename($filename ?? '')) ?>" class="view-link" target="_blank">View uploaded file</a>
                    </div>
                </div>
            <?php endif; ?>
            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Issue Title <span class="optional-badge">(optional)</span></label>
                    <input type="text" id="title" name="title" placeholder="e.g., Special Edition on AI Research">
                    <p class="helper-text">If left empty, title will be auto-generated as "Vol{X}_Issue{Y}"</p>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="volume">Volume Number *</label>
                        <input type="number" id="volume" name="volume" placeholder="e.g., 5" min="1" required>
                    </div>
                    <div class="form-group">
                        <label for="issue_no">Issue Number *</label>
                        <input type="number" id="issue_no" name="issue_no" placeholder="e.g., 2" min="1" required>
                    </div>
                    <div class="form-group">
                        <label for="published_at">Publication Date <span class="optional-badge">(optional)</span></label>
                        <input type="date" id="published_at" name="published_at">
                    </div>
                </div>
                <div class="form-group">
                    <label>Upload Issue File *</label>
                    <div class="file-upload-area" id="fileUploadArea">
                        <div class="file-upload-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14z"/><path d="M7 10h2v7H7zm4-3h2v10h-2zm4 6h2v4h-2z"/></svg>
                        </div>
                        <h3>Choose issue file or drag it here</h3>
                        <p>Large files are supported (up to 100MB)</p>
                        <input type="file" name="issue" id="issueFile" required>
                        <p class="file-types">Supported formats: PDF, DOC, DOCX, ZIP, JPG, PNG • Max size: 100MB</p>
                    </div>
                    <p class="helper-text" id="fileName"></p>
                </div>
                <button type="submit" class="btn-upload">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
                    Publish Issue
                </button>
            </form>
            <div class="info-box">
                <h4>📚 Issue Upload Guidelines</h4>
                <ul>
                    <li>Maximum file size: 100MB for large journal issues</li>
                    <li>Accepted formats: PDF, DOC, DOCX, ZIP, JPG, PNG</li>
                    <li>Volume and issue numbers are required for proper cataloging</li>
                    <li>Files are automatically named with volume, issue, and date information</li>
                    <li>Publication date is optional - leave empty for draft issues</li>
                </ul>
            </div>
        </div>
    </div>
    <script>
        const fileUploadArea = document.getElementById('fileUploadArea');
        const fileInput = document.getElementById('issueFile');
        const fileNameDisplay = document.getElementById('fileName');
        fileUploadArea.addEventListener('click', () => { fileInput.click(); });
        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                const file = e.target.files[0];
                fileNameDisplay.textContent = `Selected: ${file.name} (${formatFileSize(file.size)})`;
                fileNameDisplay.style.color = '#7c3aed';
                fileNameDisplay.style.fontWeight = '500';
            }
        });
        fileUploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            fileUploadArea.classList.add('drag-over');
        });
        fileUploadArea.addEventListener('dragleave', () => {
            fileUploadArea.classList.remove('drag-over');
        });
        fileUploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            fileUploadArea.classList.remove('drag-over');
            if (e.dataTransfer.files.length > 0) {
                fileInput.files = e.dataTransfer.files;
                const file = e.dataTransfer.files[0];
                fileNameDisplay.textContent = `Selected: ${file.name} (${formatFileSize(file.size)})`;
                fileNameDisplay.style.color = '#7c3aed';
                fileNameDisplay.style.fontWeight = '500';
            }
        });
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
        }
    </script>
</body>
</html>
