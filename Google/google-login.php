<?php

require_once __DIR__ . '/functions.php';

startSecureSession();

try {
    $client = buildGoogleClient();
    $state = createOauthState();
    $client->setState($state);
    $authUrl = $client->createAuthUrl();

    header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));
    exit;
} catch (Throwable $exception) {
    logError($exception->getMessage());
    header('Location: index.php?error=Unable+to+start+Google+login');
    exit;
}
