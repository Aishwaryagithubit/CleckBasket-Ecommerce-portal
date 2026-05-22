<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/backend/connect.php';

$order_id   = isset($_GET['order_id']) ? trim($_GET['order_id']) : null;
$homeUrl    = '/cleckbasket/index.php';
$invoiceUrl = '/cleckbasket/includes/cart/invoice.php';

// Finalise the pending order placed before PayPal redirect
if (!empty($_SESSION['pending_order_id']) && !empty($_SESSION['user_id'])) {
    $bind_order_id = $_SESSION['pending_order_id'];   // local vars for OCI8 reference binding
    $bind_user_id  = $_SESSION['user_id'];

    $conn = getDBConnection();
    if ($conn) {
        // Fetch order amount
        $s = oci_parse($conn, "SELECT order_amount FROM orders WHERE order_id = :order_id AND user_id = :user_id");
        oci_bind_by_name($s, ':order_id', $bind_order_id);
        oci_bind_by_name($s, ':user_id',  $bind_user_id);
        oci_execute($s);
        $orderRow = oci_fetch_assoc($s);
        oci_free_statement($s);

        if ($orderRow) {
            $bind_amount = (float)$orderRow['ORDER_AMOUNT'];

            // Insert payment record
            $ins = oci_parse($conn,
                "INSERT INTO payment (payment_amount, payment_method, user_id, order_id)
                 VALUES (:payment_amount, 'PayPal', :user_id, :order_id)");
            oci_bind_by_name($ins, ':payment_amount', $bind_amount);
            oci_bind_by_name($ins, ':user_id',        $bind_user_id);
            oci_bind_by_name($ins, ':order_id',       $bind_order_id);
            oci_execute($ins, OCI_NO_AUTO_COMMIT);
            oci_free_statement($ins);

            // Update order status to Confirmed
            $upd = oci_parse($conn, "UPDATE orders SET order_status = 'Confirmed' WHERE order_id = :order_id");
            oci_bind_by_name($upd, ':order_id', $bind_order_id);
            oci_execute($upd, OCI_NO_AUTO_COMMIT);
            oci_free_statement($upd);

            oci_commit($conn);
        }
        oci_close($conn);
    }
    unset($_SESSION['pending_order_id']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful — CleckBasket</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&family=Plus+Jakarta+Sans:wght@600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Manrope', sans-serif;
            background: #FFF8F5;
            color: #29170C;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        /* ── Outer card ──────────────────────────────────────────── */
        .card {
            background: #fff;
            border-radius: 28px;
            max-width: 460px;
            width: 100%;
            box-shadow: 0 20px 72px -16px rgba(41,23,12,.12);
            padding: 44px 36px 36px;
            text-align: center;
        }

        /* ── Success badge ───────────────────────────────────────── */
        .check-circle {
            width: 68px; height: 68px;
            background: #D5E8CD;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 18px;
            animation: popIn .45s cubic-bezier(.34,1.56,.64,1) both;
        }
        @keyframes popIn {
            from { transform: scale(0); opacity: 0; }
            to   { transform: scale(1); opacity: 1; }
        }

        h1 {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 24px; font-weight: 700;
            color: #29170C; margin-bottom: 6px;
        }

        .subtitle {
            font-size: 14px; color: #51443E;
            line-height: 1.6; margin-bottom: 10px;
        }

        .payer-ref {
            font-size: 11px; color: #A89080;
            margin-bottom: 28px; letter-spacing: .5px;
        }

        /* ── Divider ─────────────────────────────────────────────── */
        .divider {
            height: 1px; background: #F0E8E0;
            margin: 0 0 28px;
        }

        /* ── RFID tap section ────────────────────────────────────── */
        .tap-section { text-align: center; }

        .tap-label {
            font-size: 11px; font-weight: 700;
            letter-spacing: 1.2px; text-transform: uppercase;
            color: #865135; margin-bottom: 20px;
        }

        /* ── Card art + ripples ──────────────────────────────────── */
        .rfid-art-wrap {
            position: relative;
            width: 200px; height: 128px;
            margin: 0 auto 24px;
        }

        .ring {
            position: absolute; border-radius: 50%;
            top: 50%; left: 50%;
            border: 2px solid rgba(87,43,18,.13);
            animation: ringPulse 2s ease-out infinite;
            pointer-events: none;
        }
        .ring:nth-child(1) { width: 240px; height: 240px; animation-delay: 0s;   }
        .ring:nth-child(2) { width: 300px; height: 300px; animation-delay: .55s; }
        .ring:nth-child(3) { width: 356px; height: 356px; animation-delay: 1.1s; }

        @keyframes ringPulse {
            0%   { opacity:.8; transform:translate(-50%,-50%) scale(.72); }
            100% { opacity:0;  transform:translate(-50%,-50%) scale(1.04); }
        }

        /* Freeze rings + flash green on verified */
        .verified-state .ring { animation: none; opacity: 0; }
        .verified-state .rfid-card {
            background: linear-gradient(135deg,#2E7D32,#1B5E20) !important;
            animation: flashGreen .5s ease;
        }
        @keyframes flashGreen {
            0%,100% { transform: scale(1); }
            50%      { transform: scale(1.06); }
        }

        .rfid-card {
            position: relative;
            width: 200px; height: 128px;
            background: linear-gradient(135deg, #572B12 0%, #401E09 60%, #1C0B04 100%);
            border-radius: 16px;
            box-shadow: 0 8px 28px rgba(87,43,18,.35);
            overflow: hidden;
            transition: background .4s;
        }
        .rfid-card::before {
            content: '';
            position: absolute;
            top: -32px; left: -32px;
            width: 130px; height: 130px;
            background: rgba(255,255,255,.06);
            border-radius: 50%;
        }

        /* Gold chip */
        .chip {
            position: absolute; top: 16px; left: 14px;
            width: 32px; height: 24px;
            background: linear-gradient(135deg,#c9a96e,#e8d08b);
            border-radius: 4px;
            display: grid;
            grid-template-columns: repeat(3,1fr);
            grid-template-rows: repeat(3,1fr);
            gap: 2px; padding: 3px;
        }
        .chip span { background: rgba(0,0,0,.22); border-radius: 1px; }

        /* NFC waves */
        .nfc-icon { position: absolute; top: 12px; right: 12px; opacity: .65; }

        .card-uid  {
            position: absolute; bottom: 20px; left: 14px;
            font-family: 'Courier New', monospace;
            font-size: 9px; letter-spacing: 2px;
            color: rgba(255,255,255,.5);
        }
        .card-name {
            position: absolute; bottom: 9px; left: 14px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 10px; font-weight: 700;
            color: rgba(255,255,255,.85); letter-spacing: .4px;
        }

        /* ── Status text ─────────────────────────────────────────── */
        .tap-title {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 16px; font-weight: 700;
            color: #29170C; margin-bottom: 5px;
        }
        .tap-hint {
            font-size: 12px; color: #865135;
            line-height: 1.6; margin-bottom: 18px;
        }

        /* Bouncing dots */
        .dots { display: flex; justify-content: center; gap: 6px; margin-bottom: 24px; }
        .dot {
            width: 7px; height: 7px; border-radius: 50%;
            background: #D5C0AE;
            animation: bounce 1.2s ease-in-out infinite;
        }
        .dot:nth-child(2) { animation-delay: .2s; }
        .dot:nth-child(3) { animation-delay: .4s; }
        @keyframes bounce {
            0%,80%,100% { transform: translateY(0);   background: #D5C0AE; }
            40%          { transform: translateY(-6px); background: #572B12; }
        }

        /* Verified pill */
        .verified-pill {
            display: none;
            align-items: center; justify-content: center; gap: 7px;
            background: #D5E8CD; border-radius: 10px;
            padding: 10px 18px;
            font-weight: 700; font-size: 13px; color: #101F0E;
            margin-bottom: 20px;
        }
        .verified-state .dots { display: none; }
        .verified-state .verified-pill { display: flex; }

        /* ── Buttons ─────────────────────────────────────────────── */
        .btn-sep {
            display: flex; align-items: center; gap: 10px;
            color: #C4B5AE; font-size: 11px; font-weight: 700;
            letter-spacing: .8px; text-transform: uppercase;
            margin-bottom: 14px;
        }
        .btn-sep::before, .btn-sep::after {
            content: ''; flex: 1; height: 1px; background: #F0E8E0;
        }

        .btn-invoice {
            display: block; width: 100%; padding: 13px;
            background: #FFE3D3; color: #572B12;
            border: none; border-radius: 14px;
            font-family: 'Manrope', sans-serif;
            font-size: 13px; font-weight: 700;
            letter-spacing: .6px; text-transform: uppercase;
            text-decoration: none; cursor: pointer;
            transition: opacity .2s; margin-bottom: 10px;
        }
        .btn-invoice:hover { opacity: .8; }

        .btn-home {
            display: block; width: 100%; padding: 11px;
            background: transparent; color: #A89080;
            border: none; border-radius: 14px;
            font-family: 'Manrope', sans-serif;
            font-size: 12px; font-weight: 600;
            text-decoration: none; cursor: pointer;
            transition: color .2s;
        }
        .btn-home:hover { color: #572B12; }
    </style>
</head>
<body>
    <div class="card" id="mainCard">

        <!-- ── Payment confirmed ───────────────────────────────── -->
        <div class="check-circle">
            <svg width="30" height="30" viewBox="0 0 24 24" fill="none">
                <path d="M5 13l4 4L19 7" stroke="#101F0E" stroke-width="2.8"
                      stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        <h1>Payment Successful!</h1>
        <p class="subtitle">Your order has been confirmed.<br>Tap your RFID card to open your invoice.</p>
        <?php if ($order_id): ?>
            <p class="payer-ref">Ref: <?= htmlspecialchars($order_id) ?></p>
        <?php endif; ?>

        <div class="divider"></div>

        <!-- ── RFID tap zone ───────────────────────────────────── -->
        <div class="tap-section" id="tapSection">

            <div class="tap-label">Tap to collect</div>

            <!-- Animated card art -->
            <div class="rfid-art-wrap" id="rfidArt">
                <div class="ring"></div>
                <div class="ring"></div>
                <div class="ring"></div>
                <div class="rfid-card">
                    <div class="chip">
                        <span></span><span></span><span></span>
                        <span></span><span></span><span></span>
                        <span></span><span></span><span></span>
                    </div>
                    <svg class="nfc-icon" width="22" height="22" viewBox="0 0 24 24"
                         fill="none" stroke="rgba(255,255,255,0.65)" stroke-width="2">
                        <path d="M5 12.55a11 11 0 0 1 14.08 0"/>
                        <path d="M1.42 9a16 16 0 0 1 21.16 0"/>
                        <path d="M8.53 16.11a6 6 0 0 1 6.95 0"/>
                        <circle cx="12" cy="20" r="1" fill="rgba(255,255,255,0.65)"/>
                    </svg>
                    <div class="card-uid">A3 38 A7 13</div>
                    <div class="card-name">CleckBasket Collect</div>
                </div>
            </div>

            <div class="tap-title" id="tapTitle">Hold card on reader</div>
            <div class="tap-hint" id="tapHint">
                Place card VID A3 38 A7 13 on the MFRC522 reader.<br>
                Invoice opens automatically.
            </div>

            <!-- Waiting dots -->
            <div class="dots" id="dotsEl">
                <div class="dot"></div>
                <div class="dot"></div>
                <div class="dot"></div>
            </div>

            <!-- Verified pill (shown on success) -->
            <div class="verified-pill" id="verifiedPill">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                    <path d="M5 13l4 4L19 7" stroke="#101F0E" stroke-width="2.8"
                          stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="12" cy="12" r="10" stroke="#101F0E" stroke-width="1.5"/>
                </svg>
                Card verified — opening invoice…
            </div>

        </div>

        <!-- ── Manual fallback ────────────────────────────────── -->
        <div class="btn-sep" id="sepEl">or</div>
        <a class="btn-invoice" id="skipBtn" href="<?= htmlspecialchars($invoiceUrl) ?>">
            Skip — View Invoice
        </a>
        <a class="btn-home" href="<?= htmlspecialchars($homeUrl) ?>">Return to Homepage</a>

    </div>

    <script>
    const card     = document.getElementById('mainCard');
    const tapTitle = document.getElementById('tapTitle');
    const tapHint  = document.getElementById('tapHint');
    const skipBtn  = document.getElementById('skipBtn');
    const sepEl    = document.getElementById('sepEl');

    let polling = true;

    async function poll() {
        if (!polling) return;
        try {
            const res  = await fetch('/cleckbasket/backend/poll_card.php', {
                headers: { 'Cache-Control': 'no-cache' }
            });
            const data = await res.json();

            if (data.success && data.status === 'verified') {
                onVerified(data.redirect || '/cleckbasket/includes/cart/invoice.php');
                return;
            }
        } catch (_) { /* keep polling on network hiccup */ }

        if (polling) setTimeout(poll, 500);
    }

    function onVerified(url) {
        polling = false;
        card.classList.add('verified-state');
        tapTitle.textContent = '✅ Card Verified!';
        tapHint.textContent  = 'Redirecting to your invoice…';
        skipBtn.style.display = 'none';
        sepEl.style.display   = 'none';
        setTimeout(() => { window.location.href = url; }, 1200);
    }

    poll();
    </script>
</body>
</html>
