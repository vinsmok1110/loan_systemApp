<?php
// Start the session
session_start();

// Unset specific session variables if needed
unset($_SESSION['user_id']); // Example: Unset specific session variable

// Unset all of the session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to the login page or any other page you prefer
header("Location: ../../index.php");
exit;
?>
