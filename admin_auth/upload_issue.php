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
<!doctype html><html><head><meta charset="utf-8"><title>Upload Issue</title></head><body style="font-family:system-ui;padding:18px;">
<h2>Upload Issue (Large files allowed)</h2>
<?php if($err): ?><div style="color:red"><?=esc($err)?></div><?php endif; ?>
<?php if($ok): ?><div style="color:green"><?=esc($ok)?> — <a href="<?= PUBLIC_BASE_URL . '/issues/' . rawurlencode(basename($filename ?? '')) ?>">view</a></div><?php endif; ?>

<form method="post" enctype="multipart/form-data">
  <label>Title</label><br><input name="title" style="width:100%;padding:8px;margin:6px 0;"><br>
  <label>Volume</label><br><input name="volume" type="number" style="padding:6px;margin:6px 0;"><br>
  <label>Issue No</label><br><input name="issue_no" type="number" style="padding:6px;margin:6px 0;"><br>
  <label>Published date (optional)</label><br><input name="published_at" type="date" style="padding:6px;margin:6px 0;"><br>
  <input type="file" name="issue" required><br><br>
  <button>Upload Issue</button>
</form>

<p><a href="admin_panel.php">Back</a></p>
</body></html>
