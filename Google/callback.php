<?php

require_once __DIR__ . '/functions.php';

startSecureSession();

if (!empty($_GET['error'])) {
    $error = cleanText($_GET['error']);
    logError('OAuth error response: ' . $error);
    header('Location: index.php?error=' . urlencode($error));
    exit;
}

$authCode = $_GET['code'] ?? '';
$stateValue = $_GET['state'] ?? '';

if ($authCode === '' || $stateValue === '') {
    header('Location: index.php?error=Missing+authorization+code+or+state');
    exit;
}

if (!validateOauthState($stateValue)) {
    logError('Invalid OAuth state parameter. Possible CSRF attempt.');
    header('Location: index.php?error=Invalid+state+parameter');
    exit;
}

try {
    $client = buildGoogleClient();
    $token = $client->fetchAccessTokenWithAuthCode($authCode);

    if (isset($token['error'])) {
        throw new RuntimeException(sprintf('Token exchange failed: %s', $token['error_description'] ?? $token['error']));
    }

    $client->setAccessToken($token);
    $oauth2Service = new Google\Service\Oauth2($client);
    $profile = $oauth2Service->userinfo->get();

    $userData = [
        'google_id' => cleanText($profile->getId()),
        'name' => cleanText($profile->getName()),
        'email' => cleanText($profile->getEmail()),
        'picture' => filter_var($profile->getPicture(), FILTER_VALIDATE_URL) ? $profile->getPicture() : '',
        'access_token' => $token['access_token'] ?? '',
        'refresh_token' => $token['refresh_token'] ?? null,
        'token_expiry' => isset($token['expires_in']) ? date('Y-m-d H:i:s', time() + (int) $token['expires_in']) : null,
    ];

    saveOrUpdateUser(openDatabaseConnection(), $userData);
    createLoginSession($userData);

    header('Location: index.php');
    exit;
} catch (Throwable $exception) {
    logError($exception->getMessage());
    header('Location: index.php?error=Unable+to+complete+Google+login');
    exit;
}
