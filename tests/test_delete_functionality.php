<?php
// Test delete functionality across multiple tables and methods
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$_SESSION['user'] = [
    'user_id' => 1,
    'role' => 'admin',
    'name' => 'Admin Test',
    'email' => 'admin@h2h.com'
];

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../admin/config/tables.php';
require_once __DIR__ . '/../admin/includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

$conn = getDbConnection();
if (!$conn) die("DB connection failed\n");

$results = [];

// 1. Check HTML output of admin/index.php contains bootstrap.bundle.min.js and delete handlers
$_GET = ['page' => 'list', 'table' => 'news'];
$_SERVER['REQUEST_METHOD'] = 'GET';
ob_start();
include __DIR__ . '/../admin/index.php';
$html = ob_get_clean();

$results[] = ["admin/index.php includes bootstrap.bundle.min.js", strpos($html, 'bootstrap.bundle.min.js') !== false];
$results[] = ["admin/index.php has delete modal markup", strpos($html, 'id="deleteModal"') !== false];
$results[] = ["admin/index.php has delete form targeting delete.php", strpos($html, 'action="delete.php"') !== false];

// Helper to run delete via isolated PHP CLI process
function runDelete($method, $params) {
    $php = 'C:\\laragon\\bin\\php\\php-8.1.10-Win32-vs16-x64\\php.exe';
    $paramsJson = base64_encode(json_encode($params));
    $code = '<?php
session_start();
$_SESSION["user"] = ["user_id" => 1, "role" => "admin", "name" => "Admin Test", "email" => "admin@h2h.com"];
$method = "' . $method . '";
$params = json_decode(base64_decode("' . $paramsJson . '"), true);
$_SERVER["REQUEST_METHOD"] = $method;
if ($method === "POST") {
    $_POST = $params;
    $_GET = [];
} else {
    $_GET = $params;
    $_POST = [];
}
ob_start();
include __DIR__ . "/../admin/delete.php";
$out = ob_get_clean();
echo "DONE";
';
    $tempScript = __DIR__ . '/temp_runner.php';
    file_put_contents($tempScript, $code);
    exec("\"$php\" \"$tempScript\"", $output, $status);
    if (file_exists($tempScript)) @unlink($tempScript);
}

// 2. Test POST deletion on news
$conn->query("INSERT INTO news (title, content, published_at) VALUES ('Temp News', 'Temp Content', NOW())");
$testNewsId = $conn->insert_id;
runDelete('POST', ['table' => 'news', 'id' => $testNewsId]);
$checkNews = $conn->query("SELECT * FROM news WHERE news_id = $testNewsId")->fetch_assoc();
$results[] = ["POST to delete.php removes news record", empty($checkNews)];

// 3. Test GET deletion on news (with confirm parameter fallback)
$conn->query("INSERT INTO news (title, content, published_at) VALUES ('Temp News GET', 'Temp Content GET', NOW())");
$testNewsGetId = $conn->insert_id;
runDelete('GET', ['table' => 'news', 'id' => $testNewsGetId]);
$checkNewsGet = $conn->query("SELECT * FROM news WHERE news_id = $testNewsGetId")->fetch_assoc();
$results[] = ["GET to delete.php removes news record", empty($checkNewsGet)];

// 4. Test deletion of album with related songs (cascade handling)
$conn->query("INSERT INTO albums (album_title, release_date, album_type) VALUES ('Temp Album', '2026-01-01', 'Single')");
$testAlbumId = $conn->insert_id;
$conn->query("INSERT INTO songs (album_id, song_title, duration) VALUES ($testAlbumId, 'Temp Song', '03:30:00')");
$testSongId = $conn->insert_id;
runDelete('POST', ['table' => 'albums', 'id' => $testAlbumId]);
$checkAlbum = $conn->query("SELECT * FROM albums WHERE album_id = $testAlbumId")->fetch_assoc();
$checkSong = $conn->query("SELECT * FROM songs WHERE song_id = $testSongId")->fetch_assoc();
$results[] = ["Deleting album also cascades and removes related songs", empty($checkAlbum) && empty($checkSong)];

// 5. Test deletion of event category with related events (cascade/nullify handling)
$conn->query("INSERT INTO event_categories (category_name, description) VALUES ('Temp Cat', 'Temp Desc')");
$testCatId = $conn->insert_id;
$conn->query("INSERT INTO events (event_name, category_id) VALUES ('Temp Event', $testCatId)");
$testEventId = $conn->insert_id;
runDelete('POST', ['table' => 'event_categories', 'id' => $testCatId]);
$checkCat = $conn->query("SELECT * FROM event_categories WHERE category_id = $testCatId")->fetch_assoc();
$checkEvent = $conn->query("SELECT * FROM events WHERE event_id = $testEventId")->fetch_assoc();
$results[] = ["Deleting event category succeeds and updates/cleans events without FK error", empty($checkCat) && ($checkEvent['category_id'] === null || empty($checkEvent))];

// Clean up temp event
$conn->query("DELETE FROM events WHERE event_id = $testEventId");

// 6. Test deletion of merchandise with order_items
$conn->query("INSERT INTO merchandise (product_name, category, price, stock) VALUES ('Temp Merch', 'accessories', 50.00, 10)");
$testMerchId = $conn->insert_id;
$conn->query("INSERT INTO orders (user_id, order_date, total_amount, shipping_address, order_status) VALUES (1, NOW(), 50.00, 'Test Addr', 'pending')");
$testOrderId = $conn->insert_id;
$conn->query("INSERT INTO order_items (order_id, merchandise_id, quantity, price, subtotal) VALUES ($testOrderId, $testMerchId, 1, 50.00, 50.00)");
$testItemId = $conn->insert_id;

runDelete('POST', ['table' => 'merchandise', 'id' => $testMerchId]);
$checkMerch = $conn->query("SELECT * FROM merchandise WHERE merchandise_id = $testMerchId")->fetch_assoc();
$checkItem = $conn->query("SELECT * FROM order_items WHERE order_item_id = $testItemId")->fetch_assoc();
$results[] = ["Deleting merchandise cascades and cleans related order_items without FK error", empty($checkMerch) && empty($checkItem)];

// Clean up temp order
$conn->query("DELETE FROM orders WHERE order_id = $testOrderId");

// Output results
$allPass = true;
foreach ($results as $res) {
    echo $res[0] . " ... " . ($res[1] ? "PASS" : "FAIL") . "\n";
    if (!$res[1]) $allPass = false;
}

echo "\nResult: " . ($allPass ? "ALL DELETE TESTS PASSED" : "SOME TESTS FAILED") . "\n";
exit($allPass ? 0 : 1);
