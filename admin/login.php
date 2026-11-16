<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

// If already logged in as admin, redirect to dashboard
if (isAdmin()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
if (isset($_GET['error']) && $_GET['error'] === 'unauthorized') {
    $error = 'Restricted access for administrators. Please log in first.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Email and password are required.';
    } else {
        $db = getDbConnection();
        if (!$db) {
            $error = 'Failed to connect to the database. Please ensure MySQL is running.';
        } else {
            $stmt = $db->prepare("SELECT user_id, name, email, password, role FROM users WHERE email = ? LIMIT 1");
            if ($stmt) {
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $res = $stmt->get_result();
                $user = $res->fetch_assoc();
                $stmt->close();

                if ($user && password_verify($password, $user['password'])) {
                    if ($user['role'] === 'admin') {
                        $_SESSION['user'] = [
                            'user_id' => $user['user_id'],
                            'name'    => $user['name'],
                            'email'   => $user['email'],
                            'role'    => $user['role']
                        ];
                        header('Location: dashboard.php');
                        exit;
                    } else {
                        $error = 'Access denied: This account is not an administrator.';
                    }
                } else {
                    $error = 'Invalid email or password.';
                }
            } else {
                $error = 'A system error occurred.';
            }
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Login — Hearts2Hearts</title>
  <link rel="stylesheet" href="assets/adminlte.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
  <style>
    body {
      background-color: #f4f6f9;
      font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
    }
    .login-box {
      max-width: 400px;
      margin: 80px auto;
      padding: 20px;
    }
    .card {
      border-radius: 12px;
      border: none;
      box-shadow: 0 10px 25px rgba(0,0,0,0.08);
    }
    .card-header {
      background: transparent;
      border-bottom: none;
      text-align: center;
      padding-top: 30px;
    }
    .brand-title {
      font-weight: 700;
      color: #333;
      margin-top: 10px;
      font-size: 24px;
    }
  </style>
</head>
<body>
<div class="login-box">
  <div class="card bg-white">
    <div class="card-header">
      <img src="assets/AdminLTELogo.png" alt="Logo" style="height: 50px;">
      <div class="brand-title">H2H Admin Panel</div>
      <p class="text-muted small">System Administrators Only</p>
    </div>
    <div class="card-body p-4">
      <?php if ($error): ?>
        <div class="alert alert-danger py-2 small"><i class="bi bi-exclamation-triangle me-1"></i><?= h($error) ?></div>
      <?php endif; ?>

      <form method="POST" action="login.php">
        <div class="mb-3">
          <label class="form-label small fw-semibold" for="email">Admin Email</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
            <input type="email" class="form-control" id="email" name="email" placeholder="admin@h2h.com" required autofocus value="<?= h($_POST['email'] ?? '') ?>">
          </div>
        </div>

        <div class="mb-4">
          <label class="form-label small fw-semibold" for="password">Password</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock"></i></span>
            <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" required>
          </div>
        </div>

        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
          <i class="bi bi-box-arrow-in-right me-1"></i> Sign In to Dashboard
        </button>
      </form>

      <div class="text-center mt-4">
        <a href="../index.php" class="text-decoration-none small text-muted">
          <i class="bi bi-arrow-left me-1"></i> Back to Main Website
        </a>
      </div>
    </div>
  </div>
</div>
</body>
</html>
