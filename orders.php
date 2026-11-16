<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

requireLogin('orders.php');
$currentUser = currentUser();
$db = getDbConnection();

$ordersList = [];

if ($db) {
    $userId = (int)$currentUser['user_id'];
    $sql = "
        SELECT o.*, 
               o.order_date AS created_at,
               o.order_status AS status,
               GROUP_CONCAT(COALESCE(m.product_name, 'Merchandise Item') SEPARATOR ', ') as item_names
        FROM orders o
        LEFT JOIN order_items oi ON o.order_id = oi.order_id
        LEFT JOIN merchandise m ON oi.merchandise_id = m.merchandise_id
        WHERE o.user_id = ?
        GROUP BY o.order_id
        ORDER BY o.order_date DESC
    ";
    $stmt = $db->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($r = $res->fetch_assoc()) {
            $ordersList[] = $r;
        }
        $stmt->close();
    }
}
$currentPage = 'orders';
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Riwayat Pesanan — Hearts2Hearts</title>
  <link rel="stylesheet" href="style.css" />
  <style>
    .status-badge {
      display: inline-block;
      padding: 4px 10px;
      border-radius: 12px;
      font-size: 12px;
      font-weight: 600;
      text-transform: capitalize;
    }
    .status-pending { background: #fef3c7; color: #b45309; }
    .status-completed { background: #dcfce7; color: #15803d; }
    .status-cancelled { background: #fee2e2; color: #b91c1c; }
  </style>
</head>
<body>
  <?php include __DIR__ . '/includes/navbar.php'; ?>

  <section class="page-banner" style="background-color: var(--primary-blue)">
    <h1>Riwayat Pesanan</h1>
    <p>Kelola dan pantau status pembelian tiket dan merchandise Anda.</p>
  </section>

  <main>
    <section class="section-white">
      <div class="container" style="max-width: 900px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
          <div>
            <h3>Daftar Transaksi</h3>
            <p style="font-size: 14px; color: var(--text-light); margin: 0;">
              User: <strong><?= h($currentUser['name']) ?></strong> (<?= h($currentUser['email']) ?>)
            </p>
          </div>
          <div>
            <a href="store.php" class="btn btn-pink btn-small">+ Beli Merchandise</a>
            <a href="events.php" class="btn btn-outline btn-small">+ Beli Tiket</a>
          </div>
        </div>

        <?php if (empty($ordersList)): ?>
          <div style="text-align: center; padding: 60px 20px; background: var(--light-gray); border-radius: 8px;">
            <p style="font-size: 16px; color: var(--text-light); margin-bottom: 16px;">
              Anda belum memiliki riwayat pesanan.
            </p>
            <a href="store.php" class="btn btn-pink btn-small">Mulai Belanja di Official Store</a>
          </div>
        <?php else: ?>
          <div style="overflow-x: auto;">
            <table class="events-table">
              <thead>
                <tr>
                  <th>No. Order</th>
                  <th>Tanggal</th>
                  <th>Item</th>
                  <th>Total Bayar</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($ordersList as $ord): 
                  $st = strtolower($ord['status'] ?? 'pending');
                  $badgeClass = 'status-' . $st;
                  if (!in_array($badgeClass, ['status-pending', 'status-completed', 'status-cancelled'])) {
                      $badgeClass = 'status-pending';
                  }
                ?>
                  <tr>
                    <td><strong>#<?= h($ord['order_id']) ?></strong></td>
                    <td style="font-size: 13px; color: var(--text-light);">
                      <?= h(date('d M Y, H:i', strtotime($ord['created_at']))) ?>
                    </td>
                    <td>
                      <div style="max-width: 320px; font-size: 14px;">
                        <?= h($ord['item_names'] ?: 'Detail Item') ?>
                      </div>
                    </td>
                    <td style="font-weight: 600; color: var(--primary-blue);">
                      <?= formatRupiah($ord['total_amount']) ?>
                    </td>
                    <td>
                      <span class="status-badge <?= $badgeClass ?>">
                        <?= h($ord['status']) ?>
                      </span>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>
    </section>
  </main>

  <?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
