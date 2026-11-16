<?php
require_once __DIR__ . '/../../includes/functions.php';

function validTable(string $name, array $tables): bool { return isset($tables[$name]); }

function bindValues(mysqli_stmt $stmt, string $types, array &$values): void {
    if ($types === '') return;
    $refs = [$types]; foreach ($values as $key => &$value) $refs[] = &$value;
    call_user_func_array([$stmt, 'bind_param'], $refs);
}

function fkOptions(mysqli $conn, array $field): array {
    $table=$field['table']; $key=$field['key']; $display=$field['display'];
    $result=$conn->query("SELECT `$key`, `$display` FROM `$table` ORDER BY `$key` DESC"); $items=[];
    if ($result) {
        while($row=$result->fetch_assoc()) $items[]=$row;
    }
    return $items;
}

function displayValue(mysqli $conn, array $field, $value): string {
    if($field['type']!=='fk' || $value===null) return (string)$value;
    $table=$field['table'];$key=$field['key'];$display=$field['display'];
    $stmt=$conn->prepare("SELECT `$display` AS label FROM `$table` WHERE `$key`=?");
    if ($stmt) {
        $stmt->bind_param('i',$value);$stmt->execute();$row=$stmt->get_result()->fetch_assoc();$stmt->close();
        return $row['label'] ?? (string)$value;
    }
    return (string)$value;
}

function uploadFile(string $fieldName, array $field): ?string {
    if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error']===UPLOAD_ERR_NO_FILE) return null;
    $file=$_FILES[$fieldName];
    if ($file['error']!==UPLOAD_ERR_OK) throw new RuntimeException('Failed to upload '.$field['label'].'.');
    $isImage=$field['type']==='image';
    $max=$isImage?5*1024*1024:15*1024*1024;
    if ($file['size']>$max) throw new RuntimeException('File size for '.$field['label'].' exceeds the limit of '.($isImage?'5 MB':'15 MB').'.');
    $allowed=$isImage?['jpg'=>'image/jpeg','jpeg'=>'image/jpeg','png'=>'image/png','webp'=>'image/webp']:['mp3'=>'audio/mpeg','wav'=>'audio/wav','ogg'=>'audio/ogg','m4a'=>'audio/mp4'];
    $ext=strtolower(pathinfo($file['name'],PATHINFO_EXTENSION));
    $mime=(new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    if(!isset($allowed[$ext]) || $mime!==$allowed[$ext]) throw new RuntimeException('Unsupported file format for '.$field['label'].'.');
    $folder=__DIR__.'/../../uploads/'.$field['folder'];
    if(!is_dir($folder) && !mkdir($folder,0775,true)) throw new RuntimeException('Unable to create upload directory.');
    $filename=bin2hex(random_bytes(12)).'.'.$ext;
    if(!move_uploaded_file($file['tmp_name'],$folder.'/'.$filename)) throw new RuntimeException('Failed to save uploaded file.');
    return 'uploads/'.$field['folder'].'/'.$filename;
}

function inputType(array $field): string {
    return match($field['type']) {'datetime'=>'datetime-local','textarea'=>'textarea','fk'=>'fk','enum'=>'enum','image'=>'file','audio'=>'file',default=>$field['type']};
}

function buildAdminUrl(array $overrides = []): string {
    global $table, $keyword, $filterVal, $perPage, $sortCol, $sortOrder, $halaman;
    $base = [
        'page' => 'list',
        'table' => $table ?? 'albums',
        'keyword' => $keyword ?? '',
        'filter' => $filterVal ?? '',
        'per_page' => $perPage ?? 10,
        'sort' => $sortCol ?? '',
        'order' => $sortOrder ?? 'DESC',
        'halaman' => $halaman ?? 1,
    ];
    $merged = array_merge($base, $overrides);
    foreach ($merged as $k => $v) {
        if ($v === '' || $v === null) {
            unset($merged[$k]);
        }
    }
    return 'index.php?' . http_build_query($merged);
}
