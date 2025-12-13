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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pricing Management - Admin Portal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #002147;
            --secondary-blue: #003366;
            --light-blue: #5d85b2;
            --text-dark: #1f2937;
            --text-light: #6b7280;
            --bg-white: #ffffff;
            --bg-light: #f8fafc;
            --bg-gray: #f3f4f6;
            --border-light: #e5e7eb;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: var(--bg-light);
            color: var(--text-dark);
            min-height: 100vh;
            padding: 40px 20px;
        }
        .container { max-width: 1200px; margin: 0 auto; }
        .page-header { margin-bottom: 40px; }
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--light-blue);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 20px;
            transition: gap 0.3s ease;
        }
        .back-link:hover { gap: 12px; }
        .back-link svg { width: 16px; height: 16px; fill: currentColor; }
        .page-header h1 {
            font-size: 32px;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 8px;
        }
        .page-header p { font-size: 16px; color: var(--text-light); }
        .alert {
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 32px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .alert-error {
            background-color: #fee;
            color: #c00;
            border: 1px solid #fcc;
        }
        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
        }
        .alert svg { width: 20px; height: 20px; flex-shrink: 0; }
        .grid-layout {
            display: grid;
            grid-template-columns: 400px 1fr;
            gap: 32px;
            align-items: start;
        }
        .card {
            background: var(--bg-white);
            border-radius: 16px;
            padding: 32px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            border: 1px solid var(--border-light);
        }
        .card h2 {
            font-size: 20px;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .card h2 svg { width: 24px; height: 24px; fill: var(--light-blue); }
        .form-group { margin-bottom: 20px; }
        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 8px;
        }
        .form-group select, .form-group input {
            width: 100%;
            padding: 12px 14px;
            border: 2px solid var(--border-light);
            border-radius: 10px;
            font-size: 14px;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s ease;
            background: var(--bg-white);
        }
        .form-group select:focus, .form-group input:focus {
            outline: none;
            border-color: var(--light-blue);
            box-shadow: 0 0 0 4px rgba(93, 133, 178, 0.1);
        }
        .btn-primary {
            width: 100%;
            padding: 14px 20px;
            background: linear-gradient(135deg, #059669, #10b981);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        .table-container { overflow-x: auto; }
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 14px;
        }
        thead { background: var(--bg-gray); }
        th {
            padding: 16px 12px;
            text-align: left;
            font-weight: 600;
            color: var(--text-dark);
            border-bottom: 2px solid var(--border-light);
        }
        th:first-child { border-top-left-radius: 10px; }
        th:last-child { border-top-right-radius: 10px; }
        td {
            padding: 16px 12px;
            border-bottom: 1px solid var(--border-light);
            color: #374151;
        }
        tr:last-child td { border-bottom: none; }
        tbody tr { transition: background-color 0.2s ease; }
        tbody tr:hover { background-color: var(--bg-light); }
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            text-transform: capitalize;
        }
        .badge-article { background: #dbeafe; color: #1e40af; }
        .badge-issue { background: #e9d5ff; color: #7c3aed; }
        .badge-subscription { background: #d1fae5; color: #065f46; }
        .inline-form {
            display: flex;
            gap: 8px;
            align-items: center;
        }
        .inline-form input {
            padding: 8px 10px;
            border: 2px solid var(--border-light);
            border-radius: 6px;
            font-size: 13px;
            font-family: 'Poppins', sans-serif;
        }
        .inline-form input:focus {
            outline: none;
            border-color: var(--light-blue);
        }
        .inline-form button {
            padding: 8px 16px;
            background: var(--light-blue);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            white-space: nowrap;
            transition: background 0.3s ease;
        }
        .inline-form button:hover { background: var(--primary-blue); }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-light);
        }
        .empty-state svg {
            width: 80px;
            height: 80px;
            fill: #9ca3af;
            margin-bottom: 20px;
            opacity: 0.5;
        }
        .empty-state h3 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 8px;
        }
        .empty-state p { font-size: 14px; }
        @media (max-width: 1024px) {
            .grid-layout { grid-template-columns: 1fr; }
        }
        @media (max-width: 640px) {
            body { padding: 24px 16px; }
            .card { padding: 24px; }
            .page-header h1 { font-size: 24px; }
            table { font-size: 12px; }
            th, td { padding: 10px 8px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="page-header">
            <a href="admin_panel.php" class="back-link">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg>
                Back to Dashboard
            </a>
            <h1>Pricing Management</h1>
            <p>Set and manage prices for articles, issues, and subscriptions</p>
        </div>
        <?php if($err): ?>
            <div class="alert alert-error">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                <?=esc($err)?>
            </div>
        <?php endif; ?>
        <?php if($msg): ?>
            <div class="alert alert-success">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                <?=esc($msg)?>
            </div>
        <?php endif; ?>
        <div class="grid-layout">
            <div class="card">
                <h2>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
                    Add New Price
                </h2>
                <form method="post">
                    <input type="hidden" name="action" value="add">
                    <div class="form-group">
                        <label for="item_type">Item Type</label>
                        <select name="item_type" id="item_type" required>
                            <option value="article">Article</option>
                            <option value="issue">Issue</option>
                            <option value="subscription">Subscription</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="item_ref">Item Reference (Optional)</label>
                        <input type="number" id="item_ref" name="item_ref" placeholder="Article or Issue ID">
                    </div>
                    <div class="form-group">
                        <label for="price">Price *</label>
                        <input type="number" id="price" name="price" step="0.01" min="0" placeholder="0.00" required>
                    </div>
                    <div class="form-group">
                        <label for="currency">Currency</label>
                        <input type="text" id="currency" name="currency" value="INR" maxlength="5" placeholder="INR">
                    </div>
                    <button type="submit" class="btn-primary">Add Price</button>
                </form>
            </div>
            <div class="card">
                <h2>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/></svg>
                    Existing Prices
                </h2>
                <?php if(empty($prices)): ?>
                    <div class="empty-state">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/></svg>
                        <h3>No prices yet</h3>
                        <p>Add your first price using the form</p>
                    </div>
                <?php else: ?>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Type</th>
                                    <th>Ref</th>
                                    <th>Price</th>
                                    <th>Currency</th>
                                    <th>Updated</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($prices as $p): ?>
                                <tr>
                                    <td><?=esc($p['id'])?></td>
                                    <td><span class="badge badge-<?=esc($p['item_type'])?>"><?=esc($p['item_type'])?></span></td>
                                    <td><?=esc($p['item_ref'] ?: '—')?></td>
                                    <td><strong><?=esc($p['price'])?></strong></td>
                                    <td><?=esc($p['currency'])?></td>
                                    <td><?=date('M d, Y', strtotime($p['updated_at']))?></td>
                                    <td>
                                        <form method="post" class="inline-form">
                                            <input type="hidden" name="action" value="update">
                                            <input type="hidden" name="id" value="<?=esc($p['id'])?>">
                                            <input name="price" value="<?=esc($p['price'])?>" style="width:80px" type="number" step="0.01" required>
                                            <input name="currency" value="<?=esc($p['currency'])?>" style="width:60px" maxlength="5" required>
                                            <button>Save</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
