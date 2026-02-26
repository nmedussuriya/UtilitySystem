<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);


require_once "db_connection.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_slab'])) {

    $utilityType   = $_POST['utility_type'];
    $minUnit       = $_POST['min_unit'];
    $maxUnit       = !empty($_POST['max_unit']) ? $_POST['max_unit'] : NULL;
    $rate          = $_POST['rate'];
    $fixedCharge   = $_POST['fixed_charge'];
    $effectiveFrom = $_POST['effective_from'];
    $effectiveTo   = !empty($_POST['effective_to']) ? $_POST['effective_to'] : NULL;

    $tariffSql = "
        SELECT TOP 1 tariff_id 
        FROM Tariff
        WHERE utility_type = ?
        ORDER BY effective_from DESC
    ";

    $tariffStmt = sqlsrv_query($conn, $tariffSql, [$utilityType]);

    if ($tariffStmt === false) {
        $errorMsg = sqlsrv_errors()[0]['message'];
    } else {

        $tariffRow = sqlsrv_fetch_array($tariffStmt, SQLSRV_FETCH_ASSOC);

        if (!$tariffRow) {
            $errorMsg = "No active tariff found for $utilityType";
        } else {

            $tariffId = $tariffRow['tariff_id'];

            if ($utilityType === 'Electricity') {
                $sql = "
                    INSERT INTO Electricity_Tariff_Slab
                    (tariff_id, min_unit, max_unit, rate_per_unit, fixed_charge, effective_from, effective_to)
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ";
            } else {
                $sql = "
                    INSERT INTO Water_Tariff_Slab
                    (tariff_id, min_unit, max_unit, rate_per_m3, fixed_charge, effective_from, effective_to)
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ";
            }

            $params = [
                $tariffId,
                $minUnit,
                $maxUnit,
                $rate,
                $fixedCharge,
                $effectiveFrom,
                $effectiveTo
            ];

            $stmt = sqlsrv_query($conn, $sql, $params);

            if ($stmt === false) {
                $errorMsg = sqlsrv_errors()[0]['message'];
            } else {
                $successMsg = "$utilityType tariff slab added successfully ✅";
            }
        }
    }
}
?>
