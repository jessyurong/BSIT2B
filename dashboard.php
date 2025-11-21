<?php
session_start();
if(empty($_SESSION['user_id'])){
  header('Location: index.html');
  exit;
}

// DB access to read avatar
require_once __DIR__ . '/db.php';

$userId = $_SESSION['user_id'];
$name = htmlspecialchars($_SESSION['user_name']);
$role = htmlspecialchars($_SESSION['user_role']);
// simple CSRF token for profile form
if(empty($_SESSION['csrf'])){
  $_SESSION['csrf'] = bin2hex(random_bytes(16));
}
$msg = isset($_GET['msg']) ? $_GET['msg'] : '';

// load avatar filename from DB (if any)
$avatar = '';
try {
  $s = $pdo->prepare('SELECT avatar FROM users WHERE id = ? LIMIT 1');
  $s->execute([$userId]);
  $r = $s->fetch();
  if($r && !empty($r['avatar'])){
    $avatar = $r['avatar'];
  }
} catch (Exception $e) {
  // ignore - fallback to default placeholder
}
// provide a URL for avatar (or placeholder)
$avatar_url = $avatar ? 'uploads/avatars/' . rawurlencode($avatar) : 'https://via.placeholder.com/120?text=Avatar';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Dashboard</title>
  <link rel="stylesheet" href="style.css">
 
</head>
<body>
  <main class="main">
    <div class="dash">
      <h2>Welcome, <?php echo $name; ?>!</h2>
      <p>Your role: <strong><?php echo $role; ?></strong></p>
      <p>This is a protected page — only logged-in users can see this.</p>

      <?php if($msg): ?>
        <p style="color:green;margin-top:8px"><?php echo htmlspecialchars($msg); ?></p>
      <?php endif; ?>

      <button onclick="location.href='afterdash.php'">dashboard</button>
          </form>
        </div>
      </div>
    </div>
  </main>
</body>
</html>
