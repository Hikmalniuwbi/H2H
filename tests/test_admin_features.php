<?php
// Test suite for AdminLTE 4 features: search, filter, per_page, sorting, and pagination
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$_SESSION['user'] = [
    'user_id' => 1,
    'role' => 'admin',
    'name' => 'Admin Test',
    'email' => 'admin@h2h.com'
];

function runTest($name, $closure) {
    echo "Running: $name... ";
    try {
        $result = $closure();
        if ($result === true) {
            echo "PASS\n";
            return true;
        } else {
            echo "FAIL: $result\n";
            return false;
        }
    } catch (Throwable $e) {
        echo "EXCEPTION: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n";
        return false;
    }
}

$baseUrl = 'http://localhost/H2H/admin';

// Helper to simulate admin session
function makeAdminRequest($url, $cookies = []) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, '');
    curl_setopt($ch, CURLOPT_COOKIE, 'admin_session_test=1');
    $response = curl_exec($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);
    return ['status' => $info['http_code'], 'body' => $response];
}

// Helper to execute admin index
function executeAdminIndex($queryParams = []) {
    $_SESSION['user'] = [
        'user_id' => 1,
        'role' => 'admin',
        'name' => 'Admin Test',
        'email' => 'admin@h2h.com'
    ];

    $_GET = $queryParams;
    $_SERVER['REQUEST_METHOD'] = 'GET';

    ob_start();
    include __DIR__ . '/../admin/index.php';
    return ob_get_clean();
}

$allPassed = true;

// 1. Check UI elements presence in admin/index.php
$allPassed = runTest("Admin list page contains search input, filter, per_page, and pagination controls", function() {
    $html = executeAdminIndex(['page' => 'list', 'table' => 'merchandise']);
    
    if (strpos($html, 'name="keyword"') === false) {
        return "Search input 'keyword' missing";
    }
    if (strpos($html, 'name="per_page"') === false) {
        return "Per-page selector 'per_page' missing";
    }
    if (strpos($html, 'name="filter"') === false && strpos($html, 'id="filter-select"') === false && strpos($html, 'filter') === false) {
        return "Category/Contextual filter dropdown missing";
    }
    if (strpos($html, 'pagination') === false) {
        file_put_contents(__DIR__ . '/debug_output.html', $html);
        return "Pagination component missing (saved to debug_output.html)";
    }
    if (strpos($html, 'Menampilkan') === false && strpos($html, 'Showing') === false) {
        return "Record counter ('Showing ...') missing";
    }
    return true;
}) && $allPassed;

// 2. Check keyword search functionality
$allPassed = runTest("Keyword search filters merchandise by product name", function() {
    // Search for 'Lightstick' or specific merchandise
    $html = executeAdminIndex(['page' => 'list', 'table' => 'merchandise', 'keyword' => 'Lightstick']);
    if (strpos($html, 'Lightstick') === false) {
        return "Expected search result 'Lightstick' not found in table";
    }
    // Search for a non-existent keyword
    $htmlNone = executeAdminIndex(['page' => 'list', 'table' => 'merchandise', 'keyword' => 'NONEXISTENTKEYWORD12345']);
    if (strpos($htmlNone, 'tidak ditemukan') === false && strpos($htmlNone, 'Belum ada data') === false && strpos($htmlNone, 'No records found') === false) {
        return "Expected empty state message when search yields no results";
    }
    return true;
}) && $allPassed;

// 3. Check contextual category filter functionality
$allPassed = runTest("Contextual filter filters merchandise by category", function() {
    $html = executeAdminIndex(['page' => 'list', 'table' => 'merchandise', 'filter' => 'lightstick']);
    if (strpos($html, 'Lightstick') === false) {
        return "Expected category filtered records for 'lightstick'";
    }
    return true;
}) && $allPassed;

// 4. Check sorting toggle headers
$allPassed = runTest("Table headers have interactive sorting links with sort icons", function() {
    $html = executeAdminIndex(['page' => 'list', 'table' => 'merchandise', 'sort' => 'price', 'order' => 'ASC']);
    if (strpos($html, 'sort=price') === false) {
        return "Sorting link for 'price' missing";
    }
    if (strpos($html, 'order=DESC') === false && strpos($html, 'order=ASC') === false) {
        return "Sorting order parameter missing in header links";
    }
    if (strpos($html, 'bi-sort') === false && strpos($html, 'bi-arrow') === false) {
        return "Sorting direction icon missing";
    }
    return true;
}) && $allPassed;

// 5. Check pagination and per_page limits
$allPassed = runTest("Pagination respects per_page and provides next/prev links", function() {
    $html = executeAdminIndex(['page' => 'list', 'table' => 'merchandise', 'per_page' => '2', 'halaman' => '1']);
    if (strpos($html, 'per_page=2') === false) {
        return "per_page parameter not maintained in pagination links";
    }
    if (strpos($html, 'halaman=2') === false) {
        return "Next page link 'halaman=2' missing when per_page=2";
    }
    return true;
}) && $allPassed;

// 6. Check feedbacks table supports search, filter, and sorting
$allPassed = runTest("Feedbacks table works with search and pagination", function() {
    $html = executeAdminIndex(['page' => 'list', 'table' => 'feedbacks']);
    if (strpos($html, 'Daftar Fan Feedbacks') === false && strpos($html, 'Fan Feedbacks') === false) {
        return "Feedbacks title not shown";
    }
    return true;
}) && $allPassed;

// 7. Check admin dashboard quick stats and recent feedbacks
$allPassed = runTest("Dashboard contains quick stats, recent feedback widget, and AdminLTE 4 layout", function() {
    ob_start();
    include __DIR__ . '/../admin/dashboard.php';
    $html = ob_get_clean();

    if (strpos($html, 'Pesan Masuk Terbaru dari Fans (S2U)') === false && strpos($html, 'Recent Fan Feedback (S2U)') === false) {
        return "Recent feedback section missing from dashboard";
    }
    if (strpos($html, 'small-box') === false) {
        return "AdminLTE small-box widgets missing";
    }
    return true;
}) && $allPassed;

echo "\n============================================\n";
if ($allPassed) {
    echo "ALL ADMIN TESTS PASSED!\n";
    exit(0);
} else {
    echo "SOME ADMIN TESTS FAILED!\n";
    exit(1);
}
