<?php
// ============================================
// products/delete.php - Delete Product
// ============================================
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: /products/index.php");
    exit();
}

$conn = getConnection();
$stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    $msg = "Product+deleted+successfully";
} else {
    $msg = "Product+not+found+or+already+deleted";
}

$stmt->close();
$conn->close();
header("Location: /products/index.php?msg=$msg");
exit();
?>
