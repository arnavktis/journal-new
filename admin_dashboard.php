<?php
require_once 'auth.php';

// Require admin authentication
$admin = AdminAuth::require_admin();

// Get database connection
try {
    $pdo = new PDO(
        'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4',
        DB_USER, DB_PASS,
        [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]
    );
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

// Handle POST actions (update submission status, add notes, etc.)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $submission_id = intval($_POST['submission_id'] ?? 0);
    
    switch ($action) {
        case 'update_status':
            $status = $_POST['status'] ?? '';
            $reviewer_notes = $_POST['reviewer_notes'] ?? '';
            $admin_notes = $_POST['admin_notes'] ?? '';
            
            $stmt = $pdo->prepare("
                UPDATE submissions 
                SET status = ?, reviewer_notes = ?, admin_notes = ?, reviewed_by = ?, reviewed_at = NOW() 
                WHERE id = ?
            ");
            $stmt->execute([$status, $reviewer_notes, $admin_notes, $admin['id'], $submission_id]);
            
            $message = "Submission #{$submission_id} status updated successfully.";
            break;
            
        case 'delete_submission':
            if (AdminAuth::has_role('admin')) {
                $stmt = $pdo->prepare("DELETE FROM submissions WHERE id = ?");
                $stmt->execute([$submission_id]);
                $message = "Submission #{$submission_id} deleted successfully.";
            } else {
                $error = "You don't have permission to delete submissions.";
            }
            break;
    }
}

// Get filter parameters
$status_filter = $_GET['status'] ?? 'all';
$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'created_at';
$order = $_GET['order'] ?? 'DESC';
$page = max(1, intval($_GET['page'] ?? 1));
$per_page = 20;
$offset = ($page - 1) * $per_page;

// Build WHERE clause
$where_conditions = [];
$params = [];

if ($status_filter !== 'all') {
    $where_conditions[] = "status = ?";
    $params[] = $status_filter;
}

if (!empty($search)) {
    $where_conditions[] = "(fullname LIKE ? OR email LIKE ? OR subject LIKE ? OR article_synopsis LIKE ?)";
    $search_param = "%{$search}%";
    $params = array_merge($params, [$search_param, $search_param, $search_param, $search_param]);
}

$where_clause = empty($where_conditions) ? "" : "WHERE " . implode(" AND ", $where_conditions);

// Get submissions with pagination
$valid_sorts = ['id', 'fullname', 'email', 'subject', 'status', 'created_at', 'reviewed_at'];
$sort = in_array($sort, $valid_sorts) ? $sort : 'created_at';
$order = strtoupper($order) === 'ASC' ? 'ASC' : 'DESC';

$stmt = $pdo->prepare("
    SELECT s.*, au.full_name as reviewer_name 
    FROM submissions s 
    LEFT JOIN admin_users au ON s.reviewed_by = au.id 
    {$where_clause} 
    ORDER BY {$sort} {$order} 
    LIMIT {$per_page} OFFSET {$offset}
");
$stmt->execute($params);
$submissions = $stmt->fetchAll();

// Get total count for pagination
$count_stmt = $pdo->prepare("SELECT COUNT(*) as total FROM submissions {$where_clause}");
$count_stmt->execute($params);
$total_submissions = $count_stmt->fetch()['total'];
$total_pages = ceil($total_submissions / $per_page);

// Get dashboard statistics
$stats_stmt = $pdo->query("SELECT * FROM admin_dashboard_stats");
$stats = $stats_stmt->fetch();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - The Continuum Journal</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="admin-styles.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: #f8fafc;
            line-height: 1.6;
        }
        
        .header {
            background: white;
            border-bottom: 1px solid #e2e8f0;
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header h1 {
            color: #2d3748;
            font-size: 1.5rem;
            font-weight: 600;
        }
        
        .user-menu {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .user-info {
            text-align: right;
        }
        
        .user-name {
            font-weight: 500;
            color: #2d3748;
        }
        
        .user-role {
            font-size: 0.8rem;
            color: #718096;
            text-transform: capitalize;
        }
        
        .logout-btn {
            background: #e53e3e;
            color: white;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.9rem;
            transition: background-color 0.2s;
        }
        
        .logout-btn:hover {
            background: #c53030;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border-left: 4px solid #667eea;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: #2d3748;
        }
        
        .stat-label {
            color: #718096;
            font-size: 0.9rem;
            margin-top: 0.5rem;
        }
        
        .filters {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 1.5rem;
        }
        
        .filters-row {
            display: flex;
            gap: 1rem;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .filter-group {
            display: flex;
            flex-direction: column;
            min-width: 150px;
        }
        
        .filter-group label {
            font-size: 0.8rem;
            font-weight: 500;
            color: #4a5568;
            margin-bottom: 0.25rem;
        }
        
        .filter-group select,
        .filter-group input {
            padding: 0.5rem;
            border: 1px solid #cbd5e0;
            border-radius: 4px;
            font-size: 0.9rem;
        }
        
        .submissions-table {
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .table-header {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: between;
            align-items: center;
        }
        
        .table-header h3 {
            color: #2d3748;
            font-weight: 600;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }
        
        th {
            background: #f7fafc;
            font-weight: 600;
            color: #4a5568;
            font-size: 0.9rem;
        }
        
        th.sortable {
            cursor: pointer;
            user-select: none;
        }
        
        th.sortable:hover {
            background: #edf2f7;
        }
        
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 500;
            text-transform: capitalize;
        }
        
        .status-pending { background: #fed7cc; color: #c53030; }
        .status-under_review { background: #bee3f8; color: #2b6cb0; }
        .status-accepted { background: #c6f6d5; color: #276749; }
        .status-rejected { background: #fed7d7; color: #c53030; }
        .status-published { background: #d6f5d6; color: #22543d; }
        
        .actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .btn {
            padding: 0.25rem 0.75rem;
            border: none;
            border-radius: 4px;
            font-size: 0.8rem;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        
        .btn-sm {
            padding: 0.2rem 0.5rem;
            font-size: 0.75rem;
        }
        
        .btn-primary { background: #667eea; color: white; }
        .btn-primary:hover { background: #5a67d8; }
        
        .btn-success { background: #48bb78; color: white; }
        .btn-success:hover { background: #38a169; }
        
        .btn-danger { background: #e53e3e; color: white; }
        .btn-danger:hover { background: #c53030; }
        
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
            margin-top: 1.5rem;
        }
        
        .pagination a,
        .pagination span {
            padding: 0.5rem 0.75rem;
            border: 1px solid #cbd5e0;
            color: #4a5568;
            text-decoration: none;
            border-radius: 4px;
        }
        
        .pagination a:hover {
            background: #edf2f7;
        }
        
        .pagination .current {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }
        
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }
        
        .modal-content {
            background: white;
            margin: 5% auto;
            padding: 2rem;
            border-radius: 8px;
            max-width: 600px;
            max-height: 80vh;
            overflow-y: auto;
        }
        
        .close {
            float: right;
            font-size: 1.5rem;
            cursor: pointer;
            color: #718096;
        }
        
        .close:hover {
            color: #2d3748;
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #4a5568;
        }
        
        .form-group select,
        .form-group textarea,
        .form-group input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #cbd5e0;
            border-radius: 4px;
            font-size: 1rem;
        }
        
        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }
        
        .alert {
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1rem;
        }
        
        .alert-success {
            background: #c6f6d5;
            color: #276749;
            border: 1px solid #9ae6b4;
        }
        
        .alert-error {
            background: #fed7d7;
            color: #c53030;
            border: 1px solid #feb2b2;
        }
        
        .manuscript-link {
            color: #667eea;
            text-decoration: none;
        }
        
        .manuscript-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <h1>Admin Dashboard</h1>
            <div class="user-menu">
                <div class="user-info">
                    <div class="user-name"><?php echo htmlspecialchars($admin['full_name']); ?></div>
                    <div class="user-role"><?php echo htmlspecialchars(str_replace('_', ' ', $admin['role'])); ?></div>
                </div>
                <a href="auth.php?action=logout" class="logout-btn">Logout</a>
            </div>
        </div>
    </header>

    <div class="container">
        <?php if (isset($message)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['total_submissions']; ?></div>
                <div class="stat-label">Total Submissions</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['pending_submissions']; ?></div>
                <div class="stat-label">Pending Review</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['under_review']; ?></div>
                <div class="stat-label">Under Review</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['accepted_submissions']; ?></div>
                <div class="stat-label">Accepted</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['published_submissions']; ?></div>
                <div class="stat-label">Published</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['today_submissions']; ?></div>
                <div class="stat-label">Today's Submissions</div>
            </div>
        </div>
        
        <!-- Filters -->
        <div class="filters">
            <form method="GET" action="">
                <div class="filters-row">
                    <div class="filter-group">
                        <label for="status">Status</label>
                        <select name="status" id="status">
                            <option value="all" <?php echo $status_filter === 'all' ? 'selected' : ''; ?>>All Status</option>
                            <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="under_review" <?php echo $status_filter === 'under_review' ? 'selected' : ''; ?>>Under Review</option>
                            <option value="accepted" <?php echo $status_filter === 'accepted' ? 'selected' : ''; ?>>Accepted</option>
                            <option value="rejected" <?php echo $status_filter === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                            <option value="published" <?php echo $status_filter === 'published' ? 'selected' : ''; ?>>Published</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="search">Search</label>
                        <input type="text" name="search" id="search" placeholder="Name, email, subject..." value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <div class="filter-group">
                        <label for="sort">Sort By</label>
                        <select name="sort" id="sort">
                            <option value="created_at" <?php echo $sort === 'created_at' ? 'selected' : ''; ?>>Date Submitted</option>
                            <option value="fullname" <?php echo $sort === 'fullname' ? 'selected' : ''; ?>>Author Name</option>
                            <option value="subject" <?php echo $sort === 'subject' ? 'selected' : ''; ?>>Subject</option>
                            <option value="status" <?php echo $sort === 'status' ? 'selected' : ''; ?>>Status</option>
                            <option value="reviewed_at" <?php echo $sort === 'reviewed_at' ? 'selected' : ''; ?>>Review Date</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="order">Order</label>
                        <select name="order" id="order">
                            <option value="DESC" <?php echo $order === 'DESC' ? 'selected' : ''; ?>>Newest First</option>
                            <option value="ASC" <?php echo $order === 'ASC' ? 'selected' : ''; ?>>Oldest First</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Submissions Table -->
        <div class="submissions-table">
            <div class="table-header">
                <h3>Submissions (<?php echo $total_submissions; ?> total)</h3>
            </div>
            
            <?php if (empty($submissions)): ?>
                <div style="padding: 2rem; text-align: center; color: #718096;">
                    No submissions found matching your criteria.
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Author</th>
                            <th>Email</th>
                            <th>Subject</th>
                            <th>Status</th>
                            <th>Submitted</th>
                            <th>Reviewed By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($submissions as $sub): ?>
                            <tr>
                                <td><?php echo $sub['id']; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($sub['fullname']); ?></strong>
                                    <?php if (!empty($sub['affiliation'])): ?>
                                        <br><small style="color: #718096;"><?php echo htmlspecialchars($sub['affiliation']); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($sub['email']); ?></td>
                                <td>
                                    <?php echo htmlspecialchars($sub['subject']); ?>
                                    <?php if (!empty($sub['manuscript_file'])): ?>
                                        <br><a href="<?php echo rtrim(PUBLIC_BASE_URL, '/').'/'.htmlspecialchars($sub['manuscript_file']); ?>" 
                                               target="_blank" class="manuscript-link">📄 View Manuscript</a>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="status-badge status-<?php echo $sub['status']; ?>">
                                        <?php echo str_replace('_', ' ', $sub['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php echo date('M j, Y', strtotime($sub['created_at'])); ?>
                                    <br><small><?php echo date('H:i', strtotime($sub['created_at'])); ?></small>
                                </td>
                                <td>
                                    <?php if ($sub['reviewer_name']): ?>
                                        <?php echo htmlspecialchars($sub['reviewer_name']); ?>
                                        <br><small><?php echo date('M j, Y', strtotime($sub['reviewed_at'])); ?></small>
                                    <?php else: ?>
                                        <span style="color: #718096;">Not reviewed</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="actions">
                                        <button onclick="editSubmission(<?php echo $sub['id']; ?>)" class="btn btn-sm btn-primary">
                                            Edit
                                        </button>
                                        <?php if (AdminAuth::has_role('admin')): ?>
                                            <button onclick="deleteSubmission(<?php echo $sub['id']; ?>)" class="btn btn-sm btn-danger">
                                                Delete
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        
        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>">Previous</a>
                <?php endif; ?>
                
                <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                    <?php if ($i == $page): ?>
                        <span class="current"><?php echo $i; ?></span>
                    <?php else: ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>"><?php echo $i; ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
                
                <?php if ($page < $total_pages): ?>
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>">Next</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Edit Submission Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h3>Edit Submission</h3>
            <form id="editForm" method="POST">
                <input type="hidden" name="action" value="update_status">
                <input type="hidden" name="submission_id" id="edit_submission_id">
                
                <div class="form-group">
                    <label for="edit_status">Status</label>
                    <select name="status" id="edit_status" required>
                        <option value="pending">Pending</option>
                        <option value="under_review">Under Review</option>
                        <option value="accepted">Accepted</option>
                        <option value="rejected">Rejected</option>
                        <option value="published">Published</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="edit_reviewer_notes">Reviewer Notes</label>
                    <textarea name="reviewer_notes" id="edit_reviewer_notes" placeholder="Notes for the author/reviewer..."></textarea>
                </div>
                
                <div class="form-group">
                    <label for="edit_admin_notes">Admin Notes (Internal)</label>
                    <textarea name="admin_notes" id="edit_admin_notes" placeholder="Internal notes for admin team..."></textarea>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Update Submission</button>
                    <button type="button" onclick="closeModal()" class="btn" style="background: #cbd5e0;">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Store submission data for editing
        const submissionData = <?php echo json_encode($submissions); ?>;
        
        function editSubmission(id) {
            const submission = submissionData.find(s => s.id == id);
            if (!submission) return;
            
            document.getElementById('edit_submission_id').value = id;
            document.getElementById('edit_status').value = submission.status;
            document.getElementById('edit_reviewer_notes').value = submission.reviewer_notes || '';
            document.getElementById('edit_admin_notes').value = submission.admin_notes || '';
            
            document.getElementById('editModal').style.display = 'block';
        }
        
        function deleteSubmission(id) {
            if (confirm('Are you sure you want to delete this submission? This action cannot be undone.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete_submission">
                    <input type="hidden" name="submission_id" value="${id}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        function closeModal() {
            document.getElementById('editModal').style.display = 'none';
        }
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('editModal');
            if (event.target == modal) {
                closeModal();
            }
        }
        
        // Auto-submit form when filters change
        document.getElementById('status').addEventListener('change', function() {
            this.form.submit();
        });
        
        document.getElementById('sort').addEventListener('change', function() {
            this.form.submit();
        });
        
        document.getElementById('order').addEventListener('change', function() {
            this.form.submit();
        });
    </script>
</body>
</html>