<?php
// ============================================
// dashboard.php - Main Dashboard
// ============================================
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

$page_title = 'Dashboard';
$conn = getConnection();

// ── Stats ────────────────────────────────────
$total_products  = $conn->query("SELECT COUNT(*) AS c FROM products")->fetch_assoc()['c'];
$total_categories= $conn->query("SELECT COUNT(*) AS c FROM categories")->fetch_assoc()['c'];
$total_stock     = $conn->query("SELECT SUM(stock) AS c FROM products")->fetch_assoc()['c'] ?? 0;
$low_stock_count = $conn->query("SELECT COUNT(*) AS c FROM products WHERE stock > 0 AND stock <= low_stock_threshold")->fetch_assoc()['c'];
$out_of_stock    = $conn->query("SELECT COUNT(*) AS c FROM products WHERE stock = 0")->fetch_assoc()['c'];
$total_value     = $conn->query("SELECT SUM(price * stock) AS c FROM products")->fetch_assoc()['c'] ?? 0;

// ── Low stock products ─────────────────────
$low_stock_products = $conn->query("
    SELECT p.*, c.name AS category_name
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    WHERE p.stock <= p.low_stock_threshold
    ORDER BY p.stock ASC
    LIMIT 8
");

// ── Recent products ────────────────────────
$recent_products = $conn->query("
    SELECT p.*, c.name AS category_name
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    ORDER BY p.created_at DESC
    LIMIT 5
");

$conn->close();

require_once __DIR__ . '/includes/header.php';
?>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success"><i class="bi bi-check-circle me-2"></i><?= sanitize($_GET['msg']) ?></div>
<?php endif; ?>
<?php if (isset($_GET['error']) && $_GET['error'] === 'unauthorized'): ?>
    <div class="alert alert-danger"><i class="bi bi-x-circle me-2"></i>Access denied. Admins only.</div>
<?php endif; ?>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon orange"><i class="bi bi-box2"></i></div>
            <div class="stat-info">
                <div class="stat-value" data-count="<?= $total_products ?>"><?= $total_products ?></div>
                <div class="stat-label">Total Products</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon green"><i class="bi bi-stack"></i></div>
            <div class="stat-info">
                <div class="stat-value" data-count="<?= $total_stock ?>"><?= number_format($total_stock) ?></div>
                <div class="stat-label">Units in Stock</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon yellow"><i class="bi bi-exclamation-triangle"></i></div>
            <div class="stat-info">
                <div class="stat-value" data-count="<?= $low_stock_count ?>"><?= $low_stock_count ?></div>
                <div class="stat-label">Low Stock Items</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon blue"><i class="bi bi-tags"></i></div>
            <div class="stat-info">
                <div class="stat-value" data-count="<?= $total_categories ?>"><?= $total_categories ?></div>
                <div class="stat-label">Categories</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-sm-6">
        <div class="stat-card">
            <div class="stat-icon red"><i class="bi bi-x-circle"></i></div>
            <div class="stat-info">
                <div class="stat-value" data-count="<?= $out_of_stock ?>"><?= $out_of_stock ?></div>
                <div class="stat-label">Out of Stock</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="stat-card">
            <div class="stat-icon green"><i class="bi bi-currency-dollar"></i></div>
            <div class="stat-info">
                <div class="stat-value">₱<?= number_format($total_value, 0) ?></div>
                <div class="stat-label">Inventory Value</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Low Stock Alerts -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span><i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>Stock Alerts</span>
                <a href="/products/index.php?filter=low" class="btn btn-sm btn-outline-secondary">View all</a>
            </div>
            <div class="card-body p-0">
                <?php if ($low_stock_products->num_rows > 0): ?>
                <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Stock</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php while ($p = $low_stock_products->fetch_assoc()):
                        $pct = $p['low_stock_threshold'] > 0
                            ? min(100, round(($p['stock'] / ($p['low_stock_threshold'] * 2)) * 100))
                            : 0;
                        $status_class = $p['stock'] == 0 ? 'out-stock' : 'low-stock';
                        $status_label = $p['stock'] == 0 ? 'Out of Stock' : 'Low Stock';
                        $fill_class   = $p['stock'] == 0 ? 'low' : 'medium';
                    ?>
                    <tr>
                        <td>
                            <div style="font-weight:600; font-size:0.88rem;"><?= sanitize($p['name']) ?></div>
                            <div style="font-size:0.75rem; color:var(--text-muted)"><?= sanitize($p['category_name'] ?? 'Uncategorized') ?></div>
                        </td>
                        <td>
                            <div style="font-weight:700; font-size:0.95rem;"><?= $p['stock'] ?></div>
                            <div class="stock-bar" style="width:80px">
                                <div class="stock-fill <?= $fill_class ?>" data-pct="<?= $pct ?>"></div>
                            </div>
                        </td>
                        <td><span class="badge badge-<?= $status_class ?>"><?= $status_label ?></span></td>
                    </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
                </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="bi bi-check-circle text-success"></i>
                        <p>All products are well-stocked!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Recently Added -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span><i class="bi bi-clock-history me-2"></i>Recently Added</span>
                <a href="/products/index.php" class="btn btn-sm btn-outline-secondary">View all</a>
            </div>
            <div class="card-body p-0">
                <?php if ($recent_products->num_rows > 0): ?>
                <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php while ($p = $recent_products->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <div style="font-weight:600; font-size:0.88rem;"><?= sanitize($p['name']) ?></div>
                            <div style="font-size:0.75rem; color:var(--text-muted)"><?= sanitize($p['category_name'] ?? 'Uncategorized') ?></div>
                        </td>
                        <td style="font-weight:600;">₱<?= number_format($p['price'], 2) ?></td>
                        <td><?= $p['stock'] ?></td>
                    </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
                </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="bi bi-box"></i>
                        <p>No products yet. <a href="/products/add.php" style="color:var(--brand)">Add one!</a></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
