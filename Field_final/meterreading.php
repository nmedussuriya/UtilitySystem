<?php
include('../backend/db_connection.php');

// Handle form submission
$errorMsg = "";
$successMsg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $connection_id = $_POST['connection_id'];
    $reading_date = $_POST['reading_date'];
    $current_reading = $_POST['current_reading'];
    $officer_id = 1; // Replace with session officer ID if available

    // Get previous reading
    $stmt = $conn->prepare("SELECT TOP 1 current_reading FROM Meter_Reading WHERE connection_id = ? ORDER BY reading_date DESC, reading_id DESC");
    $stmt->execute([$connection_id]);
    $prev = $stmt->fetch(PDO::FETCH_ASSOC);
    $previous_reading = $prev['current_reading'] ?? 0;

    // Calculate consumption
    $consumption = $current_reading - $previous_reading;

    // Insert new reading
    $stmt = $conn->prepare("INSERT INTO Meter_Reading (reading_date, previous_reading, current_reading, consumption, connection_id, officer_id) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$reading_date, $previous_reading, $current_reading, $consumption, $connection_id, $officer_id])) {
        $successMsg = "Meter reading added successfully!";
    } else {
        $errorMsg = "Failed to add meter reading!";
    }
}

// Fetch connections
$connections = $conn->query("SELECT connection_id FROM Connection ORDER BY connection_id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Meter Reading</title>
<style>
/* Your CSS from previous step or simplified */
body { font-family:'Times New Roman', Times, serif; background:#f4f7fb; padding:30px; display:flex; justify-content:center;}
.card { background:#fff; padding:25px; border-radius:12px; width:600px;height: 550px; box-shadow:0 8px 20px rgba(0,0,0,0.12);}
.card h1 { text-align:center; margin-bottom:20px; background:linear-gradient(to right,#0077c2,#00b4d8); color:#fff; padding:45px; border-radius:10px;}
.form-group { margin-bottom:15px;}
label { display:block; margin-bottom:5px; font-weight:600;}
input, select { width:100%; padding:10px; border-radius:8px; border:1px solid #ccd4e0; font-size:14px;}
button { padding:10px; border:none; border-radius:8px; cursor:pointer; color:#fff; font-weight:600;}
.btn-submit { background:#0072ff; margin-right:10px;}
.btn-clear { background:#ffa000;}
/* Buttons */
.button-row {
  display: flex;
  justify-content: space-between;
  gap: 12px;
  margin-top: 20px;
}

.btn {
  flex: 1;
  padding: 12px;
  border-radius: 10px;
  border: none;
  font-size: 15px;
  font-weight: 600;
  cursor: pointer;
  color: white;
  transition: 0.3s;
}

.btn-submit { background: #1a237e; }
.btn-submit:hover { background: #0f1652; }

.btn-clear { background: #ffa000; }
.btn-clear:hover { background: #d68600; }

.btn-back {
  display: block;
  width: 100%;
  text-align: center;
  margin-top: 20px;
  padding: 12px;
  border-radius: 10px;
  background: #e0e0e0;
  color: #1a237e;
  font-weight: 600;
  text-decoration: none;
  border: 2px solid #1a237e;
  transition: 0.3s;
}

.btn-back:hover {
  background: #1a237e;
  color: white;
}
</style>
</head>
<body>

<div class="card">
<h1>Add Meter Reading</h1>

<?php if($errorMsg) echo "<div class='error'>$errorMsg</div>"; ?>
<?php if($successMsg) echo "<div class='success'>$successMsg</div>"; ?>

<form method="POST">
    <div class="form-group">
        <label>Connection ID</label>
        <select name="connection_id" id="connectionID" required>
            <option value="">Select Connection</option>
            <?php foreach($connections as $c): ?>
                <option value="<?= $c['connection_id'] ?>"><?= $c['connection_id'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label>Previous Reading</label>
        <input type="number" id="previousReading" readonly>
    </div>

    <div class="form-group">
        <label>Current Reading</label>
        <input type="number" name="current_reading" required>
    </div>

    <div class="form-group">
        <label>Reading Date</label>
        <input type="date" name="reading_date" required>
    </div>

    <div class="button-row">
        <button type="submit" class="btn btn-submit">Submit</button>
        <button type="reset" class="btn btn-clear">Clear</button>
    </div>

    <a href="../Field_final/dashboard.php" class="btn-back">← Go Back to Dashboard</a>
</form>
</div>

<script>
document.getElementById('connectionID').addEventListener('change', function(){
    let connection_id = this.value;
    if(!connection_id) return;

    fetch('meterreading_ajax.php?connection_id=' + connection_id)
    .then(res => res.json())
    .then(data => {
        document.getElementById('previousReading').value = data.previous_reading ?? 0;
    });
});
</script>

</body>
</html>
