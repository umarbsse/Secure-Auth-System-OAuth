<?php
session_start();
require_once 'config.php';

// Create a random string to protect against CSRF attacks
$state = bin2hex(random_bytes(16));
$_SESSION['oauth_state'] = $state;

// Put together the Facebook login URL
$params = [
    'client_id'    => $appId,
    'redirect_uri' => $redirectUri,
    'state'        => $state,
    'scope'        => $permissions,
    'response_type' => 'code'
];

$loginUrl = $facebookAuthUrl . '?' . http_build_query($params);

// Send user to Facebook
header('Location: ' . $loginUrl);
exit;