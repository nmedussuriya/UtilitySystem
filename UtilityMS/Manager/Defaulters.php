<?php
include('../backend/db_connection.php');

try {
    $sql = "SELECT * FROM View_UnpaidBills ORDER BY due_date ASC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $defaulters = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Error fetching defaulter data: ' . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Defaulters Report</title>
    <style>
        body { font-family: sans-serif; background: #f4f7f6; padding: 40px; }
        .container { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #e53935; color: white; padding: 12px; text-align: left; }
        td { padding: 12px; border-bottom: 1px solid #ddd; }
        .status-tag { background: #ffcdd2; color: #b71c1c; padding: 4px 8px; border-radius: 4px; font-weight: bold; }
        .btn-back {
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

        .btn-back:hover {
        background: #00b4d8;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>⚠️ Defaulters Report</h1>
        <p>List of customers with outstanding unpaid balances.</p>
        <table>
            <thead>
                <tr>
                    <th>Bill ID</th>
                    <th>Customer Name</th>
                    <th>Amount (Rs.)</th>
                    <th>Due Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($defaulters as $row): ?>
                <tr>
                    <td>#<?= $row['bill_id'] ?></td>
                    <td><?= htmlspecialchars($row['CustomerName']) ?></td>
                    <td><?= number_format($row['total_amount'], 2) ?></td>
                    <td><?= $row['due_date'] ?></td>
                    <td><span class="status-tag"><?= $row['status'] ?></span></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <br>
         <a href="../Manager/manger.php" class="btn-back">← Go Back to Dashboard</a>
    </div>
</body>
</html>