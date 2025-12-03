<?php
session_start();

// BLOCK IF NOT LOGGED IN
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$name = $_SESSION['name'] ?? "Passenger";

$server = "127.0.0.1";
$connectionOptions = [
    "Database" => "OSRH",
    "Uid" => "sa",
    "PWD" => "MyStrongPass123!",
    "Encrypt" => "no",
    "TrustServerCertificate" => "yes"
];

$conn = sqlsrv_connect($server, $connectionOptions);

if (!$conn) {
    die("Could not load services.");
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Passenger Dashboard</title>

<style>
    body {
        margin: 0;
        padding: 0;
        font-family: Arial, sans-serif;
        color: white;
        background: url('https://images.unsplash.com/photo-1503376780353-7e6692767b70?q=80&w=2065') no-repeat center center fixed;
        background-size: cover;
    }

    .overlay {
        background: rgba(0,0,0,0.75);
        width: 100%;
        min-height: 100vh;
        padding-bottom: 50px;
    }

    .header {
        text-align: center;
        padding-top: 40px;
        font-size: 34px;
        font-weight: bold;
    }

    .sub {
        text-align: center;
        margin-bottom: 25px;
        color: #cccccc;
        font-size: 18px;
    }

    .container {
        width: 90%;
        max-width: 900px;
        margin: auto;
    }

    .card {
        background: rgba(20,20,20,0.9);
        padding: 20px;
        border-radius: 12px;
        border: 1px solid #333;
        margin-bottom: 16px;
    }

    .title { font-size: 22px; }
    .desc { color: #dddddd; margin: 6px 0 12px; }
    .btn {
        background: white;
        padding: 8px 15px;
        border-radius: 6px;
        color: black;
        text-decoration: none;
        font-weight: bold;
    }
</style>

</head>

<body>

<div class="overlay">
    <div class="header">Welcome, <?= htmlspecialchars($name) ?> </div>
    <div class="sub">Choose a ride service</div>

    <div class="container">

        <?php
        $sql = "SELECT Service_Type_ID, Service_Type_Name, Description FROM SERVICE_TYPE";
        $stmt = sqlsrv_query($conn, $sql);

        while ($s = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            echo "
            <div class='card'>
                <div class='title'>{$s['Service_Type_Name']}</div>
                <div class='desc'>{$s['Description']}</div>
                <a class='btn' href='create_trip.php?service={$s['Service_Type_ID']}'>Select</a>
            </div>";
        }
        ?>
         <div class="card">
            <div class="title">Logout</div>
            <div class="desc">Sign out safely from the operator dashboard.</div>
            <a class="btn" href="logout.php">Logout</a>
        </div>
         <div class="card">
            <div class="title">Add Payment Method</div>
            <div class="desc">Add a new payment method to your account.</div>
            <a class="btn" href="add_payment_method.php">Add Payment Method</a>
        </div>
    </div>
</div>

</body>
</html>
