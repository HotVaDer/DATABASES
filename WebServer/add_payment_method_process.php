<?php
session_start();

// SECURITY CHECK
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$userID = $_SESSION['user_id'] ?? null;
if (!$userID) {
    header("Location: add_payment_method.php?error=" . urlencode("User session error."));
    exit;
}

// Only allow POST submissions
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: add_payment_method.php?error=" . urlencode("Invalid request."));
    exit;
}

// -------------------------------
// GET FORM INPUT
// -------------------------------
$holder = trim($_POST['holder'] ?? '');
$card   = trim($_POST['card'] ?? '');
$expiry = trim($_POST['expiry'] ?? '');
$type   = trim($_POST['type'] ?? '');

if (!$holder || !$card || !$expiry || !$type) {
    header("Location: add_payment_method.php?error=" . urlencode("All fields are required."));
    exit;
}

// Extract only last 4 digits
$digitsOnly = preg_replace('/\D/', '', $card);
if (strlen($digitsOnly) < 4) {
    header("Location: add_payment_method.php?error=" . urlencode("Invalid card number."));
    exit;
}
$last4 = substr($digitsOnly, -4);

// -------------------------------
// CONNECT TO SQL SERVER
// -------------------------------
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
    header("Location: add_payment_method.php?error=" . urlencode("Database connection failed."));
    exit;
}

// -------------------------------
// CALL STORED PROCEDURE
// -------------------------------
$sql = "{ CALL sp_AddPaymentMethod(?, ?, ?, ?, ?) }";

$params = [
    $userID,
    $holder,
    $last4,
    $type,
    $expiry
];

$stmt = sqlsrv_query($conn, $sql, $params);

// SQL ERROR (THROW caught here)
if ($stmt === false) {

    $errors = sqlsrv_errors();

    if ($errors && isset($errors[0]['message'])) {
        $msg = $errors[0]['message'];
    } else {
        $msg = "Unknown database error.";
    }

    header("Location: add_payment_method.php?error=" . urlencode($msg));
    exit;
}

// SUCCESS → Just go to success page
header("Location: payment_success.php");
exit;
?>
