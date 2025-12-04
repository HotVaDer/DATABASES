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

// Update driver status → submitted
$sql = "UPDATE DRIVER SET Status = 'submitted' WHERE User_ID = ?";
$stmt = sqlsrv_query($conn, $sql, [$user_id]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Application Submitted</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
body {
    background: radial-gradient(circle at top left, #18181b, #020617 50%, #000000 90%);
    color: #e5e7eb;
    font-family: "Inter", sans-serif;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
    padding: 20px;
}

.card {
    background: rgba(9,9,11,0.96);
    padding: 40px;
    width: 100%;
    max-width: 520px;
    border-radius: 20px;
    text-align: center;
    box-shadow: 0 24px 60px rgba(0,0,0,0.7), 0 0 0 1px rgba(63,63,70,0.5);
}

.success-icon {
    font-size: 60px;
    margin-bottom: 20px;
    color: #4ade80;
    animation: pulse 1.6s infinite ease-in-out;
}

@keyframes pulse {
    0% { opacity: 0.6; transform: scale(0.95); }
    50% { opacity: 1; transform: scale(1.05); }
    100% { opacity: 0.6; transform: scale(0.95); }
}

.title {
    font-size: 26px;
    font-weight: 600;
    margin-bottom: 12px;
}

.subtitle {
    font-size: 14px;
    color: #a1a1aa;
    margin-bottom: 30px;
}

.btn {
    display: inline-block;
    padding: 14px 22px;
    background: linear-gradient(135deg, #3f3f46, #18181b);
    border-radius: 999px;
    color: white;
    text-decoration: none;
    font-size: 15px;
    font-weight: 500;
    transition: 0.2s;
}

.btn:hover {
    filter: brightness(1.1);
}
</style>
</head>

<body>

<div class="card">

<?php if ($stmt): ?>
    <div class="success-icon">✔</div>
    <div class="title">Application Submitted</div>
    <div class="subtitle">
        Your driver application has been successfully submitted.<br>
        Our team will review your documents shortly.
    </div>
<?php else: ?>
    <div class="success-icon" style="color:#f87171;">✖</div>
    <div class="title">Submission Failed</div>
    <div class="subtitle">Something went wrong. Please try again later.</div>
<?php endif; ?>

<a href="dashboard.php" class="btn">Return to Dashboard →</a>

</div>

</body>
</html>
