<?php

require_once __DIR__ . '/functions.php';

startSecureSession();
$loggedIn = isLoggedIn();
$currentUser = $loggedIn ? getCurrentUser() : [];
$errorMessage = '';

if (!empty($_GET['error'])) {
    $errorMessage = cleanText($_GET['error']);
}
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google Login</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f7fb; color: #333; margin: 0; padding: 0; }
        .page { max-width: 520px; margin: 4rem auto; padding: 2rem; background: #fff; border-radius: 12px; box-shadow: 0 20px 40px rgba(60, 72, 88, 0.12); }
        .button { display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.9rem 1.4rem; color: #fff; background: #4285f4; border-radius: 8px; text-decoration: none; font-weight: 600; }
        .button img { width: 20px; }
        .profile { text-align: center; }
        .profile img { border-radius: 999px; width: 96px; height: 96px; object-fit: cover; }
        .profile .name { margin: 1rem 0 0.25rem; font-size: 1.3rem; }
        .profile .email { color: #555; }
        .notice { margin-bottom: 1rem; color: #b00020; }
    </style>
</head>
<body>
<div class="page">
    <?php if ($loggedIn && $currentUser): ?>
        <div class="profile">
            <img src="<?= htmlspecialchars($currentUser['picture'], ENT_QUOTES, 'UTF-8') ?>" alt="Profile picture">
            <div class="name"><?= htmlspecialchars($currentUser['name'], ENT_QUOTES, 'UTF-8') ?></div>
            <div class="email"><?= htmlspecialchars($currentUser['email'], ENT_QUOTES, 'UTF-8') ?></div>
        </div>
        <p style="text-align:center; margin: 1.5rem 0;">You are signed in with Google.</p>
        <p style="text-align:center;"><a class="button" href="logout.php">Logout</a></p>
    <?php else: ?>
        <h1>Login with Google</h1>
        <?php if ($errorMessage): ?>
            <div class="notice">Error: <?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>
        <p>Click below to sign in with Google and allow this app to read your profile.</p>
        <p><a class="button" href="google-login.php"><img src="https://www.gstatic.com/devrel-devsite/v0/b/google-devrel-prod.appspot.com/o/images%2Fsquare-google.svg?alt=media" alt="Google">Login with Google</a></p>
    <?php endif; ?>
</div>
</body>
</html>
