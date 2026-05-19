<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$customerName  = htmlspecialchars($_SESSION['full_name']  ?? $_SESSION['user_name'] ?? 'Customer');
$customerEmail = htmlspecialchars($_SESSION['email'] ?? '');
$invoiceDate   = date('d M Y');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Invoice — CleckBasket</title>
    <link rel="stylesheet" href="../../assets/css/invoice.css" />
    <style>
        /* Signature overrides */
        .signature-block { text-align: center; }
        .signature-name { font-size: 15px; font-weight: 700; color: #29170C; }
        .signature-title { font-size: 12px; color: #865135; margin-top: 3px; }

        /* Action buttons */
        .invoice-actions {
            display: flex; gap: 12px; justify-content: center;
            margin-top: 28px; flex-wrap: wrap;
        }
        .btn-download {
            display: inline-flex; align-items: center; gap: 9px;
            background: linear-gradient(135deg, #572B12, #401E09);
            color: #fff; border: none; border-radius: 12px;
            padding: 13px 28px; font-size: 14px; font-weight: 700;
            letter-spacing: 0.4px; cursor: pointer;
            box-shadow: 0 4px 16px rgba(87,43,18,0.28);
            transition: opacity 0.2s, transform 0.15s;
        }
        .btn-download:hover { opacity: 0.88; transform: translateY(-1px); }
        .btn-download svg { width: 17px; height: 17px; fill: #FFE3D3; flex-shrink: 0; }

        .btn-print {
            display: inline-flex; align-items: center; gap: 9px;
            background: #fff; color: #572B12;
            border: 2px solid #D5C0AE; border-radius: 12px;
            padding: 11px 24px; font-size: 14px; font-weight: 600;
            cursor: pointer;
            transition: background 0.2s, border-color 0.2s, transform 0.15s;
        }
        .btn-print:hover { background: #FFF8F5; border-color: #572B12; transform: translateY(-1px); }
        .btn-print svg { width: 16px; height: 16px; fill: #572B12; flex-shrink: 0; }

        /* Billing grid */
        .billing-grid {
            display: grid; grid-template-columns: 1fr 1fr; gap: 24px;
            padding: 24px 32px; border-bottom: 1px solid #f0e8e0;
        }
        .bill-card .bill-label {
            font-size: 10px; font-weight: 700; letter-spacing: 0.08em;
            text-transform: uppercase; color: #865135; margin-bottom: 8px;
        }
        .bill-card .bill-name {
            font-size: 15px; font-weight: 700; color: #29170C; margin-bottom: 4px;
        }
        .bill-card .bill-detail {
            font-size: 13px; color: #51443E; line-height: 1.6;
        }

        @page {
            size: A4;
            margin: 8mm 10mm;
        }

        @media print {
            /* Hide all site chrome */
            .invoice-actions,
            .top-banner,
            .site-header,
            .footer-bar { display: none !important; }

            html, body {
                background: #fff !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                height: auto !important;
            }

            .invoice-page {
                padding: 0 !important;
                margin: 0 !important;
            }

            /* Remove shadows/radius that waste space */
            .invoice-card {
                box-shadow: none !important;
                border-radius: 0 !important;
                width: 100% !important;
                page-break-inside: avoid;
                break-inside: avoid;
            }

            /* Tighten vertical spacing throughout */
            .invoice-header     { padding: 14px 20px !important; }
            .invoice-header h1  { font-size: 20px !important; }
            .billing-grid       { padding: 10px 20px !important; gap: 16px !important; }
            .bill-card .bill-name  { font-size: 13px !important; }
            .bill-card .bill-detail{ font-size: 11px !important; }
            .invoice-table-wrap { padding: 0 8px !important; }
            .invoice-table th,
            .invoice-table td   { padding: 5px 8px !important; font-size: 11px !important; }
            .product-name       { font-size: 11px !important; }
            .category-badge     { font-size: 10px !important; }
            .summary-section    {
                padding: 10px 20px !important;
                gap: 20px !important;
                flex-direction: row !important;
                align-items: flex-end !important;
            }
            .payment-info-wrap  { max-width: 50% !important; flex: 1 !important; }
            .totals-wrap        { min-width: 0 !important; flex: 1 !important; }
            .payment-card       { padding: 8px 10px !important; }
            .payment-card-label { font-size: 11px !important; }
            .payment-row        { font-size: 11px !important; }
            .payment-note       { font-size: 10px !important; }
            .totals-row         { font-size: 12px !important; }
            .due-label,
            .due-amount         { font-size: 13px !important; }
            .signature-section  { padding: 8px 20px 12px !important; }
            .signature-name     { font-size: 13px !important; }
            .signature-title    { font-size: 11px !important; }

            /* Prevent any section from breaking across pages */
            .billing-grid,
            .invoice-table-wrap,
            .summary-section,
            .signature-section  {
                page-break-inside: avoid;
                break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <?php include '../header.php'; ?>

    <div class="invoice-page">

        <!-- INVOICE CARD -->
        <div class="invoice-card">

            <!-- HEADER BANNER -->
            <div class="invoice-header">
                <div class="invoice-header-bg">
                    <div class="geo-brown"></div>
                    <div class="geo-brown-overlay"></div>
                    <div class="geo-green"></div>
                </div>
                <div class="invoice-header-left">
                    <h1>Invoice</h1>
                    <span class="order-id" id="invoiceOrderId">CleckBasket Order</span>
                </div>
                <div class="invoice-header-right">
                    <img src="/cleckbasket/assets/images/logo.png" alt="CleckBasket" class="brand-logo" />
                    <span class="brand-tagline">Shop Local &bull; Eat Fresh</span>
                    <span class="invoice-date">Invoice Date: <?= $invoiceDate ?></span>
                </div>
            </div>

            <!-- BODY -->
            <div class="invoice-body">

                <!-- BILLING INFO -->
                <div class="billing-grid">
                    <div class="bill-card">
                        <div class="bill-label">Billed To</div>
                        <div class="bill-name"><?= $customerName ?></div>
                        <?php if ($customerEmail): ?>
                            <div class="bill-detail"><?= $customerEmail ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="bill-card">
                        <div class="bill-label">From</div>
                        <div class="bill-name">CleckBasket</div>
                        <div class="bill-detail">Local Market Collection Service<br>support@cleckbasket.com</div>
                    </div>
                </div>

                <!-- TABLE -->
                <div class="invoice-table-wrap">
                    <table class="invoice-table">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Product Name &amp; Category</th>
                                <th>Price</th>
                                <th>Qty</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody id="invoiceItems"></tbody>
                    </table>
                </div>

                <!-- SUMMARY -->
                <div class="summary-section">
                    <div class="payment-info-wrap">
                        <div class="payment-card">
                            <div class="payment-card-label">Payment Info</div>
                            <div class="payment-row">
                                <svg viewBox="0 0 20 20" fill="#401E09"><path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4zM18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h2a1 1 0 010 2H5a1 1 0 01-1-1z"/></svg>
                                <span>Secure payment via PayPal</span>
                            </div>
                            <div class="payment-row">
                                <svg viewBox="0 0 20 20" fill="#401E09"><path d="M10 2a6 6 0 00-6 6v1H3a1 1 0 00-.994.89l-1 9A1 1 0 002 20h16a1 1 0 00.994-1.11l-1-9A1 1 0 0017 9h-1V8a6 6 0 00-6-6zm0 2a4 4 0 014 4v1H6V8a4 4 0 014-4z"/></svg>
                                <span id="invoicePaymentMethod">Collection order</span>
                            </div>
                        </div>
                        <p class="payment-note">Thank you for supporting local traders.<br>Your order will be ready at your selected collection slot.</p>
                    </div>

                    <div class="totals-wrap">
                        <div class="totals-lines">
                            <div class="totals-row">
                                <span class="label">Subtotal</span>
                                <span class="value" id="invoiceSubtotal">&pound;0.00</span>
                            </div>
                            <div class="totals-row">
                                <span class="label">Tax (5%)</span>
                                <span class="value" id="invoiceTax">&pound;0.00</span>
                            </div>
                            <div class="totals-row discount">
                                <span class="label">Discount</span>
                                <span class="value" id="invoiceDiscount">-&pound;0.00</span>
                            </div>
                        </div>
                        <div class="total-due-bar">
                            <span class="due-label">Total Due</span>
                            <span class="due-amount" id="invoiceTotal">&pound;0.00</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- SIGNATURE -->
            <div class="signature-section">
                <div class="signature-block">
                    <div class="signature-name">CleckBasket</div>
                    <div class="signature-title">Authorized Signature</div>
                </div>
            </div>

        </div>

        <!-- ACTION BUTTONS -->
        <div class="invoice-actions">
            <button class="btn-download" onclick="window.print()">
                <svg viewBox="0 0 16 16"><path d="M8 12l-4-4h2.5V2h3v6H12L8 12z"/><path d="M14 14H2v-2h12v2z"/></svg>
                Download PDF
            </button>
            <button class="btn-print" onclick="window.print()">
                <svg viewBox="0 0 20 18"><path d="M16 0H4v4h12V0zM17 6H3a2 2 0 00-2 2v5h4v4h10v-4h4V8a2 2 0 00-2-2zm-3 9H6v-4h8v4zm3-7a1 1 0 110-2 1 1 0 010 2z"/></svg>
                Print
            </button>
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const tableBody   = document.getElementById('invoiceItems');
            const subtotalEl  = document.getElementById('invoiceSubtotal');
            const taxEl       = document.getElementById('invoiceTax');
            const discountEl  = document.getElementById('invoiceDiscount');
            const totalEl     = document.getElementById('invoiceTotal');
            const orderIdEl   = document.getElementById('invoiceOrderId');

            if (!tableBody || !subtotalEl || !taxEl || !discountEl || !totalEl) return;

            // Populate order ID if stored
            const storedOrderId = localStorage.getItem('last_order_id');
            if (orderIdEl && storedOrderId) {
                orderIdEl.textContent = 'Order #' + storedOrderId;
            }

            const items = JSON.parse(localStorage.getItem('invoice_items') || '[]');
            tableBody.innerHTML = '';

            if (!items.length) {
                const emptyRow = document.createElement('tr');
                emptyRow.innerHTML = '<td colspan="5" style="text-align:center;padding:20px;color:#888;">No items found for this invoice.</td>';
                tableBody.appendChild(emptyRow);
                subtotalEl.textContent = '£0.00';
                taxEl.textContent      = '£0.00';
                discountEl.textContent = '-£0.00';
                totalEl.textContent    = '£0.00';
                return;
            }

            let subtotal = 0;
            items.forEach((item, index) => {
                const price     = Number(item.price)    || 0;
                const qty       = Number(item.quantity) || 1;
                const lineTotal = price * qty;
                subtotal += lineTotal;

                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${String(index + 1).padStart(2, '0')}</td>
                    <td>
                        <div class="product-name">${escapeHtml(item.name || 'Item')}</div>
                        <span class="category-badge">${qty} Qty</span>
                    </td>
                    <td>${formatMoney(price)}</td>
                    <td>${qty} Units</td>
                    <td>${formatMoney(lineTotal)}</td>
                `;
                tableBody.appendChild(row);
            });

            const tax      = subtotal * 0.05;
            const discount = 0;
            const total    = subtotal + tax - discount;

            subtotalEl.textContent = formatMoney(subtotal);
            taxEl.textContent      = formatMoney(tax);
            discountEl.textContent = `-${formatMoney(discount)}`;
            totalEl.textContent    = formatMoney(total);
        });

        function formatMoney(value) {
            return `£${Number(value).toFixed(2)}`;
        }

        function escapeHtml(value) {
            const div = document.createElement('div');
            div.textContent = value;
            return div.innerHTML;
        }
    </script>

    <?php include '../footer.php'; ?>
</body>
</html>
