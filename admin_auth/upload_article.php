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
<!doctype html><html><head><meta charset="utf-8"><title>Upload Article</title></head><body style="font-family:system-ui;padding:18px;">
<h2>Upload Article (PDF/DOC/DOCX/ZIP/JPG/PNG)</h2>
<?php if($err): ?><div style="color:red"><?=esc($err)?></div><?php endif; ?>
<?php if($ok): ?><div style="color:green"><?=esc($ok)?> — <a href="<?= PUBLIC_BASE_URL . '/articles/' . rawurlencode(basename($filename ?? '')) ?>">view</a></div><?php endif; ?>

<form method="post" enctype="multipart/form-data">
  <label>Title</label><br><input name="title" style="width:100%;padding:8px;margin:6px 0;"><br>
  <input type="file" name="article" required><br><br>
  <button>Upload</button>
</form>

<p><a href="admin_panel.php">Back</a></p>
</body></html>
