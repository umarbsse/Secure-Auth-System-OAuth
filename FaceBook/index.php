<?php
session_start();
require_once 'config.php';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login with Facebook</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background: #f0f2f5;
            margin: 0;
            padding: 20px;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-box {
            background: #fff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 400px;
            width: 100%;
        }
        h1 {
            color: #1877f2;
            margin: 0 0 30px 0;
            font-size: 24px;
        }
        .user-details {
            background: #f7f8fa;
            padding: 20px;
            border-radius: 6px;
            margin-bottom: 20px;
            text-align: left;
        }
        .user-details p {
            margin: 10px 0;
            color: #333;
        }
        .user-details strong {
            color: #666;
            display: block;
            font-size: 12px;
            margin-bottom: 4px;
        }
        .fb-btn {
            display: inline-block;
            background: #1877f2;
            color: #fff;
            padding: 14px 28px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 16px;
            transition: background 0.2s;
        }
        .fb-btn:hover {
            background: #166fe5;
        }
        .logout-btn {
            display: inline-block;
            background: #606770;
            color: #fff;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            font-size: 14px;
        }
        .logout-btn:hover {
            background: #4b4f56;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h1>Facebook Login</h1>
        
        <?php if (isset($_SESSION['fb_user'])): ?>
            <div class="user-details">
                <p><strong>User ID</strong><?php echo htmlspecialchars($_SESSION['fb_user']['id']); ?></p>
                <p><strong>Name</strong><?php echo htmlspecialchars($_SESSION['fb_user']['name']); ?></p>
                <p><strong>Email</strong><?php echo htmlspecialchars($_SESSION['fb_user']['email'] ?? 'Not provided'); ?></p>
            </div>
            <a href="logout.php" class="logout-btn">Log Out</a>
        <?php else: ?>
            <a href="login.php" class="fb-btn">Continue with Facebook</a>
        <?php endif; ?>
    </div>
</body>
</html>