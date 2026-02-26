<?php

include('../backend/db_connection.php');
$payments = $conn->query("
    SELECT p.payment_id, p.payment_date, p.amount_paid, p.bill_id,
    CASE 
        WHEN c.payment_id IS NOT NULL THEN 'Cash'
        WHEN cd.payment_id IS NOT NULL THEN 'Card'
        WHEN o.payment_id IS NOT NULL THEN 'Online'
        ELSE 'Unknown'
    END AS method
    FROM Payment p
    LEFT JOIN Cash c ON p.payment_id = c.payment_id
    LEFT JOIN Card cd ON p.payment_id = cd.payment_id
    LEFT JOIN Online_Payment o ON p.payment_id = o.payment_id
    ORDER BY p.payment_date DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>


<html>
    <head>
        <title>View All Records</title>
        <link rel="stylesheet" href="View_Records.css">
    </head>
    <body>

    <section class="view-section">
        <header>
        <h2>💳 Cashier Payment Management</h2>
        <p>Record customer payments and review recent transactions</p>
        </header>
        <table border="1" cellspacing="0" cellpadding="5">
            <thead>
                <tr>
                    <th>Payment ID</th>
                    <th>Bill ID</th>
                    <th>Amount Paid (Rs.)</th>
                    <th>Payment Date</th>
                    <th>Method</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($payments as $p): ?>
                    <tr>
                        <td><?= $p['payment_id'] ?></td>
                        <td><?= $p['bill_id'] ?></td>
                        <td><?= $p['amount_paid'] ?></td>
                        <td><?= $p['payment_date'] ?></td>
                        <td><?= $p['method'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>

            <a href="Main.php" class="btn-back">
            ← Go Back
            </a>

    </body>
    </html>