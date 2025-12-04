<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['type_name'] !== 'driver') {
    header("Location: login.php");
    exit;
}

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
    die("<pre>DB CONNECTION ERROR:\n" . print_r(sqlsrv_errors(), true) . "</pre>");
}

// FIXED: STATIC REGION CENTER (Nicosia)
$region_center_wkt = "POINT(33.375 35.21)";
$search_radius_m = 30000; // 30 km


$sql_trips = "
SELECT 
    Trip_ID,
    Request_Time,
    Start_Point.STAsText() AS StartText,
    End_Point.STAsText() AS EndText
FROM TRIP
WHERE Status = 'requested'
  AND Start_Point.STDistance(
        geography::STGeomFromText('$region_center_wkt', 4326)
      ) <= $search_radius_m
";

$stmt_trips = sqlsrv_query($conn, $sql_trips);

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Available Trips</title>

    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial;
            background: #0f0f0f;
            margin: 0;
            padding: 0;
            color: #f1f1f1;
        }

        h2 {
            text-align: center;
            padding: 25px;
            margin: 0;
            font-size: 32px;
            color: #FFFFFF;
            text-shadow: 0 0 15px rgba(255,255,255,0.2);
        }

        .container {
            max-width: 800px;
            margin: auto;
            padding: 20px;
        }

        .trip-card {
            background: #1a1a1a;
            border-radius: 14px;
            padding: 22px 26px;
            margin-bottom: 26px;
            border: 1px solid #2a2a2a;
            box-shadow: 0 4px 14px rgba(0,0,0,0.4);
            transition: 0.25s ease-in-out;
        }

        .trip-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 22px rgba(0,0,0,0.65);
            border-color: #3a7fff;
        }

        .trip-title {
            font-size: 22px;
            color: #A9C9FF;
            margin-bottom: 10px;
            text-shadow: 0 0 8px rgba(58,127,255,0.6);
        }

        .trip-info {
            font-size: 16px;
            margin: 6px 0;
            color: #d8d8d8;
        }

        .button-row {
            margin-top: 18px;
            display: flex;
            gap: 12px;
        }

        .accept-btn, .reject-btn {
            padding: 10px 18px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-size: 15px;
            font-weight: 600;
            transition: 0.2s ease;
        }

        .accept-btn {
            background: #3a7fff;
            color: white;
            box-shadow: 0 0 8px rgba(58,127,255,0.5);
        }
        .accept-btn:hover {
            background: #5396ff;
            box-shadow: 0 0 12px rgba(58,127,255,0.8);
        }

        .reject-btn {
            background: #d63c3c;
            color: white;
            box-shadow: 0 0 8px rgba(214,60,60,0.5);
        }
        .reject-btn:hover {
            background: #ff4f4f;
            box-shadow: 0 0 12px rgba(255,60,60,0.8);
        }

        .no-trips {
            margin-top: 60px;
            text-align: center;
            color: #888;
            font-size: 20px;
        }
        .back-floating {
    position: fixed;
    top: 22px;
    left: 22px;
    width: 46px;
    height: 46px;
    border-radius: 50%;
    background: linear-gradient(145deg, #1b1b1b, #0d0d0d);
    border: 1px solid #2a2a2a;
    box-shadow: 0 0 10px rgba(0,0,0,0.7), inset 0 0 6px rgba(255,255,255,0.05);
    
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;

    font-size: 22px;
    font-weight: 700;
    color: #ffffffcc;

    transition: 0.25s ease;
    backdrop-filter: blur(6px);
    z-index: 999;
}

.back-floating:hover {
    transform: translateX(-3px) translateY(-2px);
    box-shadow: 0 0 18px rgba(0,0,0,0.9), inset 0 0 10px rgba(255,255,255,0.12);
    color: #ffffff;
}

    </style>
</head>

<body>
<<a href="driver_success.php" class="back-floating">
    <span>←</span>
</a>
<h2>Available Trips (within 30 km)</h2>

<div class="container">

<?php
$found = false;

while ($row = sqlsrv_fetch_array($stmt_trips, SQLSRV_FETCH_ASSOC)) {
    $found = true;
?>

    <div class="trip-card">
        <div class="trip-title">Trip #<?php echo $row['Trip_ID']; ?></div>

        <div class="trip-info"><strong>Start:</strong> <?php echo $row['StartText']; ?></div>
        <div class="trip-info"><strong>End:</strong> <?php echo $row['EndText']; ?></div>
        <div class="trip-info"><strong>Requested:</strong> <?php echo $row['Request_Time']->format('Y-m-d H:i'); ?></div>

        <div class="button-row">

            <form action="driver_accept_trip.php" method="POST">
                <input type="hidden" name="trip_id" value="<?php echo $row['Trip_ID']; ?>">
                <button class="accept-btn">ACCEPT</button>
            </form>

            <form action="driver_reject_trip.php" method="POST">
                <input type="hidden" name="trip_id" value="<?php echo $row['Trip_ID']; ?>">
                <button class="reject-btn">REJECT</button>
            </form>

        </div>
    </div>

<?php
}

if (!$found) {
    echo '<div class="no-trips">No available trips within 30 km.</div>';
}
?>

</div>

</body>
</html>
