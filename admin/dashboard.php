<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/config/tables.php';
global $tables;
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

// Protect admin access
requireAdmin();

$counts = [];
$totalRecords = 0;
$db = getDbConnection();
if ($db) {
    foreach ($tables as $name => $item) {
        $r = $db->query("SELECT COUNT(*) AS total FROM `$name`");
        $val = $r ? (int)$r->fetch_assoc()['total'] : 0;
        $counts[$name] = $val;
    }
    $totalRecords = array_sum($counts);

    $recentFeedbacks = [];
    $fbQuery = $db->query("SELECT f.*, u.name as user_name FROM feedbacks f LEFT JOIN users u ON f.user_id = u.user_id ORDER BY f.feedback_id DESC LIMIT 5");
    if ($fbQuery) {
        while ($row = $fbQuery->fetch_assoc()) {
            $recentFeedbacks[] = $row;
        }
    }
}

$currentUser = currentUser();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard — H2H Admin</title>
  <link rel="stylesheet" href="assets/adminlte.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
  <style>
    body { font-family: system-ui, -apple-system, sans-serif; }
    .table td, .table th { vertical-align: middle; }
  </style>
</head>
<body class="layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary">
<div class="app-wrapper">
  <!-- Navbar -->
  <nav class="app-header navbar navbar-expand bg-body">
    <div class="container-fluid">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" data-lte-toggle="sidebar" href="#"><i class="bi bi-list"></i></a>
        </li>
        <li class="nav-item">
          <span class="nav-link fw-semibold">Hearts2Hearts Administration</span>
        </li>
      </ul>
      <ul class="navbar-nav ms-auto">
        <li class="nav-item me-3">
          <span class="nav-link"><i class="bi bi-person-circle me-1"></i> <?= h($currentUser['name'] ?? 'Admin') ?></span>
        </li>
        <li class="nav-item me-2">
          <a href="../index.php" target="_blank" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-globe me-1"></i> Main Website
          </a>
        </li>
        <li class="nav-item">
          <a href="logout.php" class="btn btn-danger btn-sm">
            <i class="bi bi-box-arrow-right me-1"></i> Logout
          </a>
        </li>
      </ul>
    </div>
  </nav>

  <!-- Sidebar -->
  <aside class="app-sidebar bg-dark" data-bs-theme="dark">
    <div class="sidebar-brand">
      <a href="dashboard.php" class="brand-link">
        <img src="assets/AdminLTELogo.png" class="brand-image opacity-75 shadow" alt="AdminLTE">
        <span class="brand-text fw-light">H2H Admin</span>
      </a>
    </div>
    <div class="sidebar-wrapper">
      <nav class="mt-2">
        <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu">
          <li class="nav-item">
            <a href="dashboard.php" class="nav-link active">
              <i class="nav-icon bi bi-speedometer2"></i>
              <p>Dashboard</p>
            </a>
          </li>
          <li class="nav-header">DATA MANAGEMENT</li>
          <?php foreach ($tables as $name => $item): ?>
            <li class="nav-item">
              <a href="index.php?page=list&table=<?= h($name) ?>" class="nav-link">
                <i class="nav-icon bi bi-table"></i>
                <p><?= h($item['label']) ?></p>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </nav>
    </div>
  </aside>

  <!-- Content -->
  <main class="app-main">
    <div class="app-content-header">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6"><h3>Dashboard</h3></div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-end">
              <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
              <li class="breadcrumb-item active">Dashboard</li>
            </ol>
          </div>
        </div>
      </div>
    </div>
    <div class="app-content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-lg-3 col-6">
            <div class="small-box text-bg-primary">
              <div class="inner">
                <h3><?= count($tables) ?></h3>
                <p>Total Table Modules</p>
              </div>
              <i class="small-box-icon bi bi-database"></i>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="small-box text-bg-success">
              <div class="inner">
                <h3><?= $totalRecords ?></h3>
                <p>Total Saved Records</p>
              </div>
              <i class="small-box-icon bi bi-collection"></i>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="small-box text-bg-warning">
              <div class="inner">
                <h3><?= $counts['feedbacks'] ?? 0 ?></h3>
                <p>S2U Fan Feedbacks</p>
              </div>
              <i class="small-box-icon bi bi-chat-heart"></i>
            </div>
          </div>
        </div>

        <!-- Recent Fan Feedback Card -->
        <div class="card mb-4">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0"><i class="bi bi-chat-quote me-2"></i>Recent Fan Feedback (S2U)</h3>
            <a href="index.php?page=list&table=feedbacks" class="btn btn-sm btn-outline-primary">View All Feedback</a>
          </div>
          <div class="card-body table-responsive p-0">
            <?php if (!empty($recentFeedbacks)): ?>
              <table class="table table-hover table-striped mb-0">
                <thead>
                  <tr>
                    <th>Date & Time</th>
                    <th>Fan Name</th>
                    <th>Email</th>
                    <th>Message</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($recentFeedbacks as $fb): ?>
                    <tr>
                      <td style="white-space: nowrap;"><?= date('d M Y, H:i', strtotime($fb['created_at'])) ?></td>
                      <td>
                        <strong><?= h($fb['name']) ?></strong>
                        <br><small class="text-muted">User ID: #<?= h($fb['user_id']) ?></small>
                      </td>
                      <td><?= h($fb['email']) ?></td>
                      <td><?= nl2br(h($fb['message'])) ?></td>
                      <td>
                        <a class="btn btn-sm btn-primary" href="edit.php?table=feedbacks&id=<?= h($fb['feedback_id']) ?>">
                          <i class="bi bi-pencil-square"></i> Detail / Edit
                        </a>
                        <a class="btn btn-sm btn-outline-secondary" href="index.php?page=list&table=feedbacks">
                          <i class="bi bi-list-ul"></i> Manage
                        </a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            <?php else: ?>
              <div class="p-4 text-center text-muted">
                No feedback submitted by fans yet.
              </div>
            <?php endif; ?>
          </div>
        </div>

        <!-- Database Summary Card -->
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">H2H Database Summary</h3>
          </div>
          <div class="card-body table-responsive">
            <table class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>Table</th>
                  <th>Total Records</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($tables as $name => $item): ?>
                  <tr>
                    <td><strong><?= h($item['label']) ?></strong> (<code><?= h($name) ?></code>)</td>
                    <td><?= $counts[$name] ?? 0 ?> records</td>
                    <td>
                      <a class="btn btn-sm btn-primary" href="index.php?page=list&table=<?= h($name) ?>">
                        <i class="bi bi-eye"></i> View Records
                      </a>
                      <a class="btn btn-sm btn-success" href="index.php?page=create&table=<?= h($name) ?>">
                        <i class="bi bi-plus"></i> Add New
                      </a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </main>
</div>
<script src="assets/bootstrap.bundle.min.js"></script>
<script>
  if (typeof bootstrap === 'undefined') {
    document.write('<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"><\/script>');
  }
</script>
<script src="assets/adminlte.min.js"></script>
</body>
</html>
