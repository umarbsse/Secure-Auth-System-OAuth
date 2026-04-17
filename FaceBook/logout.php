<?php
session_start();

// Clear the user session
unset($_SESSION['fb_user']);
session_destroy();

// Send them back to the login page
header('Location: index.php');
exit;