<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


include("../backend/db_connection.php");  


$meterReadings = [];
try {
    $stmt = $conn->prepare("
        SELECT mr.reading_id, mr.reading_date, mr.previous_reading, mr.current_reading,
               con.connection_id, c.customer_id
        FROM Meter_Reading mr
        JOIN [Connection] con ON mr.connection_id = con.connection_id
        JOIN Customer c ON con.customer_id = c.customer_id
        ORDER BY mr.reading_id ASC
    ");
    $stmt->execute();
    $meterReadings = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<script>alert('Error fetching meter readings: " . addslashes($e->getMessage()) . "');</script>";
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (!empty($_POST['delete_id'])) {

        $reading_id = (int) $_POST['delete_id'];

        try {
            $conn->beginTransaction();

            // 1️⃣ Get connection_id
            $stmt = $conn->prepare("
                SELECT connection_id 
                FROM Meter_Reading 
                WHERE reading_id = ?
            ");
            $stmt->execute([$reading_id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$row) {
                throw new Exception("Meter reading not found.");
            }

            $connection_id = $row['connection_id'];

            // 2️⃣ Check if ANY payment exists for this connection
            $stmt = $conn->prepare("
                SELECT COUNT(*) 
                FROM Payment p
                JOIN Bill b ON p.bill_id = b.bill_id
                WHERE b.connection_id = ?
            ");
            $stmt->execute([$connection_id]);
            $paymentCount = $stmt->fetchColumn();

            if ($paymentCount > 0) {
                throw new Exception(
                    "❌ Cannot delete. This bill has payments. Paid bills cannot be deleted."
                );
            }

            // 3️⃣ Delete ONLY unpaid bills
            $stmt = $conn->prepare("
                DELETE FROM Bill 
                WHERE connection_id = ? AND status = 'Unpaid'
            ");
            $stmt->execute([$connection_id]);

            // 4️⃣ Delete meter reading
            $stmt = $conn->prepare("
                DELETE FROM Meter_Reading 
                WHERE reading_id = ?
            ");
            $stmt->execute([$reading_id]);

            $conn->commit();

            echo "<script>
                alert('Unpaid bill and meter reading deleted successfully.');
                window.location.href='deletemeter.php';
            </script>";
            exit;

        } catch (Exception $e) {
            $conn->rollBack();
            echo "<script>alert('" . addslashes($e->getMessage()) . "');</script>";
        }

    } else {
        echo "<script>alert('Please select a meter reading to delete.');</script>";
    }
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Delete Meter Reading</title>

<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap');

*{box-sizing:border-box;margin:0;padding:0;font-family:'Times New Roman',Times,serif;}
html,body{height:100%;background:#f4f7fb;}
.main-content{min-height:100vh;display:flex;flex-direction:column;align-items:center;padding:25px;}
.delete-form-section{width:100%;max-width:600px;background:#fff;padding:25px;border-radius:14px;box-shadow:0 8px 20px rgba(0,0,0,0.12);margin-bottom:30px;}
.delete-form-section header h1{text-align:center;font-size:28px;padding:28px;background:linear-gradient(to right,#d32f2f,#f44336);color:#fff;border-radius:10px;margin-bottom:20px;box-shadow:0 6px 20px rgba(0,0,0,0.12);}
.form-group{margin-bottom:18px;}
label{display:block;margin-bottom:6px;font-weight:600;color:#33415c;}
select,input{width:100%;padding:12px 14px;border-radius:10px;border:2px solid #c5cae9;font-size:15px;outline:none;background:#f1f3f6;}
select:focus,input:focus{border-color:#d32f2f;box-shadow:0 0 8px rgba(211,47,47,0.12);background:#fff;}
.button-row{display:flex;gap:12px;margin-top:20px;}
.btn{flex:1;padding:12px;border-radius:10px;border:none;font-size:15px;font-weight:600;cursor:pointer;color:#fff;transition:0.2s;}
.btn-delete{background:#d32f2f;}
.btn-delete:hover{background:#9a0007;}
.btn-back{display:block;width:100%;text-align:center;margin-top:20px;padding:12px;border-radius:10px;background:#e0e0e0;color:#1a237e;font-weight:600;text-decoration:none;border:2px solid #1a237e;transition:0.2s;}
.btn-back:hover{background:#1a237e;color:#fff;}
.note{margin-top:10px;font-size:13px;color:#666;}
@media(max-width:480px){.delete-form-section{max-width:90%;padding:15px}.delete-form-section header h1{font-size:22px}}
</style>
</head>
<body>
<div class="dashboard-container">
  <main class="main-content">
    <section class="delete-form-section">
      <form action="deletemeterreding.php" method="POST" onsubmit="return confirmDelete();">
        <header><h1>Delete Meter Reading</h1></header>

        <div class="form-group">
          <label for="deleteID">Select Meter Reading</label>
          <select id="deleteID" name="delete_id" required>
            <option value="">-- Select Meter Reading --</option>
            <?php if (!empty($meterReadings)): ?>
                <?php foreach ($meterReadings as $mr): ?>
                    <?php
                        $rid = htmlspecialchars($mr['reading_id']);
                        $cid = htmlspecialchars($mr['customer_id']);
                        $conid = htmlspecialchars($mr['connection_id']);
                        $rdate = htmlspecialchars($mr['reading_date']);
                        $prev = isset($mr['previous_reading']) ? htmlspecialchars($mr['previous_reading']) : '';
                        $curr = isset($mr['current_reading']) ? htmlspecialchars($mr['current_reading']) : '';
                    ?>
                    <option value="<?= $rid ?>">
                      ID: <?= $rid ?> — Cust: <?= $cid ?> — Conn: <?= $conid ?> — Date: <?= $rdate ?>
                      <?php if ($prev !== '' || $curr !== ''): ?> (Prev: <?= $prev ?> Curr: <?= $curr ?>)<?php endif; ?>
                    </option>
                <?php endforeach; ?>
            <?php else: ?>
                <option value="">No meter readings found</option>
            <?php endif; ?>
          </select>
          <p class="note">Tip: choose the correct reading (date / prev / current shown) before deleting.</p>
        </div>

        <div class="button-row">
          <button type="submit" class="btn btn-delete">Delete</button>
        </div>

        <a href="../Field_final/dashboard.php" class="btn-back">← Go Back to Dashboard</a>
      </form>
    </section>
  </main>
</div>

<script>
function confirmDelete(){
    const sel = document.getElementById('deleteID');
    if(!sel.value){
        alert('Please select a meter reading to delete.');
        return false;
    }
    return confirm('Are you sure you want to delete reading ID ' + sel.value + ' ? This cannot be undone.');
}
</script>
</body>
</html>
