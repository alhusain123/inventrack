<?php
// ============================================
// header.php - Shared Page Header
// ============================================
require_once __DIR__ . '/auth.php';
requireLogin();
$user = currentUser();
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'InvenTrack' ?> &mdash; InvenTrack</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,400&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
</head>
<body>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <span class="brand-icon"><i class="bi bi-box-seam-fill"></i></span>
        <span class="brand-name">InvenTrack</span>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section-label">Main</div>
        <a href="/dashboard.php" class="nav-link <?= $current_page === 'dashboard.php' ? 'active' : '' ?>">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
        <a href="/products/index.php" class="nav-link <?= in_array($current_page, ['index.php','add.php','edit.php']) && strpos($_SERVER['PHP_SELF'], 'products') !== false ? 'active' : '' ?>">
            <i class="bi bi-box2"></i> Products
        </a>
        <a href="/categories/index.php" class="nav-link <?= in_array($current_page, ['index.php','add.php','edit.php']) && strpos($_SERVER['PHP_SELF'], 'categories') !== false ? 'active' : '' ?>">
            <i class="bi bi-tags"></i> Categories
        </a>

        <?php if (isAdmin()): ?>
        <div class="nav-section-label mt-3">Admin</div>
        <a href="/users/index.php" class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'users') !== false ? 'active' : '' ?>">
            <i class="bi bi-people"></i> Users
        </a>
        <?php endif; ?>
    </nav>

    <div class="sidebar-footer">
        <div class="user-info">
            <div class="user-avatar"><?= strtoupper(substr($user['full_name'], 0, 1)) ?></div>
            <div class="user-details">
                <span class="user-name"><?= sanitize($user['full_name']) ?></span>
                <span class="user-role"><?= ucfirst($user['role']) ?></span>
            </div>
        </div>
        <a href="/logout.php" class="logout-btn" title="Logout"><i class="bi bi-box-arrow-right"></i></a>
    </div>
</div>

<!-- Main content wrapper -->
<div class="main-wrapper">
    <!-- Top bar -->
    <header class="topbar">
        <button class="sidebar-toggle" id="sidebarToggle">
            <i class="bi bi-list"></i>
        </button>
        <h1 class="page-title"><?= $page_title ?? 'Dashboard' ?></h1>
        <div class="topbar-actions">
            <span class="badge-role <?= $user['role'] ?>"><?= ucfirst($user['role']) ?></span>
        </div>
    </header>

    <!-- Page content starts here -->
    <main class="page-content">
