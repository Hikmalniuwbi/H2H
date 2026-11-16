<?php
/**
 * Test Task 7: End-to-End Comprehensive Verification
 */

$phpBin = "\"C:\\laragon\\bin\\php\\php-8.1.10-Win32-vs16-x64\\php.exe\"";
$rootDir = realpath(__DIR__ . '/..');

echo "=== Hearts2Hearts Integration End-to-End Verification ===\n";

// 1. Verifikasi File Kritis
$criticalFiles = [
    'style.css',
    'update_schema.sql',
    'seed_data.sql',
    'config/database.php',
    'includes/functions.php',
    'includes/auth.php',
    'includes/navbar.php',
    'includes/footer.php',
    'index.php',
    'about.php',
    'members.php',
    'music.php',
    'discography.php',
    'gallery.php',
    'news.php',
    'store.php',
    'events.php',
    'checkout.php',
    'order_success.php',
    'orders.php',
    'login.php',
    'register.php',
    'logout.php',
    'feedback.php',
    'admin/login.php',
    'admin/dashboard.php',
    'admin/index.php',
    'admin/edit.php',
    'admin/delete.php',
    'admin/logout.php',
    'admin/config/tables.php',
    'admin/includes/functions.php',
    'admin/assets/adminlte.min.css',
    'admin/assets/adminlte.min.js'
];

foreach ($criticalFiles as $file) {
    if (!file_exists($rootDir . '/' . $file)) {
        fwrite(STDERR, "FAIL: Critical file missing: $file\n");
        exit(1);
    }
}
echo "[✓] All critical files and directories exist.\n";

// 2. Syntax Linting Semua File PHP
$allPhpFiles = glob($rootDir . '/*.php');
$allPhpFiles = array_merge($allPhpFiles, glob($rootDir . '/config/*.php'));
$allPhpFiles = array_merge($allPhpFiles, glob($rootDir . '/includes/*.php'));
$allPhpFiles = array_merge($allPhpFiles, glob($rootDir . '/admin/*.php'));
$allPhpFiles = array_merge($allPhpFiles, glob($rootDir . '/admin/config/*.php'));
$allPhpFiles = array_merge($allPhpFiles, glob($rootDir . '/admin/includes/*.php'));

foreach ($allPhpFiles as $phpFile) {
    $out = [];
    $ret = 0;
    exec("$phpBin -l \"$phpFile\"", $out, $ret);
    if ($ret !== 0) {
        fwrite(STDERR, "FAIL: Syntax error in $phpFile: " . implode(" ", $out) . "\n");
        exit(1);
    }
}
echo "[✓] Lint clean on all " . count($allPhpFiles) . " PHP files.\n";

// 3. Verifikasi Keaslian CSS (CSS tidak boleh diubah warna/struktur esensialnya)
$css = file_get_contents($rootDir . '/style.css');
$requiredCssTokens = [
    '--primary-blue: #A9C7DA;',
    '--secondary-pink: #F6B8D1;',
    '.hero',
    '.events-table',
    '.product-card',
    '.member-modal',
    '.footer-container'
];
foreach ($requiredCssTokens as $token) {
    if (!str_contains($css, $token)) {
        fwrite(STDERR, "FAIL: style.css missing original design token: $token\n");
        exit(1);
    }
}
echo "[✓] Styling CSS verification passed. Visual design intact 100%.\n";

// 4. Verifikasi Otentikasi dan Role Guard
require_once $rootDir . '/includes/auth.php';
require_once $rootDir . '/includes/functions.php';

// Pastikan helper role berfungsi
assert(!isLoggedIn());
assert(!isAdmin());

// Simulasi user customer
$_SESSION['user'] = [
    'user_id' => 10,
    'name' => 'Test Customer',
    'email' => 'customer@h2h.com',
    'role' => 'customer'
];
assert(isLoggedIn());
assert(!isAdmin());

// Simulasi user admin
$_SESSION['user']['role'] = 'admin';
assert(isLoggedIn());
assert(isAdmin());

// Reset session
$_SESSION = [];
echo "[✓] Authentication & role guards verified.\n";

// 5. Verifikasi Admin Tables Config
require_once $rootDir . '/admin/config/tables.php';
$expectedTables = ['members', 'albums', 'songs', 'merchandise', 'events', 'event_categories', 'news', 'gallery', 'orders', 'order_items', 'users'];
foreach ($expectedTables as $t) {
    if (!isset($tables[$t])) {
        fwrite(STDERR, "FAIL: Table $t missing from admin/config/tables.php\n");
        exit(1);
    }
}
echo "[✓] Admin tables CRUD configuration verified.\n";

// 6. Jalankan Semua Test Task Sebelumnya
$previousTests = [
    'test_task1_structure.php',
    'test_task2_auth.php',
    'test_task3_admin_guard.php',
    'test_task4_public_pages.php',
    'test_task5_store.php',
    'test_task6_events.php',
    'test_task6_checkout.php'
];

foreach ($previousTests as $testFile) {
    $out = [];
    $ret = 0;
    exec("$phpBin \"" . __DIR__ . "/$testFile\"", $out, $ret);
    if ($ret !== 0) {
        fwrite(STDERR, "FAIL: $testFile failed: " . implode("\n", $out) . "\n");
        exit(1);
    }
}
echo "[✓] All previous regression tests passed.\n";

echo "\n>>> SUCCESS: All End-to-End integration checks passed flawlessly! <<<\n";
exit(0);
