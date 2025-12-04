<?php
session_start();

if (empty($_SESSION['logged_in'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

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
    die("<pre>".print_r(sqlsrv_errors(), true)."</pre>");
}

// Fetch driver status
$sql = "SELECT Status FROM DRIVER WHERE User_ID = ?";
$stmt = sqlsrv_query($conn, $sql, [$user_id]);
$driver = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

$driverStatus = $driver['Status'] ?? 'not_driver';

// Get required docs
$sql2 = "SELECT Doc_Type_Name FROM DRIVER_DOC_TYPE";
$stmt2 = sqlsrv_query($conn, $sql2);

$requiredDocs = [];
while ($row = sqlsrv_fetch_array($stmt2, SQLSRV_FETCH_ASSOC)) {
    $requiredDocs[] = $row['Doc_Type_Name'];
}

// Get uploaded docs
$sql3 = "SELECT Doc_Type_Name FROM DRIVER_DOCUMENT WHERE User_ID = ?";
$stmt3 = sqlsrv_query($conn, $sql3, [$user_id]);

$uploadedDocs = [];
while ($row = sqlsrv_fetch_array($stmt3, SQLSRV_FETCH_ASSOC)) {
    $uploadedDocs[] = $row['Doc_Type_Name'];
}

$totalRequired = count($requiredDocs);
$totalUploaded = count($uploadedDocs);
$missing = array_diff($requiredDocs, $uploadedDocs);

$progressPercent = $totalRequired > 0 ? round(($totalUploaded / $totalRequired) * 100) : 0;

// Status display mapping
$statusColors = [
    "not_driver" => "#a1a1aa",
    "pending" => "#fbbf24",
    "submitted" => "#3b82f6",
    "approved" => "#4ade80",
    "denied" => "#ef4444"
];
?>

<!DOCTYPE html>
<html>
<head>
<title>OSRH Driver Dashboard</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
body {
    background: radial-gradient(circle at top left, #18181b, #020617 50%, #000000 90%);
    font-family: Inter, sans-serif;
    color: #e5e7eb;
    padding: 40px;
    display: flex;
    justify-content: center;
}

.container {
    background: rgba(9,9,11,0.96);
    padding: 40px;
    border-radius: 24px;
    width: 100%;
    max-width: 900px;
    box-shadow: 0 20px 50px rgba(0,0,0,0.6);
}

h1 {
    font-size: 30px;
    margin-bottom: 20px;
}

.section {
    background: #1c1c1f;
    padding: 20px;
    border-radius: 16px;
    margin-bottom: 25px;
}

.progress-bar {
    width: 100%;
    height: 18px;
    background: #2e2e32;
    border-radius: 999px;
    overflow: hidden;
    margin-top: 8px;
}
.progress-fill {
    height: 100%;
    width: <?= $progressPercent ?>%;
    background: #3b82f6;
    transition: 0.3s;
}

.btn {
    display: inline-block;
    background: #3f3f46;
    padding: 14px 20px;
    border-radius: 999px;
    text-decoration: none;
    color: white;
    margin-right: 10px;
}
.btn:hover { filter: brightness(1.1); }

.status-box {
    font-size: 18px;
    font-weight: 600;
    color: <?= $statusColors[$driverStatus] ?>;
}
.missing-doc {
    color: #ef4444;
}
.uploaded-doc {
    color: #4ade80;
}
</style>
</head>

<body>

<div class="container">

<h1>Driver Dashboard</h1>

<div class="section">
    <h2>Application Status</h2>
    <p class="status-box"><?= strtoupper($driverStatus) ?></p>

    <?php if ($driverStatus === "denied"): ?>
        <p style="color:#f87171;">Your application was denied. Please re-upload required documents.</p>
    <?php endif; ?>
</div>

<div class="section">
    <h2>Document Progress</h2>

    <p><?= $totalUploaded ?>/<?= $totalRequired ?> documents uploaded</p>

    <div class="progress-bar">
        <div class="progress-fill"></div>
    </div>

    <h3 style="margin-top:20px;">Uploaded Documents</h3>
    <?php foreach ($uploadedDocs as $doc): ?>
        <p class="uploaded-doc">✔ <?= htmlspecialchars($doc) ?></p>
    <?php endforeach; ?>

    <h3 style="margin-top:20px;">Missing Documents</h3>
    <?php foreach ($missing as $doc): ?>
        <p class="missing-doc">✖ <?= htmlspecialchars($doc) ?></p>
    <?php endforeach; ?>
</div>

<div class="section">
    <h2>Actions</h2>

    <a class="btn" href="become_driver.php">Upload Documents</a>

    <a class="btn" href="passenger_success.php" style="background:#3b82f6;">Home</a>

    <?php if ($driverStatus !== "submitted" && $driverStatus !== "approved" && empty($missing)): ?>
        <a class="btn" href="finalize_driver_application.php" style="background:#3b82f6;">
            Submit Application →
        </a>
    <?php endif; ?>

</div>

</div>
</body>
</html>
