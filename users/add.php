<?php
// ============================================
// users/add.php - Add User (Admin Only)
// ============================================
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

requireAdmin();
$page_title = 'Add User';
$conn = getConnection();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = sanitize($_POST['full_name'] ?? '');
    $username  = sanitize($_POST['username'] ?? '');
    $password  = $_POST['password'] ?? '';
    $role      = in_array($_POST['role'] ?? '', ['admin','staff']) ? $_POST['role'] : 'staff';

    if (empty($full_name)) $errors[] = 'Full name is required.';
    if (empty($username))  $errors[] = 'Username is required.';
    if (strlen($password) < 6) $errors[] = 'Password must be at least 6 characters.';

    // Duplicate check
    $check = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $check->bind_param("s", $username);
    $check->execute();
    if ($check->get_result()->num_rows > 0) $errors[] = "Username '$username' is already taken.";
    $check->close();

    if (empty($errors)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, password, full_name, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $hashed, $full_name, $role);
        if ($stmt->execute()) {
            $stmt->close(); $conn->close();
            header("Location: /users/index.php?msg=User+added+successfully");
            exit();
        }
        $errors[] = 'Database error.';
        $stmt->close();
    }
}

$conn->close();
require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h2>Add User</h2>
    <a href="/users/index.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
</div>

<div class="row justify-content-center">
<div class="col-lg-6">
<div class="card">
    <div class="card-header">New User Account</div>
    <div class="card-body">

        <?php foreach ($errors as $e): ?>
            <div class="alert alert-danger"><i class="bi bi-exclamation-circle me-2"></i><?= $e ?></div>
        <?php endforeach; ?>

        <form method="POST" action="/users/add.php">
            <div class="mb-3">
                <label class="form-label">Full Name *</label>
                <input type="text" name="full_name" class="form-control"
                       value="<?= sanitize($_POST['full_name'] ?? '') ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Username *</label>
                <input type="text" name="username" class="form-control"
                       value="<?= sanitize($_POST['username'] ?? '') ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password *</label>
                <input type="password" name="password" class="form-control"
                       placeholder="Min. 6 characters" required>
            </div>
            <div class="mb-4">
                <label class="form-label">Role</label>
                <select name="role" class="form-select">
                    <option value="staff" <?= ($_POST['role'] ?? '') === 'staff' ? 'selected' : '' ?>>Staff</option>
                    <option value="admin" <?= ($_POST['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
                </select>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-person-plus"></i> Add User</button>
                <a href="/users/index.php" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
</div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
