<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$message = '';
$error = '';
$currentUser = currentUser();

// Jika belum login dan ada permintaan POST, wajib diarahkan untuk login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isLoggedIn()) {
        header('Location: login.php?redirect=feedback.php&notice=feedback_login_required');
        exit;
    }

    $userId = (int)$currentUser['user_id'];
    // Nama dan email terintegrasi langsung dengan profil user
    $name = trim(!empty($_POST['name']) ? $_POST['name'] : $currentUser['name']);
    $email = trim(!empty($_POST['email']) ? $_POST['email'] : $currentUser['email']);
    $feedbackMsg = trim($_POST['message'] ?? '');

    if ($feedbackMsg === '') {
        $error = 'Pesan feedback tidak boleh kosong.';
    } else {
        $db = getDbConnection();
        if ($db) {
            $stmt = $db->prepare("INSERT INTO feedbacks (user_id, name, email, message, created_at) VALUES (?, ?, ?, ?, NOW())");
            if ($stmt) {
                $stmt->bind_param("isss", $userId, $name, $email, $feedbackMsg);
                if ($stmt->execute()) {
                    $message = 'Terima kasih atas feedback Anda! Pesan Anda telah kami terima dan akan ditinjau oleh tim Hearts2Hearts.';
                } else {
                    $error = 'Gagal menyimpan feedback: ' . $stmt->error;
                }
                $stmt->close();
            } else {
                $error = 'Terjadi kesalahan sistem saat memproses feedback.';
            }
        } else {
            $error = 'Gagal terhubung ke database. Pastikan MySQL sudah aktif.';
        }
    }
}

// Ambil riwayat feedback yang pernah dikirim oleh user ini
$userFeedbacks = [];
if (isLoggedIn()) {
    $db = getDbConnection();
    if ($db) {
        $uId = (int)$currentUser['user_id'];
        $q = $db->query("SELECT feedback_id, message, created_at FROM feedbacks WHERE user_id = $uId ORDER BY feedback_id DESC LIMIT 5");
        if ($q) {
            while ($row = $q->fetch_assoc()) {
                $userFeedbacks[] = $row;
            }
        }
    }
}

$currentPage = 'home';
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Feedback Fans — Hearts2Hearts</title>
  <link rel="stylesheet" href="style.css" />
  <style>
    .feedback-container {
      max-width: 680px;
      margin: 40px auto;
      padding: 0 20px;
    }
    .feedback-card {
      background: #ffffff;
      border: 1px solid var(--border-light);
      border-radius: var(--radius-round);
      box-shadow: 0 10px 30px rgba(0,0,0,0.06);
      padding: 36px;
    }
    .auth-alert-box {
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      border-radius: 16px;
      padding: 36px 24px;
      text-align: center;
    }
    .form-group-h2h {
      margin-bottom: 20px;
      text-align: left;
    }
    .form-group-h2h label {
      display: block;
      font-size: 13px;
      font-weight: 600;
      margin-bottom: 8px;
      color: var(--heading-color);
    }
    .form-control-h2h {
      width: 100%;
      padding: 12px 16px;
      border: 1px solid var(--border-light);
      border-radius: var(--radius-pill);
      font-family: var(--font-body);
      font-size: 14px;
      box-sizing: border-box;
      outline: none;
      transition: border-color var(--transition-fast);
    }
    .form-control-h2h:focus {
      border-color: var(--secondary-pink-dark);
    }
    .form-control-textarea {
      border-radius: 12px;
      resize: vertical;
    }
    .form-control-readonly {
      background-color: #f1f5f9;
      color: #475569;
      cursor: not-allowed;
    }
    .feedback-history {
      margin-top: 36px;
      text-align: left;
    }
    .feedback-history h3 {
      font-size: 18px;
      font-family: var(--font-heading);
      margin-bottom: 16px;
      color: var(--heading-color);
    }
    .history-item {
      background: #f8fafc;
      border-left: 3px solid var(--secondary-pink-dark);
      padding: 14px 18px;
      border-radius: 0 8px 8px 0;
      margin-bottom: 12px;
      font-size: 13px;
    }
    .history-time {
      font-size: 11px;
      color: #64748b;
      margin-top: 6px;
    }
  </style>
</head>
<body>
  <?php include __DIR__ . '/includes/navbar.php'; ?>

  <section class="page-banner" style="background-color: var(--primary-blue)">
    <h1>Feedback Fans</h1>
    <p>Kami sangat menghargai saran dan masukan dari S2U.</p>
  </section>

  <main>
    <div class="feedback-container">
      <?php if ($message): ?>
        <div style="background-color: #dcfce7; color: #166534; padding: 18px 24px; border-radius: 12px; margin-bottom: 24px; border: 1px solid #bbf7d0; text-align: center;">
          <h3 style="margin-bottom: 6px; font-size: 18px;">Pesan Terkirim!</h3>
          <p style="margin: 0; font-size: 14px;"><?= h($message) ?></p>
        </div>
      <?php endif; ?>

      <?php if ($error): ?>
        <div style="background-color: #fee2e2; color: #991b1b; padding: 16px 20px; border-radius: 12px; margin-bottom: 24px; border: 1px solid #fecaca; text-align: center;">
          <p style="margin: 0; font-size: 14px;"><?= h($error) ?></p>
        </div>
      <?php endif; ?>

      <?php if (!isLoggedIn()): ?>
        <!-- Todongan Login untuk user yang belum memiliki akun/login -->
        <div class="auth-alert-box">
          <div style="font-size: 44px; margin-bottom: 12px;">💌</div>
          <h2 style="font-family: var(--font-heading); font-size: 24px; margin-bottom: 10px; color: var(--heading-color);">
            Eksklusif untuk Member S2U
          </h2>
          <p style="color: var(--text-light); line-height: 1.6; margin-bottom: 24px; font-size: 14px;">
            Hanya user fan yang telah memiliki akun terdaftar yang dapat mengirimkan feedback untuk website dan grup Hearts2Hearts. Silakan login atau buat akun baru terlebih dahulu.
          </p>
          <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
            <a href="login.php?redirect=feedback.php&notice=feedback_login_required" class="btn btn-dark" style="padding: 10px 24px; border-radius: 50px;">
              Login Sekarang
            </a>
            <a href="register.php?redirect=feedback.php&notice=feedback_login_required" class="btn btn-pink" style="padding: 10px 24px; border-radius: 50px;">
              Daftar Akun Baru
            </a>
          </div>
        </div>
      <?php else: ?>
        <!-- Form Feedback untuk user fan yang sudah login -->
        <div class="feedback-card">
          <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 12px; padding: 14px 18px; margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
            <div>
              <span style="font-size: 12px; color: #1e40af; font-weight: 600; text-transform: uppercase;">Terhubung sebagai Member S2U</span>
              <div style="font-size: 15px; font-weight: 600; color: #1e3a8a; margin-top: 2px;">
                <?= h($currentUser['name']) ?> (<?= h($currentUser['email']) ?>)
              </div>
            </div>
            <span style="background: #dbeafe; color: #1d4ed8; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">S2U Verified</span>
          </div>

          <form action="feedback.php" method="POST">
            <div class="form-group-h2h">
              <label for="name">Nama Fan</label>
              <input type="text" id="name" name="name" class="form-control-h2h form-control-readonly" value="<?= h($currentUser['name']) ?>" readonly title="Nama diambil dari data akun Anda" />
            </div>

            <div class="form-group-h2h">
              <label for="email">Email Fan</label>
              <input type="email" id="email" name="email" class="form-control-h2h form-control-readonly" value="<?= h($currentUser['email']) ?>" readonly title="Email diambil dari data akun Anda" />
            </div>

            <div class="form-group-h2h">
              <label for="message">Pesan Feedback & Saran</label>
              <textarea id="message" name="message" class="form-control-h2h form-control-textarea" rows="5" placeholder="Tuliskan apresiasi, kritik membangun, atau saran fitur untuk website Hearts2Hearts..." required><?= h($_POST['message'] ?? '') ?></textarea>
            </div>

            <button type="submit" class="btn btn-dark" style="width: 100%; padding: 12px; border-radius: var(--radius-pill); font-size: 15px; font-weight: 600;">
              Kirim Feedback
            </button>
          </form>

          <?php if (!empty($userFeedbacks)): ?>
            <div class="feedback-history">
              <h3>Riwayat Feedback Anda</h3>
              <?php foreach ($userFeedbacks as $fb): ?>
                <div class="history-item">
                  <div style="color: #1e293b;"><?= nl2br(h($fb['message'])) ?></div>
                  <div class="history-time"><?= date('d M Y, H:i', strtotime($fb['created_at'])) ?> WIB</div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      <?php endif; ?>

      <div style="text-align: center; margin-top: 28px;">
        <a href="index.php" class="btn btn-pink" style="border-radius: var(--radius-pill);">&larr; Kembali ke Beranda</a>
      </div>
    </div>
  </main>

  <?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
