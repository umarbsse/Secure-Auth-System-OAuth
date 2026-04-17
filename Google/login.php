<?php
// login.php - send user to google

require_once 'config.php';
session_start();

// make a random string to keep things safe
$state = bin2hex(random_bytes(32));
$_SESSION['oauth_state'] = $state;

// build the google auth url
$params = [
    'client_id'     => GOOGLE_CLIENT_ID,
    'redirect_uri'  => GOOGLE_REDIRECT_URI,
    'response_type' => 'code',
    'scope'         => GOOGLE_SCOPES,
    'state'         => $state
];

// off to google we go
$authUrl = GOOGLE_AUTH_URL . '?' . http_build_query($params);
header('Location: ' . $authUrl);
exit;