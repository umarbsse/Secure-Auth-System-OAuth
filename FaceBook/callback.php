<?php
session_start();
require_once 'config.php';

// Make sure the state matches what we sent (security check)
if (empty($_GET['state']) || $_GET['state'] !== ($_SESSION['oauth_state'] ?? '')) {
    die('Something went wrong. Please try again.');
}
unset($_SESSION['oauth_state']);

// Facebook should give us a code back
if (empty($_GET['code'])) {
    die('No authorization code received.');
}

$code = $_GET['code'];

// Swap the code for an access token
$token = getAccessToken($code);

if (empty($token['access_token'])) {
    die('Could not get access token.');
}

// Now get the user's info using that token
$user = getUserInfo($token['access_token']);

if (empty($user['id'])) {
    die('Could not get user info.');
}

// Save user in session
$_SESSION['fb_user'] = [
    'id'    => $user['id'],
    'name'  => $user['name'] ?? '',
    'email' => $user['email'] ?? ''
];

// Go back to home page
header('Location: index.php');
exit;

// --- Functions ---

function getAccessToken($code) {
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $GLOBALS['facebookTokenUrl']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'client_id'     => $GLOBALS['appId'],
        'client_secret' => $GLOBALS['appSecret'],
        'redirect_uri'  => $GLOBALS['redirectUri'],
        'code'          => $code
    ]));
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true) ?: [];
}

function getUserInfo($accessToken) {
    $ch = curl_init();
    
    $url = $GLOBALS['facebookGraphUrl'] . '?' . http_build_query([
        'fields'       => 'id,name,email',
        'access_token' => $accessToken
    ]);
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true) ?: [];
}