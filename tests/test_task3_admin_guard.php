<?php
// Test that admin/index.php, admin/edit.php, admin/delete.php, admin/dashboard.php contain requireAdmin check
$adminFiles = ['admin/index.php', 'admin/edit.php', 'admin/delete.php', 'admin/dashboard.php'];
foreach ($adminFiles as $file) {
    if (!file_exists(__DIR__ . '/../' . $file)) {
        fwrite(STDERR, "FAIL: $file does not exist\n");
        exit(1);
    }
    $content = file_get_contents(__DIR__ . '/../' . $file);
    if (!str_contains($content, 'requireAdmin')) {
        fwrite(STDERR, "FAIL: $file does not enforce requireAdmin\n");
        exit(1);
    }
}
echo "PASS: Admin guard verification passed.\n";
exit(0);
