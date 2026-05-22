<?php
/**
 * Card Verification Page
 * /cleckbasket/includes/pages/card_verify.php
 *
 * Allows a logged-in user (or trader/staff) to scan the registered NFC card
 * (VID: A3 38 A7 13) to confirm identity and view pending collection orders.
 *
 * No DB schema changes. No relationship changes. No connect.php changes.
 */
if (session_status() === PHP_SESSION_NONE) session_start();

$isLoggedIn = !empty($_SESSION['user_id']);
$userName   = htmlspecialchars($_SESSION['user_name'] ?? 'Customer');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Card Verification — CleckBasket</title>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@600;700&display=swap" rel="stylesheet" />

    <style>
    /* ─────────────────────────────────────────
       RESET & BASE
    ───────────────────────────────────────── */
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
        --bg:        #FFF8F5;
        --surface:   #FFFFFF;
        --brown-dk:  #29170C;
        --brown-md:  #572B12;
        --brown-lt:  #865135;
        --muted:     #51443E;
        --cream:     #FFE3D3;
        --green-dk:  #101F0E;
        --green-bg:  #D5E8CD;
        --border:    #F0E8E0;
        --radius:    24px;
        --shadow:    0 16px 64px -12px rgba(41,23,12,.10);
    }

    body {
        font-family: 'Manrope', sans-serif;
        background: var(--bg);
        color: var(--brown-dk);
        min-height: 100vh;
    }

    /* ─────────────────────────────────────────
       PAGE LAYOUT
    ───────────────────────────────────────── */
    .verify-page {
        max-width: 600px;
        margin: 52px auto 80px;
        padding: 0 20px;
    }

    /* ─────────────────────────────────────────
       PAGE HEADER
    ───────────────────────────────────────── */
    .page-header {
        text-align: center;
        margin-bottom: 36px;
    }

    .page-header__badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: var(--green-bg);
        color: var(--green-dk);
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 1.2px;
        text-transform: uppercase;
        padding: 5px 14px;
        border-radius: 20px;
        margin-bottom: 14px;
    }

    .page-header h1 {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 30px;
        font-weight: 700;
        letter-spacing: -0.6px;
        color: var(--brown-dk);
        margin-bottom: 8px;
    }

    .page-header p {
        font-size: 15px;
        color: var(--muted);
        line-height: 1.6;
    }

    /* ─────────────────────────────────────────
       CARD (panel)
    ───────────────────────────────────────── */
    .card {
        background: var(--surface);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        padding: 32px;
        margin-bottom: 20px;
    }

    /* ─────────────────────────────────────────
       SCAN ZONE
    ───────────────────────────────────────── */
    .scan-zone {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 28px;
    }

    /* Animated card art */
    .card-art-wrap {
        position: relative;
        width: 220px;
        height: 140px;
    }

    .nfc-card {
        width: 220px;
        height: 140px;
        background: linear-gradient(135deg, #572B12 0%, #401E09 60%, #1C0B04 100%);
        border-radius: 18px;
        box-shadow: 0 8px 28px rgba(87,43,18,.35);
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        padding: 18px;
        transition: transform .3s ease;
    }

    .nfc-card::before {
        content: '';
        position: absolute;
        top: -40px; left: -40px;
        width: 160px; height: 160px;
        background: rgba(255,255,255,.06);
        border-radius: 50%;
    }

    .nfc-card__chip {
        position: absolute;
        top: 20px; left: 20px;
        width: 38px; height: 28px;
        background: linear-gradient(135deg, #c9a96e, #e8d08b);
        border-radius: 6px;
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        grid-template-rows: 1fr 1fr 1fr;
        gap: 2px;
        padding: 4px;
    }

    .nfc-card__chip span {
        background: rgba(0,0,0,.22);
        border-radius: 1px;
    }

    .nfc-card__nfc {
        position: absolute;
        top: 16px; right: 16px;
        opacity: .7;
    }

    .nfc-card__vid {
        font-family: 'Courier New', monospace;
        font-size: 11px;
        letter-spacing: 2px;
        color: rgba(255,255,255,.55);
        margin-bottom: 4px;
    }

    .nfc-card__label {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 13px;
        font-weight: 700;
        color: rgba(255,255,255,.9);
        letter-spacing: 0.5px;
    }

    /* Ripple rings behind the card */
    .ripple-ring {
        position: absolute;
        border: 2px solid rgba(87,43,18,.18);
        border-radius: 50%;
        top: 50%; left: 50%;
        transform: translate(-50%, -50%) scale(0);
        animation: ripple 2.4s ease-out infinite;
        pointer-events: none;
    }

    .ripple-ring:nth-child(2) { width: 260px; height: 260px; animation-delay: 0s;   }
    .ripple-ring:nth-child(3) { width: 320px; height: 320px; animation-delay: .6s;  }
    .ripple-ring:nth-child(4) { width: 380px; height: 380px; animation-delay: 1.2s; }

    @keyframes ripple {
        0%   { transform: translate(-50%,-50%) scale(.6); opacity: .7; }
        100% { transform: translate(-50%,-50%) scale(1.1); opacity: 0; }
    }

    /* Stop ripple while scanning */
    .scanning .ripple-ring   { animation-play-state: paused; }
    .scanning .nfc-card      { animation: cardPulse .6s ease-in-out infinite alternate; }

    @keyframes cardPulse {
        from { transform: scale(1);    box-shadow: 0 8px 28px rgba(87,43,18,.35); }
        to   { transform: scale(1.03); box-shadow: 0 12px 36px rgba(87,43,18,.55); }
    }

    /* ─────────────────────────────────────────
       INPUT GROUP
    ───────────────────────────────────────── */
    .input-group {
        width: 100%;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .input-label {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 1.2px;
        text-transform: uppercase;
        color: var(--brown-lt);
    }

    .input-row {
        display: flex;
        gap: 10px;
    }

    .card-input {
        flex: 1;
        padding: 14px 18px;
        background: var(--bg);
        border: 2px solid var(--border);
        border-radius: 14px;
        font-family: 'Courier New', monospace;
        font-size: 18px;
        font-weight: 600;
        letter-spacing: 3px;
        color: var(--brown-dk);
        outline: none;
        transition: border-color .2s, box-shadow .2s;
        text-transform: uppercase;
    }

    .card-input::placeholder {
        font-family: 'Manrope', sans-serif;
        font-size: 14px;
        letter-spacing: normal;
        color: #C4B5AE;
    }

    .card-input:focus {
        border-color: var(--brown-md);
        box-shadow: 0 0 0 4px rgba(87,43,18,.08);
    }

    .btn-verify {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 14px 22px;
        background: linear-gradient(135deg, #572B12, #401E09);
        color: #fff;
        border: none;
        border-radius: 14px;
        font-family: 'Manrope', sans-serif;
        font-size: 14px;
        font-weight: 700;
        letter-spacing: .5px;
        cursor: pointer;
        box-shadow: 0 4px 16px rgba(87,43,18,.28);
        transition: opacity .2s, transform .15s;
        white-space: nowrap;
    }

    .btn-verify:hover  { opacity: .88; transform: translateY(-1px); }
    .btn-verify:active { transform: translateY(0); }
    .btn-verify:disabled { opacity: .5; cursor: not-allowed; transform: none; }

    .hint-text {
        font-size: 12px;
        color: var(--muted);
        text-align: center;
        line-height: 1.5;
    }

    /* ─────────────────────────────────────────
       ALERT MESSAGES
    ───────────────────────────────────────── */
    .alert {
        display: none;
        padding: 14px 18px;
        border-radius: 14px;
        font-size: 14px;
        font-weight: 500;
        margin-bottom: 8px;
    }

    .alert.show { display: flex; align-items: flex-start; gap: 10px; }

    .alert-error   { background: #FFDAD6; color: #93000A; }
    .alert-warning { background: #FFF3CD; color: #664D03; }
    .alert-success { background: #E8F5E9; color: #1B5E20; }

    .alert svg { flex-shrink: 0; margin-top: 1px; }

    /* ─────────────────────────────────────────
       NOT-LOGGED-IN BANNER
    ───────────────────────────────────────── */
    .login-prompt {
        background: var(--surface);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        padding: 40px 32px;
        text-align: center;
    }

    .login-prompt svg { display: block; margin: 0 auto 16px; opacity: .45; }
    .login-prompt h2  { font-size: 20px; margin-bottom: 8px; }
    .login-prompt p   { color: var(--muted); font-size: 14px; margin-bottom: 24px; }

    .btn-login {
        display: inline-block;
        padding: 13px 32px;
        background: linear-gradient(135deg, #572B12, #401E09);
        color: #fff;
        border-radius: 14px;
        text-decoration: none;
        font-weight: 700;
        font-size: 14px;
        letter-spacing: .4px;
        box-shadow: 0 4px 16px rgba(87,43,18,.28);
        transition: opacity .2s;
    }

    .btn-login:hover { opacity: .88; }

    /* ─────────────────────────────────────────
       RESULT SECTION (hidden until verified)
    ───────────────────────────────────────── */
    #result-section { display: none; }
    #result-section.visible { display: block; }

    /* User identity card */
    .identity-card {
        background: linear-gradient(135deg, #572B12 0%, #401E09 100%);
        border-radius: 20px;
        padding: 28px;
        display: flex;
        align-items: center;
        gap: 20px;
        margin-bottom: 20px;
        box-shadow: 0 8px 28px rgba(87,43,18,.30);
    }

    .id-avatar {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        background: rgba(255,255,255,.15);
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 26px;
        font-weight: 700;
        color: #fff;
        flex-shrink: 0;
        border: 2px solid rgba(255,255,255,.25);
    }

    .id-info { flex: 1; min-width: 0; }

    .id-name {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 20px;
        font-weight: 700;
        color: #fff;
        margin-bottom: 4px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .id-email {
        font-size: 13px;
        color: rgba(255,255,255,.70);
        margin-bottom: 4px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .id-contact {
        font-size: 13px;
        color: rgba(255,255,255,.60);
    }

    .id-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: var(--green-bg);
        color: var(--green-dk);
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 1px;
        text-transform: uppercase;
        padding: 5px 12px;
        border-radius: 20px;
        flex-shrink: 0;
    }

    /* Section titles */
    .section-title {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 1.2px;
        text-transform: uppercase;
        color: var(--brown-lt);
        margin-bottom: 16px;
    }

    /* Orders list */
    .orders-list {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .order-card {
        border: 1px solid var(--border);
        border-radius: 16px;
        overflow: hidden;
    }

    .order-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 18px;
        background: var(--bg);
        border-bottom: 1px solid var(--border);
    }

    .order-id-lbl {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 14px;
        font-weight: 600;
        color: var(--brown-dk);
    }

    .status-pill {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .8px;
        text-transform: uppercase;
        padding: 4px 12px;
        border-radius: 20px;
    }

    .status-pending   { background: #FFF3CD; color: #664D03; }
    .status-confirmed { background: var(--green-bg); color: var(--green-dk); }
    .status-processing{ background: #E3F2FD; color: #0D47A1; }

    .order-slot {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 18px;
        border-bottom: 1px solid var(--border);
        font-size: 13px;
        color: var(--muted);
    }

    .order-slot svg { flex-shrink: 0; opacity: .7; }

    .order-items-list {
        padding: 12px 18px;
    }

    .order-item-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        font-size: 14px;
        border-bottom: 1px dashed var(--border);
    }

    .order-item-row:last-child { border-bottom: none; }

    .item-n  { color: var(--brown-dk); font-weight: 500; }
    .item-q  { font-size: 12px; color: var(--brown-lt); margin-top: 2px; }
    .item-p  { font-weight: 600; color: var(--brown-dk); }

    .order-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 18px;
        background: var(--bg);
        border-top: 1px solid var(--border);
    }

    .order-total-lbl { font-size: 13px; color: var(--muted); }

    .order-total-val {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 16px;
        font-weight: 700;
        color: var(--brown-md);
    }

    /* Empty-orders state */
    .empty-orders {
        text-align: center;
        padding: 32px;
        color: var(--muted);
        font-size: 14px;
    }

    .empty-orders svg { display: block; margin: 0 auto 12px; opacity: .35; }

    /* Verified banner */
    .verified-banner {
        display: flex;
        align-items: center;
        gap: 12px;
        background: var(--green-bg);
        border-radius: 14px;
        padding: 14px 18px;
        margin-bottom: 20px;
    }

    .verified-banner svg { flex-shrink: 0; }

    .verified-banner .vb-title {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 14px;
        font-weight: 700;
        color: var(--green-dk);
    }

    .verified-banner .vb-sub {
        font-size: 12px;
        color: #2E5427;
        margin-top: 2px;
    }

    /* Scan-again button */
    .btn-rescan {
        display: block;
        width: 100%;
        padding: 14px;
        background: var(--cream);
        border: none;
        border-radius: 14px;
        font-family: 'Manrope', sans-serif;
        font-weight: 700;
        font-size: 12px;
        letter-spacing: 1.2px;
        text-transform: uppercase;
        color: var(--brown-md);
        cursor: pointer;
        margin-top: 20px;
        transition: opacity .2s;
        text-align: center;
    }

    .btn-rescan:hover { opacity: .8; }

    /* ─────────────────────────────────────────
       SPINNER
    ───────────────────────────────────────── */
    .spinner {
        display: none;
        width: 20px; height: 20px;
        border: 3px solid rgba(255,255,255,.35);
        border-top-color: #fff;
        border-radius: 50%;
        animation: spin .7s linear infinite;
    }

    @keyframes spin { to { transform: rotate(360deg); } }

    /* ─────────────────────────────────────────
       RESPONSIVE
    ───────────────────────────────────────── */
    @media (max-width: 480px) {
        .verify-page { margin-top: 32px; }
        .card { padding: 24px 20px; }
        .identity-card { flex-direction: column; text-align: center; }
        .id-avatar { margin: 0 auto; }
        .id-name, .id-email, .id-contact { white-space: normal; }
        .input-row { flex-direction: column; }
        .btn-verify { width: 100%; justify-content: center; }
    }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../header.php'; ?>

    <div class="verify-page">

        <!-- ── Page Header ─────────────────────────────────────── -->
        <div class="page-header">
            <div class="page-header__badge">
                <!-- NFC icon -->
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M5 12.55a11 11 0 0 1 14.08 0"/>
                    <path d="M1.42 9a16 16 0 0 1 21.16 0"/>
                    <path d="M8.53 16.11a6 6 0 0 1 6.95 0"/>
                    <circle cx="12" cy="20" r="1" fill="currentColor"/>
                </svg>
                NFC Card Verification
            </div>
            <h1>Verify &amp; Collect</h1>
            <p>Scan your registered NFC card to confirm your identity<br>and view your collection order.</p>
        </div>

        <?php if (!$isLoggedIn): ?>
        <!-- ── Not Logged In ──────────────────────────────────── -->
        <div class="login-prompt">
            <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="#865135" stroke-width="1.5">
                <rect x="3" y="11" width="18" height="11" rx="2"/>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            </svg>
            <h2>Login Required</h2>
            <p>Please log in to your CleckBasket account<br>before using card verification.</p>
            <a href="/cleckbasket/includes/pages/login.php" class="btn-login">Log In Now</a>
        </div>

        <?php else: ?>
        <!-- ── Scan Card UI ───────────────────────────────────── -->
        <div class="card" id="scan-card">
            <div class="scan-zone" id="scan-zone">

                <!-- Animated NFC card art -->
                <div class="card-art-wrap">
                    <!-- Ripple rings (behind) -->
                    <div class="ripple-ring"></div>
                    <div class="ripple-ring"></div>
                    <div class="ripple-ring"></div>

                    <!-- Physical card mockup -->
                    <div class="nfc-card">
                        <div class="nfc-card__chip">
                            <span></span><span></span><span></span>
                            <span></span><span></span><span></span>
                            <span></span><span></span><span></span>
                        </div>
                        <!-- NFC waves icon -->
                        <svg class="nfc-card__nfc" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.65)" stroke-width="2">
                            <path d="M5 12.55a11 11 0 0 1 14.08 0"/>
                            <path d="M1.42 9a16 16 0 0 1 21.16 0"/>
                            <path d="M8.53 16.11a6 6 0 0 1 6.95 0"/>
                            <circle cx="12" cy="20" r="1" fill="rgba(255,255,255,0.65)"/>
                        </svg>
                        <div class="nfc-card__vid">VID: A3 38 A7 13</div>
                        <div class="nfc-card__label">CleckBasket Collect</div>
                    </div>
                </div>

                <!-- Input group -->
                <div class="input-group">
                    <label class="input-label" for="cardUidInput">Enter or scan card UID</label>
                    <div class="input-row">
                        <input
                            type="text"
                            id="cardUidInput"
                            class="card-input"
                            placeholder="e.g. A3 38 A7 13"
                            maxlength="32"
                            autocomplete="off"
                            spellcheck="false"
                        />
                        <button class="btn-verify" id="verifyBtn" onclick="verifyCard()">
                            <span class="btn-verify__txt">Verify</span>
                            <div class="spinner" id="spinner"></div>
                        </button>
                    </div>
                    <span class="hint-text">
                        Hold your NFC card near the reader — the UID is entered automatically.<br>
                        You can also type it manually and press <strong>Verify</strong>.
                    </span>
                </div>

                <!-- Alert messages -->
                <div class="alert alert-error" id="alert-error">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <span id="alert-error-msg">Card not recognised.</span>
                </div>
                <div class="alert alert-warning" id="alert-warn">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                        <line x1="12" y1="9" x2="12" y2="13"/>
                        <line x1="12" y1="17" x2="12.01" y2="17"/>
                    </svg>
                    <span id="alert-warn-msg">Please enter a card UID first.</span>
                </div>
            </div>
        </div>

        <!-- ── Result Section (shown after successful scan) ───── -->
        <div id="result-section">

            <!-- Verified banner -->
            <div class="verified-banner">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                    <circle cx="12" cy="12" r="11" fill="#101F0E" opacity=".15"/>
                    <path d="M9 12l2 2 4-4" stroke="#101F0E" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="12" cy="12" r="10" stroke="#101F0E" stroke-width="1.5"/>
                </svg>
                <div>
                    <div class="vb-title">Identity Verified ✓</div>
                    <div class="vb-sub">Card VID A3 38 A7 13 matched successfully</div>
                </div>
            </div>

            <!-- User identity -->
            <div class="identity-card" id="identity-card">
                <div class="id-avatar" id="id-avatar">–</div>
                <div class="id-info">
                    <div class="id-name"    id="id-name">–</div>
                    <div class="id-email"   id="id-email">–</div>
                    <div class="id-contact" id="id-contact"></div>
                </div>
                <div class="id-badge">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none">
                        <path d="M9 12l2 2 4-4" stroke="#101F0E" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                        <circle cx="12" cy="12" r="10" stroke="#101F0E" stroke-width="2"/>
                    </svg>
                    Verified
                </div>
            </div>

            <!-- Orders -->
            <div class="card">
                <div class="section-title">Collection Orders</div>
                <div class="orders-list" id="orders-list"></div>
            </div>

            <!-- Scan again -->
            <button class="btn-rescan" onclick="resetScan()">&#8592; Scan Another Card</button>
        </div>

        <?php endif; ?>

    </div><!-- /verify-page -->

    <?php if ($isLoggedIn): ?>
    <script>
    /* ── Auto-focus input for NFC reader ──────────────────────── */
    const inputEl   = document.getElementById('cardUidInput');
    const verifyBtn = document.getElementById('verifyBtn');
    const spinner   = document.getElementById('spinner');
    const scanZone  = document.getElementById('scan-zone');

    /* ── Normalise UID: strip non-hex, uppercase (mirrors backend) ── */
    function normaliseUid(raw) {
        return raw.replace(/[^0-9A-Fa-f]/g, '').toUpperCase();
    }

    if (inputEl) {
        inputEl.focus();
        /* Allow NFC reader to submit on Enter (most readers send Enter after UID) */
        inputEl.addEventListener('keydown', e => {
            if (e.key === 'Enter') { e.preventDefault(); verifyCard(); }
        });
        /* Auto-uppercase while typing */
        inputEl.addEventListener('input', () => {
            const pos = inputEl.selectionStart;
            inputEl.value = inputEl.value.toUpperCase();
            inputEl.setSelectionRange(pos, pos);
        });
    }

    /* ── Helpers ──────────────────────────────────────────────── */
    function showAlert(type, msg) {
        document.getElementById('alert-error').classList.remove('show');
        document.getElementById('alert-warn').classList.remove('show');
        if (type === 'error') {
            document.getElementById('alert-error-msg').textContent = msg;
            document.getElementById('alert-error').classList.add('show');
        } else if (type === 'warn') {
            document.getElementById('alert-warn-msg').textContent = msg;
            document.getElementById('alert-warn').classList.add('show');
        }
    }

    function clearAlerts() {
        document.getElementById('alert-error').classList.remove('show');
        document.getElementById('alert-warn').classList.remove('show');
    }

    function setLoading(on) {
        verifyBtn.disabled = on;
        spinner.style.display = on ? 'block' : 'none';
        document.querySelector('.btn-verify__txt').textContent = on ? 'Verifying…' : 'Verify';
        if (on) {
            scanZone.classList.add('scanning');
        } else {
            scanZone.classList.remove('scanning');
        }
    }

    function escHtml(str) {
        const d = document.createElement('div');
        d.textContent = str;
        return d.innerHTML;
    }

    function formatMoney(val) {
        return '£' + Number(val).toFixed(2);
    }

    /* ── Status pill helper ───────────────────────────────────── */
    function statusPill(status) {
        const s = (status || '').toLowerCase();
        let cls = 'status-pending', lbl = status || 'Pending';
        if (s === 'confirmed')  { cls = 'status-confirmed';  lbl = 'Confirmed'; }
        if (s === 'processing') { cls = 'status-processing'; lbl = 'Processing'; }
        return `<span class="status-pill ${cls}">${lbl}</span>`;
    }

    /* ── Main verify function ─────────────────────────────────── */
    async function verifyCard() {
        clearAlerts();
        const rawUid = (inputEl.value || '').trim();
        if (!rawUid) {
            showAlert('warn', 'Please enter or scan a card UID first.');
            inputEl.focus();
            return;
        }

        /* Quick client-side length sanity: A3 38 A7 13 = 8 hex digits */
        const hexOnly = normaliseUid(rawUid);
        if (hexOnly.length < 4) {
            showAlert('warn', 'UID too short — please scan again or type the full UID.');
            inputEl.focus();
            return;
        }

        setLoading(true);

        try {
            /* Send the raw value; backend normalises and compares */
            const res  = await fetch('/cleckbasket/backend/card_verify.php', {
                method:  'POST',
                headers: { 'Content-Type': 'application/json' },
                body:    JSON.stringify({ card_uid: rawUid })
            });
            const data = await res.json();

            if (!data.success) {
                let msg = data.message || 'Verification failed.';
                if (data.error === 'not_logged_in') {
                    msg = 'Session expired. Please log in again.';
                } else if (data.error === 'invalid_card') {
                    msg = 'Card not recognised. Make sure you are scanning the correct card (VID: A3 38 A7 13).';
                }
                showAlert('error', msg);
                setLoading(false);
                return;
            }

            /* ── Render results ─────────────────────────────── */
            renderResults(data);

        } catch (err) {
            console.error(err);
            showAlert('error', 'Network error — please try again.');
            setLoading(false);
        }
    }

    /* ── Render user + orders ─────────────────────────────────── */
    function renderResults(data) {
        const u = data.user || {};

        /* Avatar initial */
        const initial = (u.name || '?').charAt(0).toUpperCase();
        document.getElementById('id-avatar').textContent  = initial;
        document.getElementById('id-name').textContent    = u.name    || '—';
        document.getElementById('id-email').textContent   = u.email   || '—';
        const contactEl = document.getElementById('id-contact');
        contactEl.textContent = u.contact ? '📞 ' + u.contact : '';

        /* Orders */
        const listEl = document.getElementById('orders-list');
        listEl.innerHTML = '';

        const orders = data.orders || [];

        if (orders.length === 0) {
            listEl.innerHTML = `
                <div class="empty-orders">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#865135" stroke-width="1.5">
                        <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/>
                        <line x1="3" y1="6" x2="21" y2="6"/>
                        <path d="M16 10a4 4 0 01-8 0"/>
                    </svg>
                    <p>No pending or confirmed orders found.</p>
                    <p style="margin-top:6px;font-size:12px;color:#865135;">
                        This customer has no active collection orders at this time.
                    </p>
                </div>`;
        } else {
            orders.forEach(o => {
                const slotInfo = (o.slot_date && o.slot_time)
                    ? `${o.slot_day ? o.slot_day + ' ' : ''}${o.slot_date} &bull; ${o.slot_time}`
                    : 'No slot assigned';

                let itemsHtml = '';
                (o.items || []).forEach(item => {
                    const line = (item.price * item.quantity).toFixed(2);
                    itemsHtml += `
                        <div class="order-item-row">
                            <div>
                                <div class="item-n">${escHtml(item.name)}</div>
                                <div class="item-q">Qty: ${item.quantity}</div>
                            </div>
                            <div class="item-p">${formatMoney(line)}</div>
                        </div>`;
                });

                if (!itemsHtml) {
                    itemsHtml = `<div style="padding:10px 0;font-size:13px;color:#865135;">No item details available.</div>`;
                }

                const card = document.createElement('div');
                card.className = 'order-card';
                card.innerHTML = `
                    <div class="order-header">
                        <span class="order-id-lbl">Order #${o.order_id}</span>
                        ${statusPill(o.status)}
                    </div>
                    <div class="order-slot">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                        <span>${slotInfo}</span>
                    </div>
                    <div class="order-items-list">${itemsHtml}</div>
                    <div class="order-footer">
                        <span class="order-total-lbl">Total Due</span>
                        <span class="order-total-val">${formatMoney(o.amount)}</span>
                    </div>`;
                listEl.appendChild(card);
            });
        }

        /* Show result, hide scan card */
        document.getElementById('scan-card').style.display    = 'none';
        document.getElementById('result-section').classList.add('visible');
        setLoading(false);

        /* Scroll to top of results */
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    /* ── Reset for next scan ──────────────────────────────────── */
    function resetScan() {
        document.getElementById('result-section').classList.remove('visible');
        document.getElementById('scan-card').style.display = '';
        document.getElementById('orders-list').innerHTML   = '';
        clearAlerts();
        if (inputEl) { inputEl.value = ''; inputEl.focus(); }
        setLoading(false);
    }
    </script>
    <?php endif; ?>

    <?php include __DIR__ . '/../footer.php'; ?>
</body>
</html>
