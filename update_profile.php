<?php
// update_profile.php - update logged-in user's name and password
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

$userId = $_SESSION['user_id'];
$name = trim($_POST['name'] ?? '');
$current = $_POST['current_password'] ?? '';
$new = $_POST['new_password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';

if(!$name){
    header('Location: dashboard.php?msg=' . urlencode('Name is required'));
    exit;
}

try{
    // If user wants to change password
    if($new !== ''){
        if($new !== $confirm){
            header('Location: dashboard.php?msg=' . urlencode('Passwords do not match'));
            exit;
        }
        if(strlen($new) < 6){
            header('Location: dashboard.php?msg=' . urlencode('New password too short'));
            exit;
        }

        // verify current password
        $stmt = $pdo->prepare('SELECT password FROM users WHERE id = ? LIMIT 1');
        $stmt->execute([$userId]);
        $row = $stmt->fetch();
        if(!$row || !password_verify($current, $row['password'])){
            header('Location: dashboard.php?msg=' . urlencode('Current password is incorrect'));
            exit;
        }

        $hash = password_hash($new, PASSWORD_DEFAULT);
        $upd = $pdo->prepare('UPDATE users SET name = ?, password = ? WHERE id = ?');
        $upd->execute([$name, $hash, $userId]);
    } else {
        // only update name
        $upd = $pdo->prepare('UPDATE users SET name = ? WHERE id = ?');
        $upd->execute([$name, $userId]);
    }

    // update session name for display
    $_SESSION['user_name'] = $name;
    header('Location: dashboard.php?msg=' . urlencode('Profile updated'));
    exit;

} catch (Exception $e){
    header('Location: dashboard.php?msg=' . urlencode('Server error'));
    exit;
}
