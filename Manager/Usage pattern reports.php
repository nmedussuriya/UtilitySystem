<?php
include('../backend/db_connection.php');

try {
    $sql = "
        SELECT 
            t.utility_type,
            AVG(mr.consumption) as AvgConsumption,
            MAX(mr.consumption) as MaxConsumption,
            COUNT(mr.reading_id) as TotalReadings
        FROM Meter_Reading mr
        JOIN [Connection] c ON mr.connection_id = c.connection_id
        JOIN Tariff t ON c.tariff_id = t.tariff_id
        GROUP BY t.utility_type
    ";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $usageData = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Error fetching usage patterns: ' . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Usage Patterns</title>
    <style>
        body { font-family: sans-serif; background: #f4f7f6; padding: 40px; }
        .container { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .card-grid { display: flex; gap: 20px; margin-top: 20px; }
        .card { flex: 1; padding: 20px; border-radius: 8px; color: white; }
        .electricity { background: #3f51b5; }
        .water { background: #0288d1; }
        h2 { margin-top: 0; }
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
        <h1>📊 Usage Patterns Analysis</h1>
        <div class="card-grid">
            <?php foreach ($usageData as $data): ?>
                <div class="card <?= strtolower($data['utility_type']) ?>">
                    <h2><?= $data['utility_type'] ?></h2>
                    <p>Average Consumption: <strong><?= number_format($data['AvgConsumption'], 2) ?> Units</strong></p>
                    <p>Highest Recorded: <strong><?= $data['MaxConsumption'] ?> Units</strong></p>
                    <p>Total Readings: <?= $data['TotalReadings'] ?></p>
                </div>
            <?php endforeach; ?>
        </div>
        <br>
         <a href="../Manager/manger.php" class="btn-back">← Go Back to Dashboard</a>
    </div>
</body>
</html>