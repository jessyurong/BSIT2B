<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>afterdash</title>
</head>
 <style> .dash
  {max-width:980px;
  margin:40px auto;
  padding:20px;
  background:#fff;
  border-radius:8px;
 }
  .dash h2{color:#1877f2}
  .dash p{color:#333}
  .logout{
    display:inline-block;
    margin-top:12px;
    padding:8px 12px;
    border-radius:6px;
    background:#e4e6eb;
    color:#111;
    text-decoration:none
  }
  </style>


<body>
     <main class="main">
    <div class="dash">
      <h2>Welcome, <?php echo $name; ?>!</h2>
      <p>Your role: <strong><?php echo $role; ?></strong></p>
      <p>This is a protected page — only logged-in users can see this.</p>

      <div style="display:flex;gap:18px;align-items:flex-start;margin-top:12px">
        <div style="min-width:120px">
          <img src="<?php echo $avatar_url; ?>" alt="Avatar" style="width:120px;height:120px;border-radius:8px;object-fit:cover;border:1px solid #ddd">
          <form action="upload_avatar.php" method="post" enctype="multipart/form-data" style="margin-top:8px;display:flex;gap:8px;flex-direction:column">
            <input type="file" name="avatar" accept="image/*" required>
            <input type="hidden" name="csrf" value="<?php echo $_SESSION['csrf']; ?>">
            <button class="btn" type="submit">Upload avatar</button>
          </form>
        </div>

        <div style="flex:1">
          <h3 style="margin-top:0">Edit Profile</h3>
          <form method="POST" action="update_profile.php" style="margin-top:8px;display:flex;flex-direction:column;gap:8px;max-width:520px">
            <label style="font-size:14px">Name
              <input name="name" type="text" value="<?php echo $name; ?>" required style="width:100%;padding:8px;border-radius:6px;border:1px solid #ddd">
            </label>

            <label style="font-size:13px;color:#444">Current password (required to change password)
              <input name="current_password" type="password" placeholder="Current password" style="width:100%;padding:8px;border-radius:6px;border:1px solid #ddd">
            </label>

            <label style="font-size:13px;color:#444">New password
              <input name="new_password" type="password" placeholder="New password (min 6 chars)" style="width:100%;padding:8px;border-radius:6px;border:1px solid #ddd">
            </label>

            <label style="font-size:13px;color:#444">Confirm new password
              <input name="confirm_password" type="password" placeholder="Confirm new password" style="width:100%;padding:8px;border-radius:6px;border:1px solid #ddd">
            </label>

            <input type="hidden" name="csrf" value="<?php echo $_SESSION['csrf']; ?>">
            <div style="display:flex;gap:8px;margin-top:6px">
              <button class="btn" type="submit">Save changes</button>
              <a class="logout" href="logout.php">Log out</a>
            </div>
            </form>
</body>
</html>