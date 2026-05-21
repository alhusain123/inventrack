<?php
// ============================================
// products/edit.php - Edit Product
// ============================================
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

$page_title = 'Edit Product';
$conn = getConnection();
$errors = [];

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: /products/index.php");
    exit();
}

// ── Fetch product ─────────────────────────────
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$product) {
    $conn->close();
    header("Location: /products/index.php?msg=Product+not+found");
    exit();
}

// ── Handle POST ───────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = sanitize($_POST['name'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $price       = floatval($_POST['price'] ?? 0);
    $stock       = intval($_POST['stock'] ?? 0);
    $threshold   = intval($_POST['low_stock_threshold'] ?? 10);
    $category_id = intval($_POST['category_id'] ?? 0);

    if (empty($name))   $errors[] = 'Product name is required.';
    if ($price < 0)     $errors[] = 'Price cannot be negative.';
    if ($stock < 0)     $errors[] = 'Stock cannot be negative.';
    if ($threshold < 1) $errors[] = 'Low stock threshold must be at least 1.';

    if (empty($errors)) {
        $cat = $category_id > 0 ? $category_id : null;
        $stmt = $conn->prepare("UPDATE products SET category_id=?, name=?, description=?, price=?, stock=?, low_stock_threshold=? WHERE id=?");
        $stmt->bind_param("issdiiv", $cat, $name, $description, $price, $stock, $threshold, $id);

        // Log stock change if different
        if ($stock !== intval($product['stock'])) {
            $change = $stock - intval($product['stock']);
            $user   = currentUser();
            $uid    = $user['id'];
            $note   = "Manual edit";
            $log    = $conn->prepare("INSERT INTO stock_logs (product_id, user_id, change_amount, note) VALUES (?, ?, ?, ?)");
            $log->bind_param("iiis", $id, $uid, $change, $note);
            $log->execute();
            $log->close();
        }

        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header("Location: /products/index.php?msg=Product+updated+successfully");
            exit();
        } else {
            $errors[] = 'Database error: ' . $conn->error;
        }
        $stmt->close();
    }

    // Repopulate with POST data on error
    $product = array_merge($product, $_POST);
}

$categories = $conn->query("SELECT id, name FROM categories ORDER BY name");
$conn->close();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h2>Edit Product</h2>
    <a href="/products/index.php" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back
    </a>
</div>

<div class="row justify-content-center">
<div class="col-lg-8">
<div class="card">
    <div class="card-header">Edit: <?= sanitize($product['name']) ?></div>
    <div class="card-body">

        <?php foreach ($errors as $e): ?>
            <div class="alert alert-danger"><i class="bi bi-exclamation-circle me-2"></i><?= $e ?></div>
        <?php endforeach; ?>

        <form method="POST" action="/products/edit.php?id=<?= $id ?>">
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Product Name *</label>
                    <input type="text" name="name" class="form-control"
                           value="<?= sanitize($product['name']) ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Category</label>
                    <select name="category_id" class="form-select">
                        <option value="0">— None —</option>
                        <?php while ($cat = $categories->fetch_assoc()): ?>
                        <option value="<?= $cat['id'] ?>" <?= $product['category_id'] == $cat['id'] ? 'selected' : '' ?>>
                            <?= sanitize($cat['name']) ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control"><?= sanitize($product['description']) ?></textarea>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Price (₱) *</label>
                    <input type="number" name="price" class="form-control"
                           step="0.01" min="0"
                           value="<?= $product['price'] ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Current Stock *</label>
                    <input type="number" name="stock" class="form-control"
                           min="0" value="<?= $product['stock'] ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Low Stock Alert At</label>
                    <input type="number" name="low_stock_threshold" class="form-control"
                           min="1" value="<?= $product['low_stock_threshold'] ?>">
                </div>
                <div class="col-12 d-flex gap-2 pt-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Save Changes
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
