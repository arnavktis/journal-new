<?php require_once __DIR__ . '/auth_check.php'; ?>
<!doctype html><html><body style="font-family:sans-serif;padding:20px;">
<h2>Welcome, <?= esc($_SESSION['admin_name']) ?></h2>

<ul>
  <li><a href="upload_article.php">Upload Article</a></li>
  <li><a href="upload_issue.php">Upload Issue</a></li>
  <li><a href="pricing.php">Pricing</a></li>
  <li><a href="logout.php">Logout</a></li>
</ul>

</body></html>
