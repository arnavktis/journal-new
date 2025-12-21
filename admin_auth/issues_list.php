<?php
require_once __DIR__ . '/../config.php';

$ADMIN = require_admin($DB);


/* Fetch filters */
$filter_subject = trim($_GET['subject'] ?? '');
$filter_year = trim($_GET['year'] ?? '');

/* Fetch distinct subjects + years */
$subjects = $DB->query("SELECT DISTINCT subject FROM issues ORDER BY subject")->fetchAll(PDO::FETCH_COLUMN);
$years    = $DB->query("SELECT DISTINCT year FROM issues ORDER BY year DESC")->fetchAll(PDO::FETCH_COLUMN);

/* Build query */
$sql = "SELECT *, (SELECT COUNT(*) FROM articles WHERE issue_id = issues.id) AS article_count 
        FROM issues WHERE 1 ";

$params = [];

if ($filter_subject !== '') {
    $sql .= " AND subject = ? ";
    $params[] = $filter_subject;
}
if ($filter_year !== '') {
    $sql .= " AND year = ? ";
    $params[] = intval($filter_year);
}

$sql .= " ORDER BY year DESC, volume DESC, issue_no DESC";

$stmt = $DB->prepare($sql);
$stmt->execute($params);
$issues = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Manage Issues</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap">

<style>
body { font-family: 'Poppins', sans-serif; background:#f8fafc; padding:40px; }
.container { max-width:1100px; margin:auto; }
h1 { font-size:32px; margin-bottom:10px; }
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
.table th { background:#002147; color:white; text-align:left; }
.actions a {
    padding:6px 12px; border-radius:6px; text-decoration:none;
    font-size:13px; color:white;
}
.btn-edit { background:#3b82f6; }
.btn-delete { background:#ef4444; }
.no-data { padding:20px; text-align:center; font-size:16px; color:#777; }
</style>
</head>

<body>
<div class="container">

<h1>Manage Issues</h1>
<p>View, filter, edit, or delete journal issues.</p>

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

    <select name="year">
        <option value="">All Years</option>
        <?php foreach($years as $y): ?>
            <option value="<?=$y?>" <?= $filter_year == $y ? 'selected':'' ?>>
                <?=$y?>
            </option>
        <?php endforeach; ?>
    </select>

    <button style="
        padding:12px 18px; background:#004080; color:white;
        border:none; border-radius:8px; cursor:pointer;
    ">Apply</button>
</form>

<!-- TABLE -->
<table class="table">
<tr>
    <th>Subject</th>
    <th>Year</th>
    <th>Vol</th>
    <th>Issue</th>
    <th>Title</th>
    <th>Preview</th>
    <th>Full File</th>
    <th>Articles</th>
    <th>Actions</th>
</tr>

<?php if(!$issues): ?>
<tr><td colspan="9" class="no-data">No issues found.</td></tr>
<?php endif; ?>

<?php foreach($issues as $i): ?>
<tr>
    <td><?=esc($i['subject'])?></td>
    <td><?=esc($i['year'])?></td>
    <td><?=esc($i['volume'])?></td>
    <td><?=esc($i['issue_no'])?></td>
    <td><?=esc($i['title'])?></td>

    <td>
        <?php if($i['preview_filename']): ?>
            <a href="<?= PUBLIC_BASE_URL.'/previews/'.$i['preview_filename'] ?>" target="_blank">Preview</a>
        <?php else: ?>
            —
        <?php endif; ?>
    </td>

    <td>
        <a href="<?= PUBLIC_BASE_URL.'/issues/'.$i['filename'] ?>" target="_blank">Download</a>
    </td>

    <td><?= $i['article_count'] ?></td>

    <td class="actions">
        <a class="btn-edit" href="edit_issue.php?id=<?=$i['id']?>">Edit</a>
        <a class="btn-delete" href="delete_issue.php?id=<?=$i['id']?>" 
           onclick="return confirm('Delete this issue?');">Delete</a>
    </td>
</tr>
<?php endforeach; ?>

</table>

</div>
</body>
</html>