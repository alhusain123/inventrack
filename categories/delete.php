<?php
// ============================================
// categories/delete.php - Delete Category
// ============================================
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: /categories/index.php");
    exit();
}

$conn = getConnection();

// Safety check: don't delete if it has products
$check = $conn->prepare("SELECT COUNT(*) AS c FROM products WHERE category_id = ?");
$check->bind_param("i", $id);
$check->execute();
$count = $check->get_result()->fetch_assoc()['c'];
$check->close();

if ($count > 0) {
    $conn->close();
    header("Location: /categories/index.php?error=Cannot+delete+category+with+existing+products");
    exit();
}

$stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();
$conn->close();

header("Location: /categories/index.php?msg=Category+deleted+successfully");
exit();
?>
