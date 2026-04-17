<?php
/**
 * callback.php - Handles OAuth callback
 * 1. Validates state parameter (CSRF protection)
 * 2. Exchanges authorization code for access token
 * 3. Fetches user profile from Google API
 * 4. Stores user in session
 */

require_once 'config.php';
session_start();

// Check for errors from Google
if (isset($_GET['error'])) {
    die('OAuth Error: ' . htmlspecialchars($_GET['error_description'] ?? $_GET['error']));
}

// Check if authorization code is present
if (!isset($_GET['code'])) {
    die('No authorization code received.');
}

// Validate state parameter to prevent CSRF attacks
if (!isset($_SESSION['oauth_state']) || !isset($_GET['state'])) {
    die('Invalid state parameter.');
}

if ($_SESSION['oauth_state'] !== $_GET['state']) {
    die('State mismatch - possible CSRF attack.');
}

// Clear state from session after validation
unset($_SESSION['oauth_state']);

// Step 1: Exchange authorization code for access token
$tokenData = exchangeCodeForToken($_GET['code']);

if (!$tokenData || !isset($tokenData['access_token'])) {
    die('Failed to obtain access token.');
}

// Step 2: Fetch user profile using the access token
$userData = fetchUserInfo($tokenData['access_token']);

if (!$userData) {
    die('Failed to fetch user information.');
}

// Step 3: Store user in session
$_SESSION[USER_SESSION_KEY] = [
    'id'      => $userData['id'] ?? '',
    'name'    => $userData['name'] ?? '',
    'email'   => $userData['email'] ?? '',
    'picture' => $userData['picture'] ?? ''
];

// Redirect back to main page
header('Location: index.php');
exit;

/**
 * Exchange authorization code for access token
 * Uses cURL to make POST request to Google's token endpoint
 * 
 * @param string $code The authorization code from Google
 * @return array|false Token data on success, false on failure
 */
function exchangeCodeForToken($code) {
    // Prepare POST data
    $postData = [
        'code'          => $code,
        'client_id'     => GOOGLE_CLIENT_ID,
        'client_secret' => GOOGLE_CLIENT_SECRET,
        'redirect_uri'  => GOOGLE_REDIRECT_URI,
        'grant_type'    => 'authorization_code'
    ];

    // Initialize cURL
    $ch = curl_init();

    // Set cURL options
    curl_setopt($ch, CURLOPT_URL, GOOGLE_TOKEN_URL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);

    // Execute request
    $response = curl_exec($ch);

    // Check for cURL errors
    if (curl_errno($ch)) {
        error_log('cURL Error: ' . curl_error($ch));
        curl_close($ch);
        return false;
    }

    curl_close($ch);

    // Parse JSON response
    return json_decode($response, true);
}

/**
 * Fetch user profile from Google API
 * Uses the access token to get user information
 * 
 * @param string $accessToken The access token from Google
 * @return array|false User data on success, false on failure
 */
function fetchUserInfo($accessToken) {
    // Initialize cURL
    $ch = curl_init();

    // Set cURL options
    curl_setopt($ch, CURLOPT_URL, GOOGLE_USERINFO_URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $accessToken
    ]);

    // Execute request
    $response = curl_exec($ch);

    // Check for cURL errors
    if (curl_errno($ch)) {
        error_log('cURL Error: ' . curl_error($ch));
        curl_close($ch);
        return false;
    }

    curl_close($ch);

    // Parse JSON response
    return json_decode($response, true);
}