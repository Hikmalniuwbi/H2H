<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$error = '';
$redirect = $_GET['redirect'] ?? $_POST['redirect'] ?? '';
$notice = $_GET['notice'] ?? $_POST['notice'] ?? '';

$isFeedback = (strpos($redirect, 'feedback') !== false) || ($notice === 'feedback_login_required');
$isMerchandise = (strpos($redirect, 'checkout') !== false) || ($notice === 'merchandise_login_required');

if (isLoggedIn()) {
    header('Location: ' . ($redirect !== '' ? $redirect : 'index.php'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $phone    = trim($_POST['phone'] ?? '');
    $address  = trim($_POST['address'] ?? '');

    if ($name === '' || $email === '' || $password === '') {
        $error = 'Name, email, and password are required.';
    } elseif (strlen($password) < 4) {
        $error = 'Password must be at least 4 characters long.';
    } else {
        $db = getDbConnection();
        if (!$db) {
            $error = 'Database connection failed. Please ensure MySQL is running.';
        } else {
            // Check if email is already registered
            $checkStmt = $db->prepare("SELECT user_id FROM users WHERE email = ? LIMIT 1");
            $checkStmt->bind_param("s", $email);
            $checkStmt->execute();
            $checkRes = $checkStmt->get_result();

            if ($checkRes->num_rows > 0) {
                $error = 'This email is already registered. Please log in or use another email.';
                $checkStmt->close();
            } else {
                $checkStmt->close();
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $role = 'customer';

                $insStmt = $db->prepare("INSERT INTO users (name, email, password, phone, address, role) VALUES (?, ?, ?, ?, ?, ?)");
                if ($insStmt) {
                    $insStmt->bind_param("ssssss", $name, $email, $hashedPassword, $phone, $address, $role);
                    if ($insStmt->execute()) {
                        $insStmt->close();
                        $queryParams = ['status' => 'registered'];
                        if ($redirect !== '') $queryParams['redirect'] = $redirect;
                        if ($notice !== '') $queryParams['notice'] = $notice;
                        header("Location: login.php?" . http_build_query($queryParams));
                        exit;
                    } else {
                        $error = 'Failed to save registration data: ' . $insStmt->error;
                        $insStmt->close();
                    }
                } else {
                    $error = 'A system error occurred while creating the account.';
                }
            }
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Register Account — Hearts2Hearts</title>
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
      max-width: 500px;
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
      margin-bottom: 16px;
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
      padding: 10px 16px;
      border: 1px solid var(--border-light);
      border-radius: var(--radius-pill);
      font-family: var(--font-body);
      font-size: 14px;
      outline: none;
      transition: border-color var(--transition-fast);
      box-sizing: border-box;
    }
    textarea.form-control-h2h {
      border-radius: var(--radius-round);
      resize: vertical;
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
      <h2>Register S2U Account</h2>
      <?php if ($isFeedback): ?>
        <p class="subtitle">Create an account to start submitting suggestions & feedback for Hearts2Hearts.</p>
        <div class="alert" style="background-color: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe;">
          <strong>Hello S2U!</strong> Account registration takes only 1 minute to verify your feedback submissions.
        </div>
      <?php elseif ($isMerchandise): ?>
        <p class="subtitle">Create an account to proceed with Hearts2Hearts merchandise checkout.</p>
        <div class="alert" style="background-color: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe;">
          <strong>Notice:</strong> Register your account so your merchandise order history is safely tracked.
        </div>
      <?php else: ?>
        <p class="subtitle">Register to become an official member of the Hearts2Hearts S2U fanbase.</p>
      <?php endif; ?>

      <?php if ($error): ?>
        <div class="alert alert-danger"><?= h($error) ?></div>
      <?php endif; ?>

      <form method="POST" action="register.php">
        <input type="hidden" name="redirect" value="<?= h($redirect) ?>">
        <input type="hidden" name="notice" value="<?= h($notice) ?>">

        <div class="form-group">
          <label for="name">Full Name *</label>
          <input type="text" id="name" name="name" class="form-control-h2h" placeholder="Your Full Name" required value="<?= h($_POST['name'] ?? '') ?>" autofocus>
        </div>

        <div class="form-group">
          <label for="email">Email Address *</label>
          <input type="email" id="email" name="email" class="form-control-h2h" placeholder="name@email.com" required value="<?= h($_POST['email'] ?? '') ?>">
        </div>

        <div class="form-group">
          <label for="password">Password *</label>
          <input type="password" id="password" name="password" class="form-control-h2h" placeholder="At least 4 characters" required>
        </div>

        <div class="form-group">
          <label for="phone">Phone / WhatsApp Number</label>
          <input type="text" id="phone" name="phone" class="form-control-h2h" placeholder="08xxxxxxxxxx" value="<?= h($_POST['phone'] ?? '') ?>">
        </div>

        <div class="form-group">
          <label for="address">Shipping Address</label>
          <textarea id="address" name="address" class="form-control-h2h" rows="2" placeholder="Street name, house number, city, postal code"><?= h($_POST['address'] ?? '') ?></textarea>
        </div>

        <button type="submit" class="btn btn-dark" style="width: 100%; margin-top: 10px;">Register Now</button>
      </form>

      <div class="auth-footer">
        Already have an account? <a href="login.php?<?= http_build_query(array_filter(['redirect' => $redirect, 'notice' => $notice])) ?>">Back to Log In</a>
      </div>
    </div>
  </main>
</body>
</html>
