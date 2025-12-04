<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Contact Us — OSRH</title>

<style>
body {
    margin: 0;
    padding: 0;
    font-family: Arial, sans-serif;
    background: #000;
    color: white;
}

.header {
    height: 340px;
    background: 
        linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)),
        url('https://thumbs.dreamstime.com/b/concept-shot-contact-us-board-car-contact-us-147744739.jpg')
        center/cover no-repeat;
    display: flex;
    justify-content: center;
    align-items: center;
}

.header h1 {
    font-size: 48px;
    letter-spacing: 1px;
    margin: 0;
}

.container {
    max-width: 1100px;
    margin: auto;
    padding: 40px 20px;
    display: flex;
    gap: 40px;
    flex-wrap: wrap;
}

/* LEFT INFO PANEL */
.info-box {
    flex: 1;
    min-width: 280px;
    background: rgba(20,20,20,0.9);
    padding: 30px;
    border-radius: 14px;
    border: 1px solid #333;
}

.info-box h2 {
    font-size: 26px;
    margin-bottom: 20px;
}

.info-box p {
    color: #ccc;
    margin-bottom: 10px;
    line-height: 1.5;
}

/* CONTACT FORM */
.form-box {
    flex: 1.5;
    min-width: 320px;
    background: rgba(25,25,25,0.9);
    padding: 30px;
    border-radius: 14px;
    border: 1px solid #333;
}

.form-box h2 {
    margin-bottom: 20px;
    font-size: 26px;
}

label {
    display: block;
    margin: 15px 0 5px;
}

input, textarea {
    width: 100%;
    padding: 12px;
    border-radius: 10px;
    border: none;
    background: #222;
    color: white;
    font-size: 16px;
}

textarea {
    height: 150px;
    resize: none;
}

.btn {
    margin-top: 20px;
    padding: 14px 30px;
    background: white;
    color: black;
    font-weight: bold;
    border-radius: 10px;
    border: none;
    font-size: 18px;
    cursor: pointer;
    transition: 0.2s;
}

.btn:hover {
    background: #e2e2e2;
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

<!-- HERO HEADER -->
<div class="header">
    <h1>Contact OSRH</h1>
</div>

<!-- MAIN CONTENT -->
<div class="container">

    <!-- LEFT INFO -->
    <div class="info-box">
        <h2>Get in Touch</h2>
        <p>If you have any questions about OSRH, feel free to reach out!</p>

        <p><strong>Email:</strong><br> support@osrh.com</p>
        <p><strong>Phone:</strong><br> +357 22 123456</p>
        <p><strong>Headquarters:</strong><br> Nicosia, Cyprus</p>
    </div>

    <!-- CONTACT FORM -->
    <div class="form-box">
        <h2>Send Us a Message</h2>

        <form action="contact_process.php" method="POST">

            <label>Name</label>
            <input type="text" name="name" required>

            <label>Email</label>
            <input type="email" name="email" required>

            <label>Message</label>
            <textarea name="message" required></textarea>

            <button class="btn">Send Message</button>

        </form>
    </div>

</div>

<!-- FOOTER -->
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
