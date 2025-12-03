<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login Failed</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

<div class="box error-box">
    <h1>✘ Login Failed</h1>
    <p><?= htmlspecialchars($_GET['error']) ?></p>

    <a href="login.php" class="btn danger">Try Again</a>
</div>

</body>
</html>
