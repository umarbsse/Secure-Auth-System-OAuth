<?php
/**
 * Facebook OAuth Configuration
 * 
 * Get these values from your Facebook Developer Dashboard
 * https://developers.facebook.com/
 */

// Your app credentials from Facebook Developer Portal
$appId = 'YOUR_APP_ID_HERE';
$appSecret = 'YOUR_APP_SECRET_HERE';

// This URL must be added in your app's "Valid OAuth Redirect URIs"
$redirectUri = 'http://localhost:8000/callback.php';

// Facebook API endpoints
$facebookAuthUrl = 'https://www.facebook.com/v18.0/dialog/oauth';
$facebookTokenUrl = 'https://graph.facebook.com/v18.0/oauth/access_token';
$facebookGraphUrl = 'https://graph.facebook.com/v18.0/me';

// What data we want from the user
$permissions = 'email,public_profile';