<?php
include 'db.php';

$barber_id = $_GET['barber_id'] ?? '';
$date = $_GET['date'] ?? '';

if (empty($barber_id) || empty($date)) {
    echo json_encode([]);
    exit;
}

$open_time = strtotime('08:00');
$close_time = strtotime('17:00');
$interval = 30 * 60; // 30 minutes
$times = [];

// get booked times for the selected date and barber
$stmt = $conn->prepare("SELECT appointment_time FROM appointments WHERE barber_id=? AND appointment_date=? AND status != 'Cancelled'");
$stmt->bind_param("is", $barber_id, $date);
$stmt->execute();
$result = $stmt->get_result();

$booked = [];
while ($row = $result->fetch_assoc()) {
    $booked[] = date('H:i', strtotime($row['appointment_time']));
}
$stmt->close();

// generate available slots
$today = date('Y-m-d');
$now = strtotime(date('H:i'));
for ($time = $open_time; $time < $close_time; $time += $interval) {
    $formatted = date('H:i', $time);
    // skip booked times
    if (in_array($formatted, $booked)) continue;
    // skip past times if today
    if ($date === $today && $time <= $now) continue;
    $times[] = $formatted;
}

echo json_encode($times);
?>
