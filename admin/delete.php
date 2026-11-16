<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/config/tables.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

// Protect admin access
requireAdmin();

$table = $_POST['table'] ?? $_GET['table'] ?? '';
$id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);

if (!validTable($table, $tables) || $id <= 0) {
    header("Location: index.php?page=list&table=" . urlencode($table ?: 'news') . "&status=delete_error");
    exit;
}

$conn = getDbConnection();
if (!$conn) {
    header("Location: index.php?page=list&table=" . urlencode($table) . "&status=delete_error");
    exit;
}

$config = $tables[$table];
$pk = $config['pk'];

// Retrieve record before deletion for file cleanup
$stmt = $conn->prepare("SELECT * FROM `$table` WHERE `$pk` = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$data) {
    header("Location: index.php?page=list&table=" . urlencode($table));
    exit;
}

try {
    // Cascade cleanup for child records to prevent foreign key constraint blocks
    if ($table === 'event_categories') {
        $stmtClean = $conn->prepare("UPDATE `events` SET `category_id` = NULL WHERE `category_id` = ?");
        $stmtClean->bind_param('i', $id);
        $stmtClean->execute();
        $stmtClean->close();
    } elseif ($table === 'merchandise') {
        $stmtClean = $conn->prepare("DELETE FROM `order_items` WHERE `merchandise_id` = ?");
        $stmtClean->bind_param('i', $id);
        $stmtClean->execute();
        $stmtClean->close();
    } elseif ($table === 'users') {
        // Find and delete order items of user's orders
        $orderRes = $conn->query("SELECT `order_id` FROM `orders` WHERE `user_id` = $id");
        if ($orderRes) {
            while ($oRow = $orderRes->fetch_assoc()) {
                $oid = (int)$oRow['order_id'];
                $conn->query("DELETE FROM `order_items` WHERE `order_id` = $oid");
            }
        }
        $conn->query("DELETE FROM `orders` WHERE `user_id` = $id");
        $conn->query("DELETE FROM `feedbacks` WHERE `user_id` = $id");
    } elseif ($table === 'orders') {
        $stmtClean = $conn->prepare("DELETE FROM `order_items` WHERE `order_id` = ?");
        $stmtClean->bind_param('i', $id);
        $stmtClean->execute();
        $stmtClean->close();
    } elseif ($table === 'albums') {
        // Clean audio files of songs before deleting songs
        $songRes = $conn->query("SELECT `audio_file` FROM `songs` WHERE `album_id` = $id");
        if ($songRes) {
            while ($sRow = $songRes->fetch_assoc()) {
                if (!empty($sRow['audio_file'])) {
                    $sAudioPath = __DIR__ . '/../' . $sRow['audio_file'];
                    if (is_file($sAudioPath)) @unlink($sAudioPath);
                }
            }
        }
        $stmtClean = $conn->prepare("DELETE FROM `songs` WHERE `album_id` = ?");
        $stmtClean->bind_param('i', $id);
        $stmtClean->execute();
        $stmtClean->close();
    } elseif ($table === 'members') {
        // Clean gallery images of member before deleting gallery records
        $galRes = $conn->query("SELECT `image` FROM `gallery` WHERE `member_id` = $id");
        if ($galRes) {
            while ($gRow = $galRes->fetch_assoc()) {
                if (!empty($gRow['image'])) {
                    $gImgPath = __DIR__ . '/../' . $gRow['image'];
                    if (is_file($gImgPath)) @unlink($gImgPath);
                }
            }
        }
        $stmtClean = $conn->prepare("DELETE FROM `gallery` WHERE `member_id` = ?");
        $stmtClean->bind_param('i', $id);
        $stmtClean->execute();
        $stmtClean->close();
    }

    // Delete the primary record
    $stmt = $conn->prepare("DELETE FROM `$table` WHERE `$pk` = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();

    // Delete associated physical media files
    foreach ($config['fields'] as $column => $field) {
        if (in_array($field['type'], ['image', 'audio']) && !empty($data[$column])) {
            $filePath = __DIR__ . '/../' . $data[$column];
            if (is_file($filePath)) {
                @unlink($filePath);
            }
        }
    }

    header("Location: index.php?page=list&table=" . urlencode($table) . "&status=deleted");
    exit;
} catch (mysqli_sql_exception $e) {
    header("Location: index.php?page=list&table=" . urlencode($table) . "&status=delete_error");
    exit;
} catch (Throwable $e) {
    header("Location: index.php?page=list&table=" . urlencode($table) . "&status=delete_error");
    exit;
}
