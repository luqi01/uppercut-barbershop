<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<nav class="navbar navbar-expand-lg fixed-top">
  <div class="container-fluid px-5">
    <a href="home.php" class="d-flex align-items-center text-decoration-none">
      <img src="images/logo.jpg" class="logo me-2" alt="Uppercut Logo">
      <span class="navbar-brand mb-0 fw-bold">UPPERCUT</span>
    </a>

    <button class="navbar-toggler text-light" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
      <ul class="navbar-nav align-items-center">
        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="services.php">Services</a></li>
        <li class="nav-item"><a class="nav-link" href="products.php">Products</a></li>

        <?php if (isset($_SESSION['role'])): ?>
            <?php if ($_SESSION['role'] === 'barber'): ?>
                <li class="nav-item"><a class="nav-link" href="barber_dashboard.php">Dashboard</a></li>
                <li class="nav-item">
                    <a class="nav-link text-warning" href="logout.php">
                        Logout (<?php echo htmlspecialchars($_SESSION['username']); ?>)
                    </a>
                </li>

            <?php elseif ($_SESSION['role'] === 'admin'): ?>
                <li class="nav-item"><a class="nav-link" href="admin_dashboard.php">Admin Panel</a></li>
                <li class="nav-item">
                    <a class="nav-link text-warning" href="logout.php">
                        Logout (<?php echo htmlspecialchars($_SESSION['username']); ?>)
                    </a>
                </li>

            <?php elseif ($_SESSION['role'] === 'customer'): ?>
                <li class="nav-item"><a class="nav-link" href="booking.php">Book Appointment</a></li>
                <li class="nav-item"><a class="nav-link" href="my_bookings.php">My Bookings</a></li>
                <li class="nav-item">
                <a class="nav-link text-warning" href="logout.php">
               Logout (<?php echo htmlspecialchars($_SESSION['customer_name']); ?>)
        </a>
    </li>
<?php endif; ?>

        <?php else: ?>
            <li class="nav-item"><a class="nav-link" href="login_register.php">Login / Register</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<style>
.navbar {
  background-color: #000 !important;
  border-bottom: 1px solid #d4af37;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.6);
  z-index: 1000;
  height: 90px;
}

.navbar-brand {
  color: #fff !important;
  font-family: 'Poppins', sans-serif;
  font-size: 1.8rem;
  letter-spacing: 2px;
  transition: color 0.3s ease;
}

.navbar-brand:hover {
  color: #d4af37 !important;
}

.nav-link {
  color: #fff !important;
  font-weight: 500;
  font-family: 'Poppins', sans-serif;
  transition: all 0.3s ease;
  padding: 8px 15px;
}

.nav-link:hover {
  color: #d4af37 !important;
  transform: translateY(-2px);
}

.navbar-nav .nav-item {
  margin-left: 10px;
}

.logo {
  width: 90px;          /* You can adjust this size */
  height: 80px;
  border-radius: 50%;   /* Makes it a circle */
  object-fit: cover;    
  border: 2px solid #d4af37;  
  box-shadow: 0 0 10px rgba(212,175,55,0.4); 
}
  
</style>
