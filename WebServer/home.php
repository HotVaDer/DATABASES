<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>OSRH — Ride Hailing</title>

<style>
    body {
        margin: 0;
        padding: 0;
        background: url('https://images.unsplash.com/photo-1493238792000-8113da705763?q=80&w=2070') 
            no-repeat center center fixed;
        background-size: cover;
        font-family: Arial, sans-serif;
        color: white;
    }

    .overlay {
        background: rgba(0,0,0,0.70);
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 20px;
    }

    .box {
        text-align: center;
        max-width: 650px;
        padding: 40px;
        background: rgba(20,20,20,0.9);
        border-radius: 16px;
        border: 1px solid #333;
    }

    h1 {
        font-size: 48px;
        margin-bottom: 10px;
        font-weight: bold;
        letter-spacing: 1px;
    }

    .subtitle {
        font-size: 20px;
        margin-bottom: 30px;
        color: #cccccc;
    }

    .btn-container {
        margin-top: 20px;
        display: flex;
        justify-content: center;
        gap: 20px;
        flex-wrap: wrap;
    }

    .btn {
        padding: 12px 25px;
        background: white;
        color: black;
        border-radius: 8px;
        font-size: 18px;
        text-decoration: none;
        font-weight: bold;
        min-width: 140px;
        transition: 0.2s;
    }

    .btn:hover {
        background: #ddd;
    }
</style>
</head>

<body>

<div class="overlay">

    <div class="box">
        <h1>OSRH</h1>
        <div class="subtitle">On-Site Ride Hailing Platform</div>
        <div class="subtitle">Fast • Reliable • Seamless</div>

        <div class="btn-container">
            <a class="btn" href="login.php">Login</a>
            <a class="btn" href="register.php">Register</a>
        </div>
    </div>

</div>

</body>
</html>
