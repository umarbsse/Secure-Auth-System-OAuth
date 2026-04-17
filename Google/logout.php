<?php
// logout.php - clear session and go back

require_once 'config.php';
session_start();

if (isset($_SESSION[USER_SESSION_KEY])) {
    unset($_SESSION[USER_SESSION_KEY]);
}

header('Location: index.php');
exit;