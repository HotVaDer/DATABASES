<?php
// No-cache headers to prevent browser from restoring filled form on BACK
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");
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

        .page-wrapper {
            width: 100%;
            max-width: 500px;
            padding: 24px;
        }

        .card {
            background: rgba(9, 9, 11, 0.96);
            border-radius: 20px;
            padding: 36px;
            box-shadow: 0 24px 60px rgba(0,0,0,0.7), 0 0 0 1px rgba(63,63,70,0.6);
        }

        .title {
            font-size: 26px;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .subtitle {
            color: #a1a1aa;
            font-size: 13px;
            margin-bottom: 24px;
        }

        .field { margin-bottom: 18px; }
        .field label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 6px;
        }

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

        .input:focus {
            border-color: #71717a;
            background: #18181b;
        }

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

        .btn-primary:disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }

        .helper-message {
            font-size: 12px;
            margin-top: 4px;
            min-height: 16px;
        }
    </style>
</head>

<body>

<div class="page-wrapper">
    <div class="card">

        <div class="title">Create your account</div>
        <div class="subtitle">Join OSRH and start booking rides instantly.</div>

        <form action="register_process.php" method="POST" novalidate id="register-form">

            <!-- FIRST NAME -->
            <div class="field">
                <label>First Name *</label>
                <input type="text" class="input" name="first_name" required />
            </div>

            <!-- LAST NAME -->
            <div class="field">
                <label>Last Name *</label>
                <input type="text" class="input" name="last_name" required />
            </div>

            <!-- BIRTH DATE -->
            <div class="field">
                <label>Birth Date *</label>
                <input type="date" class="input" name="birth_date" required />
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
                <input type="email" class="input" id="email" name="email" required />
                <div id="email-msg" class="helper-message"></div>
            </div>

            <!-- ADDRESS -->
            <div class="field">
                <label>Address *</label>
                <input type="text" class="input" name="address" required />
            </div>

            <!-- USERNAME -->
            <div class="field">
                <label>Username *</label>
                <input type="text" class="input" id="username" name="username" required minlength="4" />
                <div id="username-msg" class="helper-message"></div>
            </div>

            <!-- PASSWORD -->
            <div class="field">
                <label>Password *</label>
                <input type="password" class="input" id="password" name="password" required minlength="6" />
                <div id="password-msg" class="helper-message"></div>
            </div>

            <!-- SUBMIT -->
            <button class="btn-primary" id="submit-btn" type="submit" disabled>
                Create Account →
            </button>

        </form>

    </div>
</div>

<!-- REAL-TIME VALIDATION SCRIPT -->
<script>
const allowedDomains = [
    "gmail.com",
    "ucy.ac.cy",
    "hotmail.com",
    "outlook.com",
    "yahoo.com"
];

function validateEmailDomain(email) {
    const parts = email.split("@");
    if (parts.length !== 2) return false;
    return allowedDomains.includes(parts[1].toLowerCase());
}

function checkAvailability(type, value, elementId) {
    if (value.length < 3) {
        document.getElementById(elementId).textContent = "";
        return;
    }

    fetch(`check_availability.php?type=${type}&value=${encodeURIComponent(value)}`)
        .then(res => res.json())
        .then(data => {
            const msg = document.getElementById(elementId);
            if (data.exists) {
                msg.textContent = type === "email"
                    ? "❌ Email is already in use."
                    : "❌ Username is already taken.";
                msg.style.color = "#ef4444";
            } else {
                msg.textContent = type === "email"
                    ? "✔ Email is available."
                    : "✔ Username is available.";
                msg.style.color = "#22c55e";
            }
            validateForm();
        });
}

function validateForm() {
    const firstName = document.querySelector("input[name='first_name']").value.trim();
    const lastName  = document.querySelector("input[name='last_name']").value.trim();
    const birthDate = document.querySelector("input[name='birth_date']").value.trim();
    const gender    = document.querySelector("select[name='gender']").value.trim();
    const email     = document.getElementById("email").value.trim();
    const username  = document.getElementById("username").value.trim();
    const password  = document.getElementById("password").value.trim();

    const emailMsg  = document.getElementById("email-msg").textContent;
    const userMsg   = document.getElementById("username-msg").textContent;
    const passMsg   = document.getElementById("password-msg").textContent;

    const allFilled = firstName && lastName && birthDate && gender && email && username && password;

    const emailValid =
        validateEmailDomain(email) &&
        !emailMsg.includes("❌");

    const usernameValid = !userMsg.includes("❌");

    const passwordValid = password.length >= 6 && !passMsg.includes("❌");

    const formValid = allFilled && emailValid && usernameValid && passwordValid;

    document.getElementById("submit-btn").disabled = !formValid;
}

// Listeners
document.getElementById("email").addEventListener("input", function () {
    const msg = document.getElementById("email-msg");

    if (!validateEmailDomain(this.value)) {
        msg.textContent = "❌ This email domain is not allowed.";
        msg.style.color = "#ef4444";
        validateForm();
        return;
    }

    checkAvailability("email", this.value, "email-msg");
});

document.getElementById("username").addEventListener("input", function () {
    checkAvailability("username", this.value, "username-msg");
});

document.getElementById("password").addEventListener("input", function () {
    const msg = document.getElementById("password-msg");

    if (this.value.length < 6) {
        msg.textContent = "❌ Password must be at least 6 characters.";
        msg.style.color = "#ef4444";
    } else {
        msg.textContent = "✔ Password length is OK.";
        msg.style.color = "#22c55e";
    }
    validateForm();
});

document.querySelectorAll("input, select").forEach(el => {
    el.addEventListener("input", validateForm);
    el.addEventListener("change", validateForm);
});

// Reset form when returning with BACK button (bfcache fix)
window.addEventListener("pageshow", function (event) {
    const form = document.getElementById("register-form");
    if (!form) return;

    form.reset();

    document.getElementById("email-msg").textContent = "";
    document.getElementById("username-msg").textContent = "";
    document.getElementById("password-msg").textContent = "";

    document.getElementById("submit-btn").disabled = true;
});
</script>

</body>
</html>
