<?php
include('../backend/db_connection.php');

$errorMsgE = $successMsgE = "";
$errorMsgW = $successMsgW = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $type = $_POST['type']; 
    $connection_id = intval($_POST['connection_id']); // force integer
    $install_date = $_POST['install_date'];

    if ($type === 'Electricity') {
        $meter_type = $_POST['meter_type'];
        $voltage = $_POST['voltage'];

        try {
            // Check for duplicate
            $stmt = $conn->prepare("SELECT * FROM Electricity WHERE connection_id = ?");
            $stmt->execute([$connection_id]);

            if ($stmt->rowCount() > 0) {
                $errorMsgE = "⚠ Electricity meter for this connection already exists!";
            } else {
                // Insert meter
                $insert = $conn->prepare("INSERT INTO Electricity (connection_id, meter_type, voltage) VALUES (?, ?, ?)");
                $insert->execute([$connection_id, $meter_type, $voltage]);
                $successMsgE = "✅ Electricity meter added successfully!";
            }

        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // PK violation
                $errorMsgE = "⚠ Electricity meter for this connection already exists!";
            } else {
                $errorMsgE = "❌ Failed to add electricity meter!";
            }
        }

    } elseif ($type === 'Water') {
        $pipe_size = $_POST['pipe_size'];
        $pressure = $_POST['pressure'];

        try {
            // Check for duplicate
            $stmt = $conn->prepare("SELECT * FROM Water WHERE connection_id = ?");
            $stmt->execute([$connection_id]);

            if ($stmt->rowCount() > 0) {
                $errorMsgW = "⚠ Water meter for this connection already exists!";
            } else {
                // Insert meter
                $insert = $conn->prepare("INSERT INTO Water (connection_id, pipe_size, pressure) VALUES (?, ?, ?)");
                $insert->execute([$connection_id, $pipe_size, $pressure]);
                $successMsgW = "✅ Water meter added successfully!";
            }

        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // PK violation
                $errorMsgW = "⚠ Water meter for this connection already exists!";
            } else {
                $errorMsgW = "❌ Failed to add water meter!";
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
<title>Meter Installation Forms</title>
<style>
body {
    font-family: 'Times New Roman', Times, serif;
    background: #f5f7fc;
    margin: 0;
    padding: 40px 0;
    display: flex;
    justify-content: center;
}

.forms-wrapper {
    display: flex;
    gap: 100px;
    flex-wrap: wrap;
}

.card {
    background: #fff;
    width: 400px;
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    padding: 20px 25px;
}

.card-header {
    background: linear-gradient(90deg, #00c6ff, #0072ff);
    color: #fff;
    font-size: 32px;
    font-weight: 600;
    padding: 40px 15px;
    border-radius: 10px;
    margin-bottom: 20px;
    text-align: center;
}

.form-group {
    margin-bottom: 15px;
}

label {
    display: block;
    margin-bottom: 5px;
    color: #1e3158;
    font-weight: 500;
}

input, select {
    width: 100%;
    padding: 10px 12px;
    border-radius: 8px;
    border: 1px solid #ccd4e0;
    font-size: 14px;
}

input[type="date"]::-webkit-calendar-picker-indicator {
    cursor: pointer;
}

.button-row {
    display: flex;
    justify-content: space-between;
    gap: 12px;
    margin-top: 20px;
}
.btn {
    font-family: 'Times New Roman', Times, serif;
    flex: 1;
    padding: 11px;
    border-radius: 10px;
    border: none;
    font-size: 15px;
    font-weight: 500;
    cursor: pointer;
    transition: 0.3s;
    color: white;
}
.btn-submit { background: #1a237e; }
.btn-submit:hover { background: #0f1652; }

.btn-clear { background: #ffa000; }
.btn-clear:hover { background: #d68600; }

.btn-back {
    width: 80%;
    display: block;
    padding: 12px;
    margin-top: 20px;
    text-align: center;
    border-radius: 10px;
    background: #e0e0e0;
    color: #1a237e;
    font-weight: 600;
    text-decoration: none;
    border: 2px solid #1a237e;
}

.btn-back:hover {
    background: #1a237e;
    color: white;
}
.error {
    background: #ffcccc;
    color: #a10000;
    padding: 10px;
    border-radius: 8px;
    margin-bottom: 15px;
    text-align: center;
}

.success {
    background: #ccffcc;
    color: #0b770b;
    padding: 10px;
    border-radius: 8px;
    margin-bottom: 15px;
    text-align: center;
}



</style>
</head>
<body>

<div class="forms-wrapper">

    <!-- ELECTRICITY FORM -->
    <div class="card">
        <div class="card-header">Electricity Meter</div>

        <?php if($errorMsgE) echo "<div class='error'>$errorMsgE</div>"; ?>
        <?php if($successMsgE) echo "<div class='success'>$successMsgE</div>"; ?>

        <form method="POST">
            <input type="hidden" name="type" value="Electricity">
            
            <div class="form-group">
                <label for="connection_id_e">Connection ID</label>
                <select id="connection_id_e" name="connection_id" required>
                    <?php
                    // Fetch only connections that are Electricity
                    $sql = "
                    SELECT c.connection_id
                    FROM Connection c
                    JOIN Tariff t ON c.tariff_id = t.tariff_id
                    WHERE t.utility_type = 'Electricity'
                    ORDER BY c.connection_id DESC
                    ";
                    $stmt = $conn->query($sql);
                    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value='{$row['connection_id']}'>{$row['connection_id']}</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="meter_type">Meter Type</label>
                <input type="text" id="meter_type" name="meter_type" required>
            </div>

            <div class="form-group">
                <label for="voltage">Voltage</label>
                <input type="text" id="voltage" name="voltage" required>
            </div>

            <div class="form-group">
                <label for="install_date_e">Install Date</label>
                <input type="date" id="install_date_e" name="install_date" required>
            </div>

            <div class="button-row">
            <button type="submit" class="btn btn-submit">Submit</button>
            <button type="reset" class="btn btn-clear">Clear</button>
            </div>

            <a href="../Field_final/dashboard.php"  class="btn-back">← Go Back to Dashboard</a>
        </form>
    </div>

    <!-- WATER FORM -->
    <div class="card">
        <div class="card-header">Water Meter</div>

        <?php if($errorMsgW) echo "<div class='error'>$errorMsgW</div>"; ?>
        <?php if($successMsgW) echo "<div class='success'>$successMsgW</div>"; ?>

        <form method="POST">
            <input type="hidden" name="type" value="Water">

            <div class="form-group">
                <label for="connection_id_w">Connection ID</label>
                <select id="connection_id_w" name="connection_id" required>
                    <?php
                    // Fetch only connections that are Water
                    $sql = "
                    SELECT c.connection_id
                    FROM Connection c
                    JOIN Tariff t ON c.tariff_id = t.tariff_id
                    WHERE t.utility_type = 'Water'
                    ORDER BY c.connection_id DESC
                    ";
                    $stmt = $conn->query($sql);
                    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value='{$row['connection_id']}'>{$row['connection_id']}</option>";
                    }
                    ?>

                </select>
            </div>

            <div class="form-group">
                <label for="pipe_size">Pipe Size</label>
                <input type="text" id="pipe_size" name="pipe_size" required>
            </div>

            <div class="form-group">
                <label for="pressure">Pressure</label>
                <input type="text" id="pressure" name="pressure" required>
            </div>

            <div class="form-group">
                <label for="install_date_w">Install Date</label>
                <input type="date" id="install_date_w" name="install_date" required>
            </div>
            <div class="button-row">
            <button type="submit" class="btn btn-submit">Submit</button>
            <button type="reset" class="btn btn-clear">Clear</button>
            </div>

                  <a href="../Field_final/dashboard.php"  class="btn-back">← Go Back to Dashboard</a>
        </form>

    </div>

</div>

</body>
</html>
