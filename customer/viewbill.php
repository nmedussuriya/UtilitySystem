<?php
session_start();
include('../backend/db_connection.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Customer') {
    header("Location: ../backend/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "
SELECT 
    t.utility_type,
    FORMAT(b.issue_date, 'MMMM yyyy') AS bill_month,
    b.total_amount,
    b.status
FROM Bill b
LEFT JOIN [Connection] c ON b.connection_id = c.connection_id
LEFT JOIN Tariff t ON c.tariff_id = t.tariff_id
LEFT JOIN Customer cu ON c.customer_id = cu.customer_id
WHERE cu.user_id = :user_id
  AND b.status <> 'Paid'
ORDER BY b.issue_date DESC
";



$stmt = $conn->prepare($sql);
$stmt->execute(['user_id' => $user_id]);
$bills = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>View Bills | Water & Electricity Board</title>

  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

  <style>
    body {
      font-family: 'Times New Roman', Times, serif;
      background: #f3f7fb;
      margin: 0;
      padding: 0;
      color: #333;
    }

    header {
      background: linear-gradient(90deg, #0077b6, #00b4d8);
      color: white;
      text-align: center;
      padding: 2rem 1rem;
    }

    header h1 {
      margin: 0;
      font-size: 2rem;
    }

    header p {
      font-size: 1.1rem;
      margin-top: 0.5rem;
      opacity: 0.9;
    }

    .table-container {
      width: 90%;
      max-width: 900px;
      margin: 2rem auto;
      background: white;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      overflow: hidden;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    th, td {
      padding: 1rem;
      text-align: center;
      border-bottom: 1px solid #ddd;
    }

    th {
      background: #00b4d8;
      color: white;
      font-weight: 600;
    }

    tr:hover {
      background: #f1faff;
    }

    .status-paid {
      color: green;
      font-weight: 600;
    }

    .status-pending {
      color: #d62828;
      font-weight: 600;
    }

    .back-btn {
      display: block;
      width: fit-content;
      margin: 2rem auto;
      padding: 0.8rem 1.5rem;
      background: #0077b6;
      color: white;
      text-decoration: none;
      border-radius: 8px;
      font-weight: bold;
      transition: background 0.3s;
    }

    .back-btn:hover {
      background: #00b4d8;
    }

    footer {
      text-align: center;
      background: #0077b6;
      color: white;
      padding: 1rem;
      margin-top: 3rem;
      font-size: 0.9rem;
    }
  </style>
</head>
<body>

  <header>
    <h1>View Your Bills</h1>
    <p>Check your current water and electricity bills below</p>
  </header>

  <div class="table-container">
<table>
  <thead>
    <tr>
      <th>Service</th>
      <th>Month</th>
      <th>Amount (LKR)</th>
      <th>Status</th>
    </tr>
  </thead>

  <tbody>
    <?php if (count($bills) > 0): ?>
      <?php foreach ($bills as $bill): ?>
        <tr>
          <td><?= htmlspecialchars($bill['utility_type']) ?></td>
          <td><?= htmlspecialchars($bill['bill_month']) ?></td>
          <td><?= number_format($bill['total_amount'], 2) ?></td>
          <td class="<?= $bill['status'] === 'Paid' ? 'status-paid' : 'status-pending' ?>">
            <?= htmlspecialchars($bill['status']) ?>
          </td>
        </tr>
      <?php endforeach; ?>
    <?php else: ?>
      <tr>
        <td colspan="4">No bills found</td>
      </tr>
    <?php endif; ?>
  </tbody>
</table>
  </div>

  <a href="customer.php" class="back-btn">⬅ Back to Customer Page</a>

  <footer>
    © 2025 Water & Electricity Board | All Rights Reserved
  </footer>

</body>
</html>
