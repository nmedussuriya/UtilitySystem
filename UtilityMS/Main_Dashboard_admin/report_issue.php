<?php

include('../backend/db_connection.php');
try {
    $conn = new PDO("sqlsrv:Server=$serverName;Database=$dbName");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed");
}


$service = $_GET['service'] ?? '';
$status  = $_GET['status'] ?? '';


$sql = "SELECT * FROM Issue WHERE 1=1";
$params = [];

if (!empty($service)) {
    $sql .= " AND service_type = ?";
    $params[] = $service;
}

if (!empty($status)) {
    $sql .= " AND status = ?";
    $params[] = $status;
}

$sql .= " ORDER BY reported_date DESC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$issues = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>View Reported Issues - Utility Management System</title>
  <link rel="stylesheet" href="view_reportissue.css">
</head>
<body>
  <style>

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
}

body {
  background: #eef3fb;
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
}


.form-container {
  background: #ffffff;
  width: 520px;
  padding: 35px 40px;
  border-radius: 18px;
  box-shadow: 0 15px 40px rgba(0, 0, 0, 0.08);
}


.form-header {
  background: linear-gradient(135deg, #0288d1, #00acc1);
  color: #ffffff;
  text-align: center;
  padding: 16px;
  border-radius: 14px;
  margin-bottom: 25px;
  font-weight: 600;
  font-size: 15px;
}


.form-container h2 {
  text-align: center;
  color: #1a237e;
  margin-bottom: 25px;
  font-size: 24px;
}


.form-container label {
  display: block;
  margin-bottom: 6px;
  font-size: 14px;
  font-weight: 600;
  color: #0d47a1;
}


.form-container input,
.form-container select {
  width: 100%;
  padding: 12px 14px;
  margin-bottom: 20px;
  border-radius: 10px;
  border: 2px solid #c5cae9;
  font-size: 14px;
  color: #333;
  transition: all 0.3s ease;
  background: #ffffff;
}

.form-container input::placeholder {
  color: #9fa8da;
}

.form-container input:focus,
.form-container select:focus {
  outline: none;
  border-color: #3f51b5;
  box-shadow: 0 0 0 3px rgba(63, 81, 181, 0.15);
}


.button-group {
  display: flex;
  gap: 15px;
  margin-top: 10px;
}

.button-group button {
  flex: 1;
  padding: 14px;
  border: none;
  border-radius: 12px;
  font-size: 15px;
  font-weight: 600;
  cursor: pointer;
  transition: transform 0.2s, box-shadow 0.2s;
}

.btn-add {
  background: #1a237e;
  color: #ffffff;
}

.btn-add:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 20px rgba(26, 35, 126, 0.3);
}

.btn-clear {
  background: #ffa000;
  color: #ffffff;
}

.btn-clear:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 20px rgba(255, 160, 0, 0.3);
}


@media (max-width: 600px) {
  .form-container {
    width: 90%;
    padding: 30px;
  }

  .button-group {
    flex-direction: column;
  }
}

</style>

<div class="issue-table-container">
  <header>
    <h2>🧾 Reported Utility Issues</h2>
    <p>Admin panel to view and manage all reported water and electricity issues</p>
  </header>

  <form method="GET" class="filter-bar">
    <select name="service">
      <option value="">All Services</option>
      <option value="Water" <?= ($service == 'Water') ? 'selected' : '' ?>>Water</option>
      <option value="Electricity" <?= ($service == 'Electricity') ? 'selected' : '' ?>>Electricity</option>
    </select>

    <select name="status">
      <option value="">All Status</option>
      <option value="Open" <?= ($status == 'Open') ? 'selected' : '' ?>>Pending</option>
      <option value="In Progress" <?= ($status == 'In Progress') ? 'selected' : '' ?>>In Progress</option>
      <option value="Resolved" <?= ($status == 'Resolved') ? 'selected' : '' ?>>Resolved</option>
      <option value="Closed" <?= ($status == 'Closed') ? 'selected' : '' ?>>Closed</option>
    </select>

    <button type="submit" class="filter-btn">Filter</button>
  </form>


  <table class="issues-table">
    <thead>
      <tr>
        <th>ID</th>
        <th>Service</th>
        <th>Customer ID</th>
        <th>Location</th>
        <th>Description</th>
        <th>Priority</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>
    </thead>

    <tbody>
      <?php if (count($issues) > 0): ?>
        <?php foreach ($issues as $issue): ?>
          <tr>
            <td><?= $issue['issue_id'] ?></td>
            <td><?= htmlspecialchars($issue['service_type']) ?></td>
            <td><?= $issue['customer_id'] ?></td>
            <td><?= htmlspecialchars($issue['location']) ?></td>
            <td><?= htmlspecialchars($issue['description']) ?></td>

            <td>
              <span class="priority <?= strtolower($issue['priority']) ?>">
                <?= $issue['priority'] ?>
              </span>
            </td>

            <td>
              <span class="status <?= strtolower(str_replace(' ', '-', $issue['status'])) ?>">
                <?= $issue['status'] ?>
              </span>
            </td>

            <td class="actions">
              <a href="edit_issue.php?id=<?= $issue['issue_id'] ?>" class="edit">Edit</a>
              <a href="delete_issue.php?id=<?= $issue['issue_id'] ?>" class="delete"
                 onclick="return confirm('Delete this issue?')">Delete</a>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td colspan="8" style="text-align:center;">No issues found</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
  <a href="admin.php" class="btn-back">← Go Back to Dashboard</a>

</div>

</body>
</html>
