<?php
session_start();

// SECURITY CHECK
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$tripID = $_GET['trip_id'] ?? null;
if (!$tripID) {
    die("Invalid trip ID.");
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Trip Requested</title>

<style>
    body {
        margin: 0;
        padding: 0;
        background: url('https://images.unsplash.com/photo-1503376780353-7e6692767b70?q=80&w=2065')
            no-repeat center center fixed;
        background-size: cover;
        font-family: Arial, sans-serif;
        color: white;
    }

    .overlay {
        background: rgba(0,0,0,0.78);
        min-height: 100vh;
        padding-top: 60px;
    }

    .box {
        width: 90%;
        max-width: 550px;
        background: rgba(20,20,20,0.92);
        border-radius: 12px;
        border: 1px solid #333;
        margin: auto;
        padding: 30px;
        text-align: center;
    }

    h1 {
        font-size: 32px;
        margin-bottom: 10px;
    }

    .trip-id {
        margin-top: 15px;
        font-size: 20px;
        color: #ccc;
        padding: 12px;
        background: rgba(255,255,255,0.1);
        border-radius: 8px;
        display: inline-block;
    }

    .btn {
        display: inline-block;
        margin-top: 25px;
        padding: 12px 25px;
        background: white;
        color: black;
        font-weight: bold;
        border-radius: 8px;
        text-decoration: none;
        transition: 0.2s;
    }

    .btn:hover {
        background: #ddd;
    }
</style>

</head>
<body>
<div class="overlay">

    <div class="box">
        <h1>🚗 Trip Requested Successfully</h1>
        <p>Your trip has been created and is now waiting for a driver match.</p>

        <div class="trip-id">Trip ID: <strong><?= htmlspecialchars($tripID) ?></strong></div>

        <a class="btn" href="passenger_success.php">Return to Dashboard</a>
    </div>

</div>
</body>
</html>
