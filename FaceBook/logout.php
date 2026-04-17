<?php
session_start();

// Unset Facebook user data from session
unset($_SESSION['fb_user']);

// Destroy the session
session_destroy();

// Redirect to index page
header('Location: index.php');
exit;