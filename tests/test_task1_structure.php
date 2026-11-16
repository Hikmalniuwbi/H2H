<?php
// Test file exists
$requiredFiles = [
    'style.css',
    'config/database.php',
    'includes/functions.php',
    'update_schema.sql'
];
foreach ($requiredFiles as $file) {
    if (!file_exists(__DIR__ . '/../' . $file)) {
        fwrite(STDERR, "FAIL: File $file does not exist\n");
        exit(1);
    }
}
require_once __DIR__ . '/../includes/functions.php';
// Test helper functions
if (formatRupiah(165000) !== 'Rp 165.000') {
    fwrite(STDERR, "FAIL: formatRupiah output incorrect: " . formatRupiah(165000) . "\n");
    exit(1);
}
if (h('<script>alert(1)</script>') !== '&lt;script&gt;alert(1)&lt;/script&gt;') {
    fwrite(STDERR, "FAIL: h() sanitization incorrect\n");
    exit(1);
}
echo "PASS: Task 1 structure and helper tests passed.\n";
exit(0);
