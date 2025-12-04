<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>About OSRH</title>

<style>
body {
    margin: 0;
    background: #000;
    color: white;
    font-family: Arial, sans-serif;
}

.header {
    background: #111;
    padding: 40px;
    text-align: center;
}

.header h1 {
    font-size: 42px;
    margin: 0;
}

.content {
    max-width: 950px;
    margin: 40px auto;
    padding: 30px;
    background: rgba(20,20,20,0.9);
    border-radius: 14px;
    border: 1px solid #333;
}

.content p {
    line-height: 1.7;
    color: #ccc;
    margin-bottom: 20px;
}

.about-grid {
    display: flex;
    gap: 40px;
    flex-wrap: wrap;
    margin-top: 30px;
}

.box {
    flex: 1;
    min-width: 280px;
    background: rgba(30,30,30,0.9);
    padding: 25px;
    border-radius: 14px;
    border: 1px solid #333;
}

.box h3 {
    margin-top: 0;
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

<div class="header">
    <h1>About OSRH</h1>
</div>

<div class="content">

    <p>OSRH (On-Site Ride Hailing) is a modern transportation platform designed to provide fast, seamless, and safe ride services to passengers and drivers across Cyprus.</p>

    <p>Developed as a next-generation mobility system, OSRH integrates live geolocation, service catalogues, driver-verification workflows, and secure payment architecture.</p>

    <div class="about-grid">
        <div class="box">
            <h3> Our Mission</h3>
            <p>To redefine local transportation with simplicity, transparency, and speed.</p>
        </div>

        <div class="box">
            <h3> Why OSRH?</h3>
            <p>We provide real-time ride matching, verified drivers, and a premium customer experience.</p>
        </div>

        <div class="box">
            <h3> Safety First</h3>
            <p>All drivers undergo identity verification and vehicle document checks to ensure passenger safety.</p>
        </div>
    </div>

</div>

<footer>
    OSRH © 2025 — All Rights Reserved
    <br>
    <a href="home.php">Home</a> |
    <a href="about.php">About</a> |
    <a href="contact.php">Contact</a> |
    <a href="terms.php">Terms</a> |
    <a href="privacy.php">Privacy</a>
</footer>

</body>
</html>
