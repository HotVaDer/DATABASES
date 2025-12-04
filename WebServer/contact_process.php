<?php
session_start();

// BLOCK DIRECT ACCESS IF NOT POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: contact.php");
    exit;
}

// GET FIELDS
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$message = trim($_POST['message'] ?? '');

if (!$name || !$email || !$message) {
    header("Location: contact.php?error=" . urlencode("All fields are required."));
    exit;
}

// --------------------------------------
// CONNECT TO DATABASE
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
    header("Location: contact.php?error=" . urlencode("Database connection failed."));
    exit;
}

// --------------------------------------
// INSERT MESSAGE INTO DB
// --------------------------------------
$sql = "INSERT INTO CONTACT_MESSAGES (User_Name, User_Email, Message_Text)
        VALUES (?, ?, ?)";

$params = [$name, $email, $message];

$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    header("Location: contact.php?error=" . urlencode("Failed to submit message."));
    exit;
}

// SUCCESS
header("Location: contact_success.php");
exit;
?>
