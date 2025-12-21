<?php
require '../config.php';
$ADMIN = require_admin($DB);

$id = (int)($_GET['id'] ?? 0);
if ($id < 1) {
    header("Location: subjects_list.php");
    exit;
}

// check subject exists
$subject = $DB->query("SELECT * FROM subjects WHERE id = $id")->fetch();
if (!$subject) {
    header("Location: subjects_list.php");
    exit;
}

// delete cover image file
if (!empty($subject['cover_image'])) {
    $file = "../uploads/subjects/" . $subject['cover_image'];
    if (file_exists($file)) {
        unlink($file);
    }
}

// delete from DB
$DB->prepare("DELETE FROM subjects WHERE id = ?")->execute([$id]);

header("Location: subjects_list.php");
exit;
