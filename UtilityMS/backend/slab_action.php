<?php
require_once "db_connection.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['add_slab'])) {
    die("Invalid request");
}

$utilityType     = $_POST['utility_type'];
$connectionType  = $_POST['connection_type'];
$minUnit         = $_POST['min_unit'];
$maxUnit         = $_POST['max_unit'] !== "" ? $_POST['max_unit'] : null;
$rate            = $_POST['rate'];
$fixedCharge     = $_POST['fixed_charge'];
$effectiveFrom   = $_POST['effective_from'];
$effectiveTo     = $_POST['effective_to'] !== "" ? $_POST['effective_to'] : null;

$tariffSql = "
    SELECT tariff_id
    FROM Tariff
    WHERE utility_type = :utility
      AND connection_type = :connection
      AND (effective_to IS NULL OR effective_to >= GETDATE())
";

$stmt = $conn->prepare($tariffSql);
$stmt->execute([
    ':utility'    => $utilityType,
    ':connection' => $connectionType
]);

$tariff = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$tariff) {
    die("No matching tariff found");
}

$tariffId = $tariff['tariff_id'];

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

$stmt = $conn->prepare($sql);
$stmt->execute([
    $tariffId,
    $minUnit,
    $maxUnit,
    $rate,
    $fixedCharge,
    $effectiveFrom,
    $effectiveTo
]);


if ($utilityType === 'Electricity') {
    header("Location: ../Main_Dashboard_admin/slab.php?success=electricity");
} else {
    header("Location: ../Main_Dashboard_admin/slab.php?success=water");
}
exit;
