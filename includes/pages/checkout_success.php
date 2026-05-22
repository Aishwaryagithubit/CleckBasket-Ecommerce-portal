<?php
/**
 * Checkout Success — RFID Card Tap to View Invoice
 * After payment the customer taps their RFID card (A3 38 A7 13).
 * The page polls poll_card.php every 500 ms.
 * On a valid tap it redirects to invoice.php automatically.
 */
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Payment Successful — CleckBasket</title>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@600;700&display=swap" rel="stylesheet" />

    <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
        font-family: 'Manrope', sans-serif;
        background: #FFF8F5;
        color: #29170C;
        min-height: 100vh;
    }

    /* ── Page wrapper ────────────────────────────────────────── */
    .success-page {
        max-width: 520px;
        margin: 56px auto 80px;
        padding: 0 20px;
    }

    /* ── Top success badge ───────────────────────────────────── */
    .success-badge {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
        margin-bottom: 32px;
        text-align: center;
    }

    .check-circle {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        background: #D5E8CD;
        display: flex;
        align-items: center;
        justify-content: center;
        animation: popIn .4s cubic-bezier(.34,1.56,.64,1) both;
    }

    @keyframes popIn {
        from { transform: scale(0); opacity: 0; }
        to   { transform: scale(1); opacity: 1; }
    }

    .success-title {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 26px;
        font-weight: 700;
        letter-spacing: -.5px;
        color: #29170C;
    }

    .success-sub {
        font-size: 14px;
        color: #51443E;
    }

    /* ── Main card ───────────────────────────────────────────── */
    .tap-card {
        background: #fff;
        border-radius: 24px;
        box-shadow: 0 16px 64px -12px rgba(41,23,12,.10);
        padding: 36px 32px;
        text-align: center;
    }

    /* ── RFID card art + ripples ─────────────────────────────── */
    .rfid-art-wrap {
        position: relative;
        width: 200px;
        height: 130px;
        margin: 0 auto 28px;
    }

    /* Ripple rings */
    .ring {
        position: absolute;
        border-radius: 50%;
        top: 50%; left: 50%;
        transform: translate(-50%, -50%);
        border: 2px solid rgba(87,43,18,.15);
        animation: ringPulse 2s ease-out infinite;
    }

    .ring:nth-child(1) { width: 240px; height: 240px; animation-delay: 0s;   }
    .ring:nth-child(2) { width: 300px; height: 300px; animation-delay: .55s; }
    .ring:nth-child(3) { width: 360px; height: 360px; animation-delay: 1.1s; }

    @keyframes ringPulse {
        0%   { opacity: .7; transform: translate(-50%,-50%) scale(.75); }
        100% { opacity: 0;  transform: translate(-50%,-50%) scale(1.05); }
    }

    /* While verified — freeze rings, flash card green */
    .verified-state .ring { animation: none; opacity: 0; }
    .verified-state .rfid-card {
        background: linear-gradient(135deg, #2E7D32, #1B5E20) !important;
        animation: flashGreen .5s ease;
    }

    @keyframes flashGreen {
        0%,100% { transform: scale(1); }
        50%      { transform: scale(1.06); }
    }

    /* RFID card mockup */
    .rfid-card {
        position: relative;
        width: 200px;
        height: 130px;
        background: linear-gradient(135deg, #572B12 0%, #401E09 60%, #1C0B04 100%);
        border-radius: 16px;
        box-shadow: 0 8px 28px rgba(87,43,18,.35);
        overflow: hidden;
        transition: background .4s;
    }

    .rfid-card::before {
        content: '';
        position: absolute;
        top: -36px; left: -36px;
        width: 140px; height: 140px;
        background: rgba(255,255,255,.06);
        border-radius: 50%;
    }

    /* Gold chip */
    .chip {
        position: absolute;
        top: 18px; left: 16px;
        width: 34px; height: 26px;
        background: linear-gradient(135deg, #c9a96e, #e8d08b);
        border-radius: 5px;
        display: grid;
        grid-template-columns: repeat(3,1fr);
        grid-template-rows: repeat(3,1fr);
        gap: 2px;
        padding: 3px;
    }

    .chip span { background: rgba(0,0,0,.22); border-radius: 1px; }

    /* NFC waves */
    .nfc-icon {
        position: absolute;
        top: 14px; right: 14px;
        opacity: .65;
    }

    /* Card labels */
    .card-uid-lbl {
        position: absolute;
        bottom: 22px; left: 16px;
        font-family: 'Courier New', monospace;
        font-size: 10px;
        letter-spacing: 2px;
        color: rgba(255,255,255,.5);
    }

    .card-name-lbl {
        position: absolute;
        bottom: 10px; left: 16px;
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 11px;
        font-weight: 700;
        color: rgba(255,255,255,.85);
        letter-spacing: .4px;
    }

    /* ── Status text ─────────────────────────────────────────── */
    .tap-instruction {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 18px;
        font-weight: 700;
        color: #29170C;
        margin-bottom: 6px;
    }

    .tap-hint {
        font-size: 13px;
        color: #865135;
        line-height: 1.6;
        margin-bottom: 24px;
    }

    /* Polling dots */
    .dots-wrap {
        display: flex;
        justify-content: center;
        gap: 7px;
        margin-bottom: 28px;
    }

    .dot {
        width: 8px; height: 8px;
        border-radius: 50%;
        background: #D5C0AE;
        animation: dotBounce 1.2s ease-in-out infinite;
    }

    .dot:nth-child(2) { animation-delay: .2s; }
    .dot:nth-child(3) { animation-delay: .4s; }

    @keyframes dotBounce {
        0%,80%,100% { transform: translateY(0);   background: #D5C0AE; }
        40%          { transform: translateY(-7px); background: #572B12; }
    }

    /* Verified state — dots → single green tick */
    .verified-state .dots-wrap { display: none; }

    .verified-msg {
        display: none;
        align-items: center;
        justify-content: center;
        gap: 8px;
        background: #D5E8CD;
        border-radius: 12px;
        padding: 12px 20px;
        font-weight: 700;
        font-size: 14px;
        color: #101F0E;
        margin-bottom: 24px;
    }

    .verified-state .verified-msg { display: flex; }

    /* ── Divider ─────────────────────────────────────────────── */
    .divider {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 20px;
        color: #C4B5AE;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: .8px;
        text-transform: uppercase;
    }

    .divider::before,
    .divider::after {
        content: '';
        flex: 1;
        height: 1px;
        background: #F0E8E0;
    }

    /* ── Skip link ───────────────────────────────────────────── */
    .btn-skip {
        display: block;
        width: 100%;
        padding: 13px;
        background: #FFE3D3;
        border: none;
        border-radius: 14px;
        font-family: 'Manrope', sans-serif;
        font-weight: 700;
        font-size: 13px;
        letter-spacing: .6px;
        color: #572B12;
        text-decoration: none;
        text-align: center;
        cursor: pointer;
        transition: opacity .2s;
    }

    .btn-skip:hover { opacity: .8; }

    @media (max-width: 480px) {
        .success-page { margin-top: 32px; }
        .tap-card { padding: 28px 20px; }
    }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../header.php'; ?>

    <div class="success-page">

        <!-- ── Payment confirmed badge ─────────────────────── -->
        <div class="success-badge">
            <div class="check-circle">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none">
                    <path d="M5 13l4 4L19 7" stroke="#101F0E" stroke-width="2.8"
                          stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div class="success-title">Payment Successful!</div>
            <div class="success-sub">Your order has been placed.</div>
        </div>

        <!-- ── Tap card panel ──────────────────────────────── -->
        <div class="tap-card" id="tapCard">

            <!-- Animated RFID card art -->
            <div class="rfid-art-wrap" id="rfidArtWrap">
                <div class="ring"></div>
                <div class="ring"></div>
                <div class="ring"></div>

                <div class="rfid-card">
                    <!-- Gold chip -->
                    <div class="chip">
                        <span></span><span></span><span></span>
                        <span></span><span></span><span></span>
                        <span></span><span></span><span></span>
                    </div>

                    <!-- NFC waves -->
                    <svg class="nfc-icon" width="24" height="24" viewBox="0 0 24 24"
                         fill="none" stroke="rgba(255,255,255,0.65)" stroke-width="2">
                        <path d="M5 12.55a11 11 0 0 1 14.08 0"/>
                        <path d="M1.42 9a16 16 0 0 1 21.16 0"/>
                        <path d="M8.53 16.11a6 6 0 0 1 6.95 0"/>
                        <circle cx="12" cy="20" r="1" fill="rgba(255,255,255,0.65)"/>
                    </svg>

                    <div class="card-uid-lbl">A3 38 A7 13</div>
                    <div class="card-name-lbl">CleckBasket Collect</div>
                </div>
            </div>

            <!-- Status text -->
            <div class="tap-instruction" id="tapInstruction">
                Tap your card to view invoice
            </div>
            <div class="tap-hint" id="tapHint">
                Hold the RFID card (VID: A3 38 A7 13) on the reader.<br>
                Your invoice opens automatically.
            </div>

            <!-- Polling dots -->
            <div class="dots-wrap" id="dotsWrap">
                <div class="dot"></div>
                <div class="dot"></div>
                <div class="dot"></div>
            </div>

            <!-- Verified message (hidden until success) -->
            <div class="verified-msg" id="verifiedMsg">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                    <path d="M5 13l4 4L19 7" stroke="#101F0E" stroke-width="2.8"
                          stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="12" cy="12" r="10" stroke="#101F0E" stroke-width="1.5"/>
                </svg>
                Card verified — opening invoice…
            </div>

            <div class="divider">or</div>

            <!-- Manual skip -->
            <a href="/cleckbasket/includes/cart/invoice.php" class="btn-skip" id="skipBtn">
                Skip — View Invoice Manually →
            </a>
        </div>

    </div>

    <script>
    /* ── Poll poll_card.php every 500 ms ─────────────────────── */
    const tapCard   = document.getElementById('tapCard');
    const artWrap   = document.getElementById('rfidArtWrap');
    const instrEl   = document.getElementById('tapInstruction');
    const hintEl    = document.getElementById('tapHint');
    const verifyMsg = document.getElementById('verifiedMsg');
    const skipBtn   = document.getElementById('skipBtn');

    let polling  = true;
    let attempts = 0;

    async function poll() {
        if (!polling) return;
        attempts++;

        try {
            const res  = await fetch('/cleckbasket/backend/poll_card.php', {
                /* no-cache so we always get fresh data */
                headers: { 'Cache-Control': 'no-cache' }
            });
            const data = await res.json();

            if (data.success && data.status === 'verified') {
                onVerified(data.redirect || '/cleckbasket/includes/cart/invoice.php');
                return;
            }

            /* not_logged_in → redirect to login */
            if (data.status === 'not_logged_in') {
                polling = false;
                window.location.href = '/cleckbasket/includes/pages/login.php';
                return;
            }

        } catch (_) {
            /* network hiccup — keep polling */
        }

        if (polling) setTimeout(poll, 500);
    }

    function onVerified(redirectUrl) {
        polling = false;

        /* Visual feedback */
        tapCard.classList.add('verified-state');
        instrEl.textContent  = '✅ Card Verified!';
        hintEl.textContent   = 'Redirecting to your invoice…';
        skipBtn.style.display = 'none';

        /* Redirect after short delay so user sees the confirmation */
        setTimeout(() => {
            window.location.href = redirectUrl;
        }, 1200);
    }

    /* Start polling immediately */
    poll();
    </script>

    <?php include __DIR__ . '/../footer.php'; ?>
</body>
</html>
