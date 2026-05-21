<?php
// ============================================
// products/index.php - Product List
// ============================================
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

$page_title = 'Products';
$conn = getConnection();

// ── Filters ──────────────────────────────────
$search      = sanitize($_GET['search'] ?? '');
$category_id = intval($_GET['category'] ?? 0);
$filter      = sanitize($_GET['filter'] ?? '');

// ── Build query ───────────────────────────────
$where  = ["1=1"];
$params = [];
$types  = '';

if ($search !== '') {
    $where[]  = "(p.name LIKE ? OR p.description LIKE ?)";
    $like     = "%$search%";
    $params[] = $like;
    $params[] = $like;
    $types   .= 'ss';
}

if ($category_id > 0) {
    $where[]  = "p.category_id = ?";
    $params[] = $category_id;
    $types   .= 'i';
}

if ($filter === 'low') {
    $where[] = "p.stock <= p.low_stock_threshold";
}
if ($filter === 'out') {
    $where[] = "p.stock = 0";
}

$where_sql = implode(' AND ', $where);

$sql = "SELECT p.*, c.name AS category_name
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE $where_sql
        ORDER BY p.created_at DESC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$products = $stmt->get_result();
$stmt->close();

// ── Categories for filter dropdown ───────────
$categories = $conn->query("SELECT id, name FROM categories ORDER BY name");
$conn->close();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h2>Products</h2>
    <a href="/products/add.php" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Add Product
    </a>
</div>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success mb-3"><i class="bi bi-check-circle me-2"></i><?= sanitize($_GET['msg']) ?></div>
<?php endif; ?>

<!-- Filter Bar -->
<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET" action="/products/index.php">
            <div class="filter-bar">
                <div class="search-box">
                    <i class="bi bi-search"></i>
                    <input type="text" name="search" class="form-control"
                           placeholder="Search products..." value="<?= $search ?>">
                </div>
                <select name="category" class="form-select" style="max-width:180px">
                    <option value="0">All Categories</option>
                    <?php $categories->data_seek(0); while ($cat = $categories->fetch_assoc()): ?>
                    <option value="<?= $cat['id'] ?>" <?= $category_id == $cat['id'] ? 'selected' : '' ?>>
                        <?= sanitize($cat['name']) ?>
                    </option>
                    <?php endwhile; ?>
                </select>
                <select name="filter" class="form-select" style="max-width:160px">
                    <option value="">All Stock</option>
                    <option value="low" <?= $filter === 'low' ? 'selected' : '' ?>>Low Stock</option>
                    <option value="out" <?= $filter === 'out' ? 'selected' : '' ?>>Out of Stock</option>
                </select>
                <button type="submit" class="btn btn-primary"><i class="bi bi-funnel"></i> Filter</button>
                <a href="/products/index.php" class="btn btn-outline-secondary"><i class="bi bi-x"></i> Clear</a>
            </div>
        </form>
    </div>
</div>

<!-- Products Table -->
<div class="card">
    <div class="card-header">
        <span>
            All Products
            <?php if ($products->num_rows > 0): ?>
            <span class="badge bg-secondary ms-2"><?= $products->num_rows ?></span>
            <?php endif; ?>
        </span>
    </div>
    <div class="card-body p-0">
        <?php if ($products->num_rows > 0): ?>
        <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php $i = 1; while ($p = $products->fetch_assoc()):
                if ($p['stock'] == 0) {
                    $status_badge = 'badge-out-stock'; $status_label = 'Out of Stock';
                } elseif ($p['stock'] <= $p['low_stock_threshold']) {
                    $status_badge = 'badge-low-stock'; $status_label = 'Low Stock';
                } else {
                    $status_badge = 'badge-in-stock'; $status_label = 'In Stock';
                }
            ?>
            <tr class="<?= $p['stock'] == 0 ? 'out-stock-row' : ($p['stock'] <= $p['low_stock_threshold'] ? 'low-stock-row' : '') ?>">
                <td style="color:var(--text-muted); font-size:0.8rem;"><?= $i++ ?></td>
                <td>
                    <div style="font-weight:600;"><?= sanitize($p['name']) ?></div>
                    <?php if ($p['description']): ?>
                    <div style="font-size:0.75rem; color:var(--text-muted); max-width:220px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis">
                        <?= sanitize($p['description']) ?>
                    </div>
                    <?php endif; ?>
                </td>
                <td>
                    <span style="font-size:0.82rem; color:var(--text-muted);">
                        <?= sanitize($p['category_name'] ?? 'Uncategorized') ?>
                    </span>
                </td>
                <td style="font-weight:600;">₱<?= number_format($p['price'], 2) ?></td>
                <td style="font-weight:700; font-size:1rem;"><?= $p['stock'] ?></td>
                <td><span class="badge <?= $status_badge ?>"><?= $status_label ?></span></td>
                <td>
                    <div class="d-flex gap-1">
                        <a href="/products/edit.php?id=<?= $p['id'] ?>"
                           class="btn btn-sm btn-outline-secondary btn-icon" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <a href="/products/delete.php?id=<?= $p['id'] ?>"
                           class="btn btn-sm btn-danger btn-icon"
                           data-confirm="Delete '<?= sanitize($p['name']) ?>'? This cannot be undone."
                           title="Delete">
                            <i class="bi bi-trash"></i>
                        </a>
                    </div>
                </td>
            </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
        </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="bi bi-box2"></i>
                <p>No products found. <a href="/products/add.php" style="color:var(--brand)">Add your first product!</a></p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
