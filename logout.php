<?php
// ============================================
// logout.php - Destroy session and redirect
// ============================================
session_start();
$_SESSION = [];
session_destroy();
header("Location: /login.php?logout=1");
exit();
?>
