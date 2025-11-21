<?php
// upload_avatar.php - handle avatar upload for logged-in user
require_once __DIR__ . '/db.php';
session_start();

if(empty($_SESSION['user_id'])){
    header('Location: index.html');
    exit;
}

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    header('Location: dashboard.php');
    exit;
}

$csrf = $_POST['csrf'] ?? '';
if(empty($csrf) || empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $csrf)){
    header('Location: dashboard.php?msg=' . urlencode('Invalid request'));
    exit;
}

if(empty($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK){
    header('Location: dashboard.php?msg=' . urlencode('No file uploaded'));
    exit;
}

$file = $_FILES['avatar'];
// Basic validations
$maxSize = 2 * 1024 * 1024; // 2 MB
if($file['size'] > $maxSize){
    header('Location: dashboard.php?msg=' . urlencode('File too large (max 2MB)'));
    exit;
}

// Verify mime type using finfo
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->file($file['tmp_name']);
$allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif'];
if(!isset($allowed[$mime])){
    header('Location: dashboard.php?msg=' . urlencode('Invalid image type'));
    exit;
}

$ext = $allowed[$mime];
$userId = (int) $_SESSION['user_id'];

// ensure upload directory exists
$uploadDir = __DIR__ . '/uploads/avatars';
if(!is_dir($uploadDir)){
    mkdir($uploadDir, 0755, true);
}

// generate filename
$filename = $userId . '_' . time() . '.' . $ext;
$target = $uploadDir . DIRECTORY_SEPARATOR . $filename;

if(!move_uploaded_file($file['tmp_name'], $target)){
    header('Location: dashboard.php?msg=' . urlencode('Failed to move uploaded file'));
    exit;
}

// update DB and remove old avatar if present
try{
    $stmt = $pdo->prepare('SELECT avatar FROM users WHERE id = ? LIMIT 1');
    $stmt->execute([$userId]);
    $row = $stmt->fetch();
    $old = $row['avatar'] ?? '';

    $upd = $pdo->prepare('UPDATE users SET avatar = ? WHERE id = ?');
    $upd->execute([$filename, $userId]);

    // delete old file if exists and not same as new
    if($old && $old !== $filename){
        $oldPath = $uploadDir . DIRECTORY_SEPARATOR . $old;
        if(is_file($oldPath)){
            @unlink($oldPath);
        }
    }

    header('Location: dashboard.php?msg=' . urlencode('Avatar uploaded'));
    exit;
} catch (Exception $e){
    // cleanup uploaded file
    if(is_file($target)){@unlink($target);} 
    header('Location: dashboard.php?msg=' . urlencode('Server error'));
    exit;
}
