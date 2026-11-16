<?php
$pages = [
    'includes/navbar.php',
    'includes/footer.php',
    'index.php',
    'members.php',
    'discography.php',
    'gallery.php',
    'news.php',
    'feedback.php'
];

foreach ($pages as $page) {
    if (!file_exists(__DIR__ . '/../' . $page)) {
        fwrite(STDERR, "FAIL: $page does not exist\n");
        exit(1);
    }
}

// Lint each PHP file
foreach ($pages as $page) {
    $out = [];
    $ret = 0;
    exec("\"C:\\laragon\\bin\\php\\php-8.1.10-Win32-vs16-x64\\php.exe\" -l \"" . __DIR__ . "/../$page\"", $out, $ret);
    if ($ret !== 0) {
        fwrite(STDERR, "FAIL: Syntax error in $page\n");
        exit(1);
    }
}

echo "PASS: Public pages and templates pass syntax and existence checks.\n";
exit(0);
