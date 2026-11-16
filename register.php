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
        $error = 'Nama, email, dan password wajib diisi.';
    } elseif (strlen($password) < 4) {
        $error = 'Password minimal 4 karakter.';
    } else {
        $db = getDbConnection();
        if (!$db) {
            $error = 'Gagal terhubung ke database. Pastikan MySQL sudah aktif.';
        } else {
            // Cek apakah email sudah terdaftar
            $checkStmt = $db->prepare("SELECT user_id FROM users WHERE email = ? LIMIT 1");
            $checkStmt->bind_param("s", $email);
            $checkStmt->execute();
            $checkRes = $checkStmt->get_result();

            if ($checkRes->num_rows > 0) {
                $error = 'Email ini sudah terdaftar. Silakan login atau gunakan email lain.';
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
                        $error = 'Gagal menyimpan data pendaftaran: ' . $insStmt->error;
                        $insStmt->close();
                    }
                } else {
                    $error = 'Terjadi kesalahan sistem saat membuat akun.';
                }
            }
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Registrasi Akun — Hearts2Hearts</title>
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
      <h2>Daftar Akun S2U</h2>
      <?php if ($isFeedback): ?>
        <p class="subtitle">Buat akun untuk mulai mengirimkan saran & feedback bagi Hearts2Hearts.</p>
        <div class="alert" style="background-color: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe;">
          <strong>Hai S2U!</strong> Pendaftaran akun hanya butuh waktu 1 menit agar pesan feedback Anda terverifikasi.
        </div>
      <?php elseif ($isMerchandise): ?>
        <p class="subtitle">Buat akun untuk melanjutkan checkout merchandise Hearts2Hearts.</p>
        <div class="alert" style="background-color: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe;">
          <strong>Pemberitahuan:</strong> Daftarkan akun Anda agar riwayat pesanan merchandise tersimpan dengan aman.
        </div>
      <?php else: ?>
        <p class="subtitle">Daftarkan diri Anda sebagai bagian resmi dari fanbase S2U Hearts2Hearts.</p>
      <?php endif; ?>

      <?php if ($error): ?>
        <div class="alert alert-danger"><?= h($error) ?></div>
      <?php endif; ?>

      <form method="POST" action="register.php">
        <input type="hidden" name="redirect" value="<?= h($redirect) ?>">
        <input type="hidden" name="notice" value="<?= h($notice) ?>">

        <div class="form-group">
          <label for="name">Nama Lengkap *</label>
          <input type="text" id="name" name="name" class="form-control-h2h" placeholder="Nama Anda" required value="<?= h($_POST['name'] ?? '') ?>" autofocus>
        </div>

        <div class="form-group">
          <label for="email">Alamat Email *</label>
          <input type="email" id="email" name="email" class="form-control-h2h" placeholder="nama@email.com" required value="<?= h($_POST['email'] ?? '') ?>">
        </div>

        <div class="form-group">
          <label for="password">Password *</label>
          <input type="password" id="password" name="password" class="form-control-h2h" placeholder="Minimal 4 karakter" required>
        </div>

        <div class="form-group">
          <label for="phone">Nomor Telepon / WhatsApp</label>
          <input type="text" id="phone" name="phone" class="form-control-h2h" placeholder="08xxxxxxxxxx" value="<?= h($_POST['phone'] ?? '') ?>">
        </div>

        <div class="form-group">
          <label for="address">Alamat Pengiriman</label>
          <textarea id="address" name="address" class="form-control-h2h" rows="2" placeholder="Nama jalan, nomor rumah, kota, kode pos"><?= h($_POST['address'] ?? '') ?></textarea>
        </div>

        <button type="submit" class="btn btn-dark" style="width: 100%; margin-top: 10px;">Daftar Sekarang</button>
      </form>

      <div class="auth-footer">
        Sudah memiliki akun? <a href="login.php?<?= http_build_query(array_filter(['redirect' => $redirect, 'notice' => $notice])) ?>">Kembali ke Login</a>
      </div>
    </div>
  </main>
</body>
</html>
