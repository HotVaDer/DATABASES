<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>OSRH — Ride Hailing Platform</title>

<style>
/* GLOBAL */
body {
    margin: 0;
    padding: 0;
    font-family: Arial, sans-serif;
    background: #000;
    color: white;
    overflow-x: hidden;
}

section {
    padding: 80px 20px;
}

/* HERO SECTION */
.hero {
    height: 100vh;
    background: 
        linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)),
        url('https://images.unsplash.com/photo-1493238792000-8113da705763?q=80&w=2070')
        center/cover no-repeat;
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;
}

.hero h1 {
    font-size: 70px;
    margin-bottom: 10px;
    letter-spacing: 2px;
    font-weight: bold;
}

.hero p {
    font-size: 22px;
    margin-bottom: 30px;
    color: #e0e0e0;
}

.btn {
    display: inline-block;
    padding: 14px 30px;
    background: white;
    color: black;
    font-weight: bold;
    border-radius: 10px;
    text-decoration: none;
    font-size: 20px;
    margin: 10px;
    transition: 0.2s;
}

.btn:hover {
    background: #ddd;
}

/* HOW IT WORKS */
.how-container {
    max-width: 1100px;
    margin: auto;
    text-align: center;
}

.how-steps {
    display: flex;
    justify-content: center;
    gap: 40px;
    margin-top: 40px;
    flex-wrap: wrap;
}

.step {
    width: 300px;
    background: rgba(30,30,30,0.85);
    padding: 30px;
    border-radius: 14px;
    border: 1px solid #333;
    transition: 0.3s;
}

.step:hover {
    transform: translateY(-5px);
    background: rgba(50,50,50,0.85);
}

.step h3 {
    margin-top: 15px;
    font-size: 24px;
}

.step p {
    color: #cfcfcf;
}

/* WHY OSRH */
.why-container {
    max-width: 1100px;
    margin: auto;
}

.why-grid {
    display: flex;
    gap: 40px;
    flex-wrap: wrap;
    margin-top: 40px;
}

.why-card {
    background: rgba(20,20,20,0.85);
    border-radius: 14px;
    padding: 30px;
    flex: 1;
    min-width: 280px;
    border: 1px solid #333;
    transition: 0.3s;
}

.why-card:hover {
    transform: translateY(-5px);
    background: rgba(40,40,40,0.85);
}

.why-card h3 {
    margin-bottom: 10px;
    font-size: 24px;
}

.why-card p {
    color: #d0d0d0;
}

/* FOOTER */
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

<!-- HERO SECTION -->
<section class="hero">
    <div>
        <h1>OSRH</h1>
        <p>Your trusted ride-hailing platform — fast, safe, reliable.</p>

        <a href="login.php" class="btn">Login</a>
        <a href="register.php" class="btn">Register</a>
    </div>
</section>

<!-- HOW IT WORKS -->
<section>
    <div class="how-container">
        <h2 style="text-align:center; font-size:36px;">How OSRH Works</h2>
        <div class="how-steps">

            <div class="step">
                <img src="https://img.icons8.com/ios-filled/100/ffffff/street-view.png"/>
                <h3>1. Select Your Route</h3>
                <p>Pick your start and destination on the map easily.</p>
            </div>

            <div class="step">
                <img src="https://img.icons8.com/ios-filled/100/ffffff/taxi.png"/>
                <h3>2. Request a Ride</h3>
                <p>Your trip enters the driver queue instantly.</p>
            </div>

            <div class="step">
                <img src="https://img.icons8.com/ios-filled/100/ffffff/steering-wheel.png"/>
                <h3>3. Get Matched</h3>
                <p>A nearby verified driver accepts your request.</p>
            </div>

        </div>
    </div>
</section>

<!-- WHY OSRH -->
<section>
    <div class="why-container">
        <h2 style="text-align:center; font-size:36px;">Why Choose OSRH?</h2>

        <div class="why-grid">

            <div class="why-card">
                <h3>✔ Fast Response</h3>
                <p>Our system matches passengers and drivers in seconds.</p>
            </div>

            <div class="why-card">
                <h3>✔ Safety First</h3>
                <p>All drivers are verified with documents and live tracking.</p>
            </div>

            <div class="why-card">
                <h3>✔ Transparent Pricing</h3>
                <p>No hidden fees — all prices are shown before you ride.</p>
            </div>

        </div>
    </div>
</section>

<!-- FOOTER -->
<footer>
    OSRH © 2025 — All Rights Reserved  
    <br>
    <a href="about.php">About</a> |
    <a href="contact.php">Contact</a> |
    <a href="terms.php">Terms</a> |
    <a href="privacy.php">Privacy</a>
</footer>

</body>
</html>
