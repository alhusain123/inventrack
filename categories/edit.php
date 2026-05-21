<?php
// ============================================
// categories/edit.php - Edit Category
// ============================================
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

$page_title = 'Edit Category';
$conn = getConnection();
$errors = [];

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: /categories/index.php");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM categories WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$category = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$category) {
    $conn->close();
    header("Location: /categories/index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = sanitize($_POST['name'] ?? '');
    $description = sanitize($_POST['description'] ?? '');

    if (empty($name)) $errors[] = 'Category name is required.';

    // Duplicate check (excluding current)
    $check = $conn->prepare("SELECT id FROM categories WHERE name = ? AND id != ?");
    $check->bind_param("si", $name, $id);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        $errors[] = "Another category named '$name' already exists.";
    }
    $check->close();

    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE categories SET name=?, description=? WHERE id=?");
        $stmt->bind_param("ssi", $name, $description, $id);
        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header("Location: /categories/index.php?msg=Category+updated+successfully");
            exit();
        } else {
            $errors[] = 'Database error.';
        }
        $stmt->close();
    }
    $category = array_merge($category, $_POST);
}

$conn->close();
require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h2>Edit Category</h2>
    <a href="/categories/index.php" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back
    </a>
</div>

<div class="row justify-content-center">
<div class="col-lg-6">
<div class="card">
    <div class="card-header">Edit: <?= sanitize($category['name']) ?></div>
    <div class="card-body">

        <?php foreach ($errors as $e): ?>
            <div class="alert alert-danger"><i class="bi bi-exclamation-circle me-2"></i><?= $e ?></div>
        <?php endforeach; ?>

        <form method="POST" action="/categories/edit.php?id=<?= $id ?>">
            <div class="mb-3">
                <label class="form-label">Category Name *</label>
                <input type="text" name="name" class="form-control"
                       value="<?= sanitize($category['name']) ?>" required>
            </div>
            <div class="mb-4">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control"><?= sanitize($category['description']) ?></textarea>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Save Changes
                </button>
                <a href="/categories/index.php" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
</div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
