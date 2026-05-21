<?php
// ============================================
// login.php - User Login
// ============================================
session_start();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

// Already logged in? Go to dashboard
if (isLoggedIn()) {
    header("Location: /dashboard.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Please fill in all fields.';
    } else {
        $conn = getConnection();
        $stmt = $conn->prepare("SELECT id, username, password, full_name, role FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        $conn->close();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['username']  = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['user_role'] = $user['role'];
            session_regenerate_id(true);
            header("Location: /dashboard.php");
            exit();
        } else {
            $error = 'Invalid username or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login &mdash; InvenTrack</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
</head>
<body class="login-page">

<div class="login-card">
    <div class="login-logo">
        <span class="brand-icon"><i class="bi bi-box-seam-fill"></i></span>
        <span class="brand-name">InvenTrack</span>
    </div>

    <h2 style="font-size:1.3rem; font-weight:700; margin-bottom:6px;">Welcome back</h2>
    <p style="color:var(--text-muted); font-size:0.88rem; margin-bottom:28px;">Sign in to manage your inventory</p>

    <?php if ($error): ?>
        <div class="alert alert-danger mb-4"><i class="bi bi-exclamation-circle me-2"></i><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" action="/login.php">
        <div class="mb-4">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control"
                   placeholder="Enter your username"
                   value="<?= sanitize($_POST['username'] ?? '') ?>"
                   required autofocus>
        </div>
        <div class="mb-4">
            <label class="form-label">Password</label>
            <div class="position-relative">
                <input type="password" name="password" id="passwordInput"
                       class="form-control" placeholder="Enter your password" required>
                <button type="button" class="btn btn-sm position-absolute top-50 end-0 translate-middle-y pe-3 bg-transparent border-0"
                        style="color:var(--text-muted)" onclick="togglePw()">
                    <i class="bi bi-eye" id="pwEyeIcon"></i>
                </button>
            </div>
        </div>
        <button type="submit" class="btn btn-primary w-100 py-2 mt-2">
            <i class="bi bi-box-arrow-in-right"></i> Sign In
        </button>
    </form>

    <p class="text-center mt-4" style="font-size:0.8rem; color:var(--text-muted);">
        Demo credentials: <strong>admin</strong> / <strong>password</strong>
    </p>
</div>

<script>
function togglePw() {
    const input = document.getElementById('passwordInput');
    const icon  = document.getElementById('pwEyeIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'bi bi-eye';
    }
}
</script>
</body>
</html>
