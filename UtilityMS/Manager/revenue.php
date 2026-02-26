<?php

$dbConnPath = __DIR__ . '/../backend/db_connection.php';
if (!file_exists($dbConnPath)) {
    die('Database connection file not found');
}

require_once $dbConnPath;

if (!isset($conn) || $conn === null) {
    die('Database connection not established');
}


try {
    $sql = "
        SELECT 
            Year,
            Month,
            Revenue AS TotalRevenue
        FROM View_MonthlyRevenue
        ORDER BY Year DESC, Month DESC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $revenueData = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die('Error fetching revenue data: ' . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Revenue Report</title>

    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

    <style>
        body {
            background-color: #f0f4f8;
            font-family: 'Roboto', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .report-container {
            background: #ffffff;
            padding: 40px;
            border-radius: 18px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            width: 100%;
            max-width: 720px;
            text-align: center;
        }

        h1 {
            color: #4169E1;
            font-size: 28px;
            margin-bottom: 5px;
        }

        .subtitle {
            color: #666;
            font-size: 14px;
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        th {
            background-color: #f8f9fa;
            padding: 12px;
            text-align: left;
            border-bottom: 2px solid #eaeaea;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #eeeeee;
            color: #444;
        }

        .btn-group {
            display: flex;
            gap: 12px;
        }

        .btn {
            flex: 1;
            padding: 12px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            color: #fff;
            cursor: pointer;
            border: none;
        }

        .btn-back { background-color: #4169E1; }
        .btn-print { background-color: #ff9800; }
        .btn-export { background-color: #00bfa5; }

        .btn:hover {
            opacity: 0.85;
        }
    </style>
</head>

<body>

<div class="report-container">
    <h1>📈 Revenue Trends Report</h1>
    <p class="subtitle">Monthly Revenue Summary</p>

    <table>
        <thead>
            <tr>
                <th>Year</th>
                <th>Month</th>
                <th>Total Revenue (Rs.)</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($revenueData)): ?>
                <?php foreach ($revenueData as $row): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($row['Year']) ?></strong></td>
                        <td><?= date("F", mktime(0, 0, 0, $row['Month'], 1)) ?></td>
                        <td><?= number_format($row['TotalRevenue'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3" style="text-align:center;">No revenue data found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="btn-group">
        <a href="../Manager/manger.php" class="btn btn-back">Back</a>
        <button onclick="window.print()" class="btn btn-print">Print</button>
        <a href="#" class="btn btn-export">Export</a>
    </div>
</div>

</body>
</html>
