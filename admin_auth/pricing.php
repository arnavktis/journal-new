<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/auth_check.php';

$err=''; $msg='';

// Add price
if($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add'){
    $item_type = in_array($_POST['item_type'], ['article','issue','subscription']) ? $_POST['item_type'] : 'subscription';
    $item_ref = !empty($_POST['item_ref']) ? intval($_POST['item_ref']): null;
    $price = number_format((float)($_POST['price'] ?? 0),2,'.','');
    $currency = strtoupper(substr($_POST['currency'] ?? 'INR',0,5));
    if($price <= 0) $err='invalid price';
    else {
        $stmt = $DB->prepare('INSERT INTO pricing (item_type,item_ref,price,currency) VALUES (?,?,?,?)');
        $stmt->execute([$item_type,$item_ref,$price,$currency]);
        $msg='Price added';
    }
}

// Update price
if($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'update'){
    $id = intval($_POST['id'] ?? 0);
    $price = number_format((float)($_POST['price'] ?? 0),2,'.','');
    $currency = strtoupper(substr($_POST['currency'] ?? 'INR',0,5));
    if($id && $price>0){
        $stmt = $DB->prepare('UPDATE pricing SET price=?,currency=? WHERE id=?');
        $stmt->execute([$price,$currency,$id]);
        $msg='Updated';
    } else $err='invalid';
}

// fetch list
$stmt = $DB->query('SELECT * FROM pricing ORDER BY updated_at DESC');
$prices = $stmt->fetchAll(PDO::FETCH_ASSOC);

// helper to list articles/issues for selection
$arts = $DB->query('SELECT id,title FROM articles ORDER BY uploaded_at DESC LIMIT 200')->fetchAll(PDO::FETCH_ASSOC);
$iss = $DB->query('SELECT id,title,volume,issue_no FROM issues ORDER BY created_at DESC LIMIT 200')->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html><html><head><meta charset="utf-8"><title>Pricing</title></head><body style="font-family:system-ui;padding:18px;">
<h2>Pricing</h2>
<?php if($err): ?><div style="color:red"><?=esc($err)?></div><?php endif; ?>
<?php if($msg): ?><div style="color:green"><?=esc($msg)?></div><?php endif; ?>

<h3>Add Price</h3>
<form method="post">
  <input type="hidden" name="action" value="add">
  <label>Type</label><br>
  <select name="item_type">
    <option value="article">Article</option>
    <option value="issue">Issue</option>
    <option value="subscription">Subscription</option>
  </select><br>
  <label>Item ref (article id / issue id) optional</label><br>
  <input name="item_ref" placeholder="id"><br>
  <label>Price</label><br><input name="price" required placeholder="0.00"><br>
  <label>Currency</label><br><input name="currency" value="INR"><br><br>
  <button>Add</button>
</form>

<h3>Existing</h3>
<table border="1" cellpadding="6" cellspacing="0">
<tr><th>ID</th><th>Type</th><th>Ref</th><th>Price</th><th>Currency</th><th>Updated</th><th>Action</th></tr>
<?php foreach($prices as $p): ?>
<tr>
  <td><?=esc($p['id'])?></td>
  <td><?=esc($p['item_type'])?></td>
  <td><?=esc($p['item_ref'])?></td>
  <td><?=esc($p['price'])?></td>
  <td><?=esc($p['currency'])?></td>
  <td><?=esc($p['updated_at'])?></td>
  <td>
    <form method="post" style="display:inline">
      <input type="hidden" name="action" value="update">
      <input type="hidden" name="id" value="<?=esc($p['id'])?>">
      <input name="price" value="<?=esc($p['price'])?>" style="width:80px">
      <input name="currency" value="<?=esc($p['currency'])?>" style="width:60px">
      <button>Save</button>
    </form>
  </td>
</tr>
<?php endforeach; ?>
</table>

<p><a href="admin_panel.php">Back</a></p>
</body></html>
