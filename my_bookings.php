<?php
session_start();
include 'db.php';

if (!isset($_SESSION['customer_id'])) {
    header("Location: login_register.php");
    exit;
}

$customer_id = $_SESSION['customer_id'];

$query = "SELECT a.appointment_id, a.appointment_date, a.appointment_time, a.status, 
                 u.username AS barber_name, 
                 GROUP_CONCAT(s.service_name SEPARATOR ', ') AS services
          FROM appointments a
          JOIN users u ON a.barber_id = u.id
          JOIN appointment_services aps ON a.appointment_id = aps.appointment_id
          JOIN services s ON aps.service_id = s.service_id
          WHERE a.customer_id = $customer_id
          GROUP BY a.appointment_id
          ORDER BY a.appointment_date DESC, a.appointment_time ASC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Bookings | UPPERCUT</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<style>
body {
  background: #0d0d0d;
  color: #fff;
  font-family: 'Poppins', sans-serif;
  margin: 0;
  padding-top: 100px;
}
.container {
  background: rgba(0,0,0,0.8);
  border: 1px solid rgba(212,175,55,0.3);
  border-radius: 10px;
  box-shadow: 0 8px 25px rgba(0,0,0,0.6);
  padding: 2rem;
  max-width: 900px;
}
h2 {
  color: #d4af37;
  text-align: center;
  font-weight: 700;
  margin-bottom: 2rem;
}
.table {
  color: #fff;
}
.table thead {
  background: #d4af37;
  color: #000;
}
.table tbody tr {
  border-bottom: 1px solid rgba(255,255,255,0.1);
}
.status {
  font-weight: 600;
  text-transform: capitalize;
}
.status.Pending { color: #d4af37; }
.status.Completed { color: #28a745; }
.status.Cancelled { color: #dc3545; }
</style>
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container">
  <h2>My Bookings</h2>
  <?php if ($result->num_rows > 0): ?>
    <div class="table-responsive">
      <table class="table table-dark table-hover align-middle">
        <thead>
          <tr>
            <th>Date</th>
            <th>Time</th>
            <th>Barber</th>
            <th>Services</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?php echo htmlspecialchars($row['appointment_date']); ?></td>
              <td><?php echo htmlspecialchars(substr($row['appointment_time'], 0, 5)); ?></td>
              <td><?php echo htmlspecialchars($row['barber_name']); ?></td>
              <td><?php echo htmlspecialchars($row['services']); ?></td>
              <td class="status <?php echo $row['status']; ?>"><?php echo $row['status']; ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  <?php else: ?>
    <p class="text-center">No bookings found.</p>
  <?php endif; ?>
</div>
</body>
</html>
