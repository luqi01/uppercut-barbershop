<?php
session_start();

// Prevent cached pages
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Destroy all session data
session_unset();
session_destroy();

// Redirect to home page
header("Location: index.php");
exit;
?>
