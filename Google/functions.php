<?php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

/**
 * Ensure the session is initialized with secure cookie settings.
 */
function startSecureSession(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    session_name(SESSION_NAME);
    session_set_cookie_params([
        'lifetime' => SESSION_LIFETIME,
        'path' => '/',
        'domain' => $_SERVER['HTTP_HOST'] ?? '',
        'secure' => SESSION_SECURE,
        'httponly' => SESSION_HTTP_ONLY,
        'samesite' => SESSION_SAME_SITE,
    ]);

    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_httponly', SESSION_HTTP_ONLY ? '1' : '0');

    session_start();

    if (empty($_SESSION['session_initialized'])) {
        session_regenerate_id(true);
        $_SESSION['session_initialized'] = true;
    }
}

/**
 * Create a configured Google client for OAuth.
 */
function buildGoogleClient(): Google\Client
{
    $client = new Google\Client();
    $client->setClientId(getRequiredConfig(GOOGLE_CLIENT_ID));
    $client->setClientSecret(getRequiredConfig(GOOGLE_CLIENT_SECRET));
    $client->setRedirectUri(getRequiredConfig(GOOGLE_REDIRECT_URI));
    $client->setAccessType('offline');
    $client->setPrompt('consent');
    $client->addScope([
        Google\Service\Oauth2::USERINFO_EMAIL,
        Google\Service\Oauth2::USERINFO_PROFILE,
        'openid',
    ]);

    return $client;
}

/**
 * Generate a random state string for OAuth CSRF protection.
 */
function createOauthState(): string
{
    $state = bin2hex(random_bytes(16));
    $_SESSION['oauth2_state'] = $state;

    return $state;
}

/**
 * Validate the OAuth state string returned by Google.
 */
function validateOauthState(?string $state): bool
{
    if (empty($state) || empty($_SESSION['oauth2_state'])) {
        return false;
    }

    $isValid = hash_equals($_SESSION['oauth2_state'], $state);
    unset($_SESSION['oauth2_state']);

    return $isValid;
}

/**
 * Trim a string and normalize null values to an empty string.
 */
function cleanText(?string $value): string
{
    return trim((string) $value);
}

/**
 * Write an application error to the PHP error log.
 */
function logError(string $message): void
{
    error_log('[Google OAuth] ' . $message);
}

/**
 * Insert or update the user record in the database.
 */
function saveOrUpdateUser(PDO $db, array $user): void
{
    $sql = <<<'SQL'
INSERT INTO users (
    google_id,
    name,
    email,
    picture,
    access_token,
    refresh_token,
    token_expiry,
    updated_at
) VALUES (
    :google_id,
    :name,
    :email,
    :picture,
    :access_token,
    :refresh_token,
    :token_expiry,
    NOW()
)
ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    email = VALUES(email),
    picture = VALUES(picture),
    access_token = VALUES(access_token),
    refresh_token = CASE
        WHEN VALUES(refresh_token) IS NULL OR VALUES(refresh_token) = '' THEN refresh_token
        ELSE VALUES(refresh_token)
    END,
    token_expiry = VALUES(token_expiry),
    updated_at = NOW()
SQL;

    $statement = $db->prepare($sql);
    $statement->execute([
        ':google_id' => $user['google_id'],
        ':name' => $user['name'],
        ':email' => $user['email'],
        ':picture' => $user['picture'],
        ':access_token' => $user['access_token'],
        ':refresh_token' => $user['refresh_token'] ?? null,
        ':token_expiry' => $user['token_expiry'],
    ]);
}

/**
 * Store the logged in user in the session after successful OAuth.
 */
function createLoginSession(array $user): void
{
    startSecureSession();
    session_regenerate_id(true);

    $_SESSION['user'] = [
        'google_id' => $user['google_id'],
        'name' => $user['name'],
        'email' => $user['email'],
        'picture' => $user['picture'],
        'access_token' => $user['access_token'],
        'token_expiry' => $user['token_expiry'],
    ];
}

/**
 * Return true when a user is logged in.
 */
function isLoggedIn(): bool
{
    startSecureSession();

    return !empty($_SESSION['user']['google_id']);
}

/**
 * Get the current user from the session.
 */
function getCurrentUser(): array
{
    startSecureSession();

    return $_SESSION['user'] ?? [];
}

/**
 * Clear session data and expire the session cookie.
 */
function signOut(): void
{
    startSecureSession();

    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', [
            'expires' => time() - 42000,
            'path' => $params['path'],
            'domain' => $params['domain'],
            'secure' => $params['secure'],
            'httponly' => $params['httponly'],
            'samesite' => SESSION_SAME_SITE,
        ]);
    }

    session_destroy();
}

/**
 * Refresh an expired access token using the refresh token.
 */
function refreshAccessToken(Google\Client $client, string $refreshToken): ?array
{
    try {
        $client->refreshToken($refreshToken);

        return $client->getAccessToken();
    } catch (Throwable $exception) {
        logError($exception->getMessage());

        return null;
    }
}

/**
 * Determine whether a Google token has expired.
 */
function isTokenExpired(array $token): bool
{
    if (empty($token['access_token'])) {
        return true;
    }

    if (!empty($token['expiry'])) {
        return strtotime((string) $token['expiry']) <= time();
    }

    if (!empty($token['expires_in'])) {
        return ($token['created'] ?? time()) + (int) $token['expires_in'] <= time();
    }

    return false;
}
