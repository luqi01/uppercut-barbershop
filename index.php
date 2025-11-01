<?php include 'navbar.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Home | UPPERCUT</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<style>
body {
  margin: 0;
  font-family: 'Poppins', sans-serif;
  color: #fff;
  background-color: #000;
  overflow-x: hidden;
}

/* Hero Section */
.hero {
  position: relative;
  height: 100vh;
  background: url('images/shop_interior.png') center center/cover no-repeat fixed;
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
}

.hero::after {
  content: "";
  position: absolute;
  inset: 0;
  background: linear-gradient(
    180deg,
    rgba(0,0,0,0.4) 0%,   
    rgba(0,0,0,0.0) 100%  
  );
}

.hero-content {
  position: relative;
  z-index: 2;
  max-width: 700px;
  padding: 20px;
}

.hero h1 {
  font-size: 3rem;
  font-weight: 700;
  color: #d4af37;
  letter-spacing: 2px;
  text-transform: uppercase;
  margin-bottom: 1rem;
}

.hero p {
  font-size: 1.2rem;
  color: #f0f0f0;
  margin-bottom: 2rem;
}

.hero .btn-primary {
  background: #d4af37;
  border: none;
  font-weight: 600;
  border-radius: 8px;
  padding: 0.8rem 2rem;
  transition: all 0.3s ease;
}
.hero .btn-primary:hover {
  background: #fff;
  color: #000;
  transform: translateY(-2px);
}

.about {
  position: relative;
  background: rgba(0, 0, 0, 0.4); 
  padding: 90px 20px;
  text-align: center;
  backdrop-filter: blur(6px); 
  border-top: 1px solid rgba(212, 175, 55, 0.3); 
}

.about h2 {
  color: #d4af37;
  font-weight: 700;
  margin-bottom: 20px;
  font-size: 2rem;
}

.about p {
  color: #f0f0f0;
  max-width: 800px;
  margin: 0 auto;
  line-height: 1.7;
  font-size: 1.1rem;
}


/* Fade In Animation */
@keyframes fadeIn {
  from {opacity: 0; transform: translateY(20px);}
  to {opacity: 1; transform: translateY(0);}
}
.hero-content {
  animation: fadeIn 1.2s ease-in-out;
}
</style>
</head>
<body>

<!-- Hero Section -->
<section class="hero">
  <div class="hero-content">
    <h1>Welcome to UPPERCUT</h1>
    <p>A barbershop experience like no other</p>
    <a href="booking.php" class="btn btn-primary">Book Appointment</a>
  </div>
</section>

<!-- About Section -->
<section class="about">
  <div class="container">
    <h2>About Our Shop</h2>
    <p>UPPERCUT Barbershop blends craftsmanship with modern sophistication. Our expert barbers ensure every detail is sharp, 
    clean, and stylish</p>
    <p>EST 1990</p>
    <p>Contact us on 061 405 6785 | Find us at 6 Bardia Ave, Reservoir Hills</p>
    <p></p>
  </div>
</section>

</body>
</html>
