<?php
require_once __DIR__ . '/../config.php';

/* AUTH REQUIRED */
$ADMIN = require_admin($DB);

$id = intval($_GET['id'] ?? 0);
if (!$id) {
    die("Invalid issue ID.");
}

/* Check linked articles */
$stmt = $DB->prepare("SELECT COUNT(*) FROM articles WHERE issue_id=?");
$stmt->execute([$id]);
$count = (int)$stmt->fetchColumn();

if ($count > 0) {
    die("<h2>❌ Cannot delete issue. It has {$count} article(s). Delete them first.</h2>");
}

/* Fetch issue for file removal */
$stmt = $DB->prepare("SELECT issue_file FROM issues WHERE id=?");
$stmt->execute([$id]);
$issue = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$issue) {
    die("Issue not found.");
}

/* Delete PDF from folder */
if (!empty($issue['issue_file'])) {
    $filePath = __DIR__ . '/../uploads/issues/' . $issue['issue_file'];

    if (file_exists($filePath)) {
        unlink($filePath); // delete file
    }
}

/* Delete issue row */
$stmt = $DB->prepare("DELETE FROM issues WHERE id=?");
$stmt->execute([$id]);

header("Location: issues_list.php?deleted=1");
exit;
