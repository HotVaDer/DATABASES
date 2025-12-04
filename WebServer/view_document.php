<?php
session_start();

// ONLY ADMIN CAN VIEW
if (empty($_SESSION['logged_in']) || $_SESSION['type_name'] !== "admin") {
    die("ACCESS DENIED");
}

// GET DOCUMENT ID
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

if (!$conn) {
    die("Database error");
}

// FETCH DOCUMENT BY ID
$sql = "SELECT File_Data, Doc_Type_Name FROM DRIVER_DOCUMENT WHERE Driver_Document_ID = ?";
$stmt = sqlsrv_query($conn, $sql, [$docID]);

if ($stmt === false) {
    die("<pre>".print_r(sqlsrv_errors(), true)."</pre>");
}

$row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

if (!$row) {
    die("Document not found.");
}

$fileData = $row['File_Data'];

// ------------------------------------
// MIME TYPE DETECTION (simple)
// ------------------------------------
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$tempFile = tempnam(sys_get_temp_dir(), 'doc');
file_put_contents($tempFile, $fileData);
$mimeType = finfo_file($finfo, $tempFile);
finfo_close($finfo);

// default fallback
if (!$mimeType) { 
    $mimeType = "application/octet-stream"; 
}

// ------------------------------------
// OUTPUT FILE INLINE
// ------------------------------------
header("Content-Type: $mimeType");
header("Content-Length: " . strlen($fileData));
header("Content-Disposition: inline; filename=\"document_$docID\"");

echo $fileData;

unlink($tempFile);
exit;
?>
