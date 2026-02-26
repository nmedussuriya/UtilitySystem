<?php

include('db_connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = $_POST['name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];
    $address = $_POST['address'];
    $nic = $_POST['nic'];
    $type = $_POST['type']; 

    try {
 
        $stmt = $conn->prepare("
            EXEC sp_RegisterCustomer 
                @name = :name,
                @username = :username,
                @email = :email,
                @contact = :contact,
                @address = :address,
                @nic = :nic,
                @type = :type
        ");

        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':contact', $contact);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':nic', $nic);
        $stmt->bindParam(':type', $type);

        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $generatedPassword = $result['created_password'] ?? null;

        header("Location: ../Main_Dashboard_admin/addcustomer.php?success=1&password=$generatedPassword");
        exit();

        if ($generatedPassword) {
            echo "
                <div class='alert alert-success'>
                    Customer registered successfully!<br>
                    <strong>Generated Password:</strong> $generatedPassword
                </div>
            ";
        } else {
            echo "
                <div class='alert alert-warning'>
                    Customer registered successfully!<br>
                    <strong>Generated Password could not be retrieved.</strong>
                </div>
            ";
        }

    } catch (PDOException $e) {
        echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
    }


}
?>
