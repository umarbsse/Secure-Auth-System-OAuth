# Google OAuth Login in PHP

Simple Google login using plain PHP and cURL - no frameworks needed.

## Files

- config.php - your google credentials
- index.php - main page, shows login button or user info
- login.php - sends user to google
- callback.php - handles the google response
- logout.php - logs out

## Setup

1. Go to https://console.cloud.google.com/apis/credentials
2. Create OAuth client ID (web app)
3. Add redirect URI: http://localhost:8000/callback.php
4. Copy client ID and secret to config.php
5. Run: php -S localhost:8000
6. Open http://localhost:8000

## How it works

1. User clicks login -> goes to google
2. User approves -> google sends back a code
3. callback.php swaps the code for an access token
4. Uses the token to get user info from google
5. Saves user in session, shows profile

## Notes

- Uses state parameter to prevent CSRF
- Stores user id, name, email, picture in session
- Requires PHP 7+ for random_bytes()
- `callback.php` makes POST request to `https://oauth2.googleapis.com/token`
- Uses cURL to send authorization code, client ID, client secret

### 5. Fetch user profile
- Uses access token to call `https://www.googleapis.com/oauth2/v2/userinfo`
- Gets user ID, name, email, picture

### 6. Store user in session
- Saves user data in `$_SESSION`
- Redirects to main page showing user info

## Security Features

- **State parameter**: Prevents CSRF attacks by validating a unique state per request
- **Input sanitization**: Uses `htmlspecialchars()` to prevent XSS
- **SSL verification**: cURL verifies SSL certificates
- **Error handling**: Checks for OAuth errors and cURL errors

## Testing

1. Start PHP server: `php -S localhost:8000`
2. Open browser to `http://localhost:8000`
3. Click "Login with Google"
4. After login, you should see your profile info

## Notes

- The `access_type: offline` and `prompt: consent` parameters request a refresh token
- For production, use HTTPS
- Store credentials securely and never commit them to version control