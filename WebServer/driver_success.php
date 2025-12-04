<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}



$name = $_SESSION['name'] ?? "Driver";

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
    die("Could not load driver data.");
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Driver Dashboard</title>

<style>
    body {
        margin: 0;
        padding: 0;
        font-family: Arial, sans-serif;
        color: white;
        background: url('https://images.unsplash.com/photo-1493238792000-8113da705763?q=80&w=2070') 
            no-repeat center center fixed;
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
    <div class="header">Welcome, <?= htmlspecialchars($name) ?> 👋</div>
    <div class="sub">Driver Control Panel</div>

    <div class="container">

        <!-- View Assigned Trips -->
        <div class="card">
            <div class="title">View Assigned Trips</div>
            <div class="desc">Check upcoming and active trips assigned to you.</div>
            <a class="btn" href="driver_available_trips.php">Open</a>
        </div>

        <!-- Manage Vehicle -->
        <div class="card">
            <div class="title">Manage Vehicle</div>
            <div class="desc">Update your vehicle details, documents, or photos.</div>
            <a class="btn" href="add_vehicle.php">Manage</a>
        </div>

        <!-- Set Availability -->
        <div class="card">
            <div class="title">Availability</div>
            <div class="desc">Set when you are available to take rides.</div>
            <a class="btn" href="driver_availability.php">Set Availability</a>
        </div>

        <!-- Logout -->
        <div class="card">
            <div class="title">Logout</div>
            <div class="desc">Sign out from your driver dashboard.</div>
            <a class="btn" href="logout.php">Logout</a>
        </div>

    </div>
</div>

</body>
</html>
