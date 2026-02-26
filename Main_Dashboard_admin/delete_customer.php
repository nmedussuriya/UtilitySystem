<?php
header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);
$customerId = $data['id'] ?? 0;

if(!$customerId){
    echo json_encode(["error"=>"Customer ID missing"]);
    exit;
}

$serverName = "localhost";
$connectionOptions = ["Database"=>"UtilityManagementDB","Uid"=>"your_username","PWD"=>"your_password"];
$conn = sqlsrv_connect($serverName, $connectionOptions);

if(!$conn){
    echo json_encode(["error"=>"DB connection failed"]);
    exit;
}

$deleteCust = "DELETE FROM Customer WHERE customer_id = ?";
$stmtCust = sqlsrv_query($conn, $deleteCust, [$customerId]);

$deleteUser = "DELETE FROM [User] WHERE user_id NOT IN (SELECT user_id FROM Customer)";
$stmtUser = sqlsrv_query($conn, $deleteUser);

if($stmtCust){
    echo json_encode(["success"=>true]);
}else{
    echo json_encode(["error"=>"Failed to delete customer"]);
}

sqlsrv_close($conn);
?>
