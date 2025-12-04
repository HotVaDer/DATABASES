<?php
session_start();

if (empty($_SESSION['logged_in'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

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

// 1) Get required document types
$sql = "SELECT Doc_Type_Name FROM DRIVER_DOC_TYPE ORDER BY Doc_Type_Name";
$stmt = sqlsrv_query($conn, $sql);

$requiredDocs = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $requiredDocs[] = $row['Doc_Type_Name'];
}

// 2) Get uploaded docs
$sql2 = "SELECT Doc_Type_Name FROM DRIVER_DOCUMENT WHERE User_ID = ?";
$stmt2 = sqlsrv_query($conn, $sql2, [$user_id]);

$uploadedDocs = [];
while ($row = sqlsrv_fetch_array($stmt2, SQLSRV_FETCH_ASSOC)) {
    $uploadedDocs[] = $row['Doc_Type_Name'];
}

$missing = array_diff($requiredDocs, $uploadedDocs);
$allUploaded = count($missing) === 0;
?>

<!DOCTYPE html>
<html>
<head>
<title>Driver Documents</title>
<style>
body { background:#0f0f11; color:white; font-family:Inter; padding:40px; }
.container { max-width:800px; margin:auto; background:#18181b; padding:30px; border-radius:16px; }
.doc-box { background:#222225; padding:20px; border-radius:14px; margin-bottom:25px; }
button { padding:10px 20px; border-radius:10px; border:none; cursor:pointer; }
button:disabled { opacity:0.4; cursor:not-allowed; }
.good { color:#4ade80; }
.bad { color:#f87171; }
</style>
</head>

<body>

<div class="container">
<h2>Upload Required Driver Documents</h2>

<?php foreach ($requiredDocs as $doc): ?>
    <div class="doc-box">
        <h3><?= htmlspecialchars($doc) ?></h3>

        <?php if (in_array($doc, $uploadedDocs)): ?>
            <p class="good">✔ Uploaded</p>
        <?php else: ?>
            <p class="bad">✖ Missing</p>

            <form action="become_driver_process.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="doc_type" value="<?= htmlspecialchars($doc) ?>">

                <label>Issue Date *</label>
                <input required type="date" name="issue_date">

                <label>Expiry Date *</label>
                <input required type="date" name="expiry_date">

                <label>File *</label>
                <input required type="file" name="driver_doc" accept=".png,.jpg,.jpeg,.pdf">

                <button type="submit">Upload <?= htmlspecialchars($doc) ?> →</button>
            </form>
        <?php endif; ?>
    </div>
<?php endforeach; ?>

<hr style="margin:30px 0; border-color:#333;">

<form action="finalize_driver_application.php" method="POST">
    <button 
        <?= $allUploaded ? "" : "disabled" ?> 
        style="background:#3b82f6; color:white; width:100%; padding:15px; font-size:18px;"
    >
        Submit Full Driver Application →
    </button>
</form>

</div>
</body>
</html>
