<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$serviceID = $_GET['service'] ?? null;

if (!$serviceID) {
    die("Invalid service selected.");
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
    die("Could not load service.");
}

// Fetch service details
$sql = "SELECT Service_Type_Name, Description FROM SERVICE_TYPE WHERE Service_Type_ID = ?";
$stmt = sqlsrv_query($conn, $sql, [$serviceID]);
$service = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

if (!$service) {
    die("Service not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Create Trip</title>

<style>
    body {
        margin: 0;
        padding: 0;
        font-family: Arial, sans-serif;
        color: white;
        background: url('https://images.unsplash.com/photo-1503376780353-7e6692767b70?q=80&w=2065')
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
        max-width: 600px;
        margin: auto;
    }

    .card {
        background: rgba(20,20,20,0.9);
        padding: 25px;
        border-radius: 12px;
        border: 1px solid #333;
        margin-bottom: 16px;
        backdrop-filter: blur(6px);
    }

    label {
        font-size: 16px;
        display: block;
        margin-top: 15px;
    }

    input[type="text"] {
        width: 100%;
        padding: 10px;
        border-radius: 6px;
        border: none;
        margin-top: 8px;
    }

    .btn {
        margin-top: 25px;
        background: white;
        padding: 10px 20px;
        border-radius: 6px;
        color: black;
        text-decoration: none;
        font-weight: bold;
        display: inline-block;
        border: none;
        cursor: pointer;
        font-size: 16px;
    }

    .btn:hover {
        background: #dddddd;
    }
</style>

</head>
<body>

<div class="overlay">
    <div class="header">Create Trip</div>
    <div class="sub">Service: <?= htmlspecialchars($service['Service_Type_Name']) ?></div>

    <div class="container">

        <form action="create_trip_process.php" method="POST" class="card">

            <input type="hidden" name="service_id" value="<?= $serviceID ?>">

            <label>Start Location (Latitude, Longitude):</label>
            <input type="text" name="start_point" placeholder="e.g. 35.1676, 33.3736" required>

            <label>End Location (Latitude, Longitude):</label>
            <input type="text" name="end_point" placeholder="e.g. 35.1998, 33.3823" required>

            <button class="btn" type="submit">Submit Trip</button>
        </form>

    </div>
</div>

</body>
</html>
