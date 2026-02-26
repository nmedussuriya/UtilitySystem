<?php
include('../backend/db_connection.php');


define('TARIFF_ELECTRICITY', 1);
define('TARIFF_WATER', 2);
$allowedTariffs = [TARIFF_ELECTRICITY, TARIFF_WATER];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $connection_date = isset($_POST['connection_date']) ? trim($_POST['connection_date']) : '';
    $customer_id     = isset($_POST['customer_id']) ? (int) $_POST['customer_id'] : 0;
    $tariff_id       = isset($_POST['tariff_id']) ? (int) $_POST['tariff_id'] : 0;
    $status          = isset($_POST['status']) ? trim($_POST['status']) : '';

    if (empty($connection_date) || $customer_id <= 0 || !in_array($tariff_id, $allowedTariffs, true) || empty($status)) {
        echo "<script>alert('Please fill all fields correctly.'); window.location.href='connection.php';</script>";
        exit;
    }

    try {
        $check = $conn->prepare("SELECT 1 FROM [Connection] WHERE customer_id = :customer_id AND tariff_id = :tariff_id");
        $check->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
        $check->bindParam(':tariff_id', $tariff_id, PDO::PARAM_INT);
        $check->execute();

        if ($check->fetchColumn()) {
            echo "<script>alert('This customer already has the selected service (duplicate not allowed).'); window.location.href='connection.php';</script>";
            exit;
        }

        $stmt = $conn->prepare("
            INSERT INTO [Connection] (connection_date, status, customer_id, tariff_id)
            VALUES (:connection_date, :status, :customer_id, :tariff_id)
        ");
        $stmt->bindParam(':connection_date', $connection_date);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
        $stmt->bindParam(':tariff_id', $tariff_id, PDO::PARAM_INT);
        $stmt->execute();

        echo "<script>alert('Connection added successfully!'); window.location.href='connection.php';</script>";
        exit;
    } catch (PDOException $e) {
        echo "Error: " . htmlspecialchars($e->getMessage());
        exit;
    }
}

$customerDetails = [];
try {
    $sql = "
        SELECT 
            u.name AS CustomerName,
            c.customer_id,
            c.customer_type,
            t.utility_type AS ServiceType
        FROM Customer c
        JOIN [User] u ON c.user_id = u.user_id
        LEFT JOIN [Connection] con ON c.customer_id = con.customer_id
        LEFT JOIN Tariff t ON con.tariff_id = t.tariff_id
        ORDER BY u.name;
    ";
    $stmt = $conn->query($sql);
    $customerDetails = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching customer details: " . htmlspecialchars($e->getMessage());
    $customerDetails = [];
}

$tariffs = [];
try {
    $stmt = $conn->query("
        SELECT tariff_id, utility_type 
        FROM Tariff
        WHERE tariff_id IN (" . implode(',', array_map('intval', $allowedTariffs)) . ")
    ");
    $tariffs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching tariffs: " . htmlspecialchars($e->getMessage());
    $tariffs = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Connection</title>
    <link rel="stylesheet" href="connection.css">
</head>
<body>

<div class="main-content">
<section class="form-container">

<header>
    <h2>Add New Connection</h2>
</header>

<form action="connection.php" method="POST">

    <div class="form-group">
        <label for="connection_date">Connection Date</label>
        <input type="date" id="connection_date" name="connection_date" required>
    </div>

    <div class="form-group">
        <label for="customer_id">Customer</label>
        <select id="customer_id" name="customer_id" required>
            <option value="">Select Customer</option>

            <?php foreach ($customerDetails as $cust): ?>
                <option value="<?= $cust['customer_id'] ?>">
                    <?= $cust['CustomerName'] ?> 
                    (ID: <?= $cust['customer_id'] ?>)
                    <?php if ($cust['ServiceType']): ?>
                        — Current Service: <?= $cust['ServiceType'] ?>
                    <?php endif; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="status">Status</label>
        <select id="status" name="status" required>
            <option value="Active">Active</option>
            <option value="Pending">Pending</option>
        </select>
    </div>

    <div class="form-group">
        <label for="tariff_id">Service Type (Tariff)</label>
        <select id="tariff_id" name="tariff_id" required>
            <option value="">Select Service</option>

            <?php foreach ($tariffs as $tariff): ?>
                <option value="<?= $tariff['tariff_id'] ?>">
                    <?= $tariff['utility_type'] ?> (ID: <?= $tariff['tariff_id'] ?>)
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="button-row">
        <button type="submit" class="btn btn-submit">Add Connection</button>
        <button type="reset" class="btn btn-clear">Clear</button>
    </div>

    <a href="admin.php" class="btn-back">← Go Back to Dashboard</a>

</form>

</section>
</div>

</body>
</html>
