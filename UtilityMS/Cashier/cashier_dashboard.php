<?php
session_start();
$role = $_SESSION['role'] ?? '';
if ($role !== 'Cashier' && $role !== 'Admin') {
    header("Location: ../Main_Dashboard_admin/Main.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cashier Dashboard</title>
    <link rel="stylesheet" href="cashier_dashboard.css">
</head>
<body>

    <header class="header">
        <h1>Welcome to the Cashier Dashboard</h1>
        <p>Reliable service. Sustainable future. Powered for you.</p>
    </header>


    <!-- Action Buttons -->
    <section class="action-container">

        <div class="action-card card">
            <h3>Issuing Utility Bills</h3>
            <a href="billissue.php" class="btn custom-btn">Report</a>
        </div>

        <div class="action-card card">
            <h3>Record Payments</h3>
            <a href="record_payment.php" class="btn custom-btn">Record</a>
        </div>

        <div class="action-card card">
            <h3>View All Records</h3>
            <a href="View_All_Records.php" class="btn custom-btn">Update</a>
        </div>

    </section>

    <section class="bottom-container">

        <div class="action-card card">
            <h3>Log Out</h3>
            <a href="../Main_Dashboard_admin/Main.php" class="btn custom-btn red">Logout</a>
        </div>

    </section>

</body>
</html>
