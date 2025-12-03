<?php
session_start();

// BLOCK IF NOT LOGGED IN
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$name = $_SESSION['name'] ?? "Operator";

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
    die("Could not load operator dashboard.");
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Operator Dashboard</title>

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

    .btn:hover {
        background: #dcdcdc;
    }
</style>

</head>

<body>

<div class="overlay">
    <div class="header">Welcome, <?= htmlspecialchars($name) ?> 👋</div>
    <div class="sub">Operator Control Panel</div>

    <div class="container">

        <!-- Manage Users -->
        <div class="card">
            <div class="title">Manage Users</div>
            <div class="desc">View, edit, or verify platform users.</div>
            <a class="btn" href="operator_users.php">Open</a>
        </div>

        <!-- Monitor Trips -->
        <div class="card">
            <div class="title">Monitor Trips</div>
            <div class="desc">Track all active and pending trips in the system.</div>
            <a class="btn" href="operator_trips.php">View</a>
        </div>

        <!-- Geofence / Region Tools -->
        <div class="card">
            <div class="title">Geofence Tools</div>
            <div class="desc">Edit regions, service areas, and bridge points.</div>
            <a class="btn" href="operator_geofence.php">Manage</a>
        </div>

        <!-- Service Types -->
        <div class="card">
            <div class="title">Service Types</div>
            <div class="desc">Modify service categories and descriptions.</div>
            <a class="btn" href="operator_services.php">Modify</a>
        </div>

        <!-- Logout -->
        <div class="card">
            <div class="title">Logout</div>
            <div class="desc">Sign out safely from the operator dashboard.</div>
            <a class="btn" href="logout.php">Logout</a>
        </div>

    </div>
</div>

</body>
</html>
