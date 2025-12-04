<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: register.php");
    exit;
}

// Collect input
$first   = trim($_POST['first'] ?? '');
$last    = trim($_POST['last'] ?? '');
$email   = trim($_POST['email'] ?? '');
$pass    = trim($_POST['password'] ?? '');
$pass2   = trim($_POST['confirm_password'] ?? '');
$birth   = trim($_POST['birthdate'] ?? '');
$address = trim($_POST['address'] ?? '');
$gender  = trim($_POST['gender'] ?? '');
$type    = "Passenger"; // Default user type

// Basic validation
if (!$first || !$last || !$email || !$pass || !$pass2 || !$birth || !$address || !$gender) {
    header("Location: register.php?error=missing_fields");
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: register.php?error=invalid_email");
    exit;
}

if ($pass !== $pass2) {
    header("Location: register.php?error=password_mismatch");
    exit;
}

// Hash password
$hashedPassword = password_hash($pass, PASSWORD_DEFAULT);

// Database connection
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
    die("DATABASE ERROR");
}

// Call stored procedure
$sql = "EXEC sp_RegisterUser 
        @FirstName=?, 
        @LastName=?, 
        @BirthDate=?, 
        @Email=?, 
        @Address=?, 
        @Gender=?, 
        @TypeName=?, 
        @PasswordHash=?";

$params = [$first, $last, $birth, $email, $address, $gender, $type, $hashedPassword];
$stmt = sqlsrv_query($conn, $sql, $params);

if (!$stmt) {
    header("Location: register.php?error=db_error");
    exit;
}

$result = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

if ($result['Status'] === "EMAIL_EXISTS") {
    header("Location: register.php?error=email_taken");
    exit;
}

if ($result['Status'] === "SUCCESS") {

    $_SESSION['logged_in'] = true;
    $_SESSION['email'] = $email;
    $_SESSION['name'] = $first;
    $_SESSION['user_id'] = $result['User_ID'];

    header("Location: passenger_success.php");
    exit;
}

header("Location: register.php?error=unknown");
exit;
?>
