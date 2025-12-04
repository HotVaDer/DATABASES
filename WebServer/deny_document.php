<?php
session_start();

if (empty($_SESSION['logged_in']) || $_SESSION['type_name'] !== "admin") {
    die("ACCESS DENIED");
}

$docID = $_GET['id'] ?? null;
if (!$docID) die("Missing document ID");

// DB CONNECTION
$server = "127.0.0.1";
$connectionOptions = [
    "Database" => "OSRH",
    "Uid" => "sa",
    "PWD" => "MyStrongPass123!",
    "Encrypt" => "no",
    "TrustServerCertificate" => "yes"
];
$conn = sqlsrv_connect($server, $connectionOptions);
if (!$conn) die("DB ERROR");

// EXECUTE SP
$sql = "{ CALL sp_DenyDriverDocument(?) }";
$stmt = sqlsrv_query($conn, $sql, [$docID]);

if ($stmt === false) {
    die("<pre>".print_r(sqlsrv_errors(), true)."</pre>");
}

// STAY ON SAME PAGE
header("Location: " . $_SERVER['HTTP_REFERER']);
exit;
