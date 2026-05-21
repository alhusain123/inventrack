<?php
// ============================================
// users/edit.php - Edit User (Admin or Self)
// ============================================
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

requireLogin();
$page_title = 'Edit User';
$conn = getConnection();
$errors = [];
$current = currentUser();

$id = intval($_GET['id'] ?? 0);
// Only admin can edit others; staff can only edit themselves
if (!isAdmin() && $id != $current['id']) {
    $conn->close();
    header("Location: /dashboard.php?error=unauthorized");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) { $conn->close(); header("Location: /users/index.php"); exit(); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = sanitize($_POST['full_name'] ?? '');
    $username  = sanitize($_POST['username'] ?? '');
    $password  = $_POST['password'] ?? '';
    $role      = isAdmin() && in_array($_POST['role'] ?? '', ['admin','staff']) ? $_POST['role'] : $user['role'];

    if (empty($full_name)) $errors[] = 'Full name is required.';
    if (empty($username))  $errors[] = 'Username is required.';
    if ($password !== '' && strlen($password) < 6) $errors[] = 'Password must be at least 6 characters.';

    $check = $conn->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
    $check->bind_param("si", $username, $id);
    $check->execute();
    if ($check->get_result()->num_rows > 0) $errors[] = "Username '$username' is already taken.";
    $check->close();

    if (empty($errors)) {
        if ($password !== '') {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET username=?, full_name=?, role=?, password=? WHERE id=?");
            $stmt->bind_param("ssssi", $username, $full_name, $role, $hashed, $id);
        } else {
            $stmt = $conn->prepare("UPDATE users SET username=?, full_name=?, role=? WHERE id=?");
            $stmt->bind_param("sssi", $username, $full_name, $role, $id);
        }
        if ($stmt->execute()) {
            // Update session if editing self
            if ($id == $current['id']) {
                $_SESSION['username']  = $username;
                $_SESSION['full_name'] = $full_name;
            }
            $stmt->close(); $conn->close();
            header("Location: /users/index.php?msg=User+updated+successfully");
            exit();
        }
        $errors[] = 'Database error.';
        $stmt->close();
    }
    $user = array_merge($user, $_POST);
}

$conn->close();
require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h2>Edit User</h2>
    <a href="/users/index.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
</div>

<div class="row justify-content-center">
<div class="col-lg-6">
<div class="card">
    <div class="card-header">Edit: <?= sanitize($user['username']) ?></div>
    <div class="card-body">

        <?php foreach ($errors as $e): ?>
            <div class="alert alert-danger"><i class="bi bi-exclamation-circle me-2"></i><?= $e ?></div>
        <?php endforeach; ?>

        <form method="POST" action="/users/edit.php?id=<?= $id ?>">
            <div class="mb-3">
                <label class="form-label">Full Name *</label>
                <input type="text" name="full_name" class="form-control"
                       value="<?= sanitize($user['full_name']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Username *</label>
                <input type="text" name="username" class="form-control"
                       value="<?= sanitize($user['username']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">New Password <span style="color:var(--text-muted); font-weight:400">(leave blank to keep current)</span></label>
                <input type="password" name="password" class="form-control" placeholder="Min. 6 characters">
            </div>
            <?php if (isAdmin()): ?>
            <div class="mb-4">
                <label class="form-label">Role</label>
                <select name="role" class="form-select">
                    <option value="staff" <?= $user['role'] === 'staff' ? 'selected' : '' ?>>Staff</option>
                    <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                </select>
            </div>
            <?php endif; ?>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Save Changes</button>
                <a href="/users/index.php" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
</div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
