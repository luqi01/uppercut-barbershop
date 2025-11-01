<?php
session_start();
include 'db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'barber') {
    header("Location: login_register.php");
    exit;
}

if (isset($_POST['appointment_id'], $_POST['status'])) {
    $appointment_id = intval($_POST['appointment_id']);
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE appointments SET status = ? WHERE appointment_id = ?");
    $stmt->bind_param("si", $status, $appointment_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "✅ Appointment approved successfully!";
    } else {
        $_SESSION['message'] = "❌ Failed to update status.";
    }

    header("Location: barber_dashboard.php");
    exit;
} else {
    header("Location: barber_dashboard.php");
    exit;
}
?>
