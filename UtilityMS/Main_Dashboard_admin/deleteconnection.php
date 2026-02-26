<?php
include('../backend/db_connection.php');

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $connection_id = isset($_POST['connection_id']) ? (int)$_POST['connection_id'] : 0;

    if ($connection_id > 0) {
        try {
            $conn->beginTransaction();

            // Check if connection exists
            $check = $conn->prepare("SELECT * FROM [Connection] WHERE connection_id = :connection_id");
            $check->execute([':connection_id' => $connection_id]);
            $existing = $check->fetch(PDO::FETCH_ASSOC);

            if ($existing) {
                // 1️⃣ Delete Cards
                $conn->prepare("
                    DELETE c
                    FROM Card c
                    INNER JOIN Payment p ON c.payment_id = p.payment_id
                    INNER JOIN Bill b ON p.bill_id = b.bill_id
                    WHERE b.connection_id = :connection_id
                ")->execute([':connection_id' => $connection_id]);

                // 2️⃣ Delete Payments
                $conn->prepare("
                    DELETE p
                    FROM Payment p
                    INNER JOIN Bill b ON p.bill_id = b.bill_id
                    WHERE b.connection_id = :connection_id
                ")->execute([':connection_id' => $connection_id]);

                // 3️⃣ Delete Bills
                $conn->prepare("DELETE FROM Bill WHERE connection_id = :connection_id")
                     ->execute([':connection_id' => $connection_id]);

                // 4️⃣ Delete Meter_Reading
                $conn->prepare("DELETE FROM Meter_Reading WHERE connection_id = :connection_id")
                     ->execute([':connection_id' => $connection_id]);

                // 5️⃣ Delete Meter
                $conn->prepare("DELETE FROM Meter WHERE connection_id = :connection_id")
                     ->execute([':connection_id' => $connection_id]);

                // 6️⃣ Delete Electricity
                $conn->prepare("DELETE FROM Electricity WHERE connection_id = :connection_id")
                     ->execute([':connection_id' => $connection_id]);

                // 7️⃣ Delete Water
                $conn->prepare("DELETE FROM Water WHERE connection_id = :connection_id")
                     ->execute([':connection_id' => $connection_id]);

                // 8️⃣ Delete Connection
                $conn->prepare("DELETE FROM [Connection] WHERE connection_id = :connection_id")
                     ->execute([':connection_id' => $connection_id]);

                $conn->commit();
                $message = "<span style='color: green;'>Connection and all related data deleted successfully!</span>";
            } else {
                $conn->rollBack();
                $message = "<span style='color: red;'>Connection ID not found!</span>";
            }

        } catch (PDOException $e) {
            $conn->rollBack();
            $message = "<span style='color: red;'>Error: " . htmlspecialchars($e->getMessage()) . "</span>";
        }
    } else {
        $message = "<span style='color: red;'>Please select a valid Connection ID.</span>";
    }
}

// Fetch connections for dropdown
$connections = [];
try {
    $stmt = $conn->query("SELECT connection_id, customer_id, tariff_id FROM [Connection] ORDER BY connection_id ASC");
    $connections = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message = "<span style='color: red;'>Error fetching connections: " . htmlspecialchars($e->getMessage()) . "</span>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Delete Connection</title>
<link rel="stylesheet" href="connection.css">
</head>
<body>
<div class="main-content">

<section class="form-container">
<header>
    <h2>Delete Connection</h2>
</header>

<form action="deleteconnection.php" method="POST">

            <?php if($message != ""): ?>
                <p><?= $message ?></p>
            <?php endif; ?>

            <div class="form-group">
                <label for="connection_id">Select Connection to Delete</label>
                <select id="connection_id" name="connection_id" required>
                    <option value="">Select Connection ID</option>
                    <?php foreach($connections as $connRow): ?>
                        <option value="<?= $connRow['connection_id'] ?>">
                            ID: <?= $connRow['connection_id'] ?> 
                            — Customer ID: <?= $connRow['customer_id'] ?> 
                            — Tariff ID: <?= $connRow['tariff_id'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>


    <div class="button-row">
        <button type="submit" class="btn btn-submit">Delete Connection</button>
        <button type="reset" class="btn btn-clear">Clear</button>
    </div>

    <a href="admin.php" class="btn-back">← Go Back to Dashboard</a>

</form>
</section>

</div>
</body>
</html>
