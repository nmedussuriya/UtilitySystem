<?php
include('../backend/db_connection.php');

$success = "";
$error   = "";

// Fetch customers for the dropdown
try {
    $stmt = $conn->query("SELECT customer_id, user_id FROM Customer ORDER BY customer_id");
    $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching customers: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $service     = $_POST['service']     ?? '';
    $customer_id = $_POST['cust_id']     ?? '';
    $location    = $_POST['location']    ?? '';
    $description = $_POST['description'] ?? '';
    $priority    = $_POST['priority']    ?? '';
    $status      = $_POST['status']      ?? 'pending';

    // Validate required fields
    if (
        empty($service) ||
        empty($customer_id) ||
        empty($location) ||
        empty($description) ||
        empty($priority)
    ) {
        $error = "❌ Please fill in all required fields.";
    } else {

        // Check if the customer exists
        $check = $conn->prepare("SELECT * FROM Customer WHERE customer_id = ?");
        $check->execute([$customer_id]);
        if (!$check->fetch()) {
            $error = "❌ Selected customer does not exist!";
        } else {
            // Insert into Issue table
            $sql = "INSERT INTO Issue
                    (service_type, customer_id, location, description, priority, status)
                    VALUES
                    (:service, :customer_id, :location, :description, :priority, :status)";

            $stmt = $conn->prepare($sql);
            try {
                $stmt->execute([
                    ':service'     => ucfirst($service),
                    ':customer_id' => $customer_id,
                    ':location'    => ucfirst($location),
                    ':description' => $description,
                    ':priority'    => ucfirst($priority),
                    ':status'      => ucfirst($status)
                ]);
                $success = "✅ Issue submitted successfully!";
            } catch (PDOException $e) {
                $error = "❌ Failed to submit issue: " . $e->getMessage();
            }
        }
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
        <option value="Water">Water</option>
        <option value="Electricity">Electricity</option>
      </select>
    </div>

    <div class="form-group">
      <label for="cust-id">Customer</label>
      <select id="cust-id" name="cust_id" required>
        <option value="">-- Select Customer --</option>
        <?php foreach($customers as $c): ?>
            <option value="<?= $c['customer_id'] ?>">Customer ID: <?= $c['customer_id'] ?></option>
        <?php endforeach; ?>
      </select>
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
        <option value="Low">Low</option>
        <option value="Medium">Medium</option>
        <option value="High">High</option>
      </select>
    </div>

    <div class="form-group">
      <label for="status">Status</label>
      <select id="status" name="status">
        <option value="Pending">Pending</option>
        <option value="In-progress">In Progress</option>
        <option value="Resolved">Resolved</option>
      </select>
    </div>

    <div class="button-row">
      <button type="submit" class="submit-btn">Submit Issue</button>
      <button type="reset" class="clear-btn">Clear</button>
    </div>

    <a href="customer.php" class="btn-back">← Go Back to Dashboard</a>
  </form>
</div>

</body>
</html>
