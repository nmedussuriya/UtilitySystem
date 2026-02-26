
<?php
session_start();
include('../backend/db_connection.php');

// Get role from session
$role = $_SESSION['role'] ?? null;

// Block access if NOT logged in
if ($role === null) {
    header("Location: ../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Utility Management System</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Times New Roman', Times, serif;
}

body {
    overflow-x: hidden;
}

.navbar {
    width: 100%;
    min-height: 12.5vh;
    background: #fff;
    display: flex;
    flex-direction: row;
    align-items: center;
    padding: 15px 90px;
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1000;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.navbar .logo {
    font-size: 35px;
    font-weight: bold;
    color: #0f3d87;
}

.navbar .logo span{
    color: black;
}

.navbar nav a {
    font-size: 17.5px;
    background: transparent;
    color: #000000;
    padding: 12px 25px;
    border: none;
    border-bottom: 2px solid transparent;
    border-radius: 0;
    text-decoration: none;
    transition: border-bottom 0.3s ease, color 0.3s ease;
}

.navbar nav a:hover {
    border-bottom: 5px solid #0f3d87;
    color: #0f3d87;
    background: transparent;
}

.hamburger {
    display: none;
    flex-direction: column;
    gap: 5px;
    cursor: pointer;
    margin-left: auto;
}

.hamburger span {
    width: 28px;
    height: 3px;
    background: #0f3d87;
}

.sidebar {
    position: fixed;
    top: 0;
    left: -260px;
    width: 260px;
    height: 100%;
    background: #0f3d87;
    padding-top: 90px;
    display: flex;
    flex-direction: column;
    gap: 30px;
    padding-left: 30px;
    transition: left 0.3s ease;
    z-index: 2000;
}

.sidebar a {
    color: white;
    text-decoration: none;
    font-size: 18px;
}

.sidebar.active {
    left: 0;
}

@media (max-width: 992px) {
    .navbar {
        padding: 10px 20px;
        flex-direction: row;
    }

    nav {
        display: none;
    }

    .hamburger {
        display: flex;
    }
}


.carousel-inner, .carousel-item img {
    height: 80vh;
    object-fit: cover;
}

.carousel-caption {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 80vh;
    background: rgba(0,0,0,0.4);
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    color: #fff;
}

.carousel-caption h1 {
    font-size: 43px;
    margin-bottom: 15px;
    text-align: center;
}

.carousel-caption p {
    font-size: 18px;
    margin-bottom: 20px;
    text-align: center;
}

.btn-get-started {
    background: #fff;
    color: #0f3d87;
    padding: 12px 25px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: bold;
    transition: 0.3s;
}

.btn-get-started:hover {
    background: #0f3d87;
    color: #fff;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-top: 25px;
}

.stat-card {
    margin: 30px 30px;
    background-color: hsl(203, 22%, 25%);
    backdrop-filter: blur(6px);
    -webkit-backdrop-filter: blur(6px);
    padding: 30px;
    border-radius: 16px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.07);
    text-align: center;
}

.stat-card h3 {
    font-size: 32px;
    color: #ffffff;
}

.stat-card p {
    margin-top: 8px;
    font-size: 16px;
    color: #ffffff;
}

.main-content{
    background: #2729393e;
}
.grid-2 {
    margin: 30px 30px;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 25px;
    margin-top: 30px;
}

.card {
    background: #04264af1;
    backdrop-filter: blur(6px);
    -webkit-backdrop-filter: blur(6px);
    padding: 25px;
    border-radius: 16px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.07);
}

h2 {
    margin-bottom: 15px;
    color: #ffffff;
}

.task-list input {
    margin: 8px 15px;
    color: #ffffff;
}

.task-list label {
    color: #ffffff;
}

.btn {
    margin-top: 20px;
    padding: 10px 20px;
    background: #f6f6f6;
    color: #04021c;
    font-weight: 600;
    border: none;
    border-radius: 8px;
    cursor: pointer;
}

.btn:hover {
    background: #02011b;
    color: #ffffff;
}

.bar {
    width: 100%;
    height: 30px;
    background: #e3e9ff;
    border-radius: 6px;
    margin: 5px 0 15px;
}

.bar div {
    height: 30px;
    background: #0f718b;
    border-radius: 6px;
}

.bar-title {
    display: flex;
    justify-content: space-between;
    font-size: 14px;
    color: #ffffff;
}

.summary-boxes {
    display: flex;
    justify-content: space-between;
    gap: 15px;
    margin-top: 20px;
    flex-wrap: wrap;
}

.summary {
    flex: 1;
    background: #e6eafa3c;
    padding: 15px;
    border-radius: 12px;
    text-align: center;
    margin-top: 10px;
}

.summary p {
    font-size: 14px;
    font-weight: 500;
    color: #ffffff;
}

.summary h3 {
    margin-top: 5px;
    color: #000;
    font-weight: 700;
}

.all {
    display: flex;
    align-items: center;
    justify-content: flex-start;
    gap: 30px; 
    padding: 40px;
    background: #f4f7fb;
    flex-wrap: wrap; 
    margin-top:80px;
}

.all img {
    max-width: 700px; 
    width: 100%;
    height: auto;
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
}

.header-section {
    flex: 1; 
    min-width: 280px; 
}

.header-section h1 {
    font-size: 32px;
    margin-bottom: 20px;
    color: #1e3158;
}

.header-section p {
    font-size: 16px;
    line-height: 1.9;
    color: #33415c;
}


@media (max-width: 768px) {
    .all {
        flex-direction: column;
        align-items: center;
        text-align: center;
    }
    .header-section {
        min-width: auto;
    }
    .all img {
        max-width: 80%;
        margin-bottom: 20px;
    }
}

.utility-cards {
    display: flex;
    justify-content: center;
    gap: 100px;
    flex-wrap: wrap;
    padding: 60px 20px;
}

.card-utility {
    background-color: hsl(203, 61%, 74%);
    width: 320px;
    height: 40vh;
    padding: 30px 20px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    text-align: center;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    cursor: pointer;
}

.card-utility:hover {
    transform: translateY(-8px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.card-utility i {
    font-size: 50px;
    color: #0f3d87;
    margin-bottom: 25px;
}

.card-utility h3 {
    font-size: 25px;
    margin-bottom: 25px;
}

.card-utility p {
    font-size: 16.5px;
    color: #555;
}

.footer {
    background: #0d1623;
    color: #fff;
    padding: 50px 0 20px;
    font-family: Arial, sans-serif;
    margin-top: 80px;
}

.footer-container {
    width: 85%;
    margin: auto;
    display: flex;
    justify-content: space-between;
    gap: 40px;
    flex-wrap: wrap;
}

.footer-section {
    flex: 1;
    min-width: 220px;
}

.footer-logo {
    color: white;
    display: flex;
    align-items: center;
    font-size: 22px;
    font-weight: bold;
}

.logo-icon {
    font-size: 26px;
    margin-right: 8px;
}

.footer-text {
    font-size: 14px;
    margin: 10px 0 20px;
    opacity: 0.8;
    line-height: 1.6;
}

.social-icons a {
    color: white;
    margin-right: 15px;
    font-size: 20px;
    transition: 0.3s;
}

.social-icons a:hover {
    color: #4fc3f7;
}

.footer-section h3 {
    margin-bottom: 15px;
    font-size: 18px;
}

.footer-section ul {
    list-style: none;
    padding: 0;
}

.footer-section ul li {
    margin: 8px 0;
}

.footer-section ul li a {
    color: #d4d4d4;
    text-decoration: none;
    transition: 0.3s;
}

.footer-section ul li a:hover {
    color: #4fc3f7;
}

.footer-bottom {
    text-align: center;
    border-top: 1px solid #2a3543;
    margin-top: 30px;
    padding-top: 15px;
    font-size: 14px;
    opacity: 0.7;
}

@media (max-width: 600px) {
    .footer-section {
        text-align: center;
    }
    .footer-container {
        flex-direction: column;
    }
}
</style>
</head>
<body>

<header class="navbar">
<div class="sidebar" id="sidebar">
    <a href="../Main_Dashboard_admin/Main.php">Dashboard</a>
    <a href="admin.php">Admin</a>
    <a href="../Manager/manger.php">Manager</a>
    <a href="../Field_final/dashboard.php">Field Officer</a>
    <a href="../Cashier/cashier_dashboard.php">Cashier</a>
    <a href="../backend/logout.php">⬅️ Logout</a>
</div>
    <div class="logo">Utility<span>MS</span></div>

    <div class="hamburger" id="hamburger">
        <span></span>
        <span></span>
        <span></span>
    </div>

    <nav>
        <a href="../Main_Dashboard_admin/Main.php" class="active"> Dashboard</a>
        <a href="admin.php"> Admin</a>
        <a href="../Manager/manger.php"> Manager</a>
        <a href="../Field_final/dashboard.php">Field Officer</a>
        <a href="../Cashier/cashier_dashboard.php"> Cashier</a>           
        <a href="../backend/logout.php">⬅️ Logout</a>
    </nav>
</header>

<div id="utilityCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="7000" style="margin-top:80px;">
    <div class="carousel-inner">
        <div class="carousel-item active">
            <img src="backgroundimg.jpg" class="d-block w-100" alt="Secure Payment">
            <div class="carousel-caption">
                <h1>Secure and Reliable Utility Management System</h1>
                <p>Built with role-based access for Admins, Cashiers, Field Officers, and Managers.</p>
                <a href="#" class="btn-get-started">Get Started Now</a>
            </div>
        </div>

        <div class="carousel-item">
            <img src="b2.png" class="d-block w-100" alt="Track Bills">
            <div class="carousel-caption">
                <h1>Start Managing Utility Records Effortlessly</h1>
                <p>Monitor and manage all your utility payments in one place</p>
                <a href="View_Records.php" class="btn-get-started">Learn More</a>
            </div>
        </div>
    </div>

    <button class="carousel-control-prev" type="button" data-bs-target="#utilityCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
        <span class="visually-hidden">Previous</span>
    </button>

    <button class="carousel-control-next" type="button" data-bs-target="#utilityCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>

 <div class ="all">
    <img src="https://i.pinimg.com/736x/75/59/70/755970bbffcafc61449c108940f6f6cb.jpg">
<div class="header-section">
    <h1><b>Utility Categories</b></h1>
    <p>Our Utility Categories module provides a comprehensive and centralized platform to manage, monitor, 
        and analyze all utility-related information efficiently. Whether it’s electricity or water supply, you can access detailed records of 
        connections, track consumption patterns, and monitor operational status in real time. <br><br>The system enables you to 
        streamline data management for your department, ensuring accuracy and accountability while reducing manual errors. With the ability 
        to view historical usage, detect anomalies, and generate reports, this tool empowers your team to make informed decisions, optimize resource allocation, 
        and maintain seamless service delivery.<br><br>Additionally, it supports proactive maintenance planning, allowing you to anticipate demand, prevent service 
        interruptions, and enhance overall operational efficiency. Designed to be intuitive and user-friendly, the Utility Categories dashboard serves as a one-stop 
        solution for effectively overseeing all utility operations and ensuring reliable service to customers.</p>
</div>
</div>
<div class="utility-cards">
    <div class="card-utility">
        <i class="fas fa-bolt"></i>
        <h3>Electricity Supply</h3>
        <p>Access all information related to electricity supply, consumption, and operational status in your area.</p>
    </div>

    <div class="card-utility">
        <i class="fas fa-water"></i>
        <h3>Water Supply</h3>
        <p>View water supply data, monitor usage trends, and ensure smooth water distribution management.</p>
    </div>
</div>

<div class="main-content">



    <section class="grid-2">
        <div class="card">
            <h2>Field Tasks</h2>
            <div class="task-list">
                <label><input type="checkbox"> Meter Reading</label><br>
                <label><input type="checkbox"> Service Connection Installation</label><br>
                <label><input type="checkbox"> Maintenance & Repairs</label><br>
                <label><input type="checkbox"> Disconnect / Reconnect Services</label><br>
                <label><input type="checkbox"> Leak / Fault Inspection</label><br>
                <label><input type="checkbox"> Customer Complaints</label><br>
                <label><input type="checkbox"> Bill Verification / Delivery</label><br>
                <label><input type="checkbox"> Emergency Response</label><br>
                <label><input type="checkbox"> Data Collection</label><br>
                <label><input type="checkbox"> Site Survey</label>
            </div>
            <button class="btn">View All</button>
        </div>

        <div class="card">
            <h2>Customers by Service</h2>

            <div class="bar-title">
                <span>Water Customers</span>
                <span>46%</span>
            </div>
            <div class="bar"><div style="width:46%"></div></div>

            <div class="bar-title">
                <span>Electricity Customers</span>
                <span>54%</span>
            </div>
            <div class="bar"><div style="width:54%"></div></div>

            <div class="summary-boxes">
                <div class="summary"><p>Total Customers</p><h3>1,750</h3></div>
                <div class="summary"><p>Water (%)</p><h3>46%</h3></div>
                <div class="summary"><p>Electricity (%)</p><h3>54%</h3></div>
            </div>
        </div>
    </section>

</div>

<footer class="footer">
    <div class="footer-container">

        <div class="footer-section">
            <h2 class="footer-logo">
                <span class="logo-icon">⚡</span> UtilityMS
            </h2>
            <p class="footer-text">Your trusted utility bill management system. Easily track, manage, and pay your electricity and water bills all in one place.</p>

            <div class="social-icons">
                <a href="#"><i class="fab fa-facebook"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fas fa-envelope"></i></a>
            </div>
        </div>

        <div class="footer-section">
            <h3>Useful Links</h3>
            <ul>
                <li><a href="Main.php">Home</a></li>
                <li><a href="../Cashier/View_All_Records.php">Bills</a></li>
                <li><a href="../login/index.php">Login</a></li>
                <li><a href="addcustomer.php">Register</a></li>
            </ul>
        </div>

        <div class="footer-section">
            <h3>Support</h3>
            <ul>
                <li><a href="#">Help Center</a></li>
                <li><a href="#">Privacy Policy</a></li>
                <li><a href="#">Terms of Service</a></li>
                <li><a href="#">Contact Us</a></li>
            </ul>
        </div>

    </div>

    
    <div class="footer-bottom">
        © UtilityMS. All rights reserved.
    </div>
</footer>

<script>
const ham = document.getElementById("hamburger");
const sidebar = document.getElementById("sidebar");

ham.onclick = () => {
    sidebar.classList.toggle("active");
};
</script>

</body>
</html>
