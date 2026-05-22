<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment — CleckBasket</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Manrope', sans-serif;
            background: #FFF8F5;
            color: #29170C;
            min-height: 100vh;
        }

        .pay-page {
            max-width: 560px;
            margin: 60px auto;
            padding: 0 20px 80px;
        }

        .pay-header {
            text-align: center;
            margin-bottom: 32px;
        }

        .pay-header h1 {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 28px;
            font-weight: 600;
            letter-spacing: -0.5px;
            margin-bottom: 6px;
        }

        .pay-header p {
            font-size: 15px;
            color: #51443E;
        }

        .pay-card {
            background: #fff;
            border-radius: 24px;
            padding: 28px;
            box-shadow: 0 16px 64px -12px rgba(41,23,12,0.08);
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            color: #865135;
            margin-bottom: 16px;
        }

        .slot-summary {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .slot-icon {
            width: 44px;
            height: 44px;
            background: #D5E8CD;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .slot-detail {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .slot-label {
            font-size: 13px;
            color: #51443E;
        }

        .slot-value {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 16px;
            font-weight: 600;
            color: #29170C;
        }

        .order-items {
            display: flex;
            flex-direction: column;
            gap: 0;
        }

        .order-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #FFE3D3;
            font-size: 14px;
        }

        .order-item:last-child { border-bottom: none; }

        .item-name { color: #29170C; font-weight: 500; }
        .item-qty  { color: #865135; font-size: 12px; margin-top: 2px; }
        .item-price { font-weight: 600; color: #29170C; }

        .totals {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .totals-row {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            color: #51443E;
        }

        /* ADDED: discount row style */
        .totals-row.discount {
            color: #2e7d32;
            font-weight: 600;
        }

        .totals-row.grand {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 18px;
            font-weight: 600;
            color: #29170C;
            padding-top: 12px;
            border-top: 2px solid #FFE3D3;
            margin-top: 4px;
        }

        #paypal-button-container {
            margin-top: 8px;
        }

        .btn-back {
            display: block;
            width: 100%;
            padding: 14px;
            background: #FFE3D3;
            border: none;
            border-radius: 14px;
            font-family: 'Manrope', sans-serif;
            font-weight: 700;
            font-size: 12px;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            color: #5A331C;
            cursor: pointer;
            transition: opacity 0.2s;
            text-align: center;
            margin-top: 12px;
        }

        .btn-back:hover { opacity: 0.8; }

        #empty-cart-msg {
            text-align: center;
            color: #865135;
            padding: 20px 0;
            font-size: 15px;
        }
    </style>
</head>
<body>
    <?php include '../header.php'; ?>

    <div class="pay-page">
        <div class="pay-header">
            <h1>Complete Payment</h1>
            <p>Review your order and pay securely via PayPal.</p>
        </div>

        <!-- Slot Summary -->
        <div class="pay-card">
            <div class="section-title">Collection Slot</div>
            <div class="slot-summary">
                <div class="slot-icon">
                    <svg width="20" height="20" viewBox="0 0 18 20" fill="none">
                        <path d="M16 2H15V0H13V2H5V0H3V2H2C0.89 2 0.01 2.9 0.01 4L0 18C0 19.1 0.89 20 2 20H16C17.1 20 18 19.1 18 18V4C18 2.9 17.1 2 16 2ZM16 18H2V7H16V18ZM4 9H9V14H4V9Z" fill="#101F0E"/>
                    </svg>
                </div>
                <div class="slot-detail">
                    <span class="slot-label">Selected time</span>
                    <span class="slot-value" id="slot-display">—</span>
                </div>
            </div>
        </div>

        <!-- Order Items -->
        <div class="pay-card">
            <div class="section-title">Order Summary</div>
            <div id="empty-cart-msg" style="display:none;">No items in cart. <a href="/cleckbasket/includes/pages/shop.php" style="color:#572B12;">Shop now</a></div>
            <div class="order-items" id="order-items"></div>
        </div>

        <!-- Totals + PayPal -->
        <div class="pay-card">
            <div class="totals" id="totals-wrap">
                <div class="totals-row">
                    <span>Subtotal</span>
                    <span id="pay-subtotal">£0.00</span>
                </div>
                <!-- ADDED: coupon discount row, hidden until coupon applied -->
                <div class="totals-row discount" id="pay-discount-row" style="display:none;">
                    <span>Coupon (<span id="pay-coupon-label"></span>)</span>
                    <span id="pay-discount">-£0.00</span>
                </div>
                <div class="totals-row">
                    <span>Tax (5%)</span>
                    <span id="pay-tax">£0.00</span>
                </div>
                <div class="totals-row grand">
                    <span>Total Due</span>
                    <span id="pay-total">£0.00</span>
                </div>
            </div>

            <div id="paypal-button-container"></div>
            <div id="paypal-error" style="display:none;margin-top:16px;padding:12px;background:#ffdad6;border-radius:10px;color:#93000a;font-size:13px;text-align:center;">
                PayPal failed to load. Check your internet connection or Client ID.
            </div>
        </div>

        <button class="btn-back" onclick="window.location.href='/cleckbasket/includes/pages/collection.php'">
            &larr; Back to Slot Selection
        </button>
    </div>

    <script>
    // ── Read localStorage ───────────────────────────────────────────
    const slotLabel  = localStorage.getItem('selected_slot_label') || '—';
    const items      = JSON.parse(localStorage.getItem('invoice_items') || '[]');
    // ADDED: read coupon from localStorage
    const couponCode = localStorage.getItem('coupon_code')    || '';
    const couponPct  = parseInt(localStorage.getItem('coupon_percent') || '0', 10);

    document.getElementById('slot-display').textContent = slotLabel;

    // ── Render order items ──────────────────────────────────────────
    const itemsEl = document.getElementById('order-items');
    const emptyEl = document.getElementById('empty-cart-msg');

    let subtotal = 0;
    if (items.length === 0) {
        emptyEl.style.display = 'block';
    } else {
        items.forEach(item => {
            const price = Number(item.price) || 0;
            const qty   = Number(item.quantity) || 1;
            const line  = price * qty;
            subtotal   += line;

            const row = document.createElement('div');
            row.className = 'order-item';
            row.innerHTML = `
                <div>
                    <div class="item-name">${escHtml(item.name || 'Item')}</div>
                    <div class="item-qty">Qty: ${qty}</div>
                </div>
                <div class="item-price">£${line.toFixed(2)}</div>
            `;
            itemsEl.appendChild(row);
        });
    }

    // FIXED: apply coupon discount before calculating tax
    const discountAmt = couponPct > 0 ? parseFloat((subtotal * couponPct / 100).toFixed(2)) : 0;
    const tax         = parseFloat(((subtotal - discountAmt) * 0.05).toFixed(2));
    const total       = (subtotal - discountAmt + tax).toFixed(2);

    document.getElementById('pay-subtotal').textContent = `£${subtotal.toFixed(2)}`;
    document.getElementById('pay-tax').textContent      = `£${tax.toFixed(2)}`;
    document.getElementById('pay-total').textContent    = `£${total}`;

    // ADDED: show discount row if coupon was applied
    if (discountAmt > 0) {
        document.getElementById('pay-discount-row').style.display  = 'flex';
        document.getElementById('pay-coupon-label').textContent    = couponCode + ' ' + couponPct + '% off';
        document.getElementById('pay-discount').textContent        = `-£${discountAmt.toFixed(2)}`;
    }

    function escHtml(str) {
        const d = document.createElement('div');
        d.textContent = str;
        return d.innerHTML;
    }
    </script>

    <!-- PayPal SDK loaded here so DOM is ready when it executes -->
    <script src="https://www.paypal.com/sdk/js?client-id=sb-igrpv41153131@gmail.com&currency=GBP"
            onload="initPayPal()"
            onerror="document.getElementById('paypal-error').style.display='block'"></script>

    <script>
    function initPayPal() {
        const totalVal = document.getElementById('pay-total').textContent.replace('£','');
        paypal.Buttons({
            style: { shape: 'rect', color: 'gold', layout: 'vertical', label: 'pay' },
            createOrder: function(data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        description: 'CleckBasket Order',
                        amount: { value: totalVal, currency_code: 'GBP' }
                    }]
                });
            },
            onApprove: function(data, actions) {
                return actions.order.capture().then(function() {
                    localStorage.setItem('paypal_order_id', data.orderID);
                    // ADDED: clear coupon after successful payment
                    localStorage.removeItem('coupon_code');
                    localStorage.removeItem('coupon_percent');
                    window.location.href = '/cleckbasket/includes/pages/checkout_success.php?order_id=' + encodeURIComponent(data.orderID);
                });
            },
            onCancel: function() {
                window.location.href = '/cleckbasket/includes/pages/collection.php';
            },
            onError: function(err) {
                console.error('PayPal error:', err);
                document.getElementById('paypal-error').style.display = 'block';
            }
        }).render('#paypal-button-container');
    }
    </script>

    <?php include '../footer.php'; ?>
</body>
</html>