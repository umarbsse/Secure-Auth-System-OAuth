<?php
/**
 * index.php - Main landing page
 * Displays login button or user info if already logged in
 */

require_once 'config.php';
session_start();

// Check if user is already logged in
$user = isset($_SESSION[USER_SESSION_KEY]) ? $_SESSION[USER_SESSION_KEY] : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google OAuth Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            text-align: center;
        }
        .login-btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #4285f4;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 16px;
        }
        .login-btn:hover {
            background-color: #357ae8;
        }
        .user-info {
            background: #f5f5f5;
            padding: 20px;
            border-radius: 8px;
            text-align: left;
        }
        .user-info img {
            border-radius: 50%;
            margin-right: 15px;
        }
        .logout-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 8px 16px;
            background-color: #dc3545;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <h1>Google OAuth 2.0 Login</h1>
    
    <?php if ($user): ?>
        <!-- User is logged in - show profile -->
        <div class="user-info">
            <h2>Welcome, <?php echo htmlspecialchars($user['name']); ?>!</h2>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Google ID:</strong> <?php echo htmlspecialchars($user['id']); ?></p>
            <?php if (!empty($user['picture'])): ?>
                <img src="<?php echo htmlspecialchars($user['picture']); ?>" alt="Profile Picture" width="100" height="100">
            <?php endif; ?>
        </div>
        <a href="logout.php" class="logout-btn">Logout</a>
    <?php else: ?>
        <!-- User is not logged in - show login button -->
        <p>Click below to sign in with your Google account</p>
        <a href="login.php" class="login-btn">Login with Google</a>
    <?php endif; ?>
</body>
</html>