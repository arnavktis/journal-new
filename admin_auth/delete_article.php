<?php
require '../config.php';

$ADMIN = require_admin($DB);

$id = intval($_GET['id'] ?? 0);
if (!$id) {
    die("Invalid article ID.");
}

/* Fetch article */
$stmt = $DB->prepare("SELECT filename FROM articles WHERE id=?");
$stmt->execute([$id]);
$article = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$article) {
    die("Article not found.");
}

/* Delete file from disk */
if (!empty($article['filename'])) {
    $filePath = dirname(__DIR__) . "/uploads/articles/" . $article['filename'];
    if (is_file($filePath)) {
        unlink($filePath);
    }
}

/* Delete author mappings */
$DB->prepare("DELETE FROM article_authors WHERE article_id=?")
   ->execute([$id]);

/* Delete article record */
$DB->prepare("DELETE FROM articles WHERE id=?")
   ->execute([$id]);

header("Location: articles_list.php?deleted=1");
exit;
