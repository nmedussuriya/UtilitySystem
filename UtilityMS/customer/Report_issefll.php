<?php
include('../backend/db_connection.php');

try {
    $conn = new PDO(
        "sqlsrv:Server=$serverName;Database=$dbName",
        "",
        ""
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$success = "";
$error   = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $service     = $_POST['service']     ?? '';
    $customer_id = $_POST['cust_id']     ?? '';
    $location    = $_POST['location']    ?? '';
    $description = $_POST['description'] ?? '';
    $priority    = $_POST['priority']    ?? '';
    $status      = $_POST['status']      ?? 'pending';

    if (
        empty($service) ||
        empty($customer_id) ||
        empty($location) ||
        empty($description) ||
        empty($priority)
    ) {
        $error = "❌ Please fill in all required fields.";
    } else {

        $sql = "INSERT INTO Issue
                (service_type, customer_id, location, description, priority, status)
                VALUES
                (:service, :customer_id, :location, :description, :priority, :status)";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':service'     => ucfirst($service),
            ':customer_id' => $customer_id,
            ':location'    => $location,
            ':description' => $description,
            ':priority'    => ucfirst($priority),
            ':status'      => ucfirst($status)
        ]);

        $success = "✅ Issue submitted successfully!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Report Utility Issue - Utility Management System</title>
  <link rel="stylesheet" href="report_issue.css">
</head>
<body>

<div class="issue-container">
  <header>
    <h2>🚨 Report Utility Issue</h2>
    <p>Submit or update issues related to Water or Electricity services</p>
  </header>

  <?php if ($error): ?>
    <p style="color:red; font-weight:bold;"><?php echo $error; ?></p>
  <?php endif; ?>

  <?php if ($success): ?>
    <p style="color:green; font-weight:bold;"><?php echo $success; ?></p>
  <?php endif; ?>

  <form class="issue-form" method="POST">

    <div class="form-group">
      <label for="service">Service Type</label>
      <select id="service" name="service" required>
        <option value="">-- Select Service --</option>
        <option value="water">Water</option>
        <option value="electricity">Electricity</option>
      </select>
    </div>

    <div class="form-group">
      <label for="cust-id">Customer ID</label>
      <input type="text" id="cust-id" name="cust_id" placeholder="e.g. CUST001" required>
    </div>

    <div class="form-group">
      <label for="location">Location / Area</label>
      <input type="text" id="location" name="location" placeholder="e.g. Galle Road, Colombo" required>
    </div>

    <div class="form-group">
      <label for="description">Issue Description</label>
      <textarea id="description" name="description" rows="4"
        placeholder="Describe the issue clearly..." required></textarea>
    </div>

    <div class="form-group">
      <label for="priority">Priority Level</label>
      <select id="priority" name="priority" required>
        <option value="">-- Select Priority --</option>
        <option value="low">Low</option>
        <option value="medium">Medium</option>
        <option value="high">High</option>
      </select>
    </div>

    <div class="form-group">
      <label for="status">Status</label>
      <select id="status" name="status">
        <option value="pending">Pending</option>
        <option value="in-progress">In Progress</option>
        <option value="resolved">Resolved</option>
      </select>
    </div>

    <div class="button-row">
      <button type="submit" class="submit-btn">Submit Issue</button>
      <button type="reset" class="clear-btn">Clear</button>
  
    </div>

      <a href="customer.php" class="btn-back">
        ← Go Back to Dashboard
      </a>
  </form>
</div>

</body>
</html>
