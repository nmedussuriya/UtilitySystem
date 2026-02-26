<?php
require_once __DIR__ . '/../backend/db_connection.php';

if (!isset($_GET['id'])) {
    die("Issue ID not provided");
}

$issue_id = $_GET['id'];

$sql = "DELETE FROM Issue WHERE issue_id = :id";
$stmt = $conn->prepare($sql);
$stmt->execute([':id' => $issue_id]);

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Deleting Issue...</title>
</head>
<body>
  <script>
    alert('Issue deleted successfully!');
    window.location.href = 'report_issue.php';
  </script>
</body>
</html>
