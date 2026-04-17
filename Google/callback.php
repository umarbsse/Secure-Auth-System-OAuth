<?php
// callback.php - handle the google callback

require_once 'config.php';
session_start();

// check for errors from google
if (isset($_GET['error'])) {
    die('Error: ' . htmlspecialchars($_GET['error_description'] ?? $_GET['error']));
}

// make sure we got the code
if (!isset($_GET['code'])) {
    die('No code received.');
}

// check the state to prevent csrf
if (!isset($_SESSION['oauth_state']) || !isset($_GET['state'])) {
    die('Invalid state.');
}

if ($_SESSION['oauth_state'] !== $_GET['state']) {
    die('State mismatch.');
}

// clean up
unset($_SESSION['oauth_state']);

// get the access token
$tokenData = getAccessToken($_GET['code']);

if (!$tokenData || !isset($tokenData['access_token'])) {
    die('Could not get access token.');
}

// get user info from google
$userData = getUserInfo($tokenData['access_token']);

if (!$userData) {
    die('Could not get user info.');
}

// save user in session
$_SESSION[USER_SESSION_KEY] = [
    'id'      => $userData['id'] ?? '',
    'name'    => $userData['name'] ?? '',
    'email'   => $userData['email'] ?? '',
    'picture' => $userData['picture'] ?? ''
];

// back to home
header('Location: index.php');
exit;

function getAccessToken($code) {
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, GOOGLE_TOKEN_URL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'code'          => $code,
        'client_id'     => GOOGLE_CLIENT_ID,
        'client_secret' => GOOGLE_CLIENT_SECRET,
        'redirect_uri'  => GOOGLE_REDIRECT_URI,
        'grant_type'    => 'authorization_code'
    ]));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
}

function getUserInfo($token) {
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, GOOGLE_USERINFO_URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $token]);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
}