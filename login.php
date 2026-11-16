<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$error = '';
$redirect = $_GET['redirect'] ?? $_POST['redirect'] ?? '';
$notice = $_GET['notice'] ?? $_POST['notice'] ?? '';

$isFeedback = (strpos($redirect, 'feedback') !== false) || ($notice === 'feedback_login_required');
$isMerchandise = (strpos($redirect, 'checkout') !== false) || ($notice === 'merchandise_login_required');

// If already logged in, redirect immediately
if (isLoggedIn()) {
    header('Location: ' . ($redirect !== '' ? $redirect : 'index.php'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Email and password are required.';
    } else {
        $db = getDbConnection();
        if (!$db) {
            $error = 'Database connection failed. Please ensure MySQL is running.';
        } else {
            $stmt = $db->prepare("SELECT user_id, name, email, password, role FROM users WHERE email = ? LIMIT 1");
            if ($stmt) {
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $res = $stmt->get_result();
                $user = $res->fetch_assoc();
                $stmt->close();

                if ($user && password_verify($password, $user['password'])) {
                    $_SESSION['user'] = [
                        'user_id' => $user['user_id'],
                        'name'    => $user['name'],
                        'email'   => $user['email'],
                        'role'    => $user['role']
                    ];

                    $target = $redirect !== '' ? $redirect : 'index.php';
                    header("Location: $target");
                    exit;
                } else {
                    $error = 'Invalid email or password.';
                }
            } else {
                $error = 'A system error occurred.';
            }
        }
    }
}

$status = $_GET['status'] ?? '';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Log In — Hearts2Hearts</title>
  <link rel="stylesheet" href="style.css" />
  <style>
    .auth-section {
      min-height: calc(100vh - 200px);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 60px 20px;
      background-color: var(--light-gray);
    }
    .auth-card {
      background: var(--white);
      border-radius: var(--radius-round);
      box-shadow: 0 10px 30px rgba(0,0,0,0.06);
      padding: 40px;
      max-width: 440px;
      width: 100%;
      border: 1px solid var(--border-light);
    }
    .auth-card h2 {
      font-family: var(--font-heading);
      font-size: 28px;
      margin-bottom: 8px;
      text-align: center;
      color: var(--heading-color);
    }
    .auth-card p.subtitle {
      text-align: center;
      color: var(--text-light);
      font-size: 14px;
      margin-bottom: 24px;
    }
    .form-group {
      margin-bottom: 18px;
    }
    .form-group label {
      display: block;
      font-size: 13px;
      font-weight: 500;
      margin-bottom: 6px;
      color: var(--body-color);
    }
    .form-control-h2h {
      width: 100%;
      padding: 12px 16px;
      border: 1px solid var(--border-light);
      border-radius: var(--radius-pill);
      font-family: var(--font-body);
      font-size: 14px;
      outline: none;
      transition: border-color var(--transition-fast);
      box-sizing: border-box;
    }
    .form-control-h2h:focus {
      border-color: var(--primary-blue-dark);
    }
    .alert {
      padding: 12px 16px;
      border-radius: var(--radius-sm);
      font-size: 13px;
      margin-bottom: 20px;
    }
    .alert-danger {
      background-color: #fee2e2;
      color: #991b1b;
      border: 1px solid #fecaca;
    }
    .alert-success {
      background-color: #dcfce7;
      color: #166534;
      border: 1px solid #bbf7d0;
    }
    .auth-footer {
      text-align: center;
      margin-top: 24px;
      font-size: 13px;
      color: var(--body-color);
    }
    .auth-footer a {
      color: var(--secondary-pink-dark);
      font-weight: 600;
      text-decoration: none;
    }
    .auth-footer a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <header>
    <div class="nav-container">
      <div class="logo">
        <a href="index.php"><img src="assets/logo/logo.png" alt="Hearts2Hearts" class="logo-img" /> Hearts2Hearts</a>
      </div>
      <nav>
        <ul id="nav-menu">
          <li><a href="index.php">Home</a></li>
          <li><a href="store.php">Store</a></li>
          <li><a href="events.php">Events</a></li>
        </ul>
      </nav>
    </div>
  </header>

  <main class="auth-section">
    <div class="auth-card">
      <h2>Welcome, S2U</h2>
      <?php if ($isFeedback): ?>
        <p class="subtitle">Please log in or create an account to submit fan feedback.</p>
        <div class="alert" style="background-color: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe;">
          <strong>Hello S2U!</strong> Only registered fan members can submit feedback. Please log in or create a new account below.
        </div>
      <?php elseif ($isMerchandise): ?>
        <p class="subtitle">Please log in or create an account to proceed with your merchandise checkout.</p>
        <div class="alert" style="background-color: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe;">
          <strong>Notice:</strong> Please log in or create an account to complete your Hearts2Hearts merchandise order.
        </div>
      <?php else: ?>
        <p class="subtitle">Please log in to your S2U account to continue.</p>
      <?php endif; ?>

      <?php if ($status === 'registered'): ?>
        <div class="alert alert-success">
          Registration successful! Please enter your email and password to log in.
        </div>
      <?php endif; ?>

      <?php if ($error): ?>
        <div class="alert alert-danger"><?= h($error) ?></div>
      <?php endif; ?>

      <form method="POST" action="login.php">
        <input type="hidden" name="redirect" value="<?= h($redirect) ?>">
        <input type="hidden" name="notice" value="<?= h($notice) ?>">
        <div class="form-group">
          <label for="email">Email Address</label>
          <input type="email" id="email" name="email" class="form-control-h2h" placeholder="name@email.com" required value="<?= h($_POST['email'] ?? '') ?>" autofocus>
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" class="form-control-h2h" placeholder="••••••••" required>
        </div>

        <button type="submit" class="btn btn-dark" style="width: 100%; margin-top: 10px;">Log In Now</button>
      </form>

      <div class="auth-footer">
        Don't have an account yet? <a href="register.php?<?= http_build_query(array_filter(['redirect' => $redirect, 'notice' => $notice])) ?>">Register New Account</a>
      </div>
    </div>
  </main>
</body>
</html>
