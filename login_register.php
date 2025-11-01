<?php
session_start();
include 'db.php';
$message = "";

// =========================
// HANDLE REGISTRATION
// =========================
if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "❌ Please enter a valid email address.";
    } elseif (!preg_match('/^\d{10}$/', $phone)) {
        $message = "❌ Phone number must be 10 digits.";
    } elseif (!preg_match('/^(?=.*[A-Z])(?=.*\W).{6,}$/', $password)) {
        $message = "❌ Password must have at least 6 characters, one uppercase letter, and one symbol.";
    } else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $check = $conn->query("SELECT id FROM customers WHERE email='$email'");
        if ($check->num_rows > 0) {
            $message = "⚠️ Email already registered!";
        } else {
            $conn->query("INSERT INTO customers (name, phone, email, password)
                          VALUES ('$name','$phone','$email','$password_hash')");
            $message = "✅ Registration successful! You can now log in.";
        }
    }
}

// =========================
// HANDLE LOGIN
// =========================
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check both customers and users (barbers/admins)
    $result = $conn->query("SELECT * FROM customers WHERE email='$email'");
    $user_result = $conn->query("SELECT * FROM users WHERE username='$email'");

    // --- CUSTOMER LOGIN ---
    if ($result->num_rows > 0) {
        $customer = $result->fetch_assoc();
        if (password_verify($password, $customer['password'])) {
            $_SESSION['customer_id'] = $customer['id'];
            $_SESSION['customer_name'] = $customer['name'];
            $_SESSION['customer_email'] = $customer['email']; // ✅ FIXED: store email
            $_SESSION['role'] = 'customer';
            header("Location: index.php");
            exit;
        } else {
            $message = "❌ Incorrect password.";
        }
    }
    // BARBER OR ADMIN LOGIN 
    elseif ($user_result->num_rows > 0) {
        $user = $user_result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['role'] = $user['role'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_id'] = $user['id'];

            if ($user['role'] === 'barber') {
                header("Location: barber_dashboard.php");
            } else {
                header("Location: admin_dashboard.php");
            }
            exit;
        } else {
            $message = "❌ Incorrect password.";
        }
    }
    // NO ACCOUNT FOUND 
    else {
        $message = "⚠️ No account found with that email or username.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login / Register | UPPERCUT</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

<style>
body {
    background: url('images/background.png') center center/cover no-repeat fixed;
    min-height: 100vh;
    font-family: 'Poppins', sans-serif;
    color: #fff;
}


body::before {
    content: "";
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.6);
    z-index: 0;
}


.login-container {
    position: relative;
    z-index: 1;
    background: rgba(0, 0, 0, 0.7);
    padding: 2.5rem;
    border-radius: 10px;
    border: 1px solid rgba(212, 175, 55, 0.3);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.7);
    margin: 140px auto 60px;
    max-width: 500px;
    color: #ffffff;
    backdrop-filter: blur(6px);
}


.login-container h2 {
    color: #d4af37;
    text-align: center;
    margin-bottom: 1.5rem;
    font-weight: 700;
}

/* Alerts */
.alert {
    background: rgba(255,255,255,0.1);
    border: 1px solid #444;
    color: #fff;
    border-left: 4px solid #d4af37;
}

/* Input Fields */
.login-container .form-control {
    background: rgba(0, 0, 0, 0.6);
    border: 1px solid #444;
    color: #fff;
    border-radius: 6px;
    padding: 0.8rem;
    margin-bottom: 1rem;
}
.login-container .form-control:focus {
    border-color: #d4af37;
    box-shadow: 0 0 5px #d4af37;
}

/* Buttons */
.login-container .btn-primary {
    background: #d4af37;
    border: none;
    color: #000;
    font-weight: 600;
    border-radius: 6px;
    padding: 0.8rem;
    width: 100%;
    transition: all 0.3s ease;
}
.login-container .btn-primary:hover {
    background: #fff;
    color: #000;
}

/* Tabs */
.nav-tabs {
    border: none !important;
    justify-content: center;
    margin-bottom: 1.5rem;
}

.nav-tabs .nav-link {
    background: rgba(0, 0, 0, 0.6);
    color: #bbb;
    border: none !important;
    border-radius: 6px 6px 0 0;
    margin: 0 4px;
    font-weight: 500;
    padding: 10px 20px;
    transition: all 0.3s ease;
}

.nav-tabs .nav-link:hover {
    color: #d4af37;
}

.nav-tabs .nav-link.active {
    background: #d4af37;
    color: #000;
    border: none !important;
    font-weight: 600;
}
</style>
</head>

<?php include 'navbar.php'; ?>

<body>
<div class="login-container">
  <h2>Welcome to UPPERCUT</h2>

  <?php if($message): ?>
    <div class="alert alert-info text-center"><?php echo $message; ?></div>
  <?php endif; ?>

  <ul class="nav nav-tabs" id="authTab" role="tablist">
    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#login">Login</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#register">Register</button></li>
  </ul>

  <div class="tab-content">
    <!-- LOGIN TAB -->
    <div class="tab-pane fade show active" id="login">
      <form method="POST">
        <div class="mb-3">
          <label>Email or Username</label>
          <input type="text" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Password</label>
          <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" name="login" class="btn btn-primary">Login</button>
      </form>
    </div>

    <!-- REGISTER TAB -->
    <div class="tab-pane fade" id="register">
      <form method="POST">
        <div class="mb-3">
          <label>Full Name</label>
          <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Phone (10 digits)</label>
          <input type="text" name="phone" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Email</label>
          <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Password</label>
          <input type="password" name="password" class="form-control" required>
          <small class="text-muted">Min 6 chars, 1 uppercase, 1 symbol</small>
        </div>
        <button type="submit" name="register" class="btn btn-primary">Register</button>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
