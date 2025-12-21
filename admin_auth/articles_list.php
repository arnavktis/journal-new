<?php
require '../config.php';
$ADMIN = require_admin($DB);

/* FILTERS */
$filter_subject = intval($_GET['subject_id'] ?? 0);
$filter_issue   = intval($_GET['issue_id'] ?? 0);

/* Fetch subjects */
$subjects = $DB->query("SELECT id, name FROM subjects ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

/* Fetch issues (with subject name) */
$issues = $DB->query("
    SELECT issues.id, issues.title, subjects.name AS subject_name
    FROM issues
    JOIN subjects ON subjects.id = issues.subject_id
    ORDER BY issues.id DESC
")->fetchAll(PDO::FETCH_ASSOC);

/* Build query */
$sql = "
SELECT 
    a.id,
    a.title,
    a.abstract,
    au.name AS author_name,
    s.name AS subject_name,
    i.title AS issue_title
FROM articles a
JOIN authors au ON au.id = a.author_id
JOIN issues i  ON i.id = a.issue_id
JOIN subjects s ON s.id = i.subject_id
WHERE 1
";

$params = [];

if ($filter_subject > 0) {
    $sql .= " AND s.id = ? ";
    $params[] = $filter_subject;
}

if ($filter_issue > 0) {
    $sql .= " AND i.id = ? ";
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

<style>
body { font-family:Poppins,sans-serif; background:#f8fafc; padding:40px; }
.container { max-width:1100px; margin:auto; }
h1 { margin-bottom:20px; }
.filter-row { display:flex; gap:16px; margin-bottom:24px; }
select {
    padding:12px; border-radius:8px;
    border:1px solid #ccc; background:white;
}
.table {
    width:100%; border-collapse:collapse;
    background:white; border-radius:12px;
    overflow:hidden; box-shadow:0 4px 8px rgba(0,0,0,0.05);
}
.table th, .table td {
    padding:14px; border-bottom:1px solid #eee;
}
.table th {
    background:#002147; color:white; text-align:left;
}
.actions a {
    padding:6px 12px; border-radius:6px;
    color:white; text-decoration:none; font-size:13px;
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
    <select name="subject_id">
        <option value="">All Subjects</option>
        <?php foreach($subjects as $s): ?>
            <option value="<?=$s['id']?>"
                <?=$filter_subject == $s['id'] ? 'selected':''?>>
                <?=esc($s['name'])?>
            </option>
        <?php endforeach; ?>
    </select>

    <select name="issue_id">
        <option value="">All Issues</option>
        <?php foreach($issues as $i): ?>
            <option value="<?=$i['id']?>"
                <?=$filter_issue == $i['id'] ? 'selected':''?>>
                <?=esc($i['subject_name'])?> — <?=esc($i['title'])?>
            </option>
        <?php endforeach; ?>
    </select>

    <button style="
        padding:12px 18px; background:#003366;
        color:white;border:none;border-radius:8px;">
        Apply
    </button>
</form>

<!-- TABLE -->
<table class="table">
<tr>
    <th>Title</th>
    <th>Author</th>
    <th>Subject</th>
    <th>Issue</th>
    <th>Actions</th>
</tr>

<?php if(!$articles): ?>
<tr><td colspan="5" style="padding:20px;text-align:center;">No articles found.</td></tr>
<?php endif; ?>

<?php foreach($articles as $a): ?>
<tr>
    <td><?=esc($a['title'])?></td>
    <td><?=esc($a['author_name'])?></td>
    <td><?=esc($a['subject_name'])?></td>
    <td><?=esc($a['issue_title'])?></td>

    <td class="actions">
        <a class="btn-edit" href="edit_article.php?id=<?=$a['id']?>">Edit</a>
        <a class="btn-delete" href="delete_article.php?id=<?=$a['id']?>"
           onclick="return confirm('Delete article?')"
        >Delete</a>
    </td>
</tr>
<?php endforeach; ?>

</table>
</div>
</body>
</html>
