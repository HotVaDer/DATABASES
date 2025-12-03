<?php session_start(); ?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>OSRH Login</title>

<style>
    body {
        margin: 0;
        padding: 0;
        font-family: Arial, sans-serif;
        background: url('https://images.unsplash.com/photo-1503376780353-7e6692767b70?q=80&w=2065') no-repeat center center fixed;
        background-size: cover;

        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .login-box {
        background: rgba(20,20,20,0.9);
        padding: 40px 35px;
        border-radius: 14px;
        width: 360px;
        border: 1px solid #333;
        box-shadow: 0px 0px 20px rgba(255,255,255,0.05);
        text-align: center;
        color: #fff;
        backdrop-filter: blur(4px);
    }

    .title { font-size: 28px; font-weight: bold; margin-bottom: 10px; }
    .subtitle { color: #cccccc; margin-bottom: 20px; }

    .field-group { text-align: left; margin-bottom: 16px; }
    .field-group label { display: block; margin-bottom: 4px; color: #dddddd; }
    .field-group input {
        width: 100%; padding: 10px 12px;
        background: #111; border: 1px solid #444;
        border-radius: 8px; color: white;
        font-size: 16px;
    }

    .btn {
        width: 100%; padding: 12px;
        background: white; color: black;
        font-weight: bold; border: none;
        border-radius: 8px; cursor: pointer;
        margin-top: 10px;
    }

    .btn:hover { background: #e6e6e6; }

    .error {
        background: rgba(255,0,0,0.2);
        padding: 10px;
        border-radius: 8px;
        color: #ffb3b3;
        margin-bottom: 15px;
        font-size: 14px;
    }
</style>
</head>

<body>

<div class="login-box">

    <div class="title">Welcome Back</div>
    <div class="subtitle">Sign in to continue</div>

    <?php if (isset($_SESSION['login_error'])): ?>
        <div class="error"><?= htmlspecialchars($_SESSION['login_error']); ?></div>
        <?php unset($_SESSION['login_error']); ?>
    <?php endif; ?>

    <form action="login_process.php" method="POST">

        <div class="field-group">
            <label for="username">Username</label>
            <input type="text" name="username" required>
        </div>

        <div class="field-group">
            <label for="password">Password</label>
            <input type="password" name="password" required>
        </div>

        <button class="btn" type="submit">Login</button>
    </form>
</div>

</body>
</html>
