<?php
include("../backend/db_connection.php");

$stmt = $conn->prepare("
    SELECT 
        c.customer_id,
        c.name,
        c.address,
        c.status,
        con.service_type
    FROM Customer c
    LEFT JOIN [Connection] con ON con.customer_id = c.customer_id
    ORDER BY c.customer_id ASC
");

$stmt->execute();

$customers = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $customers[] = [
        "id"      => $row['customer_id'],
        "name"    => $row['name'],
        "service" => $row['service_type'], 
        "address" => $row['address'],
        "status"  => $row['status']
    ];
}

echo json_encode($customers);
