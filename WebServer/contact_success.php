<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Message Sent</title>

<style>
body {
    background: url('https://images.unsplash.com/photo-1518684079-3c830dcef090?q=80&w=2070')
        center/cover no-repeat fixed;
    font-family: Arial, sans-serif;
    color: white;
    margin: 0;
    padding: 0;
}

.overlay {
    background: rgba(0,0,0,0.78);
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
}

.box {
    background: rgba(20,20,20,0.92);
    padding: 40px;
    border-radius: 14px;
    text-align: center;
    max-width: 500px;
    border: 1px solid #333;
}

h1 {
    font-size: 32px;
    margin-bottom: 20px;
}

.btn {
    display: inline-block;
    padding: 12px 25px;
    background: white;
    color: black;
    font-weight: bold;
    border-radius: 10px;
    text-decoration: none;
    margin-top: 25px;
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
        <h1>✔ Message Sent</h1>
        <p>Thank you for contacting OSRH.  
        Our support team will review your message shortly.</p>

        <a class="btn" href="home.php">Return Home</a>
    </div>
</div>

</body>
</html>
