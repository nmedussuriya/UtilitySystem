<?php
header('Content-Type: application/json');

$serverName = "localhost"; 
$connectionOptions = [
    "Database" => "UtilityManagementDB",
    "Uid" => "your_username",
    "PWD" => "your_password"
];

$conn = sqlsrv_connect($serverName, $connectionOptions);
if(!$conn) {
    echo json_encode(["error" => "Connection failed"]);
    exit;
}

$sql = "
SELECT 
    cu.customer_id AS id,
    u.name,
    t.utility_type AS service,
    cu.address,
    ISNULL(c.status, 'No Connection') AS status
FROM Customer cu
JOIN [User] u ON cu.user_id = u.user_id
LEFT JOIN [Connection] c ON cu.customer_id = c.customer_id
LEFT JOIN Tariff t ON c.tariff_id = t.tariff_id
ORDER BY cu.customer_id
";

$stmt = sqlsrv_query($conn, $sql);
$customers = [];

if($stmt){
    while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)){
        $row['status'] = $row['status'] ?? 'No Connection';
        $customers[] = $row;
    }
    echo json_encode($customers);
}else{
    echo json_encode(["error" => "Query failed"]);
}

sqlsrv_close($conn);
?>