<?php

$serverName = "NULARAMETHNADI\SQLEXPRESS"; 
$database   = "UtilityManagementDB";

try {
    $conn = new PDO("sqlsrv:Server=$serverName;Database=$database;TrustServerCertificate=1");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}


$search = $_GET['search'] ?? '';
$service = $_GET['service'] ?? 'All';

$sql = "
SELECT DISTINCT
    cu.customer_id,
    u.name,
    t.utility_type AS service,
    cu.address,
    c.status
FROM Customer cu
INNER JOIN [User] u ON cu.user_id = u.user_id
LEFT JOIN [Connection] c ON cu.customer_id = c.customer_id
LEFT JOIN Tariff t ON c.tariff_id = t.tariff_id
WHERE (u.name LIKE :search1 OR cu.customer_id LIKE :search2)
";

if ($service !== 'All') {
    $sql .= " AND t.utility_type = :service";
}

$sql .= " ORDER BY cu.customer_id";

$stmt = $conn->prepare($sql);

$searchTerm = "%" . $search . "%";
$stmt->bindValue(':search1', $searchTerm);
$stmt->bindValue(':search2', $searchTerm);

if ($service !== 'All') {
    $stmt->bindValue(':service', $service);
}

try {
    $stmt->execute();
    $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<pre>SQL State: " . $e->errorInfo[0] . "</pre>";
    echo "<pre>Error Code: " . $e->errorInfo[1] . "</pre>";
    echo "<pre>Message: " . $e->getMessage() . "</pre>";
    die();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>View All Customers</title>
  <link rel="stylesheet" href="viewcustomer.css">
</head>
<body>

<header class="top-header">
  <h1>All Customers</h1>
  <p>Manage and view customer details easily.</p>
</header>

<form method="GET" action="Viewcustomer.php" class="search-container">
  <input type="text" 
         name="search" 
         placeholder="Search by name or ID..." 
         id="searchInput" 
         value="<?= htmlspecialchars($search) ?>">
         
  <select name="service" id="serviceFilter">
    <option value="All" <?= $service == 'All' ? 'selected' : '' ?>>All Services</option>
    <option value="Water" <?= $service == 'Water' ? 'selected' : '' ?>>Water</option>
    <option value="Electricity" <?= $service == 'Electricity' ? 'selected' : '' ?>>Electricity</option>
  </select>
  
  <button type="submit" style="padding: 5px 15px; cursor: pointer;">Search</button>
</form>

<div class="table-container">
  <table id="customersTable">
    <thead>
      <tr>
        <th>Customer ID</th>
        <th>Name</th>
        <th>Service</th>
        <th>Address</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody id="tableBody">
      <?php if (count($customers) > 0): ?>
        <?php foreach ($customers as $cust): ?>
          <tr>
            <td><?= htmlspecialchars($cust['customer_id']) ?></td>
            <td><?= htmlspecialchars($cust['name']) ?></td>
            <td><?= htmlspecialchars($cust['service'] ?? 'N/A') ?></td>
            <td><?= htmlspecialchars($cust['address']) ?></td>
            <td><?= htmlspecialchars($cust['status'] ?? 'No Connection') ?></td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td colspan="5" style="text-align:center;">No customers found matching your criteria.</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<div class="back-dashboard">
  <a href="admin.php" class="btn-back">← Go Back to Dashboard</a>

</body>
</html>