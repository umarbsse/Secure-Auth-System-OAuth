<?php
/**
 * login.php - Initiates Google OAuth flow
 * Redirects user to Google's consent screen
 */

require_once 'config.php';
session_start();

// Generate state parameter for CSRF protection
// This should be a random string that's unique per session
$state = bin2hex(random_bytes(32));
$_SESSION['oauth_state'] = $state;

// Build the authorization URL with required parameters
$params = [
    'client_id'     => GOOGLE_CLIENT_ID,
    'redirect_uri'  => GOOGLE_REDIRECT_URI,
    'response_type' => 'code',
    'scope'         => GOOGLE_SCOPES,
    'state'         => $state,
    'access_type'   => 'offline',      // Get refresh token
    'prompt'        => 'consent'       // Force consent to get refresh token
];

// Redirect to Google's OAuth consent screen
$authUrl = GOOGLE_AUTH_URL . '?' . http_build_query($params);
header('Location: ' . $authUrl);
exit;