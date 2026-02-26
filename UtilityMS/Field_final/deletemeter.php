<?php
include('../backend/db_connection.php');

$errorMsg = "";
$successMsg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $utility = $_POST['utility_type'];
    $connection_id = $_POST['connection_id'];

    if ($utility === "Electricity") {
        $stmt = $conn->prepare("DELETE FROM Electricity WHERE connection_id = ?");
        if ($stmt->execute([$connection_id])) {
            $successMsg = "Electricity meter with Connection ID $connection_id deleted successfully!";
        } else {
            $errorMsg = "Failed to delete Electricity meter!";
        }
    } elseif ($utility === "Water") {
        $stmt = $conn->prepare("DELETE FROM Water WHERE connection_id = ?");
        if ($stmt->execute([$connection_id])) {
            $successMsg = "Water meter with Connection ID $connection_id deleted successfully!";
        } else {
            $errorMsg = "Failed to delete Water meter!";
        }
    }
}

$connections = $conn->query("SELECT c.connection_id, t.utility_type 
                              FROM Connection c 
                              JOIN Tariff t ON c.tariff_id = t.tariff_id
                              ORDER BY c.connection_id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Delete Meter</title>
<style>
body { font-family: 'Times New Roman', Times, serif; background: #f5f7fc; padding: 40px; display: flex; justify-content: center;}
.card { background: #fff; padding: 25px; border-radius: 12px; width: 600px;height: 480px; box-shadow: 0 8px 20px rgba(0,0,0,0.08);}
.card-header { text-align: center; font-size: 32px; font-weight: 600; margin-bottom: 20px; background: linear-gradient(90deg,#ff416c,#ff4b2b); color:#fff; padding:45px 0; border-radius:10px;}
.form-group { margin-bottom: 15px;}
label { display:block; margin-bottom:5px; color:#1e3158; font-weight:500;}
select, input { width:100%; padding:10px 12px; border-radius:8px; border:1px solid #ccd4e0; font-size:14px;}
.btn { padding:10px 28px; border:none; border-radius:8px; font-weight:500; cursor:pointer; transition:0.3s; margin-top:10px;}
.btn-submit { background:#ff4b2b; color:#fff;}
.btn-submit:hover { background:#e03e1f;}
.success { color:green; font-weight:600; margin-bottom:15px;}
.error { color:red; font-weight:600; margin-bottom:15px;}
.btn-back {
  display: block;
  width: 80%;
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
<script>
function filterConnections() {
    const utility = document.getElementById('utility_type').value;
    const allOptions = document.querySelectorAll('#connection_id option');
    allOptions.forEach(opt => {
        if(opt.dataset.utility === utility) {
            opt.style.display = 'block';
        } else {
            opt.style.display = 'none';
        }
    });
    const firstVisible = Array.from(allOptions).find(o => o.style.display === 'block');
    if(firstVisible) firstVisible.selected = true;
}
</script>
</head>
<body>

<div class="card">
    <div class="card-header">Delete Meter</div>

    <?php if($errorMsg) echo "<div class='error'>$errorMsg</div>"; ?>
    <?php if($successMsg) echo "<div class='success'>$successMsg</div>"; ?>

    <form method="POST">
        <div class="form-group">
            <label for="utility_type">Utility Type</label>
            <select id="utility_type" name="utility_type" onchange="filterConnections()" required>
                <option value="Electricity">Electricity</option>
                <option value="Water">Water</option>
            </select>
        </div>

        <div class="form-group">
            <label for="connection_id">Connection ID</label>
            <select id="connection_id" name="connection_id" required>
                <?php
                foreach($connections as $connRow) {
                    echo "<option value='{$connRow['connection_id']}' data-utility='{$connRow['utility_type']}'>{$connRow['connection_id']} ({$connRow['utility_type']})</option>";
                }
                ?>
            </select>
        </div>

        <button type="submit" class="btn btn-submit">Delete</button>
        <a href="../Field_final/dashboard.php"  class="btn-back">← Go Back to Dashboard</a>
    </form>
</div>

<script>
window.onload = filterConnections;
</script>

</body>
</html>
