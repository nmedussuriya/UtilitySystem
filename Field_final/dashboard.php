<?php
session_start();


$role = $_SESSION['role'] ?? '';
if ($role !== 'Field Officer' && $role !== 'Admin') {
    header("Location: ../Main_Dashboard_admin/Main.php"); 
    exit;
}

$serverName = "NULARAMETHNADI\SQLEXPRESS"; 
$dbName = "UtilityManagementDB";
$username = "";
$password = "";

try {
    $conn = new PDO("sqlsrv:Server=$serverName;Database=$dbName", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}



$totalConnQuery = "SELECT COUNT(*) as total_connections FROM [Connection] WHERE status = 'Active'";
$totalConnStmt = $conn->query($totalConnQuery);
$totalConn = $totalConnStmt->fetch(PDO::FETCH_ASSOC)['total_connections'];

$pendingReadingQuery = "
    SELECT COUNT(*) as pending_readings
    FROM Meter_Reading mr
    LEFT JOIN Bill b ON mr.connection_id = b.connection_id
    WHERE b.bill_id IS NULL
";
$pendingStmt = $conn->query($pendingReadingQuery);
$pendingReadings = $pendingStmt->fetch(PDO::FETCH_ASSOC)['pending_readings'];

$overdueQuery = "
    SELECT COUNT(*) as overdue_bills
    FROM Bill
    WHERE status <> 'Paid' AND due_date < GETDATE()
";
$overdueStmt = $conn->query($overdueQuery);
$overdueBills = $overdueStmt->fetch(PDO::FETCH_ASSOC)['overdue_bills'];
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cashier Dashboard</title>
    <link rel="stylesheet" href="dashboardstyle.css">
</head>
<body>

    <header class="header">
        <h1>Welcome to the Field Officer Dashboard</h1>
        <p>Reliable service. Sustainable future. Powered for you.</p>
    </header>
<section class="stats-container">

    <div class="stat-card card">
        <h3>Total Connections</h3>
        <p class="number"><?= htmlspecialchars($totalConn); ?></p>
    </div>

    <div class="stat-card card">
        <h3>Pending Reading</h3>
        <p class="number"><?= htmlspecialchars($pendingReadings); ?></p>
    </div>

    <div class="stat-card card">
        <h3>Overdue Reading</h3>
        <p class="number"><?= htmlspecialchars($overdueBills); ?></p>
    </div>

</section>
    <section class="action-container">

        <div class="action-card card">
            <h3>Meter Reading</h3>
            <a href="meterreading.php" class="btn custom-btn">Add</a>
        </div>


        
        <div class="action-card card">
            <h3>Meter Information</h3>
            <a href="meter.php" class="btn custom-btn">Add</a>
        </div>
        

        <div class="action-card card">
            <h3>Delete Meter Reading</h3>
            <a href="deletemeterreding.php" class="btn custom-btn">Delete</a>
        </div>

    </section>

    <section class="bottom-container">

        <div class="action-card card">
            <h3>Delete meter Information</h3>
            <a href="deletemeter.php" class="btn custom-btn">Delete</a>
        </div>


        <div class="action-card card">
            <h3>Log Out</h3>
            <a href="../Main_Dashboard_admin/Main.php" class="btn custom-btn red">Logout</a>
        </div>

    </section>

</body>
</html>
