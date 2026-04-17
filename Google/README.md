# Google OAuth 2.0 Login System (Core PHP)

## Setup

1. Copy `.env.example` to `.env` and fill in your values.
2. Run `composer install` in the project folder.
3. Create the database and run `schema.sql`.
4. Serve the folder using PHP or your web server.

## Files

- `index.php` — Login page and profile display
- `google-login.php` — Redirects user to Google OAuth consent screen
- `callback.php` — Handles OAuth callback and token exchange
- `logout.php` — Clears session and logs user out
- `config.php` — Loads environment configuration
- `db.php` — Creates secure PDO database connection
- `functions.php` — OAuth, session, and user helper functions
- `schema.sql` — MySQL table schema for user storage

## Notes

- Use HTTPS in production and set `SESSION_SECURE` to `true`.
- Use prepared statements and CSRF `state` validation for security.
- Store sensitive credentials outside version control.
