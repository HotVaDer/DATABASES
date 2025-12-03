<?php
session_start();

// SECURITY CHECK
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$userID = $_SESSION['user_id'] ?? null;
if (!$userID) {
    header("Location: failed_trip.php?error=" . urlencode("User session error."));
    exit;
}

// --------------------------------------
// GET INPUT FROM MAP FORM
// --------------------------------------
$serviceID = $_POST['service_id'] ?? null;

$start_lat = $_POST['start_lat'] ?? null;
$start_lon = $_POST['start_lon'] ?? null;

$end_lat   = $_POST['end_lat'] ?? null;
$end_lon   = $_POST['end_lon'] ?? null;

if (!$serviceID || !$start_lat || !$start_lon || !$end_lat || !$end_lon) {
    header("Location: failed_trip.php?error=" . urlencode("Missing required coordinates.")
        . "&service_id=$serviceID");
    exit;
}

// convert strings to float
$start_lat = floatval($start_lat);
$start_lon = floatval($start_lon);
$end_lat   = floatval($end_lat);
$end_lon   = floatval($end_lon);

// --------------------------------------
// CONNECT TO SQL SERVER
// --------------------------------------
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
    header("Location: failed_trip.php?error=" . urlencode("Database connection failed.")
        . "&service_id=$serviceID");
    exit;
}

// --------------------------------------
// CALL THE STORED PROCEDURE
// --------------------------------------
$sql = "{ CALL sp_RequestTrip(?, ?, ?, ?, ?, ?) }";

$params = [
    $userID,
    $serviceID,
    $start_lat,
    $start_lon,
    $end_lat,
    $end_lon
];

$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    header("Location: failed_trip.php?error=" . urlencode("Failed to execute trip request.")
        . "&service_id=$serviceID");
    exit;
}

$result = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

if (!$result) {
    header("Location: failed_trip.php?error=" . urlencode("Unknown server error.")
        . "&service_id=$serviceID");
    exit;
}

// --------------------------------------
// HANDLE SP RESULT
// --------------------------------------
if ($result['Success'] == 0) {

    $msg = $result['Message'];

    // detect missing payment method
    if (stripos($msg, 'payment') !== false) {
        header("Location: add_payment_method.php?error=" . urlencode($msg));
        exit;
    }

    // default → failed page
    header("Location: failed_trip.php?error=" . urlencode($msg)
        . "&service_id=$serviceID");
    exit;
}


// --------------------------------------
// SUCCESS
// --------------------------------------
$tripID = $result['Trip_ID'];
header("Location: trip_success.php?trip_id=$tripID");
exit;
?>
