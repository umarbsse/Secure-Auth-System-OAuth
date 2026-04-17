<?php
session_start();
require_once 'config.php';

// Validate state parameter (CSRF protection)
if (!isset($_GET['state']) || $_GET['state'] !== ($_SESSION['oauth_state'] ?? '')) {
    die('Invalid state parameter');
}
unset($_SESSION['oauth_state']);

// Check if authorization code is present
if (!isset($_GET['code'])) {
    die('Authorization code not received');
}

$code = $_GET['code'];

// Step 1: Exchange authorization code for access token
$tokenData = exchangeCodeForToken($code);

if (!isset($tokenData['access_token'])) {
    die('Failed to obtain access token');
}

$accessToken = $tokenData['access_token'];

// Step 2: Fetch user data from Facebook Graph API
$userData = fetchUserData($accessToken);

if (!$userData || !isset($userData['id'])) {
    die('Failed to fetch user data');
}

// Store user data in session
$_SESSION['fb_user'] = [
    'id'    => $userData['id'],
    'name'  => $userData['name'] ?? '',
    'email' => $userData['email'] ?? null
];

// Redirect to index page
header('Location: index.php');
exit;

/**
 * Exchange authorization code for access token using cURL
 */
function exchangeCodeForToken($code) {
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, FB_TOKEN_URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'client_id'     => FB_APP_ID,
        'client_secret' => FB_APP_SECRET,
        'redirect_uri'  => FB_REDIRECT_URI,
        'code'          => $code
    ]));
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
}

/**
 * Fetch user data from Facebook Graph API using cURL
 */
function fetchUserData($accessToken) {
    $ch = curl_init();
    
    $url = FB_GRAPH_URL . '?' . http_build_query([
        'fields'       => 'id,name,email',
        'access_token' => $accessToken
    ]);
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
}