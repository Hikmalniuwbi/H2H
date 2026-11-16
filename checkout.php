<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$type = $_GET['type'] ?? 'merchandise';
$id = (int)($_GET['id'] ?? 0);

// Jika ada akses ke checkout event, alihkan ke halaman events
if ($type === 'event') {
    header('Location: events.php');
    exit;
}

if ($id <= 0) {
    header('Location: store.php');
    exit;
}

// Todong login: wajib login customer sebelum checkout
$currentUrl = 'checkout.php?type=merchandise&id=' . urlencode($id);
requireLogin($currentUrl);

$currentUser = currentUser();
$db = getDbConnection();
$item = null;

if ($db) {
    $stmt = $db->prepare("SELECT * FROM merchandise WHERE merchandise_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $item = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// Fallback jika belum ada di database (mock data konsisten dengan katalog)
if (!$item) {
    $item = [
        'merchandise_id' => $id,
        'product_name' => 'Official Merchandise Item #' . $id,
        'price' => 150000,
        'stock' => 20,
        'image' => 'assets/product/light_stick.webp',
        'description' => 'Hearts2Hearts Official Merchandise'
    ];
}

$title = $item['product_name'];
$price = (float)$item['price'];
$available = (int)($item['stock'] ?? 0);
$image = $item['image'] ?? 'assets/product/light_stick.webp';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $qty = (int)($_POST['quantity'] ?? 1);
    $name = trim($_POST['recipient_name'] ?? '');
    $phone = trim($_POST['recipient_phone'] ?? '');
    $address = trim($_POST['shipping_address'] ?? '');
    $paymentMethod = trim($_POST['payment_method'] ?? 'bank_transfer');

    if ($qty < 1) {
        $error = 'Jumlah pembelian minimal 1 item.';
    } elseif ($available > 0 && $qty > $available) {
        $error = 'Stok produk yang tersedia tidak mencukupi.';
    } elseif ($name === '' || $phone === '' || $address === '') {
        $error = 'Semua field formulir wajib diisi.';
    } else {
        try {
            $totalAmount = $price * $qty;
            $userId = (int)$currentUser['user_id'];
            $fullAddress = "Nama: $name\nNo. HP: $phone\nAlamat: $address\nMetode Bayar: $paymentMethod";

            // Mulai transaksi database jika koneksi aktif
            $db->begin_transaction();

            $stmtOrder = $db->prepare("INSERT INTO orders (user_id, total_amount, shipping_address, order_status, order_date) VALUES (?, ?, ?, 'pending', NOW())");
            $stmtOrder->bind_param("ids", $userId, $totalAmount, $fullAddress);
            $stmtOrder->execute();
            $orderId = $stmtOrder->insert_id;
            $stmtOrder->close();

            // Simpan detail item pesanan merchandise
            $stmtItem = $db->prepare("INSERT INTO order_items (order_id, merchandise_id, quantity, price, subtotal) VALUES (?, ?, ?, ?, ?)");
            $stmtItem->bind_param("iiidd", $orderId, $id, $qty, $price, $totalAmount);
            $stmtItem->execute();
            $stmtItem->close();

            // Kurangi stok merchandise
            $stmtStock = $db->prepare("UPDATE merchandise SET stock = GREATEST(0, stock - ?) WHERE merchandise_id = ?");
            $stmtStock->bind_param("ii", $qty, $id);
            $stmtStock->execute();
            $stmtStock->close();

            $db->commit();

            header('Location: order_success.php?order_id=' . $orderId);
            exit;
        } catch (Throwable $e) {
            $db->rollback();
            $error = 'Gagal memproses pesanan: ' . $e->getMessage();
        }
    }
}
$currentPage = 'store';
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Checkout — Hearts2Hearts</title>
  <link rel="stylesheet" href="style.css" />
  <style>
    .checkout-container {
      display: grid;
      grid-template-columns: 1fr 1.3fr;
      gap: 32px;
      margin: 40px auto;
      max-width: 960px;
    }
    @media (max-width: 768px) {
      .checkout-container { grid-template-columns: 1fr; }
    }
    .order-summary-card {
      background: var(--white);
      border: 1px solid var(--border-light);
      border-radius: 8px;
      padding: 24px;
      height: fit-content;
    }
    .order-summary-card img {
      width: 100%;
      height: 220px;
      object-fit: cover;
      border-radius: 6px;
      margin-bottom: 16px;
    }
    .form-group {
      margin-bottom: 16px;
    }
    .form-group label {
      display: block;
      font-weight: 600;
      font-size: 14px;
      margin-bottom: 6px;
      color: var(--primary-blue);
    }
    .form-group input, .form-group textarea, .form-group select {
      width: 100%;
      padding: 10px 14px;
      border: 1px solid var(--border-light);
      border-radius: 6px;
      font-size: 14px;
      font-family: inherit;
    }
    .form-group input:focus, .form-group textarea:focus, .form-group select:focus {
      outline: none;
      border-color: var(--secondary-pink);
    }
  </style>
</head>
<body>
  <?php include __DIR__ . '/includes/navbar.php'; ?>

  <section class="page-banner" style="background-color: var(--primary-blue)">
    <h1>Checkout Pembelian</h1>
    <p>Konfirmasi pesanan dan informasi pengiriman Anda.</p>
  </section>

  <main>
    <section class="section-gray">
      <div class="container">
        <?php if ($error): ?>
          <div style="background-color: #fee2e2; color: #991b1b; padding: 14px 20px; border-radius: 6px; margin-bottom: 24px; border: 1px solid #fecaca; max-width: 960px; margin-left: auto; margin-right: auto;">
            <?= h($error) ?>
          </div>
        <?php endif; ?>

        <div class="checkout-container">
          <!-- Item Summary -->
          <div class="order-summary-card">
            <h3>Ringkasan Pesanan</h3>
            <img src="<?= h($image) ?>" alt="<?= h($title) ?>" />
            <span style="font-size: 12px; font-weight: 600; text-transform: uppercase; color: var(--secondary-pink);">
              Merchandise Resmi
            </span>
            <h4 style="margin: 6px 0 12px 0; font-size: 18px;"><?= h($title) ?></h4>
            <p style="font-size: 14px; color: var(--text-light); margin-bottom: 16px;">
              <?= h($item['description'] ?? 'Hearts2Hearts Official Merchandise') ?>
            </p>
            <div style="border-top: 1px dashed var(--border-light); padding-top: 14px; display: flex; justify-content: space-between; align-items: center;">
              <span style="font-size: 14px; color: var(--text-light);">Harga Satuan:</span>
              <strong style="font-size: 16px; color: var(--primary-blue);" id="unitPriceDisplay"><?= formatRupiah($price) ?></strong>
            </div>
            <div style="margin-top: 8px; display: flex; justify-content: space-between; align-items: center;">
              <span style="font-size: 14px; color: var(--text-light);">Tersedia:</span>
              <span style="font-size: 14px; font-weight: 600;"><?= $available ?> pcs</span>
            </div>
            <div style="border-top: 2px solid var(--border-light); margin-top: 16px; padding-top: 14px; display: flex; justify-content: space-between; align-items: center;">
              <span style="font-size: 16px; font-weight: 600;">Total Pembayaran:</span>
              <strong style="font-size: 20px; color: var(--secondary-pink-dark);" id="totalPriceDisplay"><?= formatRupiah($price) ?></strong>
            </div>
          </div>

          <!-- Checkout Form -->
          <div style="background: var(--white); border: 1px solid var(--border-light); border-radius: 8px; padding: 24px;">
            <h3>Data Pembeli & Pengiriman</h3>
            <p style="font-size: 13px; color: var(--text-light); margin-bottom: 20px;">
              Masuk sebagai <strong><?= h($currentUser['name']) ?></strong> (<?= h($currentUser['email']) ?>)
            </p>

            <form method="POST">
              <div class="form-group">
                <label for="quantity">Jumlah (Quantity) *</label>
                <input type="number" id="quantity" name="quantity" min="1" max="<?= max(1, $available) ?>" value="1" required />
              </div>

              <div class="form-group">
                <label for="recipient_name">Nama Lengkap Penerima *</label>
                <input type="text" id="recipient_name" name="recipient_name" value="<?= h($currentUser['name']) ?>" required />
              </div>

              <div class="form-group">
                <label for="recipient_phone">Nomor Telepon / WhatsApp *</label>
                <input type="tel" id="recipient_phone" name="recipient_phone" placeholder="Contoh: 08123456789" required />
              </div>

              <div class="form-group">
                <label for="shipping_address">
                  Alamat Lengkap Pengiriman *
                </label>
                <textarea id="shipping_address" name="shipping_address" rows="3" placeholder="Jalan, Nomor Rumah, RT/RW, Kecamatan, Kota, Kode Pos" required></textarea>
              </div>

              <div class="form-group">
                <label for="payment_method">Metode Pembayaran *</label>
                <select id="payment_method" name="payment_method" required>
                  <option value="BCA Virtual Account">Transfer BCA Virtual Account</option>
                  <option value="Mandiri Virtual Account">Transfer Mandiri Virtual Account</option>
                  <option value="QRIS / GoPay / OVO">QRIS (GoPay / OVO / Dana / ShopeePay)</option>
                  <option value="Kartu Kredit">Kartu Kredit / Debit Online</option>
                </select>
              </div>

              <div style="margin-top: 24px;">
                <button type="submit" class="btn btn-pink" style="width: 100%; text-align: center; padding: 12px; font-size: 16px;">
                  Konfirmasi & Bayar Sekarang
                </button>
                <a href="store.php" style="display: block; text-align: center; margin-top: 12px; font-size: 14px; color: var(--text-light);">
                  &larr; Batal & Kembali ke Store
                </a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </section>
  </main>

  <?php include __DIR__ . '/includes/footer.php'; ?>

  <script>
    const unitPrice = <?= $price ?>;
    const qtyInput = document.getElementById('quantity');
    const totalDisplay = document.getElementById('totalPriceDisplay');

    function updateTotal() {
      const q = parseInt(qtyInput.value) || 1;
      const total = unitPrice * q;
      totalDisplay.textContent = 'Rp ' + total.toLocaleString('id-ID');
    }

    if (qtyInput) {
      qtyInput.addEventListener('input', updateTotal);
      qtyInput.addEventListener('change', updateTotal);
    }
  </script>
</body>
</html>
