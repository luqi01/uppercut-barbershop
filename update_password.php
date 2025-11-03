<?php
include 'db.php'; // make sure this connects to your remote DB

$new_password = password_hash('Barber2025!', PASSWORD_DEFAULT);
$user_username = 'zaid'; // or use WHERE name = 'Zaid' if that's what you prefer

$sql = "UPDATE users SET password='$new_password' WHERE email='$user_username'";

if ($conn->query($sql) === TRUE) {
    echo "Password updated successfully for $user_username";
} else {
    echo "Error updating password: " . $conn->error;
}

$conn->close();
?>
