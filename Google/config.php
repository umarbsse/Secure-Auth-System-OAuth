<?php

/**
 * Load environment variables from the project .env file.
 * Keep this file out of version control for security.
 */
function loadEnvFromFile(string $path = __DIR__ . '/.env'): void
{
    if (!file_exists($path)) {
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        $line = trim($line);

        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }

        [$name, $value] = array_map('trim', explode('=', $line, 2) + [1 => '']);

        if ($name === '' || array_key_exists($name, $_ENV)) {
            continue;
        }

        $_ENV[$name] = $value;
        putenv("{$name}={$value}");
    }
}

loadEnvFromFile();

/**
 * Get an environment value or fallback.
 */
function envValue(string $name, ?string $default = null): ?string
{
    $value = getenv($name);

    if ($value === false) {
        return $_ENV[$name] ?? $default;
    }

    return $value;
}

// Google OAuth configuration keys
const GOOGLE_CLIENT_ID = 'GOOGLE_CLIENT_ID';
const GOOGLE_CLIENT_SECRET = 'GOOGLE_CLIENT_SECRET';
const GOOGLE_REDIRECT_URI = 'GOOGLE_REDIRECT_URI';

// Database configuration keys
const DB_HOST = 'DB_HOST';
const DB_NAME = 'DB_NAME';
const DB_USER = 'DB_USER';
const DB_PASS = 'DB_PASS';

// Session configuration
const SESSION_NAME = 'google_oauth_session';
const SESSION_LIFETIME = 3600;
const SESSION_SECURE = false; // Set to true in production when HTTPS is enabled
const SESSION_HTTP_ONLY = true;
const SESSION_SAME_SITE = 'Lax';

/**
 * Get a required configuration value and fail early when missing.
 */
function getRequiredConfig(string $name, ?string $fallback = null): string
{
    $value = envValue($name, $fallback);

    if ($value === null || trim($value) === '') {
        throw new RuntimeException("Missing required configuration: {$name}");
    }

    return trim($value);
}
