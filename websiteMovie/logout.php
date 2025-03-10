<?php
// Start session
session_start();

// Destroy all sessions
session_unset();
session_destroy();

// Redirect to login page
header("Location: user-login.php");
exit;
?>
