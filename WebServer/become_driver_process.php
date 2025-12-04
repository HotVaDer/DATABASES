<?php
session_start();

if (empty($_SESSION['logged_in'])) {
    die("Unauthorized access.");
}

$user_id = $_SESSION['user_id'];

if (!isset($_FILES['driver_doc']) || $_FILES['driver_doc']['error'] !== 0) {
    die("File upload failed.");
}

$docStream = fopen($_FILES['driver_doc']['tmp_name'], 'rb');

$docType = $_POST['doc_type'] ?? '';
$issueDate = $_POST['issue_date'] ?? '';
$expiryDate = $_POST['expiry_date'] ?? '';

if (!$docType || !$issueDate || !$expiryDate) {
    die("Missing required fields.");
}

// --- FIXED CONNECTION ---
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
    die("<pre>".print_r(sqlsrv_errors(), true)."</pre>");
}

$sql = "EXEC sp_SubmitDriverDocument @UserID=?, @Doc_Type_Name=?, @Issue_Date=?, @Expiry_Date=?, @File_Data=?";

$params = [
    [$user_id, SQLSRV_PARAM_IN],
    [$docType, SQLSRV_PARAM_IN],
    [$issueDate, SQLSRV_PARAM_IN],
    [$expiryDate, SQLSRV_PARAM_IN],
    [$docStream, SQLSRV_PARAM_IN, SQLSRV_PHPTYPE_STREAM(SQLSRV_ENC_BINARY), SQLSRV_SQLTYPE_VARBINARY('max')]
];

$stmt = sqlsrv_query($conn, $sql, $params);

if (!$stmt) {
    die("<pre>SQL ERROR:\n".print_r(sqlsrv_errors(), true)."</pre>");
}

$res = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

if ($res['Status'] === "SUCCESS") {
    header("Location: become_driver.php");
    exit;
}

echo "<h2>Error: {$res['Status']}</h2>";
echo "<a href='become_driver.php'>Back</a>";
