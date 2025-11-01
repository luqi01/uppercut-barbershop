<?php
// Fetch credentials securely from environment variables
$servername = getenv('DB_HOST') ?: 'sql7.freesqldatabase.com';
$username   = getenv('DB_USER') ?: 'sql7805649';
$password   = getenv('DB_PASS') ?: 'xM5D2aJrAU';
$dbname     = getenv('DB_NAME') ?: 'sql7805649';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
