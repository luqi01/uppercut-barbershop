<?php
session_start();
include 'db.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

if (!isset($_SESSION['customer_id'])) {
    header("Location: login_register.php");
    exit;
}

$message = "";

// Fetch barbers & services (with price)
$barbers  = $conn->query("SELECT id, username FROM users WHERE role='barber'");
$services = $conn->query("SELECT service_id, service_name, duration, price FROM services ORDER BY service_name");

// Handle Booking Submission
if (isset($_POST['book'])) {
    $customer_id = intval($_SESSION['customer_id']);
    $barber_id   = intval($_POST['barber_id'] ?? 0);
    $service_ids = $_POST['services'] ?? [];
    $date        = $_POST['appointment_date'] ?? '';
    $time        = $_POST['appointment_time'] ?? '';

    if (empty($barber_id) || empty($service_ids) || empty($date) || empty($time)) {
        $message = "⚠️ Please fill in all fields.";
    } else {
        $dayOfWeek = date('w', strtotime($date));
        if ($dayOfWeek == 0) {
            $message = "❌ We are closed on Sundays.";
        } elseif (strtotime($date) < strtotime(date('Y-m-d'))) {
            $message = "❌ You cannot book past dates.";
        } else {
            $hour = (int)date("H", strtotime($time));
            if ($hour < 8 || $hour >= 17) {
                $message = "❌ Bookings are only available between 08:00 and 17:00.";
            } else {
                $currentDate = date('Y-m-d');
                $currentTime = date('H:i');
                if ($date === $currentDate && $time <= $currentTime) {
                    $message = "❌ You cannot book a past time.";
                } else {
                    $check = $conn->prepare("
                        SELECT COUNT(*) FROM appointments
                        WHERE barber_id = ? AND appointment_date = ? AND appointment_time = ?
                        AND status != 'Cancelled'
                    ");
                    $check->bind_param("iss", $barber_id, $date, $time);
                    $check->execute();
                    $check->bind_result($count);
                    $check->fetch();
                    $check->close();

                    if ($count > 0) {
                        $message = "❌ This slot is already booked.";
                    } else {
                        $ids = array_map('intval', $service_ids);
                        $placeholders = implode(',', array_fill(0, count($ids), '?'));
                        $types = str_repeat('i', count($ids));

                        $svc_stmt = $conn->prepare("SELECT service_id, service_name, duration, price FROM services WHERE service_id IN ($placeholders)");
                        $svc_stmt->bind_param($types, ...$ids);
                        $svc_stmt->execute();
                        $svc_res = $svc_stmt->get_result();

                        $selectedServices = [];
                        $totalPrice = 0.00;
                        while ($row = $svc_res->fetch_assoc()) {
                            $selectedServices[] = $row;
                            $totalPrice += (float)$row['price'];
                        }
                        $svc_stmt->close();

                        if (empty($selectedServices)) {
                            $message = "❌ Please select at least one valid service.";
                        } else {
                            // Insert appointment
                            $stmt = $conn->prepare("
                                INSERT INTO appointments (customer_id, barber_id, appointment_date, appointment_time, status)
                                VALUES (?, ?, ?, ?, 'Pending')
                            ");
                            $stmt->bind_param("iiss", $customer_id, $barber_id, $date, $time);
                            $stmt->execute();
                            $appointment_id = $conn->insert_id;
                            $stmt->close();

                            // Link services
                            $link_stmt = $conn->prepare("INSERT INTO appointment_services (appointment_id, service_id) VALUES (?, ?)");
                            foreach ($ids as $sid) {
                                $link_stmt->bind_param("ii", $appointment_id, $sid);
                                $link_stmt->execute();
                            }
                            $link_stmt->close();

                            $custName   = $_SESSION['customer_name'] ?? 'Customer';
                            $custEmail  = $_SESSION['customer_email'] ?? '';
                            $barberName = ($barber_id == 1) ? "Faizal" : "Zaid";
                            $barberEmail = ($barber_id == 1)
                                ? "faizal.uppercut@gmail.com"
                                : "zaid.uppercut@gmail.com";

                            $svcHtml = "<ul style='margin:8px 0 0 18px;'>";
                            foreach ($selectedServices as $svc) {
                                $svcHtml .= "<li>" . htmlspecialchars($svc['service_name']) . " — R" . number_format((float)$svc['price'], 0) . "</li>";
                            }
                            $svcHtml .= "</ul>";

                            $message = "✅ Appointment booked successfully! A confirmation email has been sent.";

                            // EMAIL: Customer Confirmation (via Brevo)
                            try {
                                $mail = new PHPMailer(true);
                                $mail->isSMTP();
                                $mail->Host       = getenv('MAIL_HOST');
                                $mail->SMTPAuth   = true;
                                $mail->Username   = getenv('MAIL_USER');
                                $mail->Password   = getenv('MAIL_PASS');
                                $mail->SMTPSecure = 'tls';
                                $mail->Port       = getenv('MAIL_PORT');

                                $mail->setFrom(getenv('MAIL_USER'), 'UPPERCUT Barbershop');
                                if (!empty($custEmail)) {
                                    $mail->addAddress($custEmail, $custName);
                                }

                                $mail->isHTML(true);
                                $mail->Subject = 'Booking Confirmation - UPPERCUT Barbershop';
                                $mail->Body = "
                                    <div style='background:#0d0d0d;color:#fff;font-family:Poppins,Arial,sans-serif;padding:20px;border-radius:10px;border:1px solid #d4af37;'>
                                        <h2 style='color:#d4af37;margin:0 0 6px;'>Appointment Booked ✂️</h2>
                                        <p style='margin:8px 0;'>Hi <b>".htmlspecialchars($custName)."</b>,</p>
                                        <p>Your appointment has been successfully booked with <b>".htmlspecialchars($barberName)."</b>.</p>
                                        <table style='width:100%;margin:10px 0;border-collapse:collapse;'>
                                            <tr><td><b>Date:</b></td><td>".htmlspecialchars($date)."</td></tr>
                                            <tr><td><b>Time:</b></td><td>".htmlspecialchars($time)."</td></tr>
                                            <tr><td style='vertical-align:top;'><b>Services:</b></td><td>$svcHtml</td></tr>
                                            <tr><td><b>Total:</b></td><td><b style='color:#d4af37;'>R".number_format($totalPrice, 0)."</b></td></tr>
                                        </table>
                                        <p style='margin-top:10px;'>We look forward to seeing you at <b>UPPERCUT Barbershop</b>!</p>
                                    </div>
                                ";
                                $mail->send();
                            } catch (Exception $e) {
                                error_log("Customer Mail Error: " . $mail->ErrorInfo);
                            }

                            // EMAIL: Barber Notification
                            try {
                                $mail2 = new PHPMailer(true);
                                $mail2->isSMTP();
                                $mail2->Host       = getenv('MAIL_HOST');
                                $mail2->SMTPAuth   = true;
                                $mail2->Username   = getenv('MAIL_USER');
                                $mail2->Password   = getenv('MAIL_PASS');
                                $mail2->SMTPSecure = 'tls';
                                $mail2->Port       = getenv('MAIL_PORT');

                                $mail2->setFrom(getenv('MAIL_USER'), 'UPPERCUT Barbershop');
                                $mail2->addAddress($barberEmail, $barberName);
                                if (!empty($custEmail)) {
                                    $mail2->addReplyTo($custEmail, $custName);
                                }

                                $mail2->isHTML(true);
                                $mail2->Subject = 'New Booking - UPPERCUT';
                                $mail2->Body = "
                                    <div style='background:#0d0d0d;color:#fff;font-family:Poppins,Arial,sans-serif;padding:20px;border-radius:10px;border:1px solid #d4af37;'>
                                        <h2 style='color:#d4af37;margin:0 0 6px;'>New Appointment</h2>
                                        <p>Hello <b>".htmlspecialchars($barberName)."</b>,</p>
                                        <p><b>".htmlspecialchars($custName)."</b> has booked an appointment.</p>
                                        <table style='width:100%;margin:10px 0;border-collapse:collapse;'>
                                            <tr><td><b>Date:</b></td><td>".htmlspecialchars($date)."</td></tr>
                                            <tr><td><b>Time:</b></td><td>".htmlspecialchars($time)."</td></tr>
                                            <tr><td style='vertical-align:top;'><b>Services:</b></td><td>$svcHtml</td></tr>
                                            <tr><td><b>Total:</b></td><td><b style='color:#d4af37;'>R".number_format($totalPrice, 0)."</b></td></tr>
                                        </table>
                                        <p style='margin-top:10px;'>Please log in to approve or decline.</p>
                                    </div>
                                ";
                                $mail2->send();
                            } catch (Exception $e) {
                                error_log("Barber Mail Error: " . $mail2->ErrorInfo);
                            }
                        }
                    }
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Book Appointment | UPPERCUT</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<style>
body {
  font-family: 'Poppins', sans-serif;
  background: linear-gradient(135deg, #0d0d0d 0%, #1a1a1a 100%);
  min-height: 100vh;
  color: #fff;
  overflow-x: hidden;
}
.navbar {
  background-color: #000 !important;
  border-bottom: 1px solid #d4af37;
  box-shadow: 0 4px 10px rgba(0,0,0,0.6);
}
.navbar-brand, .nav-link {
  color: #fff !important;
  transition: all 0.3s ease;
  font-weight: 600;
}
.nav-link:hover {
  color: #d4af37 !important;
}
.container {
  background: rgba(15,15,15,0.8);
  backdrop-filter: blur(8px);
  padding: 3rem;
  border-radius: 15px;
  border: 1px solid rgba(212,175,55,0.3);
  box-shadow: 0 8px 30px rgba(0,0,0,0.7);
  max-width: 600px;
  margin: 120px auto 50px;
}
h2 {
  color: #d4af37;
  text-align: center;
  font-weight: 700;
  margin-bottom: 2rem;
  letter-spacing: 1px;
}
.form-control, select {
  background: rgba(40,40,40,0.9);
  border: 1px solid #555;
  color: #fff;
  border-radius: 8px;
  padding: 0.8rem;
}
.form-control:focus, select:focus {
  background: rgba(60,60,60,0.95);
  border-color: #d4af37;
  box-shadow: 0 0 8px rgba(212,175,55,0.5);
}
.btn-primary {
  background: #d4af37;
  border: none;
  font-weight: 600;
  padding: 0.8rem;
  border-radius: 8px;
  width: 100%;
}
.btn-primary:hover {
  background: #fff;
  color: #000;
}
.alert {
  background: rgba(255,255,255,0.1);
  border-left: 4px solid #d4af37;
  color: #fff;
  border-radius: 5px;
  padding: 1rem;
}
.total-wrap {
  margin-top: .75rem;
  padding: .75rem;
  border: 1px dashed rgba(212,175,55,0.5);
  border-radius: 8px;
  background: rgba(0,0,0,0.25);
}
.total-label { color: #bbb; }
.total-value { color: #d4af37; font-weight: 700; }
.form-control, select, input, option {
  color: #f5f5f5 !important;
  background-color: #222 !important;
}

.form-control::placeholder, option {
  color: #ccc !important;
}

small.text-muted {
  color: #aaa !important;
}

label {
  color: #f5f5f5;
}

</style>
</head>
<?php include 'navbar.php'; ?>
<body>
<div class="container">
  <h2>Book an Appointment</h2>
  <?php if ($message): ?>
      <div class="alert alert-info"><?php echo $message; ?></div>
  <?php endif; ?>

  <form method="POST" id="bookingForm">
    <div class="mb-3">
      <label>Select Barber</label>
      <select name="barber_id" id="barber_id" class="form-control" required>
        <option value="">-- Choose Barber --</option>
        <?php
        // re-run barbers resultset for the form if consumed earlier
        if ($barbers && $barbers->num_rows > 0) {
            $barbers->data_seek(0);
            while ($b = $barbers->fetch_assoc()):
        ?>
          <option value="<?php echo $b['id']; ?>"><?php echo htmlspecialchars($b['username']); ?></option>
        <?php
            endwhile;
        }
        ?>
      </select>
    </div>

    <div class="mb-3">
      <label>Select Services</label>
      <select name="services[]" id="services" class="form-control" multiple required>
        <?php
        if ($services && $services->num_rows > 0) {
            $services->data_seek(0);
            while ($s = $services->fetch_assoc()):
        ?>
          <option
            value="<?php echo $s['service_id']; ?>"
            data-price="<?php echo (float)$s['price']; ?>"
          >
            <?php echo htmlspecialchars($s['service_name']); ?>
            (<?php echo (int)$s['duration']; ?> mins) — R<?php echo number_format((float)$s['price'], 0); ?>
          </option>
        <?php
            endwhile;
        }
        ?>
      </select>
      <small class="text-muted">Hold CTRL (Windows) or CMD (Mac) to select multiple.</small>

      <div class="total-wrap">
        <div class="d-flex justify-content-between">
          <span class="total-label">Total:</span>
          <span class="total-value" id="total_display">R0</span>
        </div>
      </div>
    </div>

    <div class="mb-3">
      <label>Select Date</label>
      <input type="date" name="appointment_date" id="appointment_date"
             class="form-control"
             min="<?php echo date('Y-m-d'); ?>"
             required>
      <small class="text-muted">Sundays are unavailable.</small>
    </div>

    <div class="mb-3">
      <label>Available Times</label>
      <select name="appointment_time" id="appointment_time" class="form-control" required>
        <option value="">-- Select Time --</option>
      </select>
    </div>

    <button type="submit" name="book" class="btn btn-primary">Book Appointment</button>
  </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  const barberSelect   = document.getElementById('barber_id');
  const dateSelect     = document.getElementById('appointment_date');
  const timeSelect     = document.getElementById('appointment_time');
  const servicesSelect = document.getElementById('services');
  const totalDisplay   = document.getElementById('total_display');

  //  Disable Sundays
  dateSelect.addEventListener('change', function() {
    const day = new Date(this.value + 'T00:00:00').getDay();
    if (day === 0) {
      alert("❌ We are closed on Sundays. Please select another date.");
      this.value = '';
      timeSelect.innerHTML = '<option value="">-- Select Time --</option>';
      return;
    }
    fetchTimes();
  });

  //  Fetch available times dynamically
  async function fetchTimes() {
    const barber_id = barberSelect.value;
    const date = dateSelect.value;

    if (!barber_id || !date) return;

    timeSelect.innerHTML = '<option value="">Loading...</option>';

    try {
      const response = await fetch(`get_times.php?barber_id=${encodeURIComponent(barber_id)}&date=${encodeURIComponent(date)}`);
      const text = await response.text();
      console.log(" Raw Response:", text);

      const cleaned = text.trim().replace(/^<!--.*?-->/g, "");
      const data = JSON.parse(cleaned);

      console.log(" Parsed Times:", data);

      timeSelect.innerHTML = '<option value="">-- Select Time --</option>';

      if (!Array.isArray(data) || data.length === 0) {
        const opt = document.createElement('option');
        opt.text = 'No available times';
        opt.disabled = true;
        timeSelect.add(opt);
      } else {
        data.forEach(time => {
          const opt = document.createElement('option');
          opt.value = time;
          opt.text = time;
          timeSelect.add(opt);
        });
      }

    } catch (err) {
      console.error(" Error loading times:", err);
      timeSelect.innerHTML = '<option value="">Error loading times</option>';
    }
  }

  //  Event triggers
  barberSelect.addEventListener('change', fetchTimes);
  dateSelect.addEventListener('change', fetchTimes);

  //  Live total price calculation
  function updateTotal() {
    let total = 0;
    Array.from(servicesSelect.selectedOptions).forEach(opt => {
      const price = parseFloat(opt.getAttribute('data-price') || '0');
      total += price;
    });
    totalDisplay.textContent = 'R' + Math.round(total);
  }

  servicesSelect.addEventListener('change', updateTotal);
});
</script>
</body>
</html>
