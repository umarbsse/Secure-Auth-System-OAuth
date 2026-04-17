<?php
// index.php - main page, shows login or user info

require_once 'config.php';
session_start();

$user = isset($_SESSION[USER_SESSION_KEY]) ? $_SESSION[USER_SESSION_KEY] : null;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <style>
        body { font-family: sans-serif; max-width: 500px; margin: 50px auto; text-align: center; }
        .btn { background: #4285f4; color: #fff; padding: 12px 24px; text-decoration: none; border-radius: 4px; display: inline-block; }
        .btn:hover { background: #357ae8; }
        .profile { background: #f5f5f5; padding: 20px; border-radius: 8px; text-align: left; }
        .profile img { border-radius: 50%; }
        .logout { background: #dc3545; color: #fff; padding: 8px 16px; text-decoration: none; border-radius: 4px; display: inline-block; margin-top: 15px; }
    </style>
</head>
<body>
    <h1>Google Login</h1>
    
    <?php if ($user): ?>
        <div class="profile">
            <h2>Hi, <?php echo htmlspecialchars($user['name']); ?>!</h2>
            <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
            <p>ID: <?php echo htmlspecialchars($user['id']); ?></p>
            <?php if (!empty($user['picture'])): ?>
                <img src="<?php echo htmlspecialchars($user['picture']); ?>" width="80" height="80">
            <?php endif; ?>
        </div>
        <br>
        <a href="logout.php" class="logout">Logout</a>
    <?php else: ?>
        <p>Sign in with your Google account</p>
        <a href="login.php" class="btn">Login with Google</a>
    <?php endif; ?>
</body>
</html>