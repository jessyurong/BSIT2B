<?php
// register.php - simple signup endpoint
require_once __DIR__ . '/db.php';

$input = json_decode(file_get_contents('php://input'), true);
if(!$input){
    http_response_code(400);
    echo json_encode(['success'=>false,'message'=>'Invalid request']);
    exit;
}

$name = trim($input['name'] ?? '');
$email = trim(strtolower($input['email'] ?? ''));
$password = $input['password'] ?? '';

if(!$name || !$email || !$password){
    http_response_code(400);
    echo json_encode(['success'=>false,'message'=>'All fields are required']);
    exit;
}

if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
    http_response_code(400);
    echo json_encode(['success'=>false,'message'=>'Invalid email']);
    exit;
}

if(strlen($password) < 6){
    http_response_code(400);
    echo json_encode(['success'=>false,'message'=>'Password too short']);
    exit;
}

// check existing
try{
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    if($stmt->fetch()){
        echo json_encode(['success'=>false,'message'=>'Email already registered']);
        exit;
    }

    // store password hash in the `password` column and set default role = 'user'
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $role = 'user';
    // Insert into columns that exist in your table: name, email, password, role
    $ins = $pdo->prepare('INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)');
    $ins->execute([$name, $email, $hash, $role]);

    echo json_encode(['success'=>true,'message'=>'Account created']);
    exit;
} catch (Exception $e){
    http_response_code(500);
    echo json_encode(['success'=>false,'message'=>'Server error: '.$e->getMessage()]);
    exit;
}
