<?php
session_start();
if ($_SESSION['type_name'] !== "driver") {
    die("ACCESS DENIED");
}

$driverID = $_SESSION['user_id'];

$license = $_POST['license_plate'];
$seats = $_POST['seat_capacity'];
$space = $_POST['trunk_space'];
$weight = $_POST['trunk_weight'];
$type = $_POST['vehicle_type'];
$price = $_POST['price'];
$region = $_POST['region_id'];

$server = "127.0.0.1";
$conn = sqlsrv_connect($server, [
    "Database"=>"OSRH",
    "Uid"=>"sa",
    "PWD"=>"MyStrongPass123!",
    "Encrypt"=>"no",
    "TrustServerCertificate"=>"yes"
]);

if (!$conn) die("DB ERROR");

$sql = "{ CALL sp_AddVehicle(?, ?, ?, ?, ?, ?, ?, ?) }";
$params = [$license, $seats, $space, $weight, $type, $price, $region, $driverID];

$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    echo "<pre>";
    print_r(sqlsrv_errors());
    echo "</pre>";
    exit;
}

header("Location: vehicle_added_success.php");
exit;
?>
