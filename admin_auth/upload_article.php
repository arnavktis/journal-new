<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/auth_check.php';

$err = ''; $ok = '';
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    if(empty($_FILES['article'])) { $err='file required'; }
    else {
        $file = $_FILES['article'];
        if($file['error'] !== UPLOAD_ERR_OK){ $err='upload error'; }
        elseif($file['size'] > MAX_FILE_BYTES){ $err='file too large'; }
        else {
            // validate mime
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($file['tmp_name']);
            global $ALLOWED_MIME;
            if(!in_array($mime, $ALLOWED_MIME)){ $err='invalid file type'; }
            else {
                $title = trim($_POST['title'] ?? '');
                if($title === '') $title = pathinfo($file['name'], PATHINFO_FILENAME);
                // slug
                $slug = preg_replace('/[^a-z0-9]+/','-', strtolower(iconv('utf-8','ascii//TRANSLIT',$title)));
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $prefix = date('Ymd') . '_' . $slug;
                $unique = bin2hex(random_bytes(5));
                $filename = "{$prefix}_{$unique}.{$ext}";

                $destDir = dirname(__DIR__) . '/manuscripts/articles';
                if(!is_dir($destDir)) mkdir($destDir,0755,true);
                $destPath = $destDir . '/' . $filename;

                if(!move_uploaded_file($file['tmp_name'], $destPath)){ $err='move failed'; }
                else {
                    // save DB
                    $uploaded_by = $_SESSION['admin_name'] ?? 'admin';
                    $stmt = $DB->prepare('INSERT INTO articles (title,slug,filename,original_name,uploaded_by) VALUES (?,?,?,?,?)');
                    $stmt->execute([$title,$slug,$filename,$file['name'],$uploaded_by]);
                    $ok = 'Uploaded: ' . $filename;
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
    <title>Upload Article - Admin Portal</title>
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
        .container { max-width: 800px; margin: 0 auto; }
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
        .form-group { margin-bottom: 28px; }
        .form-group label {
            display: block;
            font-size: 15px;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 10px;
        }
        .form-group .helper-text {
            font-size: 13px;
            color: var(--text-light);
            margin-top: 6px;
        }
        .form-group input[type="text"] {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid var(--border-light);
            border-radius: 10px;
            font-size: 15px;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s ease;
            background: var(--bg-white);
        }
        .form-group input[type="text"]:focus {
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
            background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
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
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
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
            border-left: 4px solid var(--light-blue);
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
            <h1>Upload Article</h1>
            <p>Upload new research articles to your journal</p>
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
                        <?=esc($ok)?> — <a href="<?= PUBLIC_BASE_URL . '/articles/' . rawurlencode(basename($filename ?? '')) ?>" class="view-link" target="_blank">View uploaded file</a>
                    </div>
                </div>
            <?php endif; ?>
            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Article Title</label>
                    <input type="text" id="title" name="title" placeholder="Enter article title (optional - will use filename if empty)">
                    <p class="helper-text">If left empty, the filename will be used as the title</p>
                </div>
                <div class="form-group">
                    <label>Upload File</label>
                    <div class="file-upload-area" id="fileUploadArea">
                        <div class="file-upload-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M9 16h6v-6h4l-7-7-7 7h4zm-4 2h14v2H5z"/></svg>
                        </div>
                        <h3>Choose a file or drag it here</h3>
                        <p>Click to browse or drag and drop your file</p>
                        <input type="file" name="article" id="articleFile" required>
                        <p class="file-types">Supported formats: PDF, DOC, DOCX, ZIP, JPG, PNG</p>
                    </div>
                    <p class="helper-text" id="fileName"></p>
                </div>
                <button type="submit" class="btn-upload">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M9 16h6v-6h4l-7-7-7 7h4zm-4 2h14v2H5z"/></svg>
                    Upload Article
                </button>
            </form>
            <div class="info-box">
                <h4>📋 Upload Guidelines</h4>
                <ul>
                    <li>Maximum file size is determined by server settings</li>
                    <li>Accepted formats: PDF, DOC, DOCX, ZIP, JPG, PNG</li>
                    <li>Files are automatically renamed with date and unique identifier</li>
                    <li>Article metadata can be updated later if needed</li>
                </ul>
            </div>
        </div>
    </div>
    <script>
        const fileUploadArea = document.getElementById('fileUploadArea');
        const fileInput = document.getElementById('articleFile');
        const fileNameDisplay = document.getElementById('fileName');
        fileUploadArea.addEventListener('click', () => { fileInput.click(); });
        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                const file = e.target.files[0];
                fileNameDisplay.textContent = `Selected: ${file.name} (${formatFileSize(file.size)})`;
                fileNameDisplay.style.color = 'var(--primary-blue)';
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
                fileNameDisplay.style.color = 'var(--primary-blue)';
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
