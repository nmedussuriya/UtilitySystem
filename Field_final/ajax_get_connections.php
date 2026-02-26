<?php
include("../backend/db_connection.php");

$customer_id = $_GET['customer_id'] ?? null;

if (!$customer_id) {
    echo json_encode([]);
    exit;
}

$stmt = $conn->prepare("SELECT connection_id FROM [Connection] WHERE customer_id = ?");
$stmt->execute([$customer_id]);

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
