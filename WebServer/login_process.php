<?php
session_start();

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
    die("<pre>DATABASE ERROR:\n" . print_r(sqlsrv_errors(), true) . "</pre>");
}

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

$sql = "{ CALL sp_Login(?, ?) }";
$params = [$username, $password];

$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    die("<pre>SQL ERROR:\n" . print_r(sqlsrv_errors(), true) . "</pre>");
}

$user = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

// If no result → credentials wrong
if (!$user) {
    header("Location: failed.php?error=Invalid+Credentials");
    exit;
}

// ---------------------
// STORE USER IN SESSION
// ---------------------
$_SESSION['logged_in'] = true;
$_SESSION['user_id']   = $user['User_ID'] ?? null;
$_SESSION['name']      = $user['First_Name'] ?? '';
$_SESSION['type_raw']  = $user['Type_Name'] ?? '';

// normalize type (very important: trim + lowercase)
$type = strtolower(trim($user['Type_Name'] ?? ''));

// DEBUG (αν θες να δεις τι γυρνάει η SP, ξεκλείδωσε αυτά για 1 δοκιμή)
// echo "<pre>"; var_dump($user, $type); echo "</pre>"; exit;

switch ($type) {
    case "passenger":
        header("Location: passenger_success.php");
        break;

    case "driver":
        header("Location: driver_success.php");
        break;

    case "admin":
        header("Location: admin_success.php");
        break;

    case "operator":
        header("Location: operator_success.php");
        break;

    default:
        // Αν έρθει κάτι περίεργο από τη SP
        header("Location: failed.php?error=Unknown+Role+".urlencode($type));
        break;
}

exit;
?>
