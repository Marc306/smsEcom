<?php
session_start();

// Destroy the session
session_destroy();

// Redirect to login page
header("Location: login2.php");
exit();
?>

