<?php
session_start();

// ---------------------------
// CONNECT TO DB
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
    $_SESSION['login_error'] = "Database connection failed.";
    header("Location: login.php");
    exit;
}

// ---------------------------
// READ INPUT
// ---------------------------
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    $_SESSION['login_error'] = "Missing username or password.";
    header("Location: login.php");
    exit;
}

// ---------------------------
// CALL sp_Login
// ---------------------------
$sql = "{ CALL sp_Login(?) }";
$params = [$username];

$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    $_SESSION['login_error'] = "Server error while logging in (SP failed).";
    header("Location: login.php");
    exit;
}

$user = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

// USER NOT FOUND
if (!$user || ($user['Success'] ?? 0) == 0) {
    $_SESSION['login_error'] = "User not found.";
    header("Location: login.php");
    exit;
}

// ---------------------------
// PASSWORD CHECK
// ---------------------------
$storedHash = $user['Password_Hash'];

if (!password_verify($password, $storedHash)) {
    $_SESSION['login_error'] = "Invalid username or password.";
    header("Location: login.php");
    exit;
}

// ---------------------------
// SUCCESS LOGIN
// ---------------------------
session_regenerate_id(true);

$_SESSION['logged_in']  = true;
$_SESSION['user_id']    = $user['User_ID'];
$_SESSION['name']       = $user['First_Name'];

// FIXED: USE CONSISTENT KEY NAME
$_SESSION['type_name']  = strtolower($user['Type_Name']);

// ---------------------------
// REDIRECT BY ROLE
// ---------------------------
switch ($_SESSION['type_name']) {
    case "admin":
        header("Location: admin_success.php");
        break;

    case "driver":
        header("Location: driver_success.php");
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
