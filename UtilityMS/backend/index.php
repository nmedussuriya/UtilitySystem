<?php
session_start();
include('db_connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $role     = trim($_POST['role'] ?? '');

    if (empty($username) || empty($password) || empty($role)) {
        $_SESSION['message'] = "Please fill in all fields.";
        $_SESSION['color']   = "red";
        header("Location: ../login/index.php");
        exit;
    }

    try {       
        $stmt = $conn->prepare("
            SELECT * FROM [User]
            WHERE username = :username
            AND password = :password
            AND role = :role
        ");

        $stmt->execute([
            ':username' => $username,
            ':password' => $password,
            ':role'     => $role
        ]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $_SESSION['user_id']  = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role']     = $user['role'];

            if ($role === 'Customer') {
                header("Location: ../customer/customer.php");
            } else {
                header("Location: ../Main_Dashboard_admin/Main.php");
            }
            exit;
        } else {
            $_SESSION['message'] = "Incorrect username, password, or role.";
            $_SESSION['color']   = "red";
            header("Location: ../login/index.php");
            exit;
        }

    } catch (PDOException $e) {
        $_SESSION['message'] = "Database error occurred.";
        $_SESSION['color']   = "red";
        header("Location: ../login/index.php");
        exit;
    }
}
