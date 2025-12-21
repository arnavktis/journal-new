<?php
session_start();
require_once 'config.php';

$issue_id = intval($_GET['issue_id'] ?? 0);
if (!$issue_id) die("Invalid issue");

$stmt = $DB->prepare("SELECT id, slug FROM issues WHERE id = ? LIMIT 1");
$stmt->execute([$issue_id]);
$issue = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$issue) die("Issue not found");

$_SESSION['paid_issues'][$issue_id] = true;

/* redirect WITH slug */
header("Location: issue.php?slug=" . urlencode($issue['slug']));
exit;
