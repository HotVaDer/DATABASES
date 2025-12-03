<?php
session_start();

// SECURITY CHECK
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$userID = $_SESSION['user_id'] ?? null;
if (!$userID) {
    die("User session error.");
}

// If form submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $holder = trim($_POST['holder'] ?? '');
    $card   = trim($_POST['card'] ?? '');
    $expiry = trim($_POST['expiry'] ?? '');
    $type   = trim($_POST['type'] ?? '');

    if (!$holder || !$card || !$expiry || !$type) {
        $error = "All fields are required.";
    } else {
        // Keep ONLY last 4 digits
        $last4 = substr(preg_replace('/\D/', '', $card), -4);

        // CONNECT DB
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
            $error = "Database connection failed.";
        } else {

            $sql = "INSERT INTO USER_PAYMENT_METHOD
                    (Card_Holder_Name, Last_4_Digits, Card_Type, Expiry_Date, User_ID)
                    VALUES (?, ?, ?, ?, ?)";

            $params = [$holder, $last4, $type, $expiry, $userID];
            $stmt = sqlsrv_query($conn, $sql, $params);

            if ($stmt === false) {
                $error = "Failed to save payment.";
            } else {
                header("Location: payment_success.php");
                exit;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Add Payment Method</title>
<style>
    body {
        margin: 0;
        padding: 0;
        background: url('https://images.unsplash.com/photo-1503376780353-7e6692767b70?q=80&w=2065')
            no-repeat center center fixed;
        background-size: cover;
        font-family: Arial, sans-serif;
        color: white;
    }

    .overlay {
        background: rgba(0,0,0,0.78);
        min-height: 100vh;
        padding: 40px 0;
    }

    .box {
        background: rgba(20,20,20,0.9);
        width: 90%;
        max-width: 450px;
        margin: auto;
        padding: 25px;
        border-radius: 12px;
        border: 1px solid #333;
    }

    h1 {
        text-align: center;
        margin-bottom: 15px;
    }

    .error {
        background: rgba(255,0,0,0.2);
        padding: 10px;
        border-left: 4px solid red;
        margin-bottom: 15px;
        border-radius: 8px;
        text-align: center;
    }

    label { display: block; margin-top: 10px; }
    input, select {
        width: 100%;
        padding: 10px;
        margin-top: 5px;
        border-radius: 8px;
        border: none;
        background: #222;
        color: white;
    }

    .btn {
        margin-top: 20px;
        background: white;
        color: black;
        padding: 10px;
        text-align: center;
        display: block;
        border-radius: 8px;
        font-weight: bold;
        text-decoration: none;
        cursor: pointer;
    }

    .btn:hover {
        background: #ddd;
    }
</style>
</head>

<body>
<div class="overlay">

    <div class="box">
        <h1>Add Payment Method</h1>

        <?php if (!empty($error)) : ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
        <div class="error"><?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>

        <form method="POST">

            <label>Card Holder Name</label>
            <input type="text" name="holder" required>

            <label>Card Number</label>
            <input type="text" name="card" required>

            <label>Card Type</label>
            <select name="type" required>
                <option value="Visa">Visa</option>
                <option value="Mastercard">Mastercard</option>
                <option value="Amex">American Express</option>
            </select>

            <label>Expiry Date</label>
            <input type="date" name="expiry" required>

            <button class="btn">Save Payment Method</button>

        </form>

        <a href="passenger_success.php" class="btn" style="margin-top:10px;background:#444;color:white;">Cancel</a>
    </div>

</div>
</body>
</html>
