<?php
include('db_connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_id = intval($_POST['customer_id']);
    $service_type = $_POST['service_type'] ?? 'All';

    if (!$customer_id) {
        header("Location: ../Main_Dashboard_admin/delete.php?msg=error");
        exit;
    }

    try {
        $connections = $conn->query("SELECT connection_id FROM [Connection] WHERE customer_id=$customer_id")->fetchAll(PDO::FETCH_COLUMN);

        $conn->beginTransaction();

        if (!empty($connections)) {

            // 1️⃣ Delete Cash, Card, Online_Payment linked to Payments
            $conn->exec("DELETE FROM Cash WHERE payment_id IN (SELECT P.payment_id FROM Payment P JOIN Bill B ON P.bill_id = B.bill_id WHERE B.connection_id IN (".implode(',', $connections)."))");
            $conn->exec("DELETE FROM Card WHERE payment_id IN (SELECT P.payment_id FROM Payment P JOIN Bill B ON P.bill_id = B.bill_id WHERE B.connection_id IN (".implode(',', $connections)."))");
            $conn->exec("DELETE FROM Online_Payment WHERE payment_id IN (SELECT P.payment_id FROM Payment P JOIN Bill B ON P.bill_id = B.bill_id WHERE B.connection_id IN (".implode(',', $connections)."))");

            // 2️⃣ Delete Payments
            $conn->exec("DELETE P FROM Payment P JOIN Bill B ON P.bill_id = B.bill_id WHERE B.connection_id IN (".implode(',', $connections).")");

            // 3️⃣ Delete Bills
            $conn->exec("DELETE FROM Bill WHERE connection_id IN (".implode(',', $connections).")");

            // 4️⃣ Delete Meter Readings
            $conn->exec("DELETE FROM Meter_Reading WHERE connection_id IN (".implode(',', $connections).")");

            // 5️⃣ Delete Electricity / Water
            $conn->exec("DELETE FROM Electricity WHERE connection_id IN (".implode(',', $connections).")");
            $conn->exec("DELETE FROM Water WHERE connection_id IN (".implode(',', $connections).")");

            // 6️⃣ Delete Issues
            $conn->exec("DELETE FROM Issue WHERE connection_id IN (".implode(',', $connections).") OR customer_id=$customer_id");

            // 7️⃣ Delete Connections
            $conn->exec("DELETE FROM [Connection] WHERE connection_id IN (".implode(',', $connections).")");
        }

        // 8️⃣ Delete Customer
        $conn->exec("DELETE FROM Customer WHERE customer_id=$customer_id");

        // 9️⃣ Delete User if no other role exists
        $conn->exec("
            DELETE FROM [User] 
            WHERE user_id NOT IN (SELECT user_id FROM Customer)
              AND user_id NOT IN (SELECT user_id FROM Admin)
              AND user_id NOT IN (SELECT user_id FROM Field_Officer)
              AND user_id NOT IN (SELECT user_id FROM Cashier)
              AND user_id NOT IN (SELECT user_id FROM Manager)
        ");

        $conn->commit();

        header("Location: ../Main_Dashboard_admin/delete.php?msg=success");
        exit;

    } catch (PDOException $e) {
        if ($conn->inTransaction()) $conn->rollBack();
        header("Location: ../Main_Dashboard_admin/delete.php?msg=error");
        exit;
    }

} else {
    header("Location: ../Main_Dashboard_admin/delete.php?msg=error");
    exit;
}
?>
