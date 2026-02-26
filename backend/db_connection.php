<?php
$serverName = "utilitysqlserver9400.database.windows.net"; // Azure server name
$database = "utilitydb"; // your database name
$username = "sqladmin"; 
$password = "Utility@123";

// Using PDO
try {
    $conn = new PDO("sqlsrv:server=$serverName;Database=$database", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>