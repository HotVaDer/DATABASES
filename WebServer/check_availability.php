<?php
header("Content-Type: application/json");

$server = "LAPTOP-GIBMN5MB\\SQLEXPRESS01";
$conn = sqlsrv_connect($server, [
    "Database" => "OSRH",
    "Uid" => "sa",
    "PWD" => "Apollon0307!",
    "Encrypt" => 0,
    "TrustServerCertificate" => 1
]);

if (!$conn) {
    echo json_encode(["error" => "db"]);
    exit;
}

$type = $_GET['type'] ?? '';
$value = $_GET['value'] ?? '';

if ($type === "email") {
    $sql = "SELECT 1 FROM [USER] WHERE Email = ?";
} elseif ($type === "username") {
    $sql = "SELECT 1 FROM AUTHENTICATION WHERE Username = ?";
} else {
    echo json_encode(["error" => "invalid"]);
    exit;
}

$stmt = sqlsrv_query($conn, $sql, [$value]);

if ($stmt && sqlsrv_fetch($stmt)) {
    echo json_encode(["exists" => true]);
} else {
    echo json_encode(["exists" => false]);
}
?>
