<?php
session_start();
$role = $_SESSION['role'] ?? '';
if ($role !== 'Manager' && $role !== 'Admin') {
    header("Location: ../dd/Main.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Dashboard</title>
    <style>
        body {
        font-family: 'Times New Roman', Times, serif;
        margin: 0;
        padding: 0;
        background-image: url('backgroundimg.jpg'); /* Add your background image */
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        text-align: center;
        background-repeat: no-repeat;
        padding: 20px;
        }
        header {
            background: #0531578a;
            color: #fff;
            text-align: center;
            padding: 25px 20px;
            
        }

        header h1 {
            font-size: 32px;
            font-weight: bold;
            color:white;
        }

        header p {
            margin-top: 10px;
            font-size: 15px;
            opacity: 0.9;
        }


        .dashboard-container {
            width: 80%;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0px 4px 12px rgba(0,0,0,0.1);
        }
        h1 { text-align: center; color: #1e88e5; margin-bottom: 5px; }
        p.subtitle { text-align: center; color: #666; margin-bottom: 30px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { background-color: #1e88e5; color: white; padding: 15px; text-align: left; }
        td { padding: 15px; background-color: #f8f9fb; border-bottom: 1px solid #ddd; }
        tr:nth-child(even) td { background-color: #f1f4f7; }
        .btn-generate {
            background-color: #0077c8;
            color: white;
            padding: 10px 18px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            display: inline-block;
        }
        .btn-back {
            width: 30%;
            display: block;
            padding: 12px;
            margin: 25px auto 0;
            text-align: center;
            border-radius: 10px;
            background: #e0e0e0;
            color: #1a237e;
            font-weight: 600;
            text-decoration: none;
            border: 2px solid #1a237e;
        }
    </style>
</head>
<body>
    <header>
  <h1>Welcome to the Manager Dashboard</h1>
  <p>Reliable service. Sustainable future. Powered for you.</p>
    </header>

    <div class="dashboard-container">
        <h1>Reports & Analytics</h1>
        <p class="subtitle">Generate and analyze reports for revenue trends, defaulters, and usage patterns</p>
        <table>
            <tr>
                <th>Report Type</th>
                <th>Description</th>
                <th>Action</th>
            </tr>
            <tr>
                <td>Revenue Trends</td>
                <td>Analyze monthly and yearly income patterns</td>
                <td>
                    <a href="revenue.php" class="btn-generate">Generate</a>
                </td>
            </tr>
            <tr>
                <td>Defaulters</td>
                <td>View customers with unpaid bills</td>
                <td><a href="Defaulters.php" class="btn-generate">Generate</a></td>
            </tr>
            <tr>
                <td>Usage Patterns</td>
                <td>Check water and electricity consumption behavior</td>
                <td><a href="Usage pattern reports.php" class="btn-generate">Generate</a></td>
            </tr>
        </table>
        <a href="../Main_Dashboard_admin/Main.php" class="btn-back">← Go Back to Dashboard</a>
    </div>
</body>
</html>