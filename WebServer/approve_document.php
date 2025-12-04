<?php
session_start();

if (empty($_SESSION['logged_in']) || $_SESSION['type_name'] !== "admin") {
    die("ACCESS DENIED");
}

$docID = $_GET['id'] ?? null;
if (!$docID) die("Missing document ID.");

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
if (!$conn) die("Database error.");

// -----------------------------------------------------
// 1) APPROVE THE SELECTED DOCUMENT
// -----------------------------------------------------
$sql = "UPDATE DRIVER_DOCUMENT SET Status = 'approved' WHERE Driver_Document_ID = ?";
sqlsrv_query($conn, $sql, [$docID]);

// -----------------------------------------------------
// 2) FIND WHICH USER OWNS THIS DOCUMENT
// -----------------------------------------------------
$sqlUser = "SELECT User_ID FROM DRIVER_DOCUMENT WHERE Driver_Document_ID = ?";
$stmt = sqlsrv_query($conn, $sqlUser, [$docID]);
$row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

if (!$row) die("Document not found.");

$userID = $row['User_ID'];

// -----------------------------------------------------
// 3) CHECK IF THIS USER HAS ANY DOCUMENT STILL PENDING OR DENIED
// -----------------------------------------------------
$sqlCheck = "
    SELECT COUNT(*) AS NotApproved
    FROM DRIVER_DOCUMENT
    WHERE User_ID = ? AND Status <> 'approved';
";

$stmt2 = sqlsrv_query($conn, $sqlCheck, [$userID]);
$check = sqlsrv_fetch_array($stmt2, SQLSRV_FETCH_ASSOC);

if ($check['NotApproved'] == 0) {
    // -----------------------------------------------------
    // 4) ALL DOCUMENTS APPROVED → UPGRADE USER TO DRIVER
    // -----------------------------------------------------

    // DRIVER table update
    $sqlUpdateDriver = "UPDATE DRIVER SET Status = 'approved' WHERE User_ID = ?";
    sqlsrv_query($conn, $sqlUpdateDriver, [$userID]);

    // USER table update
    $sqlUpdateUser = "UPDATE [USER] SET Type_Name = 'driver' WHERE User_ID = ?";
    sqlsrv_query($conn, $sqlUpdateUser, [$userID]);
}

// -----------------------------------------------------
// REDIRECT BACK
// -----------------------------------------------------
header("Location: admin_success.php");
exit;

?>
