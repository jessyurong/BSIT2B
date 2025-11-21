<?php
// login.php - process login form (POST)
require_once __DIR__ . '/db.php';
session_start();

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    header('Location: index.html');
    exit;
}

$email = trim(strtolower($_POST['email'] ?? ''));
$password = $_POST['password'] ?? '';

if(!$email || !$password){
    header('Location: index.html?login=failed');
    exit;
}

try{
    $stmt = $pdo->prepare('SELECT id, name, email, password, role FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if(!$user){
        header('Location: index.html?login=failed');
        exit;
    }

    if(password_verify($password, $user['password'])){
        // success
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];
        header('Location: dashboard.php');
        exit;
    } else {
        header('Location: index.html?login=failed');
        exit;
    }
} catch (Exception $e){
    // on error, redirect with failure
    header('Location: index.html?login=failed');
    exit;
}
