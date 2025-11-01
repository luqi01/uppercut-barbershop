<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Our Products | UPPERCUT</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      background: #0d0d0d url('backgroundimage.jpg') center/cover no-repeat fixed;
      font-family: 'Poppins', sans-serif;
      min-height: 100vh;
      color: #fff;
    }

    .products-container {
      background: rgba(15, 15, 15, 0.9);
      padding: 3rem;
      border-radius: 12px;
      border: 1px solid rgba(212,175,55,0.3);
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.6);
      margin: 100px auto 50px;
      max-width: 1200px;
    }

    .category-section {
      margin-bottom: 4rem;
      padding-bottom: 2rem;
      border-bottom: 2px solid #d4af37;
    }

    .category-section:last-child {
      border-bottom: none;
    }

    .product-card {
      background: rgba(40, 40, 40, 0.85);
      border: 1px solid rgba(212,175,55,0.2);
      border-radius: 10px;
      padding: 1.5rem;
      margin-bottom: 2rem;
      transition: all 0.3s ease;
      text-align: center;
    }

    .product-card:hover {
      transform: translateY(-5px);
      border-color: #d4af37;
      box-shadow: 0 4px 20px rgba(212,175,55,0.3);
    }

    .product-image {
      width: 100%;
      height: 230px;
      object-fit: cover;
      border-radius: 8px;
      margin-bottom: 1rem;
      background: #1a1a1a;
    }

    .product-name {
      color: #d4af37;
      font-size: 1.2rem;
      font-weight: 600;
      margin-bottom: 0.5rem;
    }

    .product-description {
      color: #bbb;
      font-size: 0.9rem;
      margin-bottom: 1rem;
      min-height: 40px;
    }

    .product-price {
      color: #d4af37;
      font-size: 1.3rem;
      font-weight: 700;
      margin-bottom: 0.5rem;
    }

    .category-title {
      border-left: 4px solid #d4af37;
      padding-left: 1rem;
      color: #d4af37;
      font-weight: 600;
      margin-bottom: 2rem;
    }

    h1 {
      color: #d4af37;
      text-align: center;
      font-weight: 700;
      margin-bottom: 2rem;
    }

    p.text-center {
      color: #ccc;
      margin-bottom: 3rem;
    }

    @media (max-width: 768px) {
      .products-container {
        padding: 2rem;
      }
      .product-image {
        height: 180px;
      }
    }
  </style>
</head>

<body>
  <?php include 'navbar.php'; ?>

  <div class="container">
    <div class="products-container">
      <h1>Our Premium Products</h1>
      <p class="text-center">Top-quality grooming essentials used by our master barbers.</p>

      <!-- Hair Styling -->
      <div class="category-section">
        <h2 class="category-title">Hair Styling</h2>
        <div class="row">
          <div class="col-md-4">
            <div class="product-card">
              <img src="images/pomade.webp" alt="Classic Pomade" class="product-image">
              <div class="product-name">Statement Classic Pomade</div>
              <div class="product-description">Professional hold with a classic shine for timeless styles.</div>
              <div class="product-price">R180</div>
            </div>
          </div>

          <div class="col-md-4">
            <div class="product-card">
              <img src="images/hair-wax.webp" alt="Hair Wax" class="product-image">
              <div class="product-name">Hair Wax</div>
              <div class="product-description">Medium hold with a natural finish and subtle shine.</div>
              <div class="product-price">R160</div>
            </div>
          </div>

          <div class="col-md-4">
            <div class="product-card">
              <img src="images/hair-spray.webp" alt="Hair Spray" class="product-image">
              <div class="product-name">Natural Hair Spray</div>
              <div class="product-description">Lightweight non-sticky formula with all-day hold.</div>
              <div class="product-price">R120</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Beard Care -->
      <div class="category-section">
        <h2 class="category-title">Beard Care</h2>
        <div class="row">
          <div class="col-md-4">
            <div class="product-card">
              <img src="images/beard-oil.webp" alt="Beard Oil" class="product-image">
              <div class="product-name">Premium Beard Oil</div>
              <div class="product-description">Strengthens, softens, and smooths beard hair while reducing frizz.</div>
              <div class="product-price">R200</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Shaving Essentials -->
      <div class="category-section">
        <h2 class="category-title">Shaving Essentials</h2>
        <div class="row">
          <div class="col-md-4">
            <div class="product-card">
              <img src="images/shaving-cream.webp" alt="Shaving Cream" class="product-image">
              <div class="product-name">Pacific Natural Shaving Cream</div>
              <div class="product-description">Natural, plant-based formula for a smooth, irritation-free shave.</div>
              <div class="product-price">R160</div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
