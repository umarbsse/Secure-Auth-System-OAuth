<?php
session_start();
require_once 'config.php';

// Generate CSRF state parameter
$state = bin2hex(random_bytes(16));
$_SESSION['oauth_state'] = $state;

// Build Facebook OAuth URL
$params = [
    'client_id'    => FB_APP_ID,
    'redirect_uri' => FB_REDIRECT_URI,
    'state'        => $state,
    'scope'        => FB_SCOPE,
    'response_type' => 'code'
];

$authUrl = FB_AUTH_URL . '?' . http_build_query($params);

// Redirect to Facebook login
header('Location: ' . $authUrl);
exit;