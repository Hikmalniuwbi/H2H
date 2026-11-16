<?php
// Automated End-to-End Verification Test for S2U Rebranding & Feedback Integration
// Hearts2Hearts (H2H) Web Application

$baseDir = dirname(__DIR__);

require_once $baseDir . '/config/database.php';
require_once $baseDir . '/includes/functions.php';
require_once $baseDir . '/includes/auth.php';

$passed = 0;
$failed = 0;

function assertCheck($name, $condition, $details = '') {
    global $passed, $failed;
    if ($condition) {
        echo "[PASS] $name\n";
        $passed++;
    } else {
        echo "[FAIL] $name" . ($details ? " - $details" : "") . "\n";
        $failed++;
    }
}

echo "====================================================\n";
echo "RUNNING S2U REBRANDING & FEEDBACK VERIFICATION SUITE\n";
echo "====================================================\n\n";

$db = getDbConnection();
assertCheck("Database connection", $db !== null);

// 1. Check Fanbase Vocabulary Rebranding (Heartbeat -> S2U)
$aboutContent = file_get_contents($baseDir . '/about.php');
assertCheck("about.php uses S2U for fanbase", 
    strpos($aboutContent, 'connects deeply with S2U') !== false && 
    strpos($aboutContent, 'love letter to S2U') !== false &&
    strpos($aboutContent, 'love letter to Heartbeats') === false
);

$newsContent = file_get_contents($baseDir . '/news.php');
assertCheck("news.php uses S2U for fanbase", 
    strpos($newsContent, 'heartfelt gratitude to S2U worldwide') !== false &&
    strpos($newsContent, 'gratitude to Heartbeats') === false
);

$feedbackContent = file_get_contents($baseDir . '/feedback.php');
assertCheck("feedback.php uses S2U for fanbase", 
    (strpos($feedbackContent, 'suggestions from S2U') !== false || strpos($feedbackContent, 'saran dan masukan dari S2U') !== false) &&
    strpos($feedbackContent, 'Heartbeats') === false
);

$userQuery = $db->query("SELECT name FROM users WHERE email = 'fan@h2h.com'");
$fanUser = $userQuery->fetch_assoc();
assertCheck("User fan@h2h.com name is 'S2U Fan'", ($fanUser['name'] ?? '') === 'S2U Fan', "Actual: " . ($fanUser['name'] ?? 'none'));

$newsQuery = $db->query("SELECT COUNT(*) as count FROM news WHERE content LIKE '%Heartbeats%'");
$countHeartbeats = (int)$newsQuery->fetch_assoc()['count'];
assertCheck("No 'Heartbeats' in database news content", $countHeartbeats === 0, "Found $countHeartbeats occurrences");

// 2. Database Table `feedbacks` Structure & Foreign Key
$tableCheck = $db->query("SHOW TABLES LIKE 'feedbacks'");
assertCheck("Table 'feedbacks' exists in database", $tableCheck->num_rows > 0);

$descQuery = $db->query("DESCRIBE feedbacks");
$fields = [];
while ($row = $descQuery->fetch_assoc()) {
    $fields[$row['Field']] = $row;
}
assertCheck("Column 'feedback_id' exists as primary key", isset($fields['feedback_id']) && $fields['feedback_id']['Key'] === 'PRI');
assertCheck("Column 'user_id' exists as foreign key reference", isset($fields['user_id']));
assertCheck("Column 'name' exists", isset($fields['name']));
assertCheck("Column 'email' exists", isset($fields['email']));
assertCheck("Column 'message' exists", isset($fields['message']));
assertCheck("Column 'created_at' exists", isset($fields['created_at']));

// 3. Feedback Submission Simulation & User Integration
$testMsg = "Automated test feedback message " . time();
$insertStmt = $db->prepare("INSERT INTO feedbacks (user_id, name, email, message, created_at) VALUES (?, ?, ?, ?, NOW())");
$testUserId = 2;
$testName = "S2U Fan";
$testEmail = "fan@h2h.com";
$insertStmt->bind_param("isss", $testUserId, $testName, $testEmail, $testMsg);
$inserted = $insertStmt->execute();
$newFbId = $insertStmt->insert_id;
$insertStmt->close();

assertCheck("Feedback successfully inserted into database", $inserted && $newFbId > 0);

$verifyFb = $db->query("SELECT * FROM feedbacks WHERE feedback_id = $newFbId")->fetch_assoc();
assertCheck("Feedback integrity verified", 
    $verifyFb['user_id'] == 2 && 
    $verifyFb['name'] === 'S2U Fan' && 
    $verifyFb['email'] === 'fan@h2h.com' &&
    $verifyFb['message'] === $testMsg
);

// Clean up test record
$db->query("DELETE FROM feedbacks WHERE feedback_id = $newFbId");

// 4. Test Auth Gating via HTTP Apache Endpoint
// 4a. Unauthenticated POST to feedback.php
$ch = curl_init('http://localhost/H2H/feedback.php');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HEADER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => 'message=hello',
    CURLOPT_FOLLOWLOCATION => false
]);
$respFb = curl_exec($ch);
curl_close($ch);
assertCheck("Unauthenticated POST to feedback.php redirects (302) to login with notice", 
    strpos($respFb, '302') !== false && 
    strpos($respFb, 'login.php?redirect=feedback.php&notice=feedback_login_required') !== false
);

// 4b. Unauthenticated GET to feedback.php displays S2U prompt
$ch = curl_init('http://localhost/H2H/feedback.php');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => false
]);
$bodyFb = curl_exec($ch);
curl_close($ch);
assertCheck("Unauthenticated GET to feedback.php shows S2U membership login prompt", 
    (strpos($bodyFb, 'Exclusive for S2U Members') !== false || strpos($bodyFb, 'Eksklusif untuk Member S2U') !== false) &&
    strpos($bodyFb, 'login.php?redirect=feedback.php&notice=feedback_login_required') !== false
);

// 5. Test Auth Gating on checkout.php
$ch = curl_init('http://localhost/H2H/checkout.php?type=merchandise&id=1');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HEADER => true,
    CURLOPT_FOLLOWLOCATION => false
]);
$respCo = curl_exec($ch);
curl_close($ch);
assertCheck("Unauthenticated checkout.php redirects (302) to login", 
    strpos($respCo, '302') !== false && 
    strpos($respCo, 'login.php?redirect=checkout.php') !== false
);

// 6. Test login.php notice rendering
$ch = curl_init('http://localhost/H2H/login.php?redirect=feedback.php&notice=feedback_login_required');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$bodyLoginFb = curl_exec($ch);
curl_close($ch);
assertCheck("login.php displays feedback prompt when redirect=feedback.php", 
    (strpos($bodyLoginFb, 'Only registered fan members can submit feedback') !== false || strpos($bodyLoginFb, 'Hanya user terdaftar yang dapat mengirimkan feedback') !== false)
);

$ch = curl_init('http://localhost/H2H/login.php?redirect=' . urlencode('checkout.php?type=merchandise&id=1'));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$bodyLoginMerch = curl_exec($ch);
curl_close($ch);
assertCheck("login.php displays merchandise prompt when redirect=checkout.php", 
    (strpos($bodyLoginMerch, 'merchandise') !== false || strpos($bodyLoginMerch, 'pembelian merchandise') !== false)
);

// 7. Test Admin Integration
require_once $baseDir . '/admin/config/tables.php';
assertCheck("admin/config/tables.php includes 'feedbacks' configuration", 
    isset($tables['feedbacks']) && 
    $tables['feedbacks']['pk'] === 'feedback_id' &&
    isset($tables['feedbacks']['fields']['user_id']) &&
    isset($tables['feedbacks']['fields']['message'])
);

// Test Dashboard rendering with authenticated admin session
$_SESSION['user'] = [
    'user_id' => 1,
    'name' => 'Super Administrator',
    'email' => 'admin@h2h.com',
    'role' => 'admin'
];
chdir($baseDir . '/admin');
ob_start();
include $baseDir . '/admin/dashboard.php';
$dashHtml = ob_get_clean();

assertCheck("admin/dashboard.php renders Feedback widget and S2U section", 
    (strpos($dashHtml, 'Pesan Feedback S2U') !== false || strpos($dashHtml, 'S2U Fan Feedbacks') !== false) &&
    (strpos($dashHtml, 'Pesan Masuk Terbaru dari Fans (S2U)') !== false || strpos($dashHtml, 'Recent Fan Feedback (S2U)') !== false)
);

echo "\n====================================================\n";
echo "SUMMARY: Total Passed: $passed, Failed: $failed\n";
echo "====================================================\n";

if ($failed > 0) {
    exit(1);
}
