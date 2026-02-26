<?php
include "db_connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $service = $_POST['service'];
    $connection_id = $_POST['connection_id'];
    $billing_month = $_POST['billing_month'];
    $units = $_POST['units'];
    $amount = $_POST['amount'];
    $due_date = $_POST['due_date'];

    $checkSql = "
        SELECT 1 FROM Bill
        WHERE connection_id = ?
        AND FORMAT(issue_date, 'yyyy-MM') = ?
    ";

    $checkParams = array($connection_id, $billing_month);
    $checkStmt = sqlsrv_query($conn, $checkSql, $checkParams);

    if (sqlsrv_has_rows($checkStmt)) {
        echo "<script>alert('⚠️ Bill already exists for this connection and month');</script>";
        echo "<script>window.history.back();</script>";
        exit;
    }

    $insertSql = "
        INSERT INTO Bill (issue_date, due_date, total_amount, status, connection_id)
        VALUES (GETDATE(), ?, ?, 'Unpaid', ?)
    ";

    $params = array($due_date, $amount, $connection_id);
    $stmt = sqlsrv_query($conn, $insertSql, $params);

    if ($stmt) {
        echo "<script>alert('✅ Bill issued successfully');</script>";
        echo "<script>window.location='issue_bill.html';</script>";
    } else {
        echo "<script>alert('❌ Error issuing bill');</script>";
    }
}
?>
