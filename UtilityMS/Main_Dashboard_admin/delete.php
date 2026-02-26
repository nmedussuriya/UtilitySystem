<?php
include('../backend/db_connection.php');

/* LOAD ALL CUSTOMERS */
$customers = $conn->query("
    SELECT cu.customer_id, u.name AS customer_name
    FROM Customer cu
    JOIN [User] u ON u.user_id = cu.user_id
    ORDER BY u.name
")->fetchAll(PDO::FETCH_ASSOC);

$msg = $_GET['msg'] ?? '';
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Delete Customer</title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Times New Roman', Times, serif; }
body {
  background: #f0f4ff;
  padding: 80px 80px;
  display: flex;
  flex-direction: column;
  justify-content: flex-start;
  align-items: center;
  min-height: 100vh;
}
.top-header {
  width: 100%;
  max-width: 900px;
  padding: 35px 20px;
  text-align: center;
  background: linear-gradient(to right, #0077c2, #00b4d8);
  color: white;
  border-radius: 18px;
  margin-bottom: 30px;
  box-shadow: 0 6px 20px rgba(0,0,0,0.15);
}
.top-header h1 { font-size: 2rem; margin-bottom: 6px; font-weight: 700; }
.top-header p { font-size: 1rem; opacity: 0.9; }

.form-container {
  width: 100%;
  max-width: 900px;
  background: white;
  padding: 25px 30px;
  border-radius: 20px;
  box-shadow: 0 6px 20px rgba(0,0,0,0.12);
}

.input-group { margin-bottom: 20px; }
.input-group label { font-weight: 600; margin-bottom: 6px; display: block; font-size: 16px; }
.input-group input, .input-group select {
  width: 100%;
  padding: 12px;
  border-radius: 10px;
  border: 2px solid #1a73e8;
  font-size: 15px;
  outline: none;
  transition: 0.3s;
}
.input-group input:focus, .input-group select:focus { border-color: #0d47a1; box-shadow: 0 0 8px rgba(13, 71, 161, 0.3); }

.btn-delete {
  width: 100%;
  padding: 14px;
  background: #d32f2f;
  color: white;
  border: none;
  font-size: 17px;
  font-weight: 600;
  border-radius: 12px;
  cursor: pointer;
  transition: 0.3s;
  margin-top: 5px;
}
.btn-delete:hover { background: #b71c1c; }

.back-dashboard { margin-top: 25px; text-align: center; }
.btn-back {
  width: 100%;
  display: list-item;
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
.btn-back:hover { background: #1a237e; color: white; }
</style>
</head>
<body>

<div class="form-container">
  <form id="customerdeleteForm" action="../backend/delete_customer_action.php" method="POST">
    
    <div class="top-header">
      <h2>Delete Customer Connection</h2>
</div>
      <?php if ($msg === 'success'): ?>
        <div class="success">✅ Connection deleted successfully</div>
      <?php elseif ($msg === 'error'): ?>
        <div class="error">❌ Delete failed</div>
      <?php endif; ?>

      <form method="POST" action="../backend/delete_connection_action.php"
            onsubmit="return confirm('Delete this connection and all related data?')">
            

    <div class="input-group">
      <label for="customer_id">Select Customer</label>
      <select name="customer_id" id="customer_id" required>
        <option value="">-- Select Customer --</option>
        <?php foreach($customers as $c): ?>
            <option value="<?= $c['customer_id'] ?>"><?= htmlspecialchars($c['customer_name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    

    <div class="input-group">
      <label for="serviceFilter">Service Type</label>
      <select id="serviceFilter" name="service_type">
        <option value="All">All</option>
        <option value="Water">Water</option>
        <option value="Electricity">Electricity</option>
      </select>
    </div>


  <button class="btn-delete" id="deleteBtn">Delete Customer</button>
</div>

<div class="back-dashboard">
  <a href="admin.php" class="btn-back">← Go Back to Dashboard</a>
</div>
          </form>



</body>
</html> 
<script>
document.getElementById('deleteBtn').addEventListener('click', function (e) {
    if (!confirm('Are you sure you want to delete this customer and related connections?')) {
        e.preventDefault(); // stop form submit
    }
});
</script>


</body>
</html>
