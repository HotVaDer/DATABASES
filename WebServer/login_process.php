<?php
$server = "127.0.0.1";
$conn = sqlsrv_connect($server, [
    "Database" => "OSRH",
    "Uid" => "sa",
    "PWD" => "MyStrongPass123!",
    "Encrypt" => "no",
    "TrustServerCertificate" => "yes"
]);

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

$sql = "{ CALL sp_Login(?, ?) }";
$stmt = sqlsrv_query($conn, [$username, $password]);
$result = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

if (!$result || $result['Success'] == 0) {
    header("Location: failed.php?error=" . urlencode($result['Message']));
    exit;
}

$type = strtolower($result['Type_Name']);

switch ($type) {

    case "passenger":
        header("Location: passenger_success.php?fname=".$result['First_Name']);
        break;

    case "driver":
        header("Location: driver_success.php?fname=".$result['First_Name']);
        break;

    case "admin":
        header("Location: admin_dashboard.php?fname=".$result['First_Name']);
        break;

    case "operator":
        header("Location: operator_dashboard.php?fname=".$result['First_Name']);
        break;

    default:
        header("Location: failed.php?error=Unknown+role");
        break;
}
exit;
?>
