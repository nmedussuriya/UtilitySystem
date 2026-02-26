<?php
session_start();

$message = $_SESSION['message'] ?? '';
$color   = $_SESSION['color'] ?? '';

unset($_SESSION['message'], $_SESSION['color']);
?>

<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login form</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

    <style>

    
    </style>
    
</head>

<body>
    <div class="wrapper">
        <form id="login_form" action="../backend/login.php" method="POST">


            <h1>Login</h1>
            
    <?php if (!empty($message)): ?>
        <div class="alert <?= htmlspecialchars($color); ?>">
            <?= htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>



            <div class="types">
                                
        <select name="role" required>
            <option value="">Select Role</option>
            <option value="Customer">Customer</option>
            <option value="Admin">Admin</option>
            <option value="Manager">Manager</option>
            <option value="Cashier">Cashier</option>
            <option value="Field Officer">Field Officer</option>
        </select>

            </div>

    <div class="Input-box">
        <input type="text" name="username" placeholder="Username" required>
    </div>

    <div class="Input-box">
        <input type="password" name="password" placeholder="Password" required>
    </div>

            <button type="submit" class="btn" id="log_btn">Login</button>

            <script src="lgn_Valid.js"></script>




        </form>
    </div>
</body>
</html>
