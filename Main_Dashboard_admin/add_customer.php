<?php
header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);

$name = $data['name'] ?? '';
$username = $data['username'] ?? '';
$email = $data['email'] ?? '';
$contact = $data['contact'] ?? '';
$address = $data['address'] ?? '';
$nic = $data['nic'] ?? '';
$type = $data['type'] ?? '';

if(!$name || !$username || !$email || !$nic){
    echo json_encode(["error" => "Missing required fields"]);
    exit;
}

$serverName = "localhost";
$connectionOptions = ["Database" => "UtilityManagementDB", "Uid" => "your_username", "PWD" => "your_password"];
$conn = sqlsrv_connect($serverName, $connectionOptions);

if(!$conn){
    echo json_encode(["error"=>"DB connection failed"]);
    exit;
}

$checkSql = "SELECT 1 FROM [User] WHERE username = ? OR email = ?";
$stmt = sqlsrv_query($conn, $checkSql, [$username, $email]);
if(sqlsrv_fetch_array($stmt)){
    echo json_encode(["error"=>"Username or Email already exists"]);
    exit;
}

$checkNic = "SELECT 1 FROM Customer WHERE nic_no = ?";
$stmtNic = sqlsrv_query($conn, $checkNic, [$nic]);
if(sqlsrv_fetch_array($stmtNic)){
    echo json_encode(["error"=>"NIC already exists"]);
    exit;
}

$password = substr(bin2hex(random_bytes(4)),0,8).'A1!';

$insertUser = "INSERT INTO [User] (name, username, password, email, contact_no, role) VALUES (?,?,?,?,?, 'Customer')";
$stmtUser = sqlsrv_query($conn, $insertUser, [$name, $username, $password, $email, $contact]);

if(!$stmtUser){
    echo json_encode(["error"=>"Failed to insert user"]);
    exit;
}

$uid = sqlsrv_query($conn,"SELECT SCOPE_IDENTITY() AS id");
$userId = sqlsrv_fetch_array($uid, SQLSRV_FETCH_ASSOC)['id'];

$insertCust = "INSERT INTO Customer (address, nic_no, customer_type, user_id) VALUES (?,?,?,?)";
$stmtCust = sqlsrv_query($conn, $insertCust, [$address, $nic, $type, $userId]);

if($stmtCust){
    echo json_encode(["success"=>true, "password"=>$password, "id"=>$userId]);
}else{
    echo json_encode(["error"=>"Failed to insert customer"]);
}

sqlsrv_close($conn);
?>