<?php
// Test Task 5: Store merchandise existence and logic
if (!file_exists(__DIR__ . '/../store.php')) {
    fwrite(STDERR, "FAIL: store.php does not exist\n");
    exit(1);
}

$out = [];
$ret = 0;
exec("\"C:\\laragon\\bin\\php\\php-8.1.10-Win32-vs16-x64\\php.exe\" -l \"" . __DIR__ . "/../store.php\"", $out, $ret);
if ($ret !== 0) {
    fwrite(STDERR, "FAIL: Syntax error in store.php\n");
    exit(1);
}

$content = file_get_contents(__DIR__ . '/../store.php');
if (!str_contains($content, 'checkout.php?type=merchandise')) {
    fwrite(STDERR, "FAIL: store.php missing checkout direct buy link\n");
    exit(1);
}

if (!str_contains($content, 'data-category=')) {
    fwrite(STDERR, "FAIL: store.php missing data-category attribute for filtering\n");
    exit(1);
}

echo "PASS: Task 5 store tests passed.\n";
exit(0);
