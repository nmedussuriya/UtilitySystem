<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Customer') {
    header("Location: ../login/index.php"); 
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Customer Page | Water & Electricity Board</title>
  <style>
    
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Times New Roman', Times, serif;
    }

    body {
      background: #f3f7fb;
      color: #333;
      line-height: 1.6;
      padding: 20px;
    }

    header {
      background: linear-gradient(90deg, #0077b6, #00b4d8);
      color: white;
      text-align: center;
      padding: 2rem 1rem;
    }

    header h1 {
      font-size: 2rem;
      margin-bottom: 0.5rem;
    }

    h1{
      alih
    }

    header p {
      font-size: 1.1rem;
      opacity: 0.9;
    }

    .container {
      max-width: 1000px;
      margin: 2rem auto;
      padding: 1rem 2rem;
      background: white;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    h2 {
      color: #0077b6;
      margin-bottom: 1rem;
      display: inline-block;
    }

    section {
      margin-bottom: 2rem;
    }

    ul {
      list-style-type: " ";
      margin-left: 1.5rem;
    }

    ul li {
      margin: 0.5rem 0;
    }

    .contact-info {
      background: #f0f9ff;
      padding: 1rem 1.5rem;
      border-radius: 10px;
      border-left: 4px solid #00b4d8;
    }

    footer {
      text-align: center;
      background: #0077b6;
      color: white;
      padding: 1rem;
      margin-top: 2rem;
      font-size: 0.9rem;
    }

    @media (max-width: 600px) {
      header h1 {
        font-size: 1.5rem;
      }
      .container {
        padding: 1rem;
      }
    }
  </style>
</head>
<body>
  <header>
    <h1>Welcome to the Water & Electricity Customer Portal</h1>
    <p>Reliable service. Sustainable future. Powered for you.</p>
  </header>

  <div class="container">
    <section>
      <h2>Our Services</h2>
      <ul>
        <li>Reliable water distribution across all regions.</li>
        <li>Sustainable and uninterrupted electricity supply.</li>
        <li>Customer support for billing and maintenance inquiries.</li>
      </ul>
    </section>

    <section>
      <h2>Future Features (Coming Soon!)</h2>
      <ul>
        <li>💧 Usage tracking and consumption reports.</li>
        <li>💳 Secure online payment options.</li>
      </ul>
    </section> 
    <div class="container">
  <div class="card">
    <h2>💡 View Bills</h2>
    <p>Check your latest water and electricity bills.</p>
    <a href="viewbill.php">Go to Bills</a>
  </div>

  <div class="card">
    <h2>📜 Bill History</h2>
    <p>View and download your previous bills.</p>
    <a href="billhistory.php">Go to History</a>
  </div>
   <div class="card">
    <h2>⚠️ Report Issue</h2>
    <p>View and download your previous bills.</p>
    <a href="Report_issefll.php">Go to History</a>



  </div>
  <div class="logout">
      <a href="../backend/logout.php">Logout</a>
    </div>
</div>

<style>
  .container {
    text-align: center;
    margin-top: 3rem;
  }

  .card {
    display: inline-block;
    background: white;
    width: 250px;
    margin: 1rem;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s, box-shadow 0.3s;
  }

  .card:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
  }

  .card h2 {
    color: #0077b6;
    margin-bottom: 0.5rem;
  }

  .card p {
    margin-bottom: 1rem;
    color: #555;
  }

  .card a {
    text-decoration: none;
    background: #0077b6;
    color: white;
    padding: 0.6rem 1.2rem;
    border-radius: 8px;
    font-weight: bold;
    transition: background 0.3s;
  }

  .card a:hover {
    background: #00b4d8;
  }

  .logout a{
    color:white;
    background: #00273cff;
    color: white;
    padding: 0.6rem 3.9rem;
    border-radius: 8px;
    font-weight: bold;
    transition: background 0.3s;
  }


</style>


    <section>
      <h2>Customer Support</h2>
      <div class="contact-info">
        <p>📞 <strong>Hotline:</strong> 011-1234567</p>
        <p>✉️ <strong>Email:</strong> support@weboard.lk</p>
        <p>🕒 <strong>Service Hours:</strong> Mon–Fri, 8:00 AM – 5:00 PM</p>
      </div>
    </section>
  </div>

  <footer>
    © 2025 Water & Electricity Board | All Rights Reserved
  </footer>
</body>
</html>


    