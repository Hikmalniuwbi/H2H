<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$message = '';
$error = '';
$currentUser = currentUser();

// Authentication guard: require login for POST submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isLoggedIn()) {
        header('Location: login.php?redirect=feedback.php&notice=feedback_login_required');
        exit;
    }

    $userId = (int)$currentUser['user_id'];
    $name = trim(!empty($_POST['name']) ? $_POST['name'] : $currentUser['name']);
    $email = trim(!empty($_POST['email']) ? $_POST['email'] : $currentUser['email']);
    $feedbackMsg = trim($_POST['message'] ?? '');

    if ($feedbackMsg === '') {
        $error = 'Feedback message cannot be empty.';
    } else {
        $db = getDbConnection();
        if ($db) {
            $stmt = $db->prepare("INSERT INTO feedbacks (user_id, name, email, message, created_at) VALUES (?, ?, ?, ?, NOW())");
            if ($stmt) {
                $stmt->bind_param("isss", $userId, $name, $email, $feedbackMsg);
                if ($stmt->execute()) {
                    $message = 'Thank you for your feedback! Your message has been received and will be reviewed by the Hearts2Hearts team.';
                } else {
                    $error = 'Failed to save feedback: ' . $stmt->error;
                }
                $stmt->close();
            } else {
                $error = 'A system error occurred while processing your feedback.';
            }
        } else {
            $error = 'Database connection failed. Please ensure MySQL is running.';
        }
    }
}

// Fetch feedback history for this user
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
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Fan Feedback — Hearts2Hearts</title>
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
    <h1>Fan Feedback</h1>
    <p>We truly value thoughts, feedback, and suggestions from S2U.</p>
  </section>

  <main>
    <div class="feedback-container">
      <?php if ($message): ?>
        <div style="background-color: #dcfce7; color: #166534; padding: 18px 24px; border-radius: 12px; margin-bottom: 24px; border: 1px solid #bbf7d0; text-align: center;">
          <h3 style="margin-bottom: 6px; font-size: 18px;">Message Sent!</h3>
          <p style="margin: 0; font-size: 14px;"><?= h($message) ?></p>
        </div>
      <?php endif; ?>

      <?php if ($error): ?>
        <div style="background-color: #fee2e2; color: #991b1b; padding: 16px 20px; border-radius: 12px; margin-bottom: 24px; border: 1px solid #fecaca; text-align: center;">
          <p style="margin: 0; font-size: 14px;"><?= h($error) ?></p>
        </div>
      <?php endif; ?>

      <?php if (!isLoggedIn()): ?>
        <!-- Login requirement for visitors without an active session -->
        <div class="auth-alert-box">
          <div style="font-size: 44px; margin-bottom: 12px;">💌</div>
          <h2 style="font-family: var(--font-heading); font-size: 24px; margin-bottom: 10px; color: var(--heading-color);">
            Exclusive for S2U Members
          </h2>
          <p style="color: var(--text-light); line-height: 1.6; margin-bottom: 24px; font-size: 14px;">
            Only registered fan members can submit feedback for Hearts2Hearts. Please log in or create a new account to continue.
          </p>
          <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
            <a href="login.php?redirect=feedback.php&notice=feedback_login_required" class="btn btn-dark" style="padding: 10px 24px; border-radius: 50px;">
              Log In Now
            </a>
            <a href="register.php?redirect=feedback.php&notice=feedback_login_required" class="btn btn-pink" style="padding: 10px 24px; border-radius: 50px;">
              Register New Account
            </a>
          </div>
        </div>
      <?php else: ?>
        <!-- Feedback form for authenticated S2U fan users -->
        <div class="feedback-card">
          <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 12px; padding: 14px 18px; margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
            <div>
              <span style="font-size: 12px; color: #1e40af; font-weight: 600; text-transform: uppercase;">Connected as S2U Member</span>
              <div style="font-size: 15px; font-weight: 600; color: #1e3a8a; margin-top: 2px;">
                <?= h($currentUser['name']) ?> (<?= h($currentUser['email']) ?>)
              </div>
            </div>
            <span style="background: #dbeafe; color: #1d4ed8; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">S2U Verified</span>
          </div>

          <form action="feedback.php" method="POST">
            <div class="form-group-h2h">
              <label for="name">Fan Name</label>
              <input type="text" id="name" name="name" class="form-control-h2h form-control-readonly" value="<?= h($currentUser['name']) ?>" readonly title="Prefilled from your account profile" />
            </div>

            <div class="form-group-h2h">
              <label for="email">Fan Email</label>
              <input type="email" id="email" name="email" class="form-control-h2h form-control-readonly" value="<?= h($currentUser['email']) ?>" readonly title="Prefilled from your account profile" />
            </div>

            <div class="form-group-h2h">
              <label for="message">Feedback & Suggestions</label>
              <textarea id="message" name="message" class="form-control-h2h form-control-textarea" rows="5" placeholder="Share your appreciation, constructive thoughts, or feature suggestions for Hearts2Hearts..." required><?= h($_POST['message'] ?? '') ?></textarea>
            </div>

            <button type="submit" class="btn btn-dark" style="width: 100%; padding: 12px; border-radius: var(--radius-pill); font-size: 15px; font-weight: 600;">
              Submit Feedback
            </button>
          </form>

          <?php if (!empty($userFeedbacks)): ?>
            <div class="feedback-history">
              <h3>Your Feedback History</h3>
              <?php foreach ($userFeedbacks as $fb): ?>
                <div class="history-item">
                  <div style="color: #1e293b;"><?= nl2br(h($fb['message'])) ?></div>
                  <div class="history-time"><?= date('d M Y, H:i', strtotime($fb['created_at'])) ?></div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      <?php endif; ?>

      <div style="text-align: center; margin-top: 28px;">
        <a href="index.php" class="btn btn-pink" style="border-radius: var(--radius-pill);">&larr; Back to Home</a>
      </div>
    </div>
  </main>

  <?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
