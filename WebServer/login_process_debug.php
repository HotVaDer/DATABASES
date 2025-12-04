<?php
session_start();

echo "<pre>";

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
    echo "DB ERROR:\n";
    print_r(sqlsrv_errors());
    exit;
}

// ---------------------------
// READ INPUT
// ---------------------------
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

echo "USERNAME from form:\n";
var_dump($username);
echo "\nPASSWORD from form:\n";
var_dump($password);
echo "\n-------------------------\n";

// ---------------------------
// DIRECT QUERY (NO sp_Login)
// ---------------------------
$sql = "
    SELECT 
        U.User_ID,
        U.First_Name,
        U.Type_Name,
        A.Password_Hash
    FROM AUTHENTICATION A
    JOIN [USER] U ON U.User_ID = A.User_ID
    WHERE A.Username = ?
";
$params = [$username];

$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    echo "SQL ERROR:\n";
    print_r(sqlsrv_errors());
    exit;
}

$user = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

echo "ROW FROM DB:\n";
var_dump($user);
echo "\n-------------------------\n";

if (!$user) {
    echo "RESULT: NO USER FOUND FOR THAT USERNAME.\n";
    exit;
}

// ---------------------------
// PASSWORD CHECK
// ---------------------------
$storedHash = $user['Password_Hash'];

echo "STORED HASH:\n";
var_dump($storedHash);
echo "\n";

$ok = password_verify($password, $storedHash);

echo "password_verify RESULT:\n";
var_dump($ok);
echo "\n";

if ($ok) {
    echo "\n✅ LOGIN OK!\n";
    echo "User_ID: " . $user['User_ID'] . "\n";
    echo "First_Name: " . $user['First_Name'] . "\n";
    echo "Type: " . $user['Type_Name'] . "\n";
} else {
    echo "\n❌ LOGIN FAILED (WRONG PASSWORD OR HASH MISMATCH)\n";
}

echo "</pre>";
