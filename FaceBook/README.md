# Facebook Login with PHP

Just a simple OAuth implementation for Facebook login. Plain PHP, no libraries.

## What's in the folder

- `config.php` - Put your Facebook app ID and secret here
- `index.php` - The main page, shows login button or user info
- `login.php` - Sends you to Facebook to log in
- `callback.php` - Handles what comes back from Facebook
- `logout.php` - Clears the session

## Getting it running

### Get your Facebook app credentials

1. Go to developers.facebook.com and create an app
2. Add Facebook Login as a product
3. Add this as a valid redirect URI:
   ```
   http://localhost:8000/callback.php
   ```
4. Grab your App ID and App Secret

### Update config.php

```php
$appId = 'your_actual_app_id';
$appSecret = 'your_actual_app_secret';
```

### Start the server

```bash
php -S localhost:8000
```

Then open http://localhost:8000 in your browser.

## Quick walkthrough

Click the Facebook button → log in on Facebook → get redirected back → see your name and email on the page.

The state parameter in the flow is there to prevent CSRF stuff. Email might show as empty if the user hasn't confirmed it with Facebook.

That's pretty much it. Works fine for a basic demo.