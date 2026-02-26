<?php

$success = $_GET['success'] ?? null;
$generatedPassword = $_GET['password'] ?? null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Customer</title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Times New Roman', Times, serif; }

body {
  background: #eef3ff;
  display: flex;
  justify-content: center;
  padding: 40px 20px;
  min-height: 100vh;
}

.wrapper { width: 100%; max-width: 650px; }

.form-container {
  background: #ffffff;
  padding: 30px 35px;
  border-radius: 20px;
  box-shadow: 0 6px 25px rgba(0,0,0,0.12);
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
  box-shadow: 0 6px 20px rgba(0,0,0,0.15);
}

header h1 { font-size: 2rem; font-weight: 700; color: #ffffff; }
header p { color: #ffffff; margin-top: 6px; }

h2 { text-align: center; color: #1a237e; margin-bottom: 20px; font-size: 1.4rem; }

.form-group { margin-bottom: 18px; }

label { display: block; margin-bottom: 6px; font-weight: 600; color: #0d1b3f; }

input, select {
  width: 100%;
  padding: 12px 14px;
  border-radius: 10px;
  border: 2px solid #c5cae9;
  font-size: 15px;
  transition: 0.3s;
}

input:focus, select:focus {
  border-color: #3f51b5;
  box-shadow: 0 0 8px rgba(63, 81, 181, 0.2);
}

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
  transition: 0.3s;
  color: white;
}

.btn-submit { background: #1a237e; }
.btn-submit:hover { background: #0f1652; }

.btn-clear { background: #ffa000; }
.btn-clear:hover { background: #d68600; }

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

.btn-back:hover { background: #1a237e; color: white; }

.alert-success { background: #d4edda; color: #155724; padding: 12px; border-radius: 8px; margin-bottom: 15px; }
.alert-warning { background: #fff3cd; color: #856404; padding: 12px; border-radius: 8px; margin-bottom: 15px; }
</style>
</head>
<body>

<div class="wrapper">
  <div class="form-container">
    <header>
      <h1>➕ Add Customer</h1>
      <p>Enter customer details below</p>
    </header>

    <?php if ($success): ?>
        <div class="alert-success">
            Customer registered successfully!<br>
            <strong>Generated Password:</strong> <?php echo htmlspecialchars($generatedPassword); ?>
        </div>
    <?php endif; ?>

    <h2>Customer Information</h2>

    <form id="customerForm" action="../backend/add_customer_action.php" method="POST">

      <div class="form-group">
        <label for="name">Full Name</label>
        <input type="text" name="name" id="custID" placeholder="Enter Customer Name" required>
      </div>

      <div class="form-group">
        <label for="username">Username</label>
        <input type="text" name="username" id="username" placeholder="Enter Username" required>
      </div>

      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" name="email" id="email" placeholder="Enter Email" required>
      </div>

      <div class="form-group">
        <label for="Contact">Contact No</label>
        <input type="text" name="contact" id="Contact" placeholder="Enter Contact No" required>
      </div>

      <div class="form-group">
        <label for="address">Address</label>
        <input type="text" name="address" id="address" placeholder="Enter Address" required>
      </div>

      <div class="form-group">
        <label for="custName">NIC_No</label>
        <input type="text" name="nic" id="custName" placeholder="Enter NIC Number" required>
      </div>

      <div class="form-group">
        <label for="type">Customer Type</label>
        <select id="type" name="type" required>
          <option value="">Select Type</option>
          <option value="Commercial">Commercial</option>
          <option value="Residential">Residential</option>
        </select>
      </div>

      <div class="button-row">
        <button type="submit" class="btn btn-submit">Add Customer</button>
        <button type="reset" class="btn btn-clear">Clear</button>
      </div>

      <a href="admin.php" class="btn-back">← Go Back to Dashboard</a>
    </form>
  </div>
</div>

<script>
document.getElementById('customerForm').addEventListener('submit', function(e) {
    const name = document.getElementById('custID').value.trim();
    const username = document.getElementById('username').value.trim();
    const email = document.getElementById('email').value.trim();
    const contact = document.getElementById('Contact').value.trim();
    const address = document.getElementById('address').value.trim();
    const nic = document.getElementById('custName').value.trim();
    const type = document.getElementById('type').value;

    if (!name || !username || !email || !contact || !address || !nic || !type) {
        alert("Please fill in all fields.");
        e.preventDefault();
        return;
    }

    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(email)) {
        alert("Please enter a valid email address.");
        e.preventDefault();
        return;
    }

    const contactPattern = /^\d+$/;
    if (!contactPattern.test(contact)) {
        alert("Contact number should contain digits only.");
        e.preventDefault();
        return;
    }

    if (nic.length !== 10 && nic.length !== 12) {
        alert("NIC number should be 10 or 12 characters long.");
        e.preventDefault();
        return;
    }
});


</script>

</body>
</html>
