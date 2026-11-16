<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

requireLogin('orders.php');
$currentUser = currentUser();
$db = getDbConnection();

$orderId = (int)($_GET['order_id'] ?? 0);
$order = null;

if ($db && $orderId > 0) {
    $stmt = $db->prepare("SELECT * FROM orders WHERE order_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $orderId, $currentUser['user_id']);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}
$currentPage = 'orders';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Order Confirmed — Hearts2Hearts</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
  <?php include __DIR__ . '/includes/navbar.php'; ?>

  <section class="page-banner" style="background-color: var(--primary-blue)">
    <h1>Order Successful</h1>
    <p>Thank you for supporting Hearts2Hearts!</p>
  </section>

  <main>
    <section class="section-white">
      <div class="container" style="max-width: 600px; text-align: center; padding: 40px 20px;">
        <div style="font-size: 54px; color: #16a34a; margin-bottom: 16px;">✓</div>
        <h2>Your Order Has Been Placed!</h2>
        <p style="color: var(--text-light); margin-bottom: 24px;">
          Order Number: <strong style="color: var(--primary-blue);">#<?= h($orderId) ?></strong>
        </p>

        <?php if ($order): 
          $orderStatus = $order['order_status'] ?? $order['status'] ?? 'pending';
          $orderDate = $order['order_date'] ?? $order['created_at'] ?? '';
          $formattedDate = $orderDate ? date('d M Y, H:i', strtotime($orderDate)) : '-';
        ?>
          <div style="background: var(--light-gray); border-radius: 8px; padding: 20px; text-align: left; margin-bottom: 24px;">
            <p style="margin: 4px 0;"><strong>Total Amount:</strong> <?= formatRupiah($order['total_amount']) ?></p>
            <p style="margin: 4px 0;"><strong>Status:</strong> <span style="text-transform: capitalize; color: #d97706; font-weight: 600;"><?= h($orderStatus) ?></span></p>
            <p style="margin: 4px 0;"><strong>Date:</strong> <?= h($formattedDate) ?></p>
          </div>
        <?php endif; ?>

        <p style="font-size: 14px; color: var(--text-light); line-height: 1.6; margin-bottom: 30px;">
          Payment instructions and e-ticket / shipping tracking details have been sent to your registered email address.
        </p>

        <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
          <a href="orders.php" class="btn btn-pink">View Order History</a>
          <a href="store.php" class="btn btn-outline">Continue Shopping</a>
        </div>
      </div>
    </section>
  </main>

  <?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
