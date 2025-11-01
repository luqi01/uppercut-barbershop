<?php include 'navbar.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Our Services | UPPERCUT</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <style>
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #0d0d0d, #1a1a1a);
      color: #fff;
    }

   
    .content {
      max-width: 900px;
      margin: 120px auto 60px auto;
      padding: 2rem;
      background: rgba(15,15,15,0.8);
      border-radius: 12px;
      border: 1px solid rgba(212,175,55,0.3);
      box-shadow: 0 4px 20px rgba(0,0,0,0.7);
      text-align: center;
    }

    h1 {
      color: #d4af37;
      font-weight: 700;
      margin-bottom: 1rem;
    }

    p {
      color: #ccc;
      margin-bottom: 1.5rem;
    }

    iframe {
      width: 100%;
      height: 550px;
      border: 1px solid #444;
      border-radius: 10px;
      margin-top: 10px;
    }

    a.download-btn {
      display: inline-block;
      margin-top: 15px;
      background: #d4af37;
      color: #000;
      padding: 10px 20px;
      border-radius: 6px;
      text-decoration: none;
      font-weight: 600;
      transition: 0.3s ease;
    }

    a.download-btn:hover {
      background: #fff;
      color: #000;
    }

    @media (max-width: 768px) {
      .content {
        width: 90%;
        padding: 1.5rem;
      }
      iframe {
        height: 400px;
      }
    }
  </style>
</head>

<body>
  <div class="content">
    <h1>Our Services</h1>
    <p>View our full list of grooming services and prices below. You can also download the PDF version for quick reference.</p>

    <iframe src="images\Uppercut_PriceList.pdf#toolbar=0&navpanes=0&scrollbar=1"></iframe>
    <a href="images\Uppercut_PriceList.pdf" class="download-btn" download>⬇️ Download Services PDF</a>
  </div>
</body>
</html>
