<?php
// Test Task 6: Checkout and Orders
$requiredFiles = [
    'checkout.php',
    'orders.php',
    'order_success.php'
];

foreach ($requiredFiles as $file) {
    if (!file_exists(__DIR__ . '/../' . $file)) {
        fwrite(STDERR, "FAIL: $file does not exist\n");
        exit(1);
    }
}

// Linting
foreach ($requiredFiles as $file) {
    $out = [];
    $ret = 0;
    exec("\"C:\\laragon\\bin\\php\\php-8.1.10-Win32-vs16-x64\\php.exe\" -l \"" . __DIR__ . "/../$file\"", $out, $ret);
    if ($ret !== 0) {
        fwrite(STDERR, "FAIL: Syntax error in $file\n");
        exit(1);
    }
}

// Check checkout auth guard
$checkoutContent = file_get_contents(__DIR__ . '/../checkout.php');
if (!str_contains($checkoutContent, 'requireLogin')) {
    fwrite(STDERR, "FAIL: checkout.php does not enforce requireLogin\n");
    exit(1);
}

// Check orders auth guard
$ordersContent = file_get_contents(__DIR__ . '/../orders.php');
if (!str_contains($ordersContent, 'requireLogin')) {
    fwrite(STDERR, "FAIL: orders.php does not enforce requireLogin\n");
    exit(1);
}

echo "PASS: Task 6 checkout and orders verification passed.\n";
exit(0);
