<?php /* Invoice Page — Figma Layout */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Invoice - CleckBasket</title>
    <link rel="stylesheet" href="../../assets/css/invoice.css" />
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
                    <span class="order-id">Order #CB-88294</span>
                </div>
                <div class="invoice-header-right">
                    <img src="/cleckbasket/assets/images/logo.png" alt="CleckBasket" class="brand-logo" />
                    <span class="brand-tagline">Shop Local • Eat Fresh</span>
                    <span class="invoice-date">Invoice Date: Oct 24, 2024</span>
                </div>
            </div>

            <!-- BODY -->
            <div class="invoice-body">
                     quare, Fresh Port<br>Oregon, OR 97035</div>
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
                                <th>Servings</th>
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
                                <span>Central Organic Bank - Branch 02</span>
                            </div>
                            <div class="payment-row">
                                <svg viewBox="0 0 20 16" fill="#401E09"><path d="M18 0H2a2 2 0 00-2 2v12a2 2 0 002 2h16a2 2 0 002-2V2a2 2 0 00-2-2zM2 2h16v2H2V2zm16 12H2V6h16v8z"/></svg>
                                <span>IBAN: USB8 9201 2231 0044</span>
                            </div>
                        </div>
                        <p class="payment-note">Note: All organic produce is subject to seasonal availability.<br>Please pay within 15 days of receiving this invoice.</p>
                    </div>

                    <div class="totals-wrap">
                        <div class="totals-lines">
                            <div class="totals-row">
                                <span class="label">Subtotal</span>
                                <span class="value" id="invoiceSubtotal">$0.00</span>
                            </div>
                            <div class="totals-row">
                                <span class="label">Tax (5%)</span>
                                <span class="value" id="invoiceTax">$0.00</span>
                            </div>
                            <div class="totals-row discount">
                                <span class="label">Discount</span>
                                <span class="value" id="invoiceDiscount">-$0.00</span>
                            </div>
                        </div>
                        <div class="total-due-bar">
                            <span class="due-label">Total Due</span>
                            <span class="due-amount" id="invoiceTotal">$0.00</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- SIGNATURE -->
            <div class="signature-section">
                <div class="signature-block">
                    <div class="signature-line">
                        <img src="/cleckbasket/assets/images/Avatar.png" alt="Signature" />
                    </div>
                    <div class="signature-name">John Doe</div>
                    <div class="signature-title">Authorized Signature</div>
                </div>
            </div>

        </div>

        <!-- ACTION BUTTONS -->
        <div class="invoice-actions">
            <button class="btn-download" onclick="window.print()">
                <svg viewBox="0 0 16 16"><path d="M8 12l-4-4h2.5V2h3v6H12L8 12z"/><path d="M14 14H2v-2h12v2z"/></svg>
                Download Invoice
            </button>
            <button class="btn-print" onclick="window.print()">
                <svg viewBox="0 0 20 18"><path d="M16 0H4v4h12V0zM17 6H3a2 2 0 00-2 2v5h4v4h10v-4h4V8a2 2 0 00-2-2zm-3 9H6v-4h8v4zm3-7a1 1 0 110-2 1 1 0 010 2z"/></svg>
                Print
            </button>
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const customerNameEl = document.getElementById('invoiceCustomerName');
            const sellerNameEl = document.getElementById('invoiceSellerName');
            const sellerTaglineEl = document.getElementById('invoiceSellerTagline');
            const tableBody = document.getElementById('invoiceItems');
            const subtotalEl = document.getElementById('invoiceSubtotal');
            const taxEl = document.getElementById('invoiceTax');
            const discountEl = document.getElementById('invoiceDiscount');
            const totalEl = document.getElementById('invoiceTotal');

            if (!tableBody || !subtotalEl || !taxEl || !discountEl || !totalEl) {
                return;
            }

            const storedCustomerName = localStorage.getItem('user_name') || localStorage.getItem('customer_name');
            if (customerNameEl && storedCustomerName) {
                customerNameEl.textContent = storedCustomerName;
            }

            if (sellerNameEl && storedCustomerName) {
                sellerNameEl.textContent = 'Checkout Basket Headquarter';
            }

            if (sellerTaglineEl && storedCustomerName) {
                sellerTaglineEl.textContent = 'Checkout Basket Headquarter';
            }

            const items = JSON.parse(localStorage.getItem('invoice_items') || '[]');
            tableBody.innerHTML = '';

            if (!items.length) {
                const emptyRow = document.createElement('tr');
                emptyRow.innerHTML = '<td colspan="5">No items found for this invoice.</td>';
                tableBody.appendChild(emptyRow);
                subtotalEl.textContent = '$0.00';
                taxEl.textContent = '$0.00';
                discountEl.textContent = '-$0.00';
                totalEl.textContent = '$0.00';
                return;
            }

            let subtotal = 0;
            items.forEach((item, index) => {
                const price = Number(item.price) || 0;
                const qty = Number(item.quantity) || 1;
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

            const taxRate = 0.05;
            const discount = 0;
            const tax = subtotal * taxRate;
            const total = subtotal + tax - discount;

            subtotalEl.textContent = formatMoney(subtotal);
            taxEl.textContent = formatMoney(tax);
            discountEl.textContent = `-${formatMoney(discount)}`;
            totalEl.textContent = formatMoney(total);
        });

        function formatMoney(value) {
            return `$${Number(value).toFixed(2)}`;
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
