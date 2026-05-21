<?php
// ============================================
// users/delete.php - Delete User (Admin Only)
// ============================================
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

requireAdmin();
$current = currentUser();
$id = intval($_GET['id'] ?? 0);

// Cannot delete yourself
if ($id <= 0 || $id == $current['id']) {
    header("Location: /users/index.php?msg=Cannot+delete+your+own+account");
    exit();
}

$conn = getConnection();
$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();
$conn->close();

header("Location: /users/index.php?msg=User+deleted+successfully");
exit();
?>
