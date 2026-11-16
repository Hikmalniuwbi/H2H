<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

// Test unauthenticated state
$_SESSION = [];
if (isLoggedIn()) {
    fwrite(STDERR, "FAIL: isLoggedIn should be false when session is empty\n");
    exit(1);
}

// Test customer login state
$_SESSION['user'] = [
    'user_id' => 2,
    'name' => 'Test Customer',
    'email' => 'customer@h2h.com',
    'role' => 'customer'
];
if (!isLoggedIn() || currentUser()['role'] !== 'customer' || isAdmin()) {
    fwrite(STDERR, "FAIL: customer session not recognized correctly\n");
    exit(1);
}

// Test admin login state
$_SESSION['user']['role'] = 'admin';
if (!isAdmin()) {
    fwrite(STDERR, "FAIL: admin session not recognized\n");
    exit(1);
}

echo "PASS: Auth logic tests passed.\n";
exit(0);
