<?php
session_start();
include 'db.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'barber') {
    header("Location: login_register.php");
    exit;
}

$barber_id = $_SESSION['user_id'];
$barber_name = $_SESSION['username'];
$message = "";

// Handle Approve / Cancel actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $appointment_id = intval($_GET['id']);

    // Fetch appointment & customer details
    $stmt = $conn->prepare("
        SELECT a.*, c.name AS customer_name, c.email AS customer_email 
        FROM appointments a 
        JOIN customers c ON a.customer_id = c.id 
        WHERE a.appointment_id = ? AND a.barber_id = ?
    ");
    $stmt->bind_param("ii", $appointment_id, $barber_id);
    $stmt->execute();
    $appointment = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($appointment) {
        $new_status = ($action == 'approve') ? 'Accepted' : 'Cancelled';
        $update = $conn->prepare("UPDATE appointments SET status=? WHERE appointment_id=?");
        $update->bind_param("si", $new_status, $appointment_id);
        $update->execute();
        $update->close();

        $custEmail = $appointment['customer_email'];
        $custName  = $appointment['customer_name'];
        $date      = $appointment['appointment_date'];
        $time      = $appointment['appointment_time'];

        // Fetch booked services
        $svc_query = $conn->query("
            SELECT s.service_name, s.price 
            FROM appointment_services ap 
            JOIN services s ON ap.service_id = s.service_id 
            WHERE ap.appointment_id = $appointment_id
        ");
        $svc_html = "<ul style='margin:8px 0 0 18px;'>";
        $total = 0;
        while ($s = $svc_query->fetch_assoc()) {
            $svc_html .= "<li>" . htmlspecialchars($s['service_name']) . " — R" . number_format($s['price'], 0) . "</li>";
            $total += $s['price'];
        }
        $svc_html .= "</ul>";

        // Send email to customer
        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'shopappointments01@gmail.com';
            $mail->Password   = 'inuazoxbdmwicspj';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('shopappointments01@gmail.com', 'UPPERCUT Barbershop');
            $mail->addAddress($custEmail, $custName);

            $mail->isHTML(true);
            $subject = ($new_status == 'Accepted') ? 'Appointment Accepted - UPPERCUT' : 'Appointment Cancelled - UPPERCUT';
            $mail->Subject = $subject;

            if ($new_status == 'Accepted') {
                $bodyMessage = "
                    <div style='background:#0d0d0d;color:#fff;font-family:Poppins,Arial,sans-serif;padding:20px;border-radius:10px;border:1px solid #d4af37;'>
                        <h2 style='color:#4CAF50;margin:0 0 10px;'>Your Appointment is Confirmed ✅</h2>
                        <p>Hi <b>{$custName}</b>,</p>
                        <p>Your appointment with <b>{$barber_name}</b> has been <b style='color:#4CAF50;'>accepted</b>.</p>
                        <table style='width:100%;margin:10px 0;'>
                            <tr><td><b>Date:</b></td><td>{$date}</td></tr>
                            <tr><td><b>Time:</b></td><td>{$time}</td></tr>
                            <tr><td><b>Services:</b></td><td>{$svc_html}</td></tr>
                            <tr><td><b>Total:</b></td><td><b style='color:#d4af37;'>R" . number_format($total, 0) . "</b></td></tr>
                            <tr><td><b>Status:</b></td><td><span style='color:#4CAF50;'>Accepted</span></td></tr>
                        </table>
                        <p style='margin-top:15px;'>We look forward to seeing you soon!</p>
                        <p style='color:#d4af37;font-weight:bold;margin-top:20px;'>UPPERCUT Barbershop 💈</p>
                    </div>
                ";
            } else {
                $bodyMessage = "
                    <div style='background:#0d0d0d;color:#fff;font-family:Poppins,Arial,sans-serif;padding:20px;border-radius:10px;border:1px solid #d4af37;'>
                        <h2 style='color:#f44336;margin:0 0 10px;'>Appointment Cancelled ❌</h2>
                        <p>Hi <b>{$custName}</b>,</p>
                        <p>Unfortunately, your appointment with <b>{$barber_name}</b> on <b>{$date}</b> at <b>{$time}</b> has been <b style='color:#f44336;'>cancelled</b>.</p>
                        <p style='margin-top:10px;'>Please log in to rebook another slot.</p>
                        <p style='color:#d4af37;font-weight:bold;margin-top:20px;'>UPPERCUT Barbershop 💈</p>
                    </div>
                ";
            }

            $mail->Body = $bodyMessage;
            $mail->send();
        } catch (Exception $e) {
            error_log("Barber email error: " . $mail->ErrorInfo);
        }

        $message = "✅ Appointment has been {$new_status} and customer notified.";
    } else {
        $message = "❌ Invalid appointment ID.";
    }
}

$query = $conn->prepare("
    SELECT a.*, c.name AS customer_name, c.phone, c.email
    FROM appointments a
    JOIN customers c ON a.customer_id = c.id
    WHERE a.barber_id = ?
    ORDER BY a.appointment_date DESC, a.appointment_time ASC
");
$query->bind_param("i", $barber_id);
$query->execute();
$result = $query->get_result();
$query->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Barber Dashboard | UPPERCUT</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<style>
body {
  background: linear-gradient(135deg, #0d0d0d, #1a1a1a);
  color: #fff;
  font-family: 'Poppins', sans-serif;
  min-height: 100vh;
}
.navbar {
  background-color: #000;
  border-bottom: 1px solid #d4af37;
  box-shadow: 0 4px 10px rgba(0,0,0,0.6);
}
.container {
  margin-top: 100px;
  background: rgba(15,15,15,0.9);
  border-radius: 10px;
  padding: 2rem;
  border: 1px solid rgba(212,175,55,0.3);
}
h2 {
  color: #d4af37;
  text-align: center;
  margin-bottom: 20px;
  font-weight: 700;
}
.table {
  color: #fff;
  text-align: center;
}
.table th {
  color: #d4af37;
}
.status {
  font-weight: 600;
  padding: 5px 10px;
  border-radius: 6px;
}
.status.pending {
  color: #000;
  background: #d4af37;
}
.status.accepted {
  color: #fff;
  background: #28a745;
}
.status.cancelled {
  color: #fff;
  background: #dc3545;
}
.btn-approve {
  background: #28a745;
  color: #fff;
  border: none;
}
.btn-cancel {
  background: #dc3545;
  color: #fff;
  border: none;
}
.btn-approve:hover, .btn-cancel:hover {
  opacity: 0.8;
}
.alert {
  background: rgba(255,255,255,0.1);
  border-left: 4px solid #d4af37;
  color: #fff;
}
</style>
</head>
<?php include 'navbar.php'; ?>
<body>
<div class="container">
  <h2>Welcome, <?php echo htmlspecialchars($barber_name); ?></h2>

  <?php if ($message): ?>
    <div class="alert alert-info"><?php echo $message; ?></div>
  <?php endif; ?>

  <table class="table table-dark table-hover align-middle">
    <thead>
      <tr>
        <th>Customer</th>
        <th>Date</th>
        <th>Time</th>
        <th>Services</th>
        <th>Total</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $result->fetch_assoc()): ?>
        <?php
          $svc_result = $conn->query("
            SELECT s.service_name, s.price 
            FROM appointment_services ap 
            JOIN services s ON ap.service_id = s.service_id 
            WHERE ap.appointment_id = {$row['appointment_id']}
          ");
          $svc_list = [];
          $total = 0;
          while ($s = $svc_result->fetch_assoc()) {
              $svc_list[] = "{$s['service_name']} (R{$s['price']})";
              $total += $s['price'];
          }
          $svc_result->close();
        ?>
        <tr>
          <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
          <td><?php echo htmlspecialchars($row['appointment_date']); ?></td>
          <td><?php echo htmlspecialchars($row['appointment_time']); ?></td>
          <td><?php echo implode('<br>', $svc_list); ?></td>
          <td>R<?php echo number_format($total, 0); ?></td>
          <td>
            <span class="status <?php echo strtolower($row['status']); ?>">
              <?php echo htmlspecialchars($row['status']); ?>
            </span>
          </td>
          <td>
            <?php if ($row['status'] == 'Pending'): ?>
              <a href="?action=approve&id=<?php echo $row['appointment_id']; ?>" class="btn btn-sm btn-approve">Approve</a>
              <a href="?action=cancel&id=<?php echo $row['appointment_id']; ?>" class="btn btn-sm btn-cancel">Cancel</a>
            <?php else: ?>
              <em>—</em>
            <?php endif; ?>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

<script>
function updateStatus(id, action) {
  fetch(`barber_dashboard.php?action=${action}&id=${id}`)
    .then(response => response.text())
    .then(() => {
      const statusCell = document.getElementById(`status-${id}`);
      const actionCell = statusCell.nextElementSibling; 

      if (action === 'approve') {
        statusCell.innerHTML = "<span class='status accepted'>Accepted</span>";
      } else if (action === 'cancel') {
        statusCell.innerHTML = "<span class='status cancelled'>Cancelled</span>";
      }

      // Hide the approve/cancel buttons
      if (actionCell) actionCell.innerHTML = "<em>—</em>";
    })
    .catch(err => console.error("Update failed:", err));
}
</script>

</body>
</html>
