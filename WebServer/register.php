<?php
// Prevent back button cached form
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8" />
<title>OSRH – Create Account</title>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
        font-family: "Inter", sans-serif;
        background: radial-gradient(circle at top left, #18181b, #020617 50%, #000000 90%);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #e5e7eb;
    }

    .page-wrapper { width: 100%; max-width: 500px; padding: 24px; }

    .card {
        background: rgba(9, 9, 11, 0.96);
        border-radius: 20px;
        padding: 36px;
        box-shadow: 0 24px 60px rgba(0,0,0,0.7), 0 0 0 1px rgba(63,63,70,0.6);
    }

    .title { font-size: 26px; font-weight: 600; margin-bottom: 6px; }
    .subtitle { color: #a1a1aa; font-size: 13px; margin-bottom: 24px; }

    .field { margin-bottom: 18px; }
    .field label { display: block; font-size: 13px; font-weight: 500; margin-bottom: 6px; }

    .input, select {
        width: 100%;
        border-radius: 999px;
        border: 1px solid rgba(82,82,91,0.8);
        background: rgba(24,24,27,0.98);
        padding: 10px 14px;
        color: #e5e7eb;
        font-size: 14px;
        outline: none;
        transition: 0.2s;
    }

    .input:focus { border-color: #71717a; background: #18181b; }

    .btn-primary {
        width: 100%;
        margin-top: 10px;
        padding: 12px;
        background: linear-gradient(135deg, #3f3f46, #18181b);
        border: none;
        border-radius: 999px;
        color: white;
        font-size: 15px;
        font-weight: 500;
        cursor: pointer;
        transition: 0.15s;
    }

    .btn-primary:hover { filter: brightness(1.05); }
    .btn-primary:disabled { opacity: 0.4; cursor: not-allowed; }

    .helper-message { font-size: 12px; margin-top: 4px; min-height: 16px; color: #f87171; }
</style>

</head>

<body>

<div class="page-wrapper">
    <div class="card">

        <div class="title">Create your account</div>
        <div class="subtitle">Join OSRH and start booking rides instantly.</div>

        <form action="register_process.php" method="POST" id="register-form" novalidate>

            <!-- FIRST NAME -->
            <div class="field">
                <label>First Name *</label>
                <input type="text" class="input" name="first" required />
            </div>

            <!-- LAST NAME -->
            <div class="field">
                <label>Last Name *</label>
                <input type="text" class="input" name="last" required />
            </div>

            <!-- BIRTHDATE -->
            <div class="field">
                <label>Birth Date *</label>
                <input type="date" class="input" name="birthdate" required />
            </div>

            <!-- GENDER -->
            <div class="field">
                <label>Gender *</label>
                <select class="input" name="gender" required>
                    <option value="" disabled selected>Select gender</option>
                    <option>Female</option>
                    <option>Male</option>
                    <option>Other</option>
                    <option>Prefer not to say</option>
                </select>
            </div>

            <!-- EMAIL -->
            <div class="field">
                <label>Email *</label>
                <input type="email" class="input" name="email" id="email" required />
                <div id="email-msg" class="helper-message"></div>
            </div>

            <!-- ADDRESS -->
            <div class="field">
                <label>Address *</label>
                <input type="text" class="input" name="address" required />
            </div>

            <!-- PASSWORD -->
            <div class="field">
                <label>Password *</label>
                <input type="password" class="input" name="password" id="password" minlength="6" required />
                <div id="password-msg" class="helper-message"></div>
            </div>

            <!-- CONFIRM PASSWORD -->
            <div class="field">
                <label>Confirm Password *</label>
                <input type="password" class="input" name="confirm_password" id="confirm_password" minlength="6" required />
                <div id="confirm-msg" class="helper-message"></div>
            </div>

            <button class="btn-primary" id="submit-btn" type="submit" disabled>
                Create Account →
            </button>

        </form>
    </div>
</div>

<script>
const allowedDomains = ["gmail.com","ucy.ac.cy","hotmail.com","outlook.com","yahoo.com"];

function validateEmailDomain(email) {
    const parts = email.split("@");
    return parts.length === 2 && allowedDomains.includes(parts[1].toLowerCase());
}

function validateForm() {
    let ok = true;

    const email = document.getElementById("email");
    const pass = document.getElementById("password");
    const confirm = document.getElementById("confirm_password");

    // Email domain check
    if (!validateEmailDomain(email.value)) {
        document.getElementById("email-msg").innerText = "Invalid or unsupported email domain";
        ok = false;
    } else {
        document.getElementById("email-msg").innerText = "";
    }

    // Password match check
    if (pass.value !== confirm.value) {
        document.getElementById("confirm-msg").innerText = "Passwords do not match";
        ok = false;
    } else {
        document.getElementById("confirm-msg").innerText = "";
    }

    // Basic empty field validation
    document.querySelectorAll("input, select").forEach(el => {
        if (el.value.trim() === "") ok = false;
    });

    document.getElementById("submit-btn").disabled = !ok;
}

document.querySelectorAll("input, select").forEach(el => {
    el.addEventListener("input", validateForm);
    el.addEventListener("change", validateForm);
});
</script>

</body>
</html>
