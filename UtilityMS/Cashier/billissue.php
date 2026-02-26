<?php
include('../backend/db_connection.php');

$successMessage = "";
$errorMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customer_id = $_POST['customer_id'];
    $service = $_POST['service']; 
    try {
        $stmt = $conn->prepare("
            SELECT c.connection_id, c.tariff_id
            FROM [Connection] c
            JOIN Customer cu ON c.customer_id = cu.customer_id
            JOIN Tariff t ON c.tariff_id = t.tariff_id
            WHERE cu.customer_id = ? AND t.utility_type = ? AND c.status = 'Active'
        ");
        $stmt->execute([$customer_id, $service]);
        $connection = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$connection) {
            throw new Exception("No active connection found for this customer and service.");
        }

        $connection_id = $connection['connection_id'];
        $tariff_id = $connection['tariff_id'];

        $stmt = $conn->prepare("
            SELECT TOP 1 consumption, reading_date
            FROM Meter_Reading
            WHERE connection_id = ?
            ORDER BY reading_date DESC
        ");
        $stmt->execute([$connection_id]);
        $reading = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$reading) {
            throw new Exception("No meter reading found for this connection.");
        }

        $consumption = (float)$reading['consumption']; 
        $billing_month = date('Y-m', strtotime($reading['reading_date']));

        $stmt = $conn->prepare("SELECT dbo.fn_CalculateSlabAmount(?, ?, ?) AS slab_amount");
        $stmt->execute([(int)round($consumption), (int)$tariff_id, $service]); 
        $slab = $stmt->fetch(PDO::FETCH_ASSOC);
        $total = $slab['slab_amount'];

        $stmt = $conn->prepare("SELECT dbo.fn_GetFixedCharge(?, ?) AS fixed_charge");
        $stmt->execute([(int)$tariff_id, $service]); 
        $fixed = (float)$stmt->fetch(PDO::FETCH_ASSOC)['fixed_charge']; 


        $total_amount = round($total + $fixed); 

        $issue_date = date('Y-m-d');
        $due_date = date('Y-m-d', strtotime('+30 days'));

        $stmt = $conn->prepare("
            INSERT INTO Bill (issue_date, due_date, total_amount, status, connection_id)
            VALUES (?, ?, ?, 'Unpaid', ?)
        ");
        $stmt->execute([$issue_date, $due_date, $total_amount, $connection_id]);

        $successMessage = "🧾 Bill issued successfully for Customer ID: $customer_id (Service: $service)";
    } catch (Exception $e) {
        $errorMessage = "❌ Error: " . $e->getMessage();
    }
}

$customers = $conn->query("SELECT customer_id, (SELECT name FROM [User] WHERE user_id = Customer.user_id) AS customer_name FROM Customer")->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Issue Bill - Utility Management System</title>
  <link rel="stylesheet" href="billissue.css" />
</head>
<body>
<div class="bill-issue-container">
    <header>
      <h2>🧾 Issue Utility Bill</h2>
      <p>Select service and customer to automatically generate a bill</p>
    </header>

    <?php if($successMessage): ?>
      <p style="color: green; text-align:center; font-weight:600;">
        <?= $successMessage ?>
      </p>
    <?php endif; ?>
    
    <?php if($errorMessage): ?>
      <p style="color: red; text-align:center; font-weight:600;">
        <?= $errorMessage ?>
      </p>
    <?php endif; ?>

    <form class="bill-form" method="POST">
      <div class="form-group">
        <label>Service Type</label>
        <select name="service" required>
          <option value="">-- Select Service --</option>
          <option value="Electricity">Electricity</option>
          <option value="Water">Water</option>
        </select>
      </div>

      <div class="form-group">
        <label>Customer</label>
        <select name="customer_id" required>
          <option value="">-- Select Customer --</option>
          <?php foreach($customers as $cust): ?>
            <option value="<?= $cust['customer_id'] ?>"><?= $cust['customer_name'] ?> (ID: <?= $cust['customer_id'] ?>)</option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="button-row">
        <button type="submit" class="issue-btn">Issue Bill</button>
        <button type="button" class="print-btn" onclick="window.print()">Print Bill</button>
        <button type="reset" class="clear-btn">Clear</button>
      </div>

      <a href="../Cashier/cashier_dashboard.php" class="btn-back">
        ← Go Back to Dashboard
      </a>
    </form>
</div>
</body>
</html>
