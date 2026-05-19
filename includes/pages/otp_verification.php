<?php /* OTP Verification Page */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification — CleckBasket</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
        }
        
        body {
            background-color: #f2f4f2; /* off-white background */
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        /* The angled green shape at the top */
        .bg-shape {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 45vh;
            background-color: #d5eed7; /* light green from pic */
        clip-path: polygon(0 0, 100% 0, 100% 70%, 50% 100%, 0 70%);
            z-index: -1;
        }

        .otp-card {
            background: transparent;
            width: 100%;
            max-width: 420px;
            text-align: center;
            padding: 40px 20px;
            z-index: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .illustration {
            width: 120px;
            height: 120px;
            border-radius: 18px;
            object-fit: cover;
            margin: 0 auto 24px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
            background: #fff;
            padding: 8px;
        }

        h2 {
            font-size: 20px;
            color: #111;
            margin-bottom: 12px;
            font-weight: 700;
        }

        p.subtitle {
            font-size: 13px;
            color: #666;
            margin-bottom: 32px;
            max-width: 250px;
            line-height: 1.4;
        }

        .otp-inputs {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-bottom: 32px;
        }

        .otp-input {
            width: 46px;
            height: 52px;
            border: none;
            background-color: #e2e2e2;
            border-radius: 8px;
            font-size: 22px;
            text-align: center;
            font-weight: 600;
            color: #333;
            transition: all 0.2s ease;
            outline: none;
        }

        .otp-input:focus {
            background-color: #fff;
            box-shadow: 0 0 0 2px #6a9b6c;
        }

        .verify-btn {
            background-color: #6a9b6c; /* green matching pic */
            color: #fff;
            border: none;
            width: 140px;
            padding: 12px 0;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s ease;
            margin-bottom: 24px;
        }

        .verify-btn:hover {
            background-color: #558555;
        }

        .resend-text {
            font-size: 12px;
            color: #777;
        }

        .resend-link {
            color: #778d9c; /* bluish grey from pic */
            text-decoration: none;
            font-weight: 500;
        }

        .resend-link:hover {
            text-decoration: underline;
        }

        .error-msg {
            color: #d32f2f;
            font-size: 13px;
            margin-top: -20px;
            margin-bottom: 20px;
            display: none;
            font-weight: 500;
        }
    </style>
</head>
<body>

    <div class="bg-shape"></div>

    <div class="otp-card">
        <img src="/cleckbasket/assets/images/otp_illustration.png" alt="Verify Account Illustration" class="illustration">
        
        <h2>Verify Your Account</h2>
        <p class="subtitle">Enter the OTP sent to your account via your email</p>

        <div class="error-msg" id="errorMsg">Invalid OTP. Please try again.</div>

        <!-- 6 inputs as per screenshot layout -->
        <div class="otp-inputs" id="otpContainer">
            <input type="text" maxlength="1" class="otp-input" autofocus>
            <input type="text" maxlength="1" class="otp-input">
            <input type="text" maxlength="1" class="otp-input">
            <input type="text" maxlength="1" class="otp-input">
            <input type="text" maxlength="1" class="otp-input">
            <input type="text" maxlength="1" class="otp-input">
        </div>

        <button class="verify-btn" id="verifyBtn">Verify</button>

        <p class="resend-text">Didn't receive OTP? <a href="#" class="resend-link" id="resendLink">Resend OTP</a></p>
    </div>

    <script>
        const inputs = document.querySelectorAll('.otp-input');
        const verifyBtn = document.getElementById('verifyBtn');
        const errorMsg = document.getElementById('errorMsg');
        const resendLink = document.getElementById('resendLink');

        // Handle OTP input navigation
        inputs.forEach((input, index) => {
            input.addEventListener('input', (e) => {
                const val = e.target.value;
                if (val.length === 1 && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            });

            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !e.target.value && index > 0) {
                    inputs[index - 1].focus();
                }
            });
        });

        // Verification logic
        resendLink.addEventListener('click', (e) => {
            e.preventDefault();
            const email = localStorage.getItem('user_email') || '';
            if (!email) {
                errorMsg.textContent = 'Cannot resend: email not found. Please register again.';
                errorMsg.style.display = 'block';
                return;
            }
            resendLink.textContent = 'Sending…';
            resendLink.style.pointerEvents = 'none';
            fetch('/cleckbasket/backend/resend_otp.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email })
            })
            .then(r => r.json())
            .then(data => {
                errorMsg.style.color = data.success ? '#2e7d32' : '#d32f2f';
                errorMsg.textContent = data.message;
                errorMsg.style.display = 'block';
                // Re-enable after 30 s cooldown
                setTimeout(() => {
                    resendLink.textContent = 'Resend OTP';
                    resendLink.style.pointerEvents = '';
                    errorMsg.style.color = '#d32f2f';
                }, 30000);
            })
            .catch(() => {
                resendLink.textContent = 'Resend OTP';
                resendLink.style.pointerEvents = '';
            });
        });

        verifyBtn.addEventListener('click', () => {
            let otp = '';
            inputs.forEach(input => {
                otp += input.value;
            });

            if (otp.length < 6) {
                errorMsg.textContent = 'Please enter the full 6-character code.';
                errorMsg.style.display = 'block';
                return;
            }

            const email = localStorage.getItem('user_email') || '';

            fetch('/cleckbasket/backend/verify_otp.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email: email, otp: otp })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    errorMsg.style.display = 'none';
                    alert('Account verified successfully!');
                    localStorage.setItem('is_login', 'true');
                    window.location.href = data.redirect || '/cleckbasket/includes/pages/homepage.php';
                } else {
                    errorMsg.textContent = data.message || 'Invalid OTP. Please try again.';
                    errorMsg.style.display = 'block';
                    const container = document.getElementById('otpContainer');
                    container.animate([
                        { transform: 'translateX(-5px)' },
                        { transform: 'translateX(5px)' },
                        { transform: 'translateX(-5px)' },
                        { transform: 'translateX(5px)' },
                        { transform: 'translateX(0)' }
                    ], { duration: 300 });
                }
            })
            .catch(() => {
                errorMsg.textContent = 'Verification failed. Please try again.';
                errorMsg.style.display = 'block';
            });
        });
    </script>
</body>
</html>
