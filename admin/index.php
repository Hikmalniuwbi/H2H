<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/config/tables.php';
global $tables;
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

// Protect admin access
requireAdmin();

global $conn, $table, $config, $pk, $page, $keyword, $filterVal, $perPage, $sortCol, $sortOrder, $halaman, $totalData, $totalHalaman, $offset, $awalData, $akhirData;

$conn = getDbConnection();
$table = $_GET['table'] ?? 'albums';
if (!validTable($table, $tables)) $table = 'albums';
$config = $tables[$table];
$pk = $config['pk'];
$page = $_GET['page'] ?? 'list';
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'create') {
    try {
        if (!$conn) throw new RuntimeException('Database connection is unavailable.');
        $data = [];
        $types = '';
        $values = [];
        foreach ($config['fields'] as $column => $field) {
            if (in_array($field['type'], ['image', 'audio'])) {
                $uploaded = uploadFile($column, $field);
                $value = $uploaded;
                if ($value === null && ($field['required'] ?? false)) throw new RuntimeException($field['label'] . ' must be uploaded.');
            } else {
                $value = trim($_POST[$column] ?? '');
                if ($field['type'] === 'password') {
                    if ($value === '') throw new RuntimeException('Password is required.');
                    $value = password_hash($value, PASSWORD_DEFAULT);
                } else {
                    if ($value === '' && !($field['required'] ?? false)) $value = null;
                    if (($field['required'] ?? false) && ($value === null || $value === '')) throw new RuntimeException($field['label'] . ' is required.');
                    if ($field['type'] === 'datetime' && $value !== null) {
                        $value = str_replace('T', ' ', $value) . (strlen($value) === 16 ? ':00' : '');
                    }
                }
            }
            $data[$column] = $value;
            $types .= is_int($value) ? 'i' : (is_float($value) ? 'd' : 's');
            $values[] = $value;
        }

        $columnsSql = '`' . implode('`, `', array_keys($data)) . '`';
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $stmt = $conn->prepare("INSERT INTO `$table` ($columnsSql) VALUES ($placeholders)");
        bindValues($stmt, $types, $values);
        $stmt->execute();
        $stmt->close();
        header("Location: index.php?page=list&table=" . urlencode($table) . "&status=created");
        exit;
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}

$status = $_GET['status'] ?? '';
if ($status === 'created') {
    $message = 'Data successfully added.';
} elseif ($status === 'updated') {
    $message = 'Changes saved successfully.';
} elseif ($status === 'deleted') {
    $message = 'Data successfully deleted.';
} elseif ($status === 'delete_error') {
    $error = 'Data cannot be deleted because it is referenced by other records.';
}

// Auto-detect contextual filter column (enum, foreign key, or category/status)
$filterCol = '';
$filterOptionsList = [];
foreach ($config['fields'] as $colName => $colDef) {
    if ($colDef['type'] === 'enum' && !empty($colDef['options'])) {
        $filterCol = $colName;
        foreach ($colDef['options'] as $opt) {
            $filterOptionsList[$opt] = ucfirst($opt);
        }
        break;
    }
}
if (empty($filterCol)) {
    foreach ($config['fields'] as $colName => $colDef) {
        if ($colDef['type'] === 'fk' && $conn) {
            $filterCol = $colName;
            $fkTarget = $colDef['table'];
            $fkKey = $colDef['key'];
            $fkDisplay = $colDef['display'];
            $fkRes = $conn->query("SELECT `$fkKey`, `$fkDisplay` FROM `$fkTarget` ORDER BY `$fkDisplay` ASC");
            if ($fkRes) {
                while ($rowFk = $fkRes->fetch_assoc()) {
                    $filterOptionsList[$rowFk[$fkKey]] = $rowFk[$fkDisplay];
                }
            }
            break;
        }
    }
}
if (empty($filterCol)) {
    foreach (['order_status', 'tagline', 'album_type', 'position', 'nationality'] as $cand) {
        if (isset($config['fields'][$cand]) && $conn) {
            $filterCol = $cand;
            $candRes = $conn->query("SELECT DISTINCT `$cand` FROM `$table` WHERE `$cand` IS NOT NULL AND `$cand` <> '' ORDER BY `$cand` ASC");
            if ($candRes) {
                while ($rowCand = $candRes->fetch_assoc()) {
                    $filterOptionsList[$rowCand[$cand]] = $rowCand[$cand];
                }
            }
            break;
        }
    }
}

// Parameters: keyword, filter, sort, order, pagination
$keyword = trim((string)($_GET['keyword'] ?? ''));
$filterVal = trim((string)($_GET['filter'] ?? ''));
if ($filterCol !== '' && $filterVal !== '') {
    $found = false;
    foreach ($filterOptionsList as $k => $v) {
        if ((string)$k === (string)$filterVal) {
            $found = true;
            break;
        }
    }
    if (!$found) $filterVal = '';
}

$sortCol = trim((string)($_GET['sort'] ?? $pk));
if ($sortCol !== $pk && !isset($config['fields'][$sortCol])) {
    $sortCol = $pk;
}
$sortOrder = strtoupper(trim((string)($_GET['order'] ?? 'DESC')));
if (!in_array($sortOrder, ['ASC', 'DESC'], true)) {
    $sortOrder = 'DESC';
}

$allowedPerPage = [5, 10, 15, 25, 50];
$perPage = filter_var($_GET['per_page'] ?? 10, FILTER_VALIDATE_INT);
if ($perPage === false || $perPage < 1 || $perPage > 100) {
    $perPage = 10;
}

$halamanDiminta = filter_var($_GET['halaman'] ?? 1, FILTER_VALIDATE_INT);
$halamanDiminta = ($halamanDiminta !== false && $halamanDiminta > 0) ? $halamanDiminta : 1;

// Text-searchable columns
$searchableCols = [];
foreach ($config['fields'] as $colName => $colDef) {
    if (in_array($colDef['type'], ['text', 'textarea', 'email', 'number', 'datetime', 'date'])) {
        $searchableCols[] = $colName;
    }
}
if (empty($searchableCols)) {
    $searchableCols = array_keys($config['fields']);
}

// Build WHERE clause
$whereClauses = [];
$params = [];
$paramTypes = '';

if ($keyword !== '') {
    $searchSub = [];
    $searchSub[] = "`$pk` LIKE ?";
    $params[] = '%' . $keyword . '%';
    $paramTypes .= 's';
    foreach ($searchableCols as $sc) {
        $searchSub[] = "`$sc` LIKE ?";
        $params[] = '%' . $keyword . '%';
        $paramTypes .= 's';
    }
    $whereClauses[] = '(' . implode(' OR ', $searchSub) . ')';
}

if ($filterCol !== '' && $filterVal !== '') {
    $whereClauses[] = "`$filterCol` = ?";
    $params[] = $filterVal;
    $paramTypes .= 's';
}

$whereSql = '';
if (!empty($whereClauses)) {
    $whereSql = ' WHERE ' . implode(' AND ', $whereClauses);
}

// Total records count
$totalData = 0;
if ($conn && $page === 'list') {
    $countSql = "SELECT COUNT(*) AS total FROM `$table`" . $whereSql;
    if (!empty($params)) {
        $countStmt = $conn->prepare($countSql);
        bindValues($countStmt, $paramTypes, $params);
        $countStmt->execute();
        $cResult = $countStmt->get_result();
        if ($cResult) {
            $totalData = (int)($cResult->fetch_assoc()['total'] ?? 0);
        }
        $countStmt->close();
    } else {
        $cResult = $conn->query($countSql);
        if ($cResult) {
            $totalData = (int)($cResult->fetch_assoc()['total'] ?? 0);
        }
    }
}

$totalHalaman = max(1, (int)ceil($totalData / $perPage));
$halaman = min($halamanDiminta, $totalHalaman);
$offset = ($halaman - 1) * $perPage;
$awalData = $totalData > 0 ? $offset + 1 : 0;
$akhirData = min($offset + $perPage, $totalData);

// Fetch paginated rows
$rows = [];
$columns = array_keys($config['fields']);
if ($conn && $page === 'list') {
    $querySql = "SELECT * FROM `$table`" . $whereSql . " ORDER BY `$sortCol` $sortOrder LIMIT ? OFFSET ?";
    $queryParams = array_merge($params, [$perPage, $offset]);
    $queryTypes = $paramTypes . 'ii';
    $stmt = $conn->prepare($querySql);
    bindValues($stmt, $queryTypes, $queryParams);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result) {
        while ($r = $result->fetch_assoc()) $rows[] = $r;
    }
    $stmt->close();
}

$counts = [];
if ($conn) {
    foreach ($tables as $name => $item) {
        $r = $conn->query("SELECT COUNT(*) AS total FROM `$name`");
        $counts[$name] = $r ? (int)$r->fetch_assoc()['total'] : 0;
    }
}
$currentUser = currentUser();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= h($config['label']) ?> — H2H Admin</title>
  <link rel="stylesheet" href="assets/adminlte.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
  <style>
    body { font-family: system-ui, -apple-system, sans-serif; }
    .table td, .table th { vertical-align: middle; }
    .thumb { width: 64px; height: 64px; object-fit: cover; border-radius: 6px; }
    .field-hint { font-size: .82rem; color: #6c757d; }
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
          <div class="col-sm-8"><h3><?= h($config['label']) ?></h3></div>
          <div class="col-sm-4">
            <ol class="breadcrumb float-sm-end">
              <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
              <li class="breadcrumb-item active"><?= h($config['label']) ?></li>
            </ol>
          </div>
        </div>
      </div>
    </div>
    <div class="app-content">
      <div class="container-fluid">
        <?php if ($message): ?>
          <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-2"></i><?= h($message) ?>
            <button class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        <?php endif; ?>
        <?php if ($error): ?>
          <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle me-2"></i><?= h($error) ?>
            <button class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        <?php endif; ?>

        <?php if ($page === 'list'): ?>
          <div class="card mb-4">
            <div class="card-header">
              <div class="row g-2 align-items-center">
                <div class="col-12 col-xl-3">
                  <h3 class="card-title mb-0"><?= h($config['label']) ?> Directory</h3>
                </div>
                <div class="col-12 col-xl-9">
                  <div class="d-flex flex-wrap justify-content-xl-end align-items-center gap-2">
                    <form method="get" action="index.php" class="d-flex flex-wrap align-items-center gap-2 m-0">
                      <input type="hidden" name="page" value="list">
                      <input type="hidden" name="table" value="<?= h($table) ?>">
                      <input type="hidden" name="sort" value="<?= h($sortCol) ?>">
                      <input type="hidden" name="order" value="<?= h($sortOrder) ?>">

                      <!-- Per Page Selector -->
                      <div class="d-flex align-items-center gap-1">
                        <label class="small text-muted text-nowrap">Show:</label>
                        <select name="per_page" class="form-select form-select-sm w-auto" onchange="this.form.submit()" aria-label="Rows per page">
                          <?php 
                          $dropdownOptions = $allowedPerPage;
                          if (!in_array($perPage, $dropdownOptions, true)) {
                              $dropdownOptions[] = $perPage;
                              sort($dropdownOptions);
                          }
                          foreach ($dropdownOptions as $opt): ?>
                            <option value="<?= $opt ?>" <?= $perPage === $opt ? 'selected' : '' ?>><?= $opt ?></option>
                          <?php endforeach; ?>
                        </select>
                      </div>

                      <!-- Contextual Filter -->
                      <?php if (!empty($filterCol)): ?>
                        <select id="filter-select" name="filter" class="form-select form-select-sm w-auto" onchange="this.form.submit()" aria-label="Filter context">
                          <option value=""><?= 'All ' . h($config['fields'][$filterCol]['label']) ?></option>
                          <?php foreach ($filterOptionsList as $optKey => $optLabel): ?>
                            <option value="<?= h($optKey) ?>" <?= (string)$filterVal === (string)$optKey ? 'selected' : '' ?>>
                              <?= h($optLabel) ?>
                            </option>
                          <?php endforeach; ?>
                        </select>
                      <?php endif; ?>

                      <!-- Keyword Search -->
                      <div class="input-group input-group-sm w-auto">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="search" name="keyword" class="form-control" placeholder="Search records..." value="<?= h($keyword) ?>" style="min-width: 170px;" aria-label="Search records">
                      </div>

                      <button type="submit" class="btn btn-sm btn-outline-secondary" title="Run Search">
                        <i class="bi bi-search"></i>
                      </button>

                      <?php if ($keyword !== '' || $filterVal !== ''): ?>
                        <a href="index.php?page=list&table=<?= urlencode($table) ?>" class="btn btn-sm btn-outline-secondary" title="Reset filter">Reset</a>
                      <?php endif; ?>
                    </form>

                    <a class="btn btn-primary btn-sm ms-auto ms-xl-1" href="index.php?page=create&table=<?= h($table) ?>">
                      <i class="bi bi-plus-lg me-1"></i> Add New
                    </a>
                  </div>
                </div>
              </div>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-hover align-middle m-0">
                  <thead class="table-light">
                    <tr>
                      <th>
                        <a href="<?= h(buildAdminUrl(['sort' => $pk, 'order' => ($sortCol === $pk && $sortOrder === 'ASC') ? 'DESC' : 'ASC', 'halaman' => 1])) ?>" class="text-reset text-decoration-none d-inline-flex align-items-center gap-1" title="Sort by <?= h($pk) ?>">
                          <?= h($pk) ?>
                          <?php if ($sortCol === $pk): ?>
                            <i class="bi <?= $sortOrder === 'ASC' ? 'bi-sort-up text-primary' : 'bi-sort-down text-primary' ?>"></i>
                          <?php else: ?>
                            <i class="bi bi-arrow-down-up text-muted opacity-50"></i>
                          <?php endif; ?>
                        </a>
                      </th>
                      <?php foreach ($columns as $col): $colLabel = $config['fields'][$col]['label']; ?>
                        <th>
                          <a href="<?= h(buildAdminUrl(['sort' => $col, 'order' => ($sortCol === $col && $sortOrder === 'ASC') ? 'DESC' : 'ASC', 'halaman' => 1])) ?>" class="text-reset text-decoration-none d-inline-flex align-items-center gap-1" title="Sort by <?= h($colLabel) ?>">
                            <?= h($colLabel) ?>
                            <?php if ($sortCol === $col): ?>
                              <i class="bi <?= $sortOrder === 'ASC' ? 'bi-sort-up text-primary' : 'bi-sort-down text-primary' ?>"></i>
                            <?php else: ?>
                              <i class="bi bi-arrow-down-up text-muted opacity-50"></i>
                            <?php endif; ?>
                          </a>
                        </th>
                      <?php endforeach; ?>
                      <th class="text-center" style="width: 130px;">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (!$rows): ?>
                      <tr>
                        <td colspan="<?= count($columns) + 2 ?>" class="text-center text-muted py-4">
                          <i class="bi bi-inbox fs-3 d-block mb-1"></i>
                          <?= ($keyword !== '' || $filterVal !== '') ? 'No records found matching your search or filter criteria.' : 'No records available in this table.' ?>
                        </td>
                      </tr>
                    <?php else: foreach ($rows as $row): ?>
                      <tr>
                        <td class="fw-semibold">#<?= h($row[$pk]) ?></td>
                        <?php foreach ($columns as $col): $f = $config['fields'][$col]; $v = $row[$col]; ?>
                          <td>
                            <?php if ($f['type'] === 'image' && $v): ?>
                              <a href="../<?= h($v) ?>" target="_blank">
                                <img class="thumb border" src="../<?= h($v) ?>" alt="<?= h($f['label']) ?>">
                              </a>
                            <?php elseif ($f['type'] === 'audio' && $v): ?>
                              <audio controls style="max-width:180px">
                                <source src="../<?= h($v) ?>">
                              </audio>
                            <?php elseif ($f['type'] === 'password'): ?>
                              <span class="text-muted">••••••••</span>
                            <?php elseif ($f['type'] === 'enum'): ?>
                              <span class="badge bg-secondary"><?= h(ucfirst((string)$v)) ?></span>
                            <?php elseif ($f['type'] === 'fk'): ?>
                              <span class="badge bg-info text-dark"><?= h($conn ? displayValue($conn, $f, $v) : $v) ?></span>
                            <?php elseif ($f['type'] === 'datetime' || $f['type'] === 'date'): ?>
                              <small class="text-nowrap"><?= h($v) ?></small>
                            <?php else: ?>
                              <?= h(mb_strimwidth((string)$v, 0, 80, '…')) ?>
                            <?php endif; ?>
                          </td>
                        <?php endforeach; ?>
                        <td class="text-nowrap text-center">
                          <a href="edit.php?table=<?= h($table) ?>&id=<?= h($row[$pk]) ?>" class="btn btn-warning btn-sm" title="Edit">
                            <i class="bi bi-pencil-square"></i>
                          </a>
                          <button type="button" class="btn btn-danger btn-sm btn-delete-record" data-bs-toggle="modal" data-bs-target="#deleteModal" data-table="<?= h($table) ?>" data-id="<?= h($row[$pk]) ?>" title="Delete">
                            <i class="bi bi-trash"></i>
                          </button>
                        </td>
                      </tr>
                    <?php endforeach; endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
            <!-- Card Footer: Pagination & Info -->
            <div class="card-footer d-flex flex-wrap justify-content-between align-items-center gap-2">
              <span class="fs-7 text-body-secondary">
                Showing <?= $awalData ?>–<?= $akhirData ?> of <?= $totalData ?> records
              </span>
              <?php if ($totalData > 0): ?>
                <nav aria-label="Table navigation">
                  <ul class="pagination pagination-sm mb-0">
                    <li class="page-item <?= $halaman <= 1 ? 'disabled' : '' ?>">
                      <a class="page-link" href="<?= h(buildAdminUrl(['halaman' => max(1, $halaman - 1)])) ?>" <?= $halaman <= 1 ? 'tabindex="-1" aria-disabled="true"' : '' ?>>
                        <i class="bi bi-chevron-left"></i> Prev
                      </a>
                    </li>
                    <?php for ($nomor = 1; $nomor <= $totalHalaman; $nomor++): ?>
                      <li class="page-item <?= $nomor === $halaman ? 'active' : '' ?>">
                        <a class="page-link" href="<?= h(buildAdminUrl(['halaman' => $nomor])) ?>" <?= $nomor === $halaman ? 'aria-current="page"' : '' ?>><?= $nomor ?></a>
                      </li>
                    <?php endfor; ?>
                    <li class="page-item <?= $halaman >= $totalHalaman ? 'disabled' : '' ?>">
                      <a class="page-link" href="<?= h(buildAdminUrl(['halaman' => min($totalHalaman, $halaman + 1)])) ?>" <?= $halaman >= $totalHalaman ? 'tabindex="-1" aria-disabled="true"' : '' ?>>
                        Next <i class="bi bi-chevron-right"></i>
                      </a>
                    </li>
                  </ul>
                </nav>
              <?php endif; ?>
            </div>
          </div>

        <?php elseif ($page === 'create'): ?>
          <div class="card card-primary card-outline">
            <div class="card-header">
              <h3 class="card-title">Add New <?= h($config['label']) ?></h3>
            </div>
            <form method="post" enctype="multipart/form-data">
              <input type="hidden" name="action" value="create">
              <div class="card-body">
                <div class="row">
                  <?php foreach ($config['fields'] as $col => $f):
                    $value = $_POST[$col] ?? '';
                    $type = inputType($f);
                  ?>
                    <div class="col-md-6 mb-3">
                      <label class="form-label" for="f_<?= h($col) ?>">
                        <?= h($f['label']) ?> <?= ($f['required'] ?? false) ? '<span class="text-danger">*</span>' : '' ?>
                      </label>
                      <?php if ($type === 'textarea'): ?>
                        <textarea class="form-control" id="f_<?= h($col) ?>" name="<?= h($col) ?>" rows="3" <?= ($f['required'] ?? false) ? 'required' : '' ?>><?= h($value) ?></textarea>
                      <?php elseif ($type === 'fk'): ?>
                        <select class="form-select" id="f_<?= h($col) ?>" name="<?= h($col) ?>" <?= ($f['required'] ?? false) ? 'required' : '' ?>>
                          <option value="">-- Select --</option>
                          <?php if ($conn): foreach (fkOptions($conn, $f) as $o): ?>
                            <option value="<?= h($o[$f['key']]) ?>"><?= h($o[$f['display']]) ?></option>
                          <?php endforeach; endif; ?>
                        </select>
                      <?php elseif ($type === 'enum'): ?>
                        <select class="form-select" name="<?= h($col) ?>">
                          <?php foreach ($f['options'] as $o): ?>
                            <option value="<?= h($o) ?>"><?= h(ucfirst($o)) ?></option>
                          <?php endforeach; ?>
                        </select>
                      <?php elseif (in_array($f['type'], ['image', 'audio'])): ?>
                        <input class="form-control" type="file" id="f_<?= h($col) ?>" name="<?= h($col) ?>" accept="<?= $f['type'] === 'image' ? 'image/jpeg,image/png,image/webp' : 'audio/mpeg,audio/wav,audio/ogg,audio/mp4' ?>" <?= ($f['required'] ?? false) ? 'required' : '' ?>>
                        <div class="field-hint"><?= $f['type'] === 'image' ? 'Format: JPG, JPEG, PNG, WEBP. Max 5 MB.' : 'Format: MP3, WAV, OGG, M4A. Max 15 MB.' ?></div>
                      <?php else: ?>
                        <input class="form-control" type="<?= h($type) ?>" id="f_<?= h($col) ?>" name="<?= h($col) ?>" value="<?= h($value) ?>" <?= isset($f['step']) ? 'step="' . h($f['step']) . '"' : '' ?> <?= ($f['required'] ?? false) ? 'required' : '' ?>>
                      <?php endif; ?>
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>
              <div class="card-footer">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Save</button>
                <a href="index.php?page=list&table=<?= h($table) ?>" class="btn btn-secondary">Cancel</a>
              </div>
            </form>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </main>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteModalLabel"><i class="bi bi-exclamation-triangle text-warning me-2"></i>Confirm Delete</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to delete record <strong id="deleteTargetRecordText"></strong>?
        <div class="small text-danger mt-1">This action cannot be undone.</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <form method="post" action="delete.php" id="deleteForm" class="d-inline">
          <input type="hidden" name="table" id="deleteTable" value="<?= h($table) ?>">
          <input type="hidden" name="id" id="deleteId" value="">
          <button type="submit" class="btn btn-danger" id="confirmDeleteBtn">
            <i class="bi bi-trash me-1"></i> Delete
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

<script src="assets/bootstrap.bundle.min.js"></script>
<script>
  if (typeof bootstrap === 'undefined') {
    document.write('<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"><\/script>');
  }
</script>
<script src="assets/adminlte.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const deleteModalEl = document.getElementById('deleteModal');
  const deleteTableInput = document.getElementById('deleteTable');
  const deleteIdInput = document.getElementById('deleteId');
  const deleteRecordText = document.getElementById('deleteTargetRecordText');
  const deleteForm = document.getElementById('deleteForm');

  function setDeleteTarget(tbl, id) {
    if (deleteTableInput) deleteTableInput.value = tbl;
    if (deleteIdInput) deleteIdInput.value = id;
    if (deleteRecordText) deleteRecordText.textContent = '#' + id + ' (' + tbl + ')';
  }

  // Handle click on all delete buttons
  document.querySelectorAll('.btn-delete-record, [data-bs-target="#deleteModal"]').forEach(function (btn) {
    btn.addEventListener('click', function (e) {
      const tbl = this.getAttribute('data-table');
      const id = this.getAttribute('data-id');
      setDeleteTarget(tbl, id);

      if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        try {
          const bsModal = bootstrap.Modal.getOrCreateInstance(deleteModalEl);
          bsModal.show();
        } catch (err) {
          if (confirm('Are you sure you want to delete record #' + id + ' in ' + tbl + '?')) {
            deleteForm.submit();
          }
        }
      } else {
        if (confirm('Are you sure you want to delete record #' + id + ' in ' + tbl + '?')) {
          deleteForm.submit();
        }
      }
    });
  });

  if (deleteModalEl) {
    deleteModalEl.addEventListener('show.bs.modal', function (event) {
      const button = event.relatedTarget;
      if (button) {
        const tbl = button.getAttribute('data-table');
        const id = button.getAttribute('data-id');
        setDeleteTarget(tbl, id);
      }
    });
  }
});
</script>
</body>
</html>
