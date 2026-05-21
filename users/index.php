<?php
// ============================================
// users/index.php - User Management (Admin)
// ============================================
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

requireAdmin();
$page_title = 'User Management';
$conn = getConnection();

$users = $conn->query("SELECT id, username, full_name, role, created_at FROM users ORDER BY created_at DESC");
$conn->close();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h2>User Management</h2>
    <a href="/users/add.php" class="btn btn-primary">
        <i class="bi bi-person-plus"></i> Add User
    </a>
</div>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success mb-3"><i class="bi bi-check-circle me-2"></i><?= sanitize($_GET['msg']) ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header">All Users</div>
    <div class="card-body p-0">
        <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Full Name</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php $i = 1; $current = currentUser(); while ($u = $users->fetch_assoc()): ?>
            <tr>
                <td style="color:var(--text-muted); font-size:0.8rem;"><?= $i++ ?></td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <div class="user-avatar" style="width:30px;height:30px;font-size:0.75rem;">
                            <?= strtoupper(substr($u['full_name'], 0, 1)) ?>
                        </div>
                        <span style="font-weight:600;"><?= sanitize($u['full_name']) ?></span>
                        <?php if ($u['id'] == $current['id']): ?>
                            <span class="badge" style="background:rgba(59,130,246,0.15);color:var(--info);font-size:0.65rem;">You</span>
                        <?php endif; ?>
                    </div>
                </td>
                <td style="color:var(--text-muted);">@<?= sanitize($u['username']) ?></td>
                <td>
                    <span class="badge-role <?= $u['role'] ?>"><?= ucfirst($u['role']) ?></span>
                </td>
                <td style="font-size:0.82rem; color:var(--text-muted);">
                    <?= date('M d, Y', strtotime($u['created_at'])) ?>
                </td>
                <td>
                    <div class="d-flex gap-1">
                        <a href="/users/edit.php?id=<?= $u['id'] ?>"
                           class="btn btn-sm btn-outline-secondary btn-icon" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <?php if ($u['id'] != $current['id']): ?>
                        <a href="/users/delete.php?id=<?= $u['id'] ?>"
                           class="btn btn-sm btn-danger btn-icon"
                           data-confirm="Delete user '<?= sanitize($u['username']) ?>'?"
                           title="Delete">
                            <i class="bi bi-trash"></i>
                        </a>
                        <?php else: ?>
                        <button class="btn btn-sm btn-danger btn-icon" disabled title="Cannot delete yourself">
                            <i class="bi bi-trash"></i>
                        </button>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
