<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

<div class="box success-box">
    <h1>✔ Login Successful</h1>
    <p>Welcome <strong><?= htmlspecialchars($_GET['fname']) ?></strong>!</p>
    <p>Your role: <strong><?= htmlspecialchars($_GET['type']) ?></strong></p>

    <a href="dashboard.php" class="btn">Go to Dashboard</a>
</div>

</body>
</html>
