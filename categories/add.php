<?php
// ============================================
// categories/add.php - Add Category
// ============================================
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

$page_title = 'Add Category';
$conn = getConnection();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = sanitize($_POST['name'] ?? '');
    $description = sanitize($_POST['description'] ?? '');

    if (empty($name)) $errors[] = 'Category name is required.';

    // Check duplicate
    $check = $conn->prepare("SELECT id FROM categories WHERE name = ?");
    $check->bind_param("s", $name);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        $errors[] = "Category '$name' already exists.";
    }
    $check->close();

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $description);
        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header("Location: /categories/index.php?msg=Category+added+successfully");
            exit();
        } else {
            $errors[] = 'Database error.';
        }
        $stmt->close();
    }
}

$conn->close();
require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h2>Add Category</h2>
    <a href="/categories/index.php" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back
    </a>
</div>

<div class="row justify-content-center">
<div class="col-lg-6">
<div class="card">
    <div class="card-header">Category Details</div>
    <div class="card-body">

        <?php foreach ($errors as $e): ?>
            <div class="alert alert-danger"><i class="bi bi-exclamation-circle me-2"></i><?= $e ?></div>
        <?php endforeach; ?>

        <form method="POST" action="/categories/add.php">
            <div class="mb-3">
                <label class="form-label">Category Name *</label>
                <input type="text" name="name" class="form-control"
                       value="<?= sanitize($_POST['name'] ?? '') ?>"
                       placeholder="e.g. Electronics" required>
            </div>
            <div class="mb-4">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control"
                          placeholder="Optional description"><?= sanitize($_POST['description'] ?? '') ?></textarea>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Add Category
                </button>
                <a href="/categories/index.php" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
</div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
