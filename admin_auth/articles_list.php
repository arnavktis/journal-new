<?php
require_once __DIR__ . '/../config.php';

$ADMIN = require_admin($DB);

/* Filters */
$filter_subject = trim($_GET['subject'] ?? '');
$filter_issue = intval($_GET['issue_id'] ?? 0);

/* Fetch subjects + issues */
$subjects = $DB->query("SELECT DISTINCT subject FROM articles ORDER BY subject")->fetchAll(PDO::FETCH_COLUMN);
$issues = $DB->query("SELECT id, subject, year, volume, issue_no FROM issues ORDER BY year DESC")->fetchAll(PDO::FETCH_ASSOC);

/* Query */
$sql = "
    SELECT a.*, 
        (SELECT GROUP_CONCAT(name ORDER BY aa.order_no SEPARATOR ', ') 
            FROM article_authors aa 
            JOIN authors au ON au.id = aa.author_id 
            WHERE aa.article_id = a.id
        ) AS authors,
        (SELECT CONCAT(subject,' - ',year,' - Vol ',volume,' Issue ',issue_no)
            FROM issues WHERE id = a.issue_id
        ) AS issue_name
    FROM articles a
    WHERE 1
";

$params = [];

if ($filter_subject !== '') {
    $sql .= " AND a.subject = ? ";
    $params[] = $filter_subject;
}

if ($filter_issue > 0) {
    $sql .= " AND a.issue_id = ? ";
    $params[] = $filter_issue;
}

$sql .= " ORDER BY a.id DESC";

$stmt = $DB->prepare($sql);
$stmt->execute($params);
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Manage Articles</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
body { font-family:'Poppins'; background:#f8fafc; padding:40px; }
.container { max-width:1100px; margin:auto; }
h1 { font-size:32px; margin-bottom:20px; }
.filter-row { display:flex; gap:16px; margin-bottom:24px; }
select {
    padding:12px; border-radius:8px; border:1px solid #ccc; 
    background:white; font-size:14px;
}
.table {
    width:100%; border-collapse:collapse; background:white; 
    border-radius:12px; overflow:hidden; box-shadow:0 4px 8px rgba(0,0,0,0.05);
}
.table th, .table td {
    padding:14px; border-bottom:1px solid #eee; font-size:14px;
}
.table th { background:#003366; color:white; text-align:left; }
.actions a {
    padding:6px 12px; border-radius:6px; text-decoration:none; font-size:13px; color:white;
}
.btn-edit { background:#3b82f6; }
.btn-delete { background:#ef4444; }
</style>

</head>
<body>
<div class="container">

<h1>Manage Articles</h1>

<!-- FILTERS -->
<form method="GET" class="filter-row">
    <select name="subject">
        <option value="">All Subjects</option>
        <?php foreach($subjects as $s): ?>
            <option value="<?=esc($s)?>" <?= $filter_subject === $s ? 'selected':'' ?>>
                <?=esc($s)?>
            </option>
        <?php endforeach; ?>
    </select>

    <select name="issue_id">
        <option value="">All Issues</option>
        <?php foreach($issues as $i): ?>
            <option value="<?=$i['id']?>" <?= $filter_issue == $i['id'] ? 'selected':'' ?>>
                <?=esc($i['subject'])?> — <?=$i['year']?> — Vol <?=$i['volume']?> Issue <?=$i['issue_no']?>
            </option>
        <?php endforeach; ?>
    </select>

    <button style="padding:12px 18px; background:#004080; color:white; border:none; border-radius:8px;">Apply</button>
</form>

<!-- TABLE -->
<table class="table">
<tr>
    <th>Title</th>
    <th>Authors</th>
    <th>Subject</th>
    <th>Issue</th>
    <th>File</th>
    <th>Actions</th>
</tr>

<?php if(!$articles): ?>
<tr><td colspan="6" style="text-align:center; padding:20px;">No articles found.</td></tr>
<?php endif; ?>

<?php foreach($articles as $a): ?>
<tr>
    <td><?=esc($a['title'])?></td>
    <td><?=esc($a['authors'] ?: "—")?></td>
    <td><?=esc($a['subject'])?></td>
    <td><?=esc($a['issue_name'])?></td>

    <td>
        <a href="<?=PUBLIC_BASE_URL.'/articles/'.$a['filename']?>" target="_blank">Download</a>
    </td>

    <td class="actions">
        <a class="btn-edit" href="edit_article.php?id=<?=$a['id']?>">Edit</a>
        <a class="btn-delete" href="delete_article.php?id=<?=$a['id']?>" onclick="return confirm('Delete article?')">Delete</a>
    </td>
</tr>
<?php endforeach; ?>

</table>

</div>
</body>
</html>