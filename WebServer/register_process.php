<?php
session_start();

// Σύνδεση στον SQL Server
$server = "LAPTOP-GIBMN5MB\\SQLEXPRESS01";
$connectionOptions = [
    "Database" => "OSRH",
    "Uid"      => "sa",
    "PWD"      => "Apollon0307!",
    "Encrypt"  => 0,
    "TrustServerCertificate" => 1
];

$conn = sqlsrv_connect($server, $connectionOptions);

if ($conn === false) {
    header("Location: register_failed.php?error=" . urlencode("Database connection failed."));
    exit;
}

// Πάρε τα δεδομένα από τη φόρμα
$firstName = $_POST['first_name'] ?? '';
$lastName  = $_POST['last_name'] ?? '';
$birthDate = $_POST['birth_date'] ?? '';
$email     = $_POST['email'] ?? '';
$address   = $_POST['address'] ?? '';
$gender    = $_POST['gender'] ?? '';
$username  = $_POST['username'] ?? '';
$password  = $_POST['password'] ?? '';

// Client-side basic validation
if (
    $firstName === '' || $lastName === '' || $birthDate === '' ||
    $email === '' || $address === '' || $gender === '' ||
    $username === '' || $password === ''
) {
    header("Location: register_failed.php?error=" . urlencode("Please fill in all fields."));
    exit;
}

// Κλήση της stored procedure
$sql = "{ CALL dbo.sp_RegisterUser(?, ?, ?, ?, ?, ?, ?, ?) }";
$params = [
    $firstName,
    $lastName,
    $birthDate,
    $email,
    $address,
    $gender,
    $username,
    $password
];

$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    $err = sqlsrv_errors();
    $msg = "Registration failed (server error).";
    if ($err) {
        $msg .= " " . $err[0]['message'];
    }
    header("Location: register_failed.php?error=" . urlencode($msg));
    exit;
}

// Πάρε το αποτέλεσμα από την SP
$row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);


// Αν δεν έχει αποτέλεσμα ή γύρισε αποτυχία
if (
    !$row ||
    (isset($row['Success']) && (int)$row['Success'] === 0)
) {
    $errorMsg = $row['ErrorMessage'] ?? 'Registration failed.';
    header("Location: register_failed.php?error=" . urlencode($errorMsg));
    exit;
}

// Επιτυχία – μπορούμε να κρατήσουμε στοιχεία σε SESSION αν θέλουμε
$_SESSION['user_id']    = $row['User_ID'] ?? null;
$_SESSION['first_name'] = $firstName;
$_SESSION['type_name']  = 'passenger';

// ✅ Μετά την επιτυχημένη εγγραφή, πήγαινε στη σελίδα login:
header("Location: login.php?registered=1");
exit;
?>
