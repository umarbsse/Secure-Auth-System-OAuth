<?php
session_start();
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facebook OAuth Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            background-color: #f0f2f5;
        }
        .container {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
        }
        h1 { color: #1877f2; margin-bottom: 1.5rem; }
        .user-info {
            text-align: left;
            margin: 1rem 0;
            padding: 1rem;
            background: #e7f3ff;
            border-radius: 8px;
        }
        .user-info p { margin: 0.5rem 0; }
        .btn-facebook {
            display: inline-block;
            background: #1877f2;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
        }
        .btn-facebook:hover { background: #166fe5; }
        .btn-logout {
            display: inline-block;
            background: #dc3545;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 6px;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Facebook OAuth Login</h1>
        
        <?php if (isset($_SESSION['fb_user'])): ?>
            <div class="user-info">
                <p><strong>ID:</strong> <?php echo htmlspecialchars($_SESSION['fb_user']['id']); ?></p>
                <p><strong>Name:</strong> <?php echo htmlspecialchars($_SESSION['fb_user']['name']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['fb_user']['email'] ?? 'Not available'); ?></p>
            </div>
            <a href="logout.php" class="btn-logout">Logout</a>
        <?php else: ?>
            <a href="login.php" class="btn-facebook">Login with Facebook</a>
        <?php endif; ?>
    </div>
</body>
</html>