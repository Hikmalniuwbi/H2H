<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/config/tables.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

// Protect admin access
requireAdmin();

$conn = getDbConnection();
$table = $_GET['table'] ?? '';
$id = (int)($_GET['id'] ?? 0);

if (!validTable($table, $tables) || $id <= 0) {
    exit('Invalid request.');
}

$config = $tables[$table];
$pk = $config['pk'];

$stmt = $conn->prepare("SELECT * FROM `$table` WHERE `$pk` = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$data) {
    exit('Record not found.');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $values = [];
        $types = '';
        $sets = [];

        foreach ($config['fields'] as $column => $field) {
            if (in_array($field['type'], ['image', 'audio'])) {
                $uploaded = uploadFile($column, $field);
                $value = $uploaded ?? $data[$column];
            } else {
                $value = trim($_POST[$column] ?? '');

                if ($field['type'] === 'password') {
                    if ($value === '') {
                        $value = $data[$column];
                    } else {
                        $value = password_hash($value, PASSWORD_DEFAULT);
                    }
                } else {
                    if ($value === '' && !($field['required'] ?? false)) {
                        $value = null;
                    }
                    if (($field['required'] ?? false) && ($value === null || $value === '')) {
                        throw new RuntimeException($field['label'] . ' is required.');
                    }
                    if ($field['type'] === 'datetime' && $value !== null) {
                        $value = str_replace('T', ' ', $value) . (strlen($value) === 16 ? ':00' : '');
                    }
                }
            }

            $sets[] = "`$column` = ?";
            $types .= is_int($value) ? 'i' : (is_float($value) ? 'd' : 's');
            $values[] = $value;
        }

        $types .= 'i';
        $values[] = $id;

        $sql = "UPDATE `$table` SET " . implode(', ', $sets) . " WHERE `$pk` = ?";
        $stmt = $conn->prepare($sql);
        bindValues($stmt, $types, $values);
        $stmt->execute();
        $stmt->close();

        header("Location: index.php?page=list&table=" . urlencode($table) . "&status=updated");
        exit;
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}
$currentUser = currentUser();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Edit <?= h($config['label']) ?> — H2H Admin</title>
  <link rel="stylesheet" href="assets/adminlte.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
  <style>
    body { font-family: system-ui, -apple-system, sans-serif; }
    .thumb { width: 80px; height: 80px; object-fit: cover; border-radius: 6px; }
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
            <a href="dashboard.php" class="nav-link">
              <i class="nav-icon bi bi-speedometer2"></i>
              <p>Dashboard</p>
            </a>
          </li>
          <li class="nav-header">DATA MANAGEMENT</li>
          <?php foreach ($tables as $name => $item): ?>
            <li class="nav-item">
              <a href="index.php?page=list&table=<?= h($name) ?>" class="nav-link <?= ($table === $name) ? 'active' : '' ?>">
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
          <div class="col-sm-8"><h3>Edit <?= h($config['label']) ?> (#<?= h($id) ?>)</h3></div>
          <div class="col-sm-4">
            <ol class="breadcrumb float-sm-end">
              <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
              <li class="breadcrumb-item"><a href="index.php?page=list&table=<?= h($table) ?>"><?= h($config['label']) ?></a></li>
              <li class="breadcrumb-item active">Edit</li>
            </ol>
          </div>
        </div>
      </div>
    </div>
    <div class="app-content">
      <div class="container-fluid">
        <?php if ($error): ?>
          <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle me-2"></i><?= h($error) ?>
            <button class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        <?php endif; ?>

        <div class="card card-warning card-outline">
          <div class="card-header">
            <h3 class="card-title">Edit Record Form</h3>
          </div>
          <form method="post" enctype="multipart/form-data">
            <div class="card-body">
              <div class="row">
                <?php foreach ($config['fields'] as $col => $f):
                  $val = $data[$col] ?? '';
                  $type = inputType($f);
                ?>
                  <div class="col-md-6 mb-3">
                    <label class="form-label" for="f_<?= h($col) ?>">
                      <?= h($f['label']) ?> <?= ($f['required'] ?? false) ? '<span class="text-danger">*</span>' : '' ?>
                    </label>

                    <?php if ($type === 'textarea'): ?>
                      <textarea class="form-control" id="f_<?= h($col) ?>" name="<?= h($col) ?>" rows="3" <?= ($f['required'] ?? false) ? 'required' : '' ?>><?= h($val) ?></textarea>

                    <?php elseif ($type === 'fk'): ?>
                      <select class="form-select" id="f_<?= h($col) ?>" name="<?= h($col) ?>" <?= ($f['required'] ?? false) ? 'required' : '' ?>>
                        <option value="">-- Select --</option>
                        <?php foreach (fkOptions($conn, $f) as $o): ?>
                          <option value="<?= h($o[$f['key']]) ?>" <?= ($o[$f['key']] == $val) ? 'selected' : '' ?>>
                            <?= h($o[$f['display']]) ?>
                          </option>
                        <?php endforeach; ?>
                      </select>

                    <?php elseif ($type === 'enum'): ?>
                      <select class="form-select" name="<?= h($col) ?>">
                        <?php foreach ($f['options'] as $o): ?>
                          <option value="<?= h($o) ?>" <?= ($o === $val) ? 'selected' : '' ?>><?= h(ucfirst($o)) ?></option>
                        <?php endforeach; ?>
                      </select>

                    <?php elseif ($f['type'] === 'image'): ?>
                      <?php if ($val): ?>
                        <div class="mb-2">
                          <img class="thumb" src="../<?= h($val) ?>" alt="<?= h($f['label']) ?>">
                        </div>
                      <?php endif; ?>
                      <input class="form-control" type="file" id="f_<?= h($col) ?>" name="<?= h($col) ?>" accept="image/jpeg,image/png,image/webp">
                      <div class="small text-muted">Leave blank if you do not want to change the image.</div>

                    <?php elseif ($f['type'] === 'audio'): ?>
                      <?php if ($val): ?>
                        <div class="mb-2">
                          <audio controls><source src="../<?= h($val) ?>"></audio>
                        </div>
                      <?php endif; ?>
                      <input class="form-control" type="file" id="f_<?= h($col) ?>" name="<?= h($col) ?>" accept="audio/mpeg,audio/wav,audio/ogg,audio/mp4">
                      <div class="small text-muted">Leave blank if you do not want to change the audio file.</div>

                    <?php elseif ($f['type'] === 'password'): ?>
                      <input class="form-control" type="password" id="f_<?= h($col) ?>" name="<?= h($col) ?>" placeholder="Fill only if you want to change password">

                    <?php else: ?>
                      <input class="form-control" type="<?= h($type) ?>" id="f_<?= h($col) ?>" name="<?= h($col) ?>" value="<?= h($val) ?>" <?= isset($f['step']) ? 'step="' . h($f['step']) . '"' : '' ?> <?= ($f['required'] ?? false) ? 'required' : '' ?>>
                    <?php endif; ?>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
            <div class="card-footer">
              <button type="submit" class="btn btn-warning"><i class="bi bi-save me-1"></i> Save Changes</button>
              <a href="index.php?page=list&table=<?= h($table) ?>" class="btn btn-secondary">Cancel</a>
            </div>
          </form>
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
