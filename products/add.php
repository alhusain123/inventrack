<?php
// ============================================
// products/add.php - Add New Product
// ============================================
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

$page_title = 'Add Product';
$conn = getConnection();
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = sanitize($_POST['name'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $price       = floatval($_POST['price'] ?? 0);
    $stock       = intval($_POST['stock'] ?? 0);
    $threshold   = intval($_POST['low_stock_threshold'] ?? 10);
    $category_id = intval($_POST['category_id'] ?? 0);

    // Validation
    if (empty($name))      $errors[] = 'Product name is required.';
    if ($price < 0)        $errors[] = 'Price cannot be negative.';
    if ($stock < 0)        $errors[] = 'Stock cannot be negative.';
    if ($threshold < 1)    $errors[] = 'Low stock threshold must be at least 1.';

    if (empty($errors)) {
        $cat = $category_id > 0 ? $category_id : null;
        $stmt = $conn->prepare("INSERT INTO products (category_id, name, description, price, stock, low_stock_threshold) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issdii", $cat, $name, $description, $price, $stock, $threshold);

        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header("Location: /products/index.php?msg=Product+added+successfully");
            exit();
        } else {
            $errors[] = 'Database error: ' . $conn->error;
        }
        $stmt->close();
    }
}

$categories = $conn->query("SELECT id, name FROM categories ORDER BY name");
$conn->close();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h2>Add New Product</h2>
    <a href="/products/index.php" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back
    </a>
</div>

<div class="row justify-content-center">
<div class="col-lg-8">
<div class="card">
    <div class="card-header">Product Details</div>
    <div class="card-body">

        <?php foreach ($errors as $e): ?>
            <div class="alert alert-danger"><i class="bi bi-exclamation-circle me-2"></i><?= $e ?></div>
        <?php endforeach; ?>

        <form method="POST" action="/products/add.php">
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Product Name *</label>
                    <input type="text" name="name" class="form-control"
                           value="<?= sanitize($_POST['name'] ?? '') ?>"
                           placeholder="e.g. Wireless Mouse" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Category</label>
                    <select name="category_id" class="form-select">
                        <option value="0">— None —</option>
                        <?php while ($cat = $categories->fetch_assoc()): ?>
                        <option value="<?= $cat['id'] ?>" <?= ($_POST['category_id'] ?? 0) == $cat['id'] ? 'selected' : '' ?>>
                            <?= sanitize($cat['name']) ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control"
                              placeholder="Brief product description (optional)"><?= sanitize($_POST['description'] ?? '') ?></textarea>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Price (₱) *</label>
                    <input type="number" name="price" class="form-control"
                           step="0.01" min="0"
                           value="<?= $_POST['price'] ?? '' ?>"
                           placeholder="0.00" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Current Stock *</label>
                    <input type="number" name="stock" class="form-control"
                           min="0"
                           value="<?= $_POST['stock'] ?? '' ?>"
                           placeholder="0" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Low Stock Alert At</label>
                    <input type="number" name="low_stock_threshold" class="form-control"
                           min="1"
                           value="<?= $_POST['low_stock_threshold'] ?? 10 ?>"
                           placeholder="10">
                    <div style="font-size:0.75rem; color:var(--text-muted); margin-top:4px">
                        Alert shown when stock reaches this number
                    </div>
                </div>
                <div class="col-12 d-flex gap-2 pt-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Add Product
                    </button>
                    <a href="/products/index.php" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
</div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
