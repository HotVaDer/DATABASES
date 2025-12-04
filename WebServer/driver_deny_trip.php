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

$trip_id = $_POST['trip_id'];
$driver_id = $_SESSION['user_id'];

// Get driver's vehicle license plate
$sql_plate = "SELECT License_Plate FROM VEHICLE WHERE User_ID = ?";
$stmt = sqlsrv_query($conn, $sql_plate, [$driver_id]);
$row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

$plate = $row['License_Plate'];

// Update trip status
$sql = "UPDATE TRIP SET Status = 'Rejected' WHERE Trip_ID = ?";
sqlsrv_query($conn, $sql, [$trip_id]);

// Insert into TRIP_VEHICLE_MATCH log
$sql_log = "
INSERT INTO TRIP_VEHICLE_MATCH 
(Offer_Time, Response_Time, Response_Status, Trip_ID, License_Plate)
VALUES (GETDATE(), GETDATE(), 'Rejected', ?, ?)
";
sqlsrv_query($conn, $sql_log, [$trip_id, $plate]);

header("Location: driver_available_trips.php");
exit;
