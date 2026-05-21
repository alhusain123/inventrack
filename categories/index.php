<?php
// ============================================
// categories/index.php - Category List
// ============================================
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

$page_title = 'Categories';
$conn = getConnection();

$categories = $conn->query("
    SELECT c.*, COUNT(p.id) AS product_count
    FROM categories c
    LEFT JOIN products p ON p.category_id = c.id
    GROUP BY c.id
    ORDER BY c.name
");

$conn->close();
require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h2>Categories</h2>
    <a href="/categories/add.php" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Add Category
    </a>
</div>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success mb-3"><i class="bi bi-check-circle me-2"></i><?= sanitize($_GET['msg']) ?></div>
<?php endif; ?>
<?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger mb-3"><i class="bi bi-x-circle me-2"></i><?= sanitize($_GET['error']) ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header">All Categories</div>
    <div class="card-body p-0">
        <?php if ($categories->num_rows > 0): ?>
        <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Products</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php $i = 1; while ($cat = $categories->fetch_assoc()): ?>
            <tr>
                <td style="color:var(--text-muted); font-size:0.8rem;"><?= $i++ ?></td>
                <td style="font-weight:600;"><?= sanitize($cat['name']) ?></td>
                <td style="color:var(--text-muted); font-size:0.85rem; max-width:240px;">
                    <?= $cat['description'] ? sanitize($cat['description']) : '<em>No description</em>' ?>
                </td>
                <td>
                    <a href="/products/index.php?category=<?= $cat['id'] ?>"
                       style="color:var(--brand); font-weight:600;">
                        <?= $cat['product_count'] ?>
                    </a>
                </td>
                <td style="font-size:0.82rem; color:var(--text-muted);">
                    <?= date('M d, Y', strtotime($cat['created_at'])) ?>
                </td>
                <td>
                    <div class="d-flex gap-1">
                        <a href="/categories/edit.php?id=<?= $cat['id'] ?>"
                           class="btn btn-sm btn-outline-secondary btn-icon" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <?php if ($cat['product_count'] == 0): ?>
                        <a href="/categories/delete.php?id=<?= $cat['id'] ?>"
                           class="btn btn-sm btn-danger btn-icon"
                           data-confirm="Delete category '<?= sanitize($cat['name']) ?>'?"
                           title="Delete">
                            <i class="bi bi-trash"></i>
                        </a>
                        <?php else: ?>
                        <button class="btn btn-sm btn-danger btn-icon" disabled
                                title="Cannot delete: has <?= $cat['product_count'] ?> product(s)">
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
        <?php else: ?>
            <div class="empty-state">
                <i class="bi bi-tags"></i>
                <p>No categories yet. <a href="/categories/add.php" style="color:var(--brand)">Add one!</a></p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
