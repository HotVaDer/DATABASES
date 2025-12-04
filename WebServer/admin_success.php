<?php
session_start();

if (empty($_SESSION['logged_in']) || $_SESSION['type_name'] !== "admin") {
    die("ACCESS DENIED");
}

$server = "127.0.0.1";
$connectionOptions = [
    "Database" => "OSRH",
    "Uid" => "sa",
    "PWD" => "MyStrongPass123!",
    "Encrypt" => "no",
    "TrustServerCertificate" => "yes"
];

$conn = sqlsrv_connect($server, $connectionOptions);
if (!$conn) die("DB ERROR");

$sql = "
SELECT 
    U.User_ID,
    U.First_Name,
    U.Last_Name,
    D.Status AS DriverStatus,
    DD.Driver_Document_ID,
    DD.Doc_Type_Name,
    DD.Issue_Date,
    DD.Expiry_Date,
    DD.Uploaded_At,
    DD.Status AS DocumentStatus
FROM DRIVER_DOCUMENT DD
JOIN DRIVER D ON D.User_ID = DD.User_ID
JOIN [USER] U ON U.User_ID = DD.User_ID
ORDER BY U.User_ID, DD.Doc_Type_Name;
";

$stmt = sqlsrv_query($conn, $sql);
?>
<!DOCTYPE html>
<html>
<head>
<title>Admin Dashboard – Driver Documents</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
body {
    background: radial-gradient(circle at top left, #18181b, #020617 50%, #000000 90%);
    color: #e5e7eb;
    font-family: Inter, sans-serif;
    padding: 40px;
}

.container {
    max-width: 1200px;
    margin: auto;
    background: rgba(10,10,12,0.9);
    padding: 40px;
    border-radius: 22px;
    box-shadow: 0 20px 50px rgba(0,0,0,0.6);
}

/* HEADER */
h1 {
    text-align: center;
    font-size: 32px;
    margin-bottom: 30px;
    font-weight: 600;
}

/* TABLE */
.table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 25px;
    border-radius: 10px;
    overflow: hidden;
}

.table th {
    background: #242427;
    padding: 15px;
    font-size: 15px;
    text-transform: uppercase;
    color: #9ca3af;
}

.table td {
    padding: 15px;
    background: #1c1c1f;
    border-bottom: 1px solid #2a2a2d;
    vertical-align: middle;
}

.table tr:hover td {
    background: #222225;
}

/* STATUS COLORS */
.status-pending  { color: #fbbf24; font-weight: 600; }
.status-approved { color: #4ade80; font-weight: 600; }
.status-denied   { color: #ef4444; font-weight: 600; }

/* BUTTONS */
.btn {
    display: inline-block;
    padding: 12px 18px;
    margin: 4px 2px;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 500;
    text-decoration: none;
    color: white;
    transition: 0.2s;
}

.btn-view { background: #3b82f6; }
.btn-view:hover { background: #2563eb; }

.btn-approve { background: #22c55e; }
.btn-approve:hover { background: #16a34a; }

.btn-deny { background: #ef4444; }
.btn-deny:hover { background: #dc2626; }

/* Action button container to avoid overlapping */
.action-buttons {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}
footer {
    background: #111;
    padding: 20px;
    text-align: center;
    color: #777;
    font-size: 14px;
    margin-top: 60px;
}

footer a {
    color: #999;
    margin: 0 10px;
    text-decoration: none;
}

footer a:hover {
    color: white;
}
</style>
</head>

<body>

<div class="container">

<h1>Admin Dashboard – Driver Documents</h1>

<table class="table">
<tr>
    <th>User</th>
    <th>Driver Status</th>
    <th>Document</th>
    <th>Issue</th>
    <th>Expiry</th>
    <th>Uploaded</th>
    <th>Doc Status</th>
    <th>Actions</th>
</tr>

<?php while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)): ?>
<tr>
    <td><?= htmlspecialchars($row['First_Name'] . " " . $row['Last_Name']) ?> (ID: <?= $row['User_ID'] ?>)</td>
    <td><?= htmlspecialchars($row['DriverStatus']) ?></td>
    <td><?= htmlspecialchars($row['Doc_Type_Name']) ?></td>
    <td><?= $row['Issue_Date'] ? $row['Issue_Date']->format("Y-m-d") : "" ?></td>
    <td><?= $row['Expiry_Date'] ? $row['Expiry_Date']->format("Y-m-d") : "" ?></td>
    <td><?= $row['Uploaded_At'] ? $row['Uploaded_At']->format("Y-m-d H:i") : "" ?></td>

    <td class="<?=
        $row['DocumentStatus'] === 'pending' ? 'status-pending' :
        ($row['DocumentStatus'] === 'approved' ? 'status-approved' : 'status-denied')
    ?>">
        <?= htmlspecialchars($row['DocumentStatus']) ?>
    </td>

    <td>
        <div class="action-buttons">
            <a class="btn btn-view" href="view_document.php?id=<?= $row['Driver_Document_ID'] ?>">View</a>
            <a class="btn btn-approve" href="approve_document.php?id=<?= $row['Driver_Document_ID'] ?>">Approve</a>
            <a class="btn btn-deny" href="deny_document.php?id=<?= $row['Driver_Document_ID'] ?>">Deny</a>
        </div>
    </td>
</tr>
<?php endwhile; ?>

</table>

</div>
</body>
<footer>
    OSRH © 2025 — All Rights Reserved
    <br>
    <a href="home.php">Home</a> |
    <a href="about.php">About</a> |
    <a href="contact.php">Contact</a> |
    <a href="terms.php">Terms</a> |
    <a href="privacy.php">Privacy</a>
</footer>
</html>
