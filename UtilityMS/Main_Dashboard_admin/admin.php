<?php
session_start();
include('../backend/db_connection.php');

if (($_SESSION['role'] ?? '') !== 'Admin') {
    header("Location: ../Main_Dashboard_admin/Main.php");
    exit;
}



$customerCount = $conn->query("SELECT COUNT(*) FROM Customer")->fetchColumn();

$connectionCount = $conn->query("SELECT COUNT(*) FROM [Connection]")->fetchColumn();


$issueCount = $conn->query("SELECT COUNT(*) FROM Issue")->fetchColumn();
?>



<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Admin Dashboard</title>

<style>
*{
  margin: 0;
  padding: 0;
  font-family: 'Times New Roman', Times, serif;
}
body {
  background-image: url('backgroundimg.jpg'); 
  background-size: cover;
  background-position: center;
  background-attachment: fixed;
  text-align: center;
  background-repeat: no-repeat;
  padding: 20px;
}

header {
  background: #0531578a;
  color: #fff;
  text-align: center;
  padding: 35px 20px;
            
}

header h1 {
  font-size: 32px;
  font-weight: bold;
  color:white;
}

header p {
  margin-top: 10px;
  font-size: 15px;
  opacity: 0.9;
        }


.stats-grid1{
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
}

.stat-card {
    margin: 40px 40px;
    background-color: hsla(203, 71%, 22%, 0.70);
    backdrop-filter: blur(6px);
    -webkit-backdrop-filter: blur(6px);
    padding: 30px;
    border-radius: 16px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.07);
    text-align: center;
}

.stat-card h3 {
    font-size: 42px;
    color: #ffffffff;
}

.stat-card p {
    margin-top: 8px;
    font-size: 18px;
    color: #ffffffff;
}



.stats-grid, .actions-grid, .footer-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: 30px;
  max-width: 900px;
  margin: 20px auto;
}

.box {
  background: rgba(233, 235, 243, 0.899);
  border: 1px solid rgba(255, 255, 255, 0.721);
  border-radius: 20px;
  padding: 25px 20px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
  transition: 0.3s ease;
}

.box:hover {
  transform: translateY(-5px);
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
}

.box img {
  width: 50px;
  margin-bottom: 15px;
}

.box h3 {
  margin-bottom: 15px;
  font-size: 20px;
  color: #000000;
}

.box .btn {
  display: inline-block;
  background: linear-gradient(to right, #1785ca, #1474b0); 
  color: white;
  padding: 10px 35px;
  border: none;
  border-radius: 12px;
  cursor: pointer;
  font-size: 18px;
  font-weight: 500;
  transition: 0.1s;
  text-decoration: none;
}

.box .btn:hover {
  background: linear-gradient(to right, #024168, #096096); 
}

.box .btn.logout {
  background: #d9534f;
}

.box .btn.logout:hover {
  background: #b52b27;
}

@media (max-width: 768px) {

  .stats-grid1,
  .actions-grid,
  .footer-grid {
    grid-template-columns: 1fr;   
  }

  .stat-card {
    margin: 20px;
  }

  .box {
    margin: 0 auto;
    width: 100%;
  }

  .box .btn {
    padding: 10px 20px;
    font-size: 16px;
  }
}


</style>
</head>
<body>

<header>
  <h1>Welcome to the Admin Dashboard</h1>
  <p>Reliable service. Sustainable future. Powered for you.</p>
</header>



<section class="stats-grid1">
    <div class="stat-card">
        <h3><?= $customerCount ?></h3>
        <p>Customers</p>
    </div>

    <div class="stat-card">
        <h3><?= $connectionCount ?></h3>
        <p>Connections</p>
    </div>

    
     <div class="stat-card">
        <h3><?= $issueCount ?></h3>
        <p>Complaints</p>
    </div> 


</section>


<div class="actions-grid">
  <div class="box">
      <img src="https://cdn-icons-png.flaticon.com/512/747/747376.png" alt="Add Customers" />
      <h3>Add Customers</h3>
      <a href="addcustomer.php" class="btn">Add</a>
  </div>

  <div class="box">
      <img src="https://cdn-icons-png.flaticon.com/512/6861/6861362.png" alt="Delete Customers" />
      <h3>Delete Customers</h3>
      <a href="delete.php" class="btn">Delete</a>
  </div>

      <div class="box">
      <img src="https://cdn-icons-png.flaticon.com/512/747/747376.png" alt="Notifications" />
      <h3>Add Connection</h3>
      <a href="connection.php" class="btn">Add</a>
  </div>

    <div class="box">
      <img src="https://cdn-icons-png.flaticon.com/512/3602/3602145.png" alt="Notifications" />
      <h3>Report Issues</h3>
      <a href="report_issue.php" class="btn">Edit</a>
  </div>



</div>

 
<div class="footer-grid">
    <div class="box">
          <img src="https://cdn-icons-png.flaticon.com/512/709/709612.png" alt="View Customers" />
          <h3>View All Customers</h3>
          <a href="viewcustomer.php" class="btn">View</a>
      </div>

        <div class="box">
      <img src="https://cdn-icons-png.flaticon.com/512/709/709612.png" alt="View Customers" />
      <h3>Modify Tariff</h3>
      <a href="slab.php" class="btn">Modify</a>
    </div>

    <div class="box">
      <img src="https://cdn-icons-png.flaticon.com/512/6861/6861362.png" alt="Notifications" />
      <h3>Delete Connection</h3>
      <a href="deleteconnection.php" class="btn">Delete</a>
  </div>

  <div class="box">
      <img src="https://cdn-icons-png.flaticon.com/512/1828/1828479.png" alt="Logout" />
      <h3>Logout</h3>
      <a href="Main.php" class="btn logout">Logout</a>
  </div>
</div>


</body>
</html>
