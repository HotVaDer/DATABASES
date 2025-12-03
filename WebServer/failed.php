<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login Failed</title>

<style>
    body {
        margin: 0;
        padding: 0;
        font-family: Arial, sans-serif;
        color: white;
        background: url('https://images.pexels.com/photos/238226/pexels-photo-238226.jpeg?cs=srgb&dl=pexels-pascal-claivaz-66964-238226.jpg&fm=jpg')
            no-repeat center center fixed;
        background-size: cover;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
    }

    .overlay {
        background: rgba(0,0,0,0.75);
        width: 100%;
        height: 100%;
        position: absolute;
        top: 0;
        left: 0;
        z-index: 0;
    }

    .box {
        position: relative;
        z-index: 2;
        background: rgba(20,20,20,0.85);
        border: 1px solid #333;
        padding: 40px;
        width: 90%;
        max-width: 420px;
        border-radius: 14px;
        text-align: center;
        backdrop-filter: blur(6px);
        box-shadow: 0 0 20px rgba(0,0,0,0.4);
    }

    .box h1 {
        font-size: 32px;
        margin-bottom: 10px;
        color: #ff4d4d;
    }

    .box p {
        color: #cccccc;
        font-size: 18px;
        margin-bottom: 25px;
    }

    .btn {
        display: inline-block;
        padding: 10px 20px;
        background: white;
        color: black;
        text-decoration: none;
        font-weight: bold;
        border-radius: 6px;
    }

    .btn:hover {
        background: #dddddd;
    }
</style>

</head>
<body>

<div class="overlay"></div>

<div class="box">
    <h1>✘ Login Failed</h1>
    <p><?= htmlspecialchars($_GET['error'] ?? "Unknown error") ?></p>

    <a href="login.php" class="btn">Try Again</a>
</div>

</body>
</html>
