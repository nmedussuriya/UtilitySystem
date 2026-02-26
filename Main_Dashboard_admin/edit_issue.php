<?php
require_once __DIR__ . '/../backend/db_connection.php';

if (!isset($_GET['id'])) {
    die("Issue ID not provided");
}

$issue_id = $_GET['id'];

$sql = "SELECT * FROM Issue WHERE issue_id = :id";
$stmt = $conn->prepare($sql);
$stmt->execute([':id' => $issue_id]);
$issue = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$issue) {
    die("Issue not found");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $priority = $_POST['priority'];
    $status   = $_POST['status'];

    $updateSql = "
        UPDATE Issue
        SET priority = :priority,
            status   = :status
        WHERE issue_id = :id
    ";

    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->execute([
        ':priority' => $priority,
        ':status'   => $status,
        ':id'       => $issue_id
    ]);

    header("Location: report_issue.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Issue</title>

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
  box-shadow: 0 15px 40px rgba(0,0,0,0.08);
}

.form-header {
  background: linear-gradient(135deg, #0288d1, #00acc1);
  color: #ffffff;
  text-align: center;
  padding: 16px;
  border-radius: 14px;
  margin-bottom: 25px;
  font-weight: 600;
  font-size: 16px;
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
  transition: 0.3s ease;
}

.form-container input:focus,
.form-container select:focus {
  outline: none;
  border-color: #3f51b5;
  box-shadow: 0 0 0 3px rgba(63,81,181,0.15);
}

.button-group {
  display: flex;
  gap: 15px;
}

.btn-update {
  flex: 1;
  padding: 14px;
  border: none;
  border-radius: 12px;
  font-size: 15px;
  font-weight: 600;
  cursor: pointer;
  background: #1a237e;
  color: #fff;
  transition: 0.2s ease;
}

.btn-update:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 20px rgba(26,35,126,0.3);
}

.btn-cancel {
  flex: 1;
  padding: 14px;
  border: none;
  border-radius: 12px;
  font-size: 15px;
  font-weight: 600;
  cursor: pointer;
  background: #ffa000;
  color: #fff;
  transition: 0.2s ease;
}

.btn-cancel:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 20px rgba(255,160,0,0.3);
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

</head>
<body>

<div class="form-container">

    <div class="form-header">
        Edit Issue Details
    </div>

    <h2>Issue #<?= $issue_id ?></h2>

    <form method="POST">

        <label>Service Type</label>
        <input type="text"
               value="<?= htmlspecialchars($issue['service_type']) ?>"
               disabled>

        <label>Priority</label>
        <select name="priority" required>
            <option value="Low" <?= $issue['priority']=='Low'?'selected':'' ?>>Low</option>
            <option value="Medium" <?= $issue['priority']=='Medium'?'selected':'' ?>>Medium</option>
            <option value="High" <?= $issue['priority']=='High'?'selected':'' ?>>High</option>
            <option value="Critical" <?= $issue['priority']=='Critical'?'selected':'' ?>>Critical</option>
        </select>

        <label>Status</label>
        <select name="status" required>
            <option value="Open" <?= $issue['status']=='Open'?'selected':'' ?>>Open</option>
            <option value="In Progress" <?= $issue['status']=='In Progress'?'selected':'' ?>>In Progress</option>
            <option value="Resolved" <?= $issue['status']=='Resolved'?'selected':'' ?>>Resolved</option>
            <option value="Closed" <?= $issue['status']=='Closed'?'selected':'' ?>>Closed</option>
        </select>

        <div class="button-group">
            <button type="submit" class="btn-update">Update Issue</button>
            <a href="report_issue.php">
                <button type="button" class="btn-cancel">Cancel</button>
            </a>
        </div>

    </form>

</div>

</body>
</html>
