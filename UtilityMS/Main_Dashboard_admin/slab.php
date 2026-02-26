<?php
$alertMsg = "";

if (isset($_GET['success'])) {
    if ($_GET['success'] === 'electricity') {
        $alertMsg = "Electricity slab added successfully ✅";
    } elseif ($_GET['success'] === 'water') {
        $alertMsg = "Water slab added successfully ✅";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Wate/Electricityr Slab</title>

  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: "Segoe UI", sans-serif;
    }

    body {
      background-color: #f4f6fb;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
    }

    .slab-issue-container {
      background: white;
      width: 500px;
      padding: 35px 40px;
      border-radius: 16px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
      margin-left: 100px;
      margin-right: 80px;
    }

    header {
      width: 100%;
      max-width: 900px;
      padding: 35px 20px;
      text-align: center;
      background: linear-gradient(to right, #0077c2, #00b4d8);
      color: white;
      border-radius: 18px;
      margin-bottom: 30px;
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
    }

    header h2 {
      color: #ffffff;
      margin-bottom: 5px;
    }

    header p {
      font-size: 14px;
      color: #ffffff;
    }

    .slab-form {
      display: flex;
      flex-direction: column;
      gap: 15px;
    }

    .form-group {
      display: flex;
      flex-direction: column;
    }

    label {
      font-weight: 500;
      margin-bottom: 6px;
      color: #333;
    }

    input {
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 14px;
      width: 100%;

    }

    input:focus {
      border-color: #2f67e8;
      outline: none;
    }

    .button-row {
      display: flex;
      justify-content: space-between;
      margin-top: 20px;
    }

    button {
      border: none;
      padding: 10px 15px;
      border-radius: 8px;
      font-weight: 500;
      cursor: pointer;
      transition: background 0.2s;
    }

    /* Button colors */
    .issue-btn {
      background-color: #2f67e8;
      color: white;
    }

    .issue-btn:hover {
      background-color: #1f4db5;
    }

    .print-btn {
      background-color: #00b894;
      color: white;
    }

    .print-btn:hover {
      background-color: #009973;
    }

    .clear-btn {
      background-color: #ff9800;
      color: white;
    }

    .clear-btn:hover {
      background-color: #e68900;
    }

    .btn-back {
      width: 100%;
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
  </style>

</head>

<body>

  <div class="slab-issue-container">
    <header>
      <h2>Electricity Slabs</h2>
    </header>
    <form method="POST" action="../backend/slab_action.php">

        <input type="hidden" name="utility_type" value="Electricity">
  <input type="hidden" name="connection_type" value="Domestic">

      <div class="form-group">
        <label>Min Value</label>
        <input type="number" name="min_unit" min="0" required>
      </div>

      <div class="form-group">
        <label>Max Value</label>
        <input type="number" name="max_unit" min="0">
      </div>

      <div class="form-group">
        <label>Rate per (Rs)</label>
        <input type="number" name="rate" step="0.01" required>
      </div>

      <div class="form-group">
        <label>Fixed Charge (Rs)</label>
        <input type="number" name="fixed_charge" step="0.01" required>
      </div>
      
      <label>Effective From</label>
      <input type="date" name="effective_from" required>
      <label>Effective To </label>
      <input type="date" name="effective_to">

      
    <div class="button-row">
      <button type="reset" class="clear-btn">Reset</button>
      <button type="submit" name="add_slab" class="issue-btn">
        Add Electricity Tariff Slab
      </button>
  </div>
      <a href="admin.php" class="btn-back">← Back to Dashboard</a>
    </form>

  </div>


  <div class="slab-issue-container">
    <header>
      <h2>Water Slabs</h2>
    </header>

    <form method="POST" action="../backend/slab_action.php">
      <input type="hidden" name="utility_type" value="Water">
      <input type="hidden" name="connection_type" value="Domestic">

    <div class="form-group">
      <label>Min Value (m³)</label>
      <input type="number" name="min_unit" min="0" required>
    </div>

    <div class="form-group">
      <label>Max Value (m³)</label>
      <input type="number" name="max_unit" min="0">
    </div>

    <div class="form-group">
      <label>Rate per m³ (Rs)</label>
      <input type="number" name="rate" step="0.01" required>
    </div>

    <div class="form-group">
      <label>Fixed Charge (Rs)</label>
      <input type="number" name="fixed_charge" step="0.01" required>
    </div>

    <div>
      <label>Effective From:</label><br>
      <input type="date" name="effective_from" required>
    </div>

    <div>
      <label>Effective To:</label><br>
      <input type="date" name="effective_to">
    </div>

    <div class="button-row">
      <button type="reset" class="clear-btn">Reset</button>
      <button type="submit" name="add_slab" class="issue-btn">
      Add Water Tariff Slab
      </button>
    </div>

    <a href="admin.php" class="btn-back">← Back to Dashboard</a>
    </form>
  </div>
<?php if (!empty($alertMsg)): ?>
<script>
   
    alert("<?php echo $alertMsg; ?>");

 
    if (window.history.replaceState) {
        const cleanUrl = window.location.href.split('?')[0];
        window.history.replaceState(null, null, cleanUrl);
    }
</script>
<?php endif; ?>

</body>

</html>