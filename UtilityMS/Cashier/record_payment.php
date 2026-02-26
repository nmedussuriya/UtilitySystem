<?php
include('../backend/db_connection.php');

$successMessage = "";
$errorMessage = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $bill_id = $_POST['bill_id'];
    $amount_paid = $_POST['amount'];
    $method = $_POST['method'];

    try {
        // 1️⃣ Check if this bill is already paid
        $stmt = $conn->prepare("SELECT COUNT(*) FROM Payment WHERE bill_id = ?");
        $stmt->execute([$bill_id]);
        $alreadyPaid = $stmt->fetchColumn();

        if ($alreadyPaid > 0) {
            throw new Exception("❌ This bill has already been paid.");
        }

        // 2️⃣ Insert into Payment table
        $stmt = $conn->prepare("INSERT INTO Payment (amount_paid, bill_id) VALUES (?, ?)");
        $stmt->execute([$amount_paid, $bill_id]);
        $payment_id = $conn->lastInsertId();

        // 3️⃣ Insert into specific payment method table
        if ($method === "cash") {
            $receipt_no = $_POST['receipt_no'];
            $counter_no = $_POST['counter_no'];

            // Optional: Check duplicate receipt number for same counter
            $stmt = $conn->prepare("SELECT COUNT(*) FROM Cash WHERE receipt_no = ? AND counter_no = ?");
            $stmt->execute([$receipt_no, $counter_no]);
            if ($stmt->fetchColumn() > 0) {
                throw new Exception("❌ Duplicate receipt number at this counter.");
            }

            $stmt = $conn->prepare("INSERT INTO Cash (receipt_no, counter_no, payment_id) VALUES (?, ?, ?)");
            $stmt->execute([$receipt_no, $counter_no, $payment_id]);

        } elseif ($method === "card") {
            $card_type = $_POST['card_type'];
            $card_number = $_POST['card_number'];

            // Optional: Check duplicate card payment for same card number
            $stmt = $conn->prepare("SELECT COUNT(*) FROM Card WHERE card_number = ?");
            $stmt->execute([$card_number]);
            if ($stmt->fetchColumn() > 0) {
                throw new Exception("❌ This card number has already been used for a payment.");
            }

            $stmt = $conn->prepare("INSERT INTO Card (card_type, card_number, payment_id) VALUES (?, ?, ?)");
            $stmt->execute([$card_type, $card_number, $payment_id]);

        } elseif ($method === "online") {
            $platform_name = $_POST['platform_name'];
            $transaction_ref = $_POST['transaction_ref'];

            // Optional: Check duplicate transaction reference
            $stmt = $conn->prepare("SELECT COUNT(*) FROM Online_Payment WHERE transaction_ref = ?");
            $stmt->execute([$transaction_ref]);
            if ($stmt->fetchColumn() > 0) {
                throw new Exception("❌ This transaction reference has already been used.");
            }

            $stmt = $conn->prepare("INSERT INTO Online_Payment (platform_name, transaction_ref, payment_id) VALUES (?, ?, ?)");
            $stmt->execute([$platform_name, $transaction_ref, $payment_id]);

        } else {
            throw new Exception("Invalid payment method selected.");
        }

        $successMessage = "💳 Payment recorded successfully for Bill ID: $bill_id";

    } catch (Exception $e) {
        $errorMessage = "❌ Error: " . $e->getMessage();
    }
}
?>


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cashier - Record & View Payments</title>
  <link rel="stylesheet" href="record_payment.css">
  <style>
    .hidden { display: none; }
  </style>
</head>
<body>
<div class="page-container">
    <header>
        <h2>💳 Cashier Payment Management</h2>
        <p>Record customer payments and review recent transactions</p>
    </header>

    <?php if($successMessage): ?>
      <p style="color: green; text-align:center; font-weight:600; margin:10px 0;"><?= $successMessage ?></p>
    <?php endif; ?>
    
    <?php if($errorMessage): ?>
      <p style="color: red; text-align:center; font-weight:600; margin:10px 0;"><?= $errorMessage ?></p>
    <?php endif; ?>

    <section class="payment-section">
        <h3>🧾 Record Customer Payment</h3>
        <form method="POST" class="payment-form">
            <div class="form-row">
                <div class="form-group">
                    <label for="bill-id">Bill ID</label>
                    <input type="number" name="bill_id" id="bill-id" required>
                </div>
                <div class="form-group">
                    <label for="amount">Amount Paid (Rs.)</label>
                    <input type="number" name="amount" id="amount" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="method">Payment Method</label>
                    <select name="method" id="method" required>
                        <option value="">-- Select Method --</option>
                        <option value="cash">Cash</option>
                        <option value="card">Card</option>
                        <option value="online">Online</option>
                    </select>
                </div>
            </div>

            <div id="cash-fields" class="hidden">
                <div class="form-row">
                    <div class="form-group">
                        <label for="receipt-no">Receipt Number</label>
                        <input type="text" name="receipt_no" id="receipt-no">
                    </div>
                    <div class="form-group">
                        <label for="counter-no">Counter No</label>
                        <input type="text" name="counter_no" id="counter-no">
                    </div>
                </div>
            </div>

            <div id="card-fields" class="hidden">
                <div class="form-row">
                    <div class="form-group">
                        <label for="card-type">Card Type</label>
                        <input type="text" name="card_type" id="card-type">
                    </div>
                    <div class="form-group">
                        <label for="card-number">Card Number</label>
                        <input type="text" name="card_number" id="card-number">
                    </div>
                </div>
            </div>

            <div id="online-fields" class="hidden">
                <div class="form-row">
                    <div class="form-group">
                        <label for="platform-name">Platform Name</label>
                        <input type="text" name="platform_name" id="platform-name">
                    </div>
                    <div class="form-group">
                        <label for="transaction-ref">Transaction Ref</label>
                        <input type="text" name="transaction_ref" id="transaction-ref">
                    </div>
                </div>
            </div>

            <div class="button-row">
                <button type="submit" class="submit-btn">Record Payment</button>
                <button type="reset" class="clear-btn">Clear</button>

            <a href="../Cashier/cashier_dashboard.php" class="btn-back">
            ← Go Back to Dashboard
            </a>
            
            </div>
        </form>
    </section>

  
</div>

<script>
    const methodSelect = document.getElementById('method');
    const cashFields = document.getElementById('cash-fields');
    const cardFields = document.getElementById('card-fields');
    const onlineFields = document.getElementById('online-fields');

    methodSelect.addEventListener('change', () => {
        const val = methodSelect.value;
        cashFields.classList.add('hidden');
        cardFields.classList.add('hidden');
        onlineFields.classList.add('hidden');

        if(val === 'cash') cashFields.classList.remove('hidden');
        if(val === 'card') cardFields.classList.remove('hidden');
        if(val === 'online') onlineFields.classList.remove('hidden');
    });
</script>
</body>
</html>
