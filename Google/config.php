<?php
/**
 * Configuration file for Google OAuth 2.0
 * 
 * Replace the values below with your actual Google OAuth credentials
 * Get them from: https://console.cloud.google.com/apis/credentials
 */

// Your Google OAuth credentials
define('GOOGLE_CLIENT_ID', 'YOUR_CLIENT_ID.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET', 'YOUR_CLIENT_SECRET');

// Your redirect URI (must match exactly in Google Console)
define('GOOGLE_REDIRECT_URI', 'http://localhost:8000/callback.php');

// OAuth endpoints
define('GOOGLE_AUTH_URL', 'https://accounts.google.com/o/oauth2/v2/auth');
define('GOOGLE_TOKEN_URL', 'https://oauth2.googleapis.com/token');
define('GOOGLE_USERINFO_URL', 'https://www.googleapis.com/oauth2/v2/userinfo');

// Scopes (what data we want to access)
define('GOOGLE_SCOPES', 'openid email profile');

// Session key for storing user data
define('USER_SESSION_KEY', 'user');