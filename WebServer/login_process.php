<?php
session_start();

// ---------------------------
// DATABASE CONNECTION
// ---------------------------
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
    die("<pre>Database connection failed.\n" . print_r(sqlsrv_errors(), true) . "</pre>");
}

// ---------------------------
// READ USER INPUT
// ---------------------------
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if (!$username || !$password) {
    header("Location: failed.php?error=" . urlencode("Missing credentials."));
    exit;
}

// ---------------------------
// CALL sp_Login (returns ONLY hash + user fields)
// ---------------------------
$sql = "{ CALL sp_Login(?) }";
$params = [$username];

$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    die("<pre>SQL ERROR:\n" . print_r(sqlsrv_errors(), true) . "</pre>");
}

$user = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

// ---------------------------
// CASE 1: Username not found
// ---------------------------
if (!$user) {
    header("Location: failed.php?error=" . urlencode("Invalid username or password."));
    exit;
}

// ---------------------------
// EXTRACT FIELDS
// ---------------------------
$storedHash = $user['Password_Hash'];   // This is bcrypt or argon hash
$firstName  = $user['First_Name'];
$type       = strtolower($user['Type_Name']);
$userID     = $user['User_ID'];

// ---------------------------
// CASE 2: Wrong password
// ---------------------------
if (!password_verify($password, $storedHash)) {
    header("Location: failed.php?error=" . urlencode("Invalid username or password."));
    exit;
}

// ---------------------------
// SUCCESS LOGIN
// ---------------------------
$_SESSION['logged_in'] = true;
$_SESSION['user_id']   = $userID;
$_SESSION['name']      = $firstName;
$_SESSION['type']      = $type;

// REDIRECT BY ROLE
switch ($type) {
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
        header("Location: passenger_success.php");
        break;
}

exit;
?>
