<?php
session_start();

if ($_SESSION['type_name'] !== "driver") {
    die("ACCESS DENIED");
}

$driverID = $_SESSION['user_id'];

// DB
$server = "127.0.0.1";
$conn = sqlsrv_connect($server, [
    "Database" => "OSRH",
    "Uid" => "sa",
    "PWD" => "MyStrongPass123!",
    "Encrypt" => "no",
    "TrustServerCertificate" => "yes"
]);

if (!$conn) die("DB ERROR");

// FETCH EXISTING VEHICLES
$sql = "SELECT * FROM VEHICLE WHERE User_ID = ?";
$stmt = sqlsrv_query($conn, $sql, [$driverID]);

?>
<!DOCTYPE html>
<html>
<head>
<title>Add Vehicle</title>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">

<style>
body {
    background: radial-gradient(circle at top left, #18181b, #020617 50%, #000);
    font-family: Inter, sans-serif;
    color: #e5e7eb;
    padding: 30px;
}

.container {
    max-width: 900px;
    margin: auto;
}

.section-title {
    font-size: 26px;
    font-weight: 600;
    margin-bottom: 12px;
}

.card {
    background: #0f0f11;
    padding: 30px;
    border-radius: 20px;
    box-shadow: 0 18px 40px rgba(0,0,0,0.6);
    margin-bottom: 40px;
}

/* FORM */
input, select {
    width: 100%;
    padding: 10px 14px;
    margin-bottom: 16px;
    border-radius: 10px;
    border: 1px solid #333;
    background: #18181b;
    color: white;
}

button {
    width: 100%;
    padding: 12px;
    background: #2563eb;
    border: none;
    border-radius: 10px;
    color: white;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
}
button:hover { background: #1d4ed8; }

/* VEHICLE TABLE */
.table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
}

.table th {
    background: #1d1d20;
    padding: 12px;
    font-size: 14px;
    text-transform: uppercase;
    border-bottom: 1px solid #333;
    color: #a1a1aa;
}

.table td {
    padding: 12px;
    border-bottom: 1px solid #2a2a2d;
}

.table tr:hover td {
    background: #18181b;
}

.no-vehicles {
    font-style: italic;
    color: #9ca3af;
    padding: 10px 0;
}
</style>
</head>

<body>

<div class="container">

    <!-- ============================= -->
    <!-- EXISTING VEHICLES TABLE      -->
    <!-- ============================= -->

    <div class="card">
        <div class="section-title">🚗 Your Registered Vehicles</div>

        <?php if (sqlsrv_has_rows($stmt)) : ?>
            <table class="table">
                <tr>
                    <th>License Plate</th>
                    <th>Seats</th>
                    <th>Type</th>
                    <th>Price (€)</th>
                    <th>Region</th>
                </tr>

                <?php while ($v = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) : ?>
                <tr>
                    <td><?= htmlspecialchars($v['License_Plate']) ?></td>
                    <td><?= $v['Seat_Capacity'] ?></td>
                    <td><?= htmlspecialchars($v['Vehicle_Type']) ?></td>
                    <td><?= $v['Price_To_Ride'] ?></td>
                    <td><?= $v['Region_ID'] ?></td>
                </tr>
                <?php endwhile; ?>

            </table>

        <?php else: ?>
            <p class="no-vehicles">You have not added any vehicles yet.</p>
        <?php endif; ?>
    </div>


    <!-- ============================= -->
    <!-- ADD NEW VEHICLE FORM        -->
    <!-- ============================= -->

    <div class="card">
        <div class="section-title">➕ Add a New Vehicle</div>

        <form action="add_vehicle_process.php" method="POST">

            <label>License Plate *</label>
            <input type="text" name="license_plate" required>

            <label>Seat Capacity *</label>
            <input type="number" name="seat_capacity" required>

            <label>Trunk Space (m³) *</label>
            <input type="number" step="0.1" name="trunk_space" required>

            <label>Trunk Weight (kg) *</label>
            <input type="number" step="0.1" name="trunk_weight" required>

            <label>Vehicle Type *</label>
            <select name="vehicle_type" required>
                <option value="" disabled selected>Select</option>
                <option value="Sedan">Sedan</option>
                <option value="SUV">SUV</option>
                <option value="Van">Van</option>
                <option value="Luxury">Luxury</option>
            </select>

            <label>Price to Ride (€) *</label>
            <input type="number" step="0.1" name="price" required>

            <label>Region *</label>
            <select name="region_id" required>
                <option value="1">Nicosia</option>
                <option value="2">Limassol</option>
                <option value="3">Larnaca</option>
                <option value="4">Paphos</option>
            </select>

            <button type="submit">Add Vehicle</button>
        </form>
    </div>

</div>

</body>
</html>
