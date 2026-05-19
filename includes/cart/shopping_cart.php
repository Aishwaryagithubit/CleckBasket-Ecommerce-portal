<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$cartUserName = '';
if (!empty($_SESSION['full_name'])) {
    $cartUserName = $_SESSION['full_name'];
} elseif (!empty($_SESSION['user_name'])) {
    $cartUserName = $_SESSION['user_name'];
}
?>
<?php include('../header.php'); ?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Cart — Cleckbasket</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <style>
    /* ============================================================
       CSS VARIABLES & RESET
    ============================================================ */
    *,
    *::before,
    *::after {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    :root {
      --brown-primary: #5c3a21;
      --brown-hover: #4a2e1a;
      --bg-color: #f5f5f5;
      --text-dark: #222222;
      --text-mid: #666666;
      --text-muted: #999999;
      --border: #ececec;
      --white: #ffffff;
      --radius-lg: 12px;
      --radius-md: 8px;
    }

    body {
      font-family: 'Poppins', sans-serif;
      background: var(--bg-color);
      color: var(--text-dark);
      min-height: 100vh;
    }

    img {
      display: block;
      max-width: 100%;
    }

    button {
      cursor: pointer;
      font-family: inherit;
      border: none;
      outline: none;
    }

    /* ============================================================
       PAGE LAYOUT
    ============================================================ */
    .page-wrapper {
      max-width: 1200px;
      margin: 40px auto;
      padding: 0 20px;
      margin-bottom: 80px;
    }

    .cart-layout {
      display: grid;
      grid-template-columns: 1.6fr 1fr;
      gap: 50px;
      align-items: start;
    }

    /* ============================================================
       LEFT: CART ITEMS
    ============================================================ */
    .cart-left {
      display: flex;
      flex-direction: column;
      gap: 20px;
    }

    .cart-table-card {
      background: var(--white);
      border-radius: var(--radius-md);
      overflow: hidden;
      border: 1px solid var(--border);
    }

    .cart-header {
      display: grid;
      grid-template-columns: 45% 20% 20% 15%;
      padding: 20px 24px;
      font-size: 0.75rem;
      font-weight: 600;
      color: var(--text-muted);
      border-bottom: 1px solid var(--border);
      letter-spacing: 0.5px;
    }

    .cart-list {
      display: flex;
      flex-direction: column;
    }

    .cart-item {
      display: grid;
      grid-template-columns: 45% 20% 20% 15%;
      align-items: center;
      padding: 20px 24px;
      border-bottom: 1px solid var(--border);
    }

    .cart-item:last-child {
      border-bottom: none;
    }

    .cart-item__product {
      display: flex;
      align-items: center;
      gap: 20px;
    }

    .cart-item__img {
      width: 55px;
      height: 55px;
      object-fit: contain;
      border-radius: 4px;
    }

    .cart-item__name {
      font-size: 0.95rem;
      color: var(--text-mid);
    }

    .cart-item__price {
      font-size: 0.95rem;
      color: var(--text-muted);
    }

    /* Qty Selector */
    .qty-selector {
      display: flex;
      align-items: center;
      background: #fdfdfd;
      border: 1px solid var(--border);
      border-radius: 20px;
      width: 90px;
      justify-content: space-between;
      padding: 4px 10px;
    }

    .qty-btn {
      background: none;
      color: var(--text-muted);
      font-size: 1.1rem;
      line-height: 1;
      width: 24px;
      height: 24px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
    }

    .qty-btn:hover {
      background: #eee;
    }

    .qty-value {
      font-size: 0.9rem;
      font-weight: 500;
      color: var(--text-mid);
    }

    .cart-item__subtotal {
      font-size: 0.95rem;
      color: var(--text-muted);
    }

    .cart-actions-bottom {
      padding: 20px 24px;
      border-top: 1px solid var(--border);
    }

    .btn-shop-further {
      background: #f2f2f2;
      color: var(--text-dark);
      padding: 12px 28px;
      border-radius: 25px;
      font-size: 0.9rem;
      font-weight: 600;
      transition: background 0.2s;
    }

    .btn-shop-further:hover {
      background: #e2e2e2;
    }

    /* Forms under cart */
    .cart-forms {
      display: flex;
      gap: 20px;
      margin-top: 10px;
      align-items: stretch;
    }

    .coupon-wrap {
      flex: 1;
      display: flex;
      max-width: 400px;
    }

    .coupon-input {
      flex: 1;
      padding: 14px 20px;
      border: 1px solid var(--border);
      border-radius: var(--radius-md) 0 0 var(--radius-md);
      font-size: 0.9rem;
      background: var(--white);
      outline: none;
    }

    .btn-apply-coupon {
      background: var(--brown-primary);
      color: #fff;
      padding: 14px 30px;
      border-radius: 0 var(--radius-md) var(--radius-md) 0;
      font-size: 0.9rem;
      font-weight: 500;
      white-space: nowrap;
    }

    .btn-apply-coupon:hover {
      background: var(--brown-hover);
    }

    .paypal-box {
      margin-top: 10px;
      background: var(--white);
      border: 1px solid var(--border);
      border-radius: var(--radius-md);
      padding: 20px 24px;
      display: flex;
      align-items: center;
      gap: 20px;
      width: fit-content;
    }

    .paypal-box span {
      font-size: 1.1rem;
      font-weight: 600;
    }

    .paypal-box img {
      height: 24px;
      object-fit: contain;
    }

    /* ============================================================
       RIGHT: CART SUMMARY
    ============================================================ */
    .cart-right {
      display: flex;
      flex-direction: column;
      gap: 50px;
    }

    .right-section h3 {
      font-size: 1.1rem;
      font-weight: 600;
      margin-bottom: 20px;
    }

    /* Address */
    .address-header {
      display: flex;
      justify-content: space-between;
      align-items: baseline;
    }

    .change-address {
      font-size: 0.65rem;
      color: var(--text-muted);
      cursor: pointer;
    }

    .change-address:hover {
      text-decoration: underline;
    }
    .change-address a {
      text-decoration: none;
      color: var(--text-muted);
    }

    /* Pickup */
    .btn-pickup {
      background: #504440;
      color: #fff;
      width: 100%;
      padding: 18px 24px;
      border-radius: 30px;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 15px;
      font-size: 0.95rem;
    }

    .btn-pickup:hover {
      background: #3d3430;
    }

    /* Pickup Time Card */
    .cart-pickup-time-card {
      background: var(--white);
      border: 1px solid var(--border);
      border-radius: var(--radius-md);
      padding: 30px 24px;
    }

    .cart-pickup-time-card h3 {
      font-size: 1.1rem;
      font-weight: 600;
      margin-bottom: 20px;
      color: var(--text-dark);
    }

    .pickup-days,
    .pickup-times {
      display: flex;
      gap: 12px;
      margin-bottom: 16px;
    }

    .pickup-times {
      margin-bottom: 0;
    }

    .pickup-pill {
      background: var(--white);
      border: 1px solid var(--border);
      color: var(--text-mid);
      border-radius: 20px;
      padding: 10px 0;
      font-size: 0.85rem;
      font-weight: 600;
      cursor: pointer;
      flex: 1;
      text-align: center;
      transition: all 0.2s ease;
    }

    .pickup-pill:hover {
      background: #f9f9f9;
    }

    .pickup-pill.active {
      background: #10c910;
      /* Green matching the pic */
      color: var(--white);
      border-color: #10c910;
    }

    /* Cart Total Card */
    .cart-total-card {
      background: var(--white);
      border: 1px solid var(--border);
      border-radius: var(--radius-md);
      padding: 30px 24px;
    }

    .summary-row {
      display: flex;
      justify-content: space-between;
      margin-bottom: 24px;
      font-size: 0.95rem;
      color: var(--text-muted);
    }

    .summary-row span.val {
      color: var(--text-dark);
      font-weight: 600;
    }

    .summary-divider {
      border: none;
      border-top: 1px solid var(--border);
      margin: 20px 0;
    }

    .btn-checkout {
      width: max-content;
      margin: 0 auto;
      display: block;
      background: var(--brown-primary);
      color: #fff;
      padding: 12px 30px;
      border-radius: var(--radius-md);
      font-size: 0.9rem;
      font-weight: 500;
      margin-top: 10px;
    }

    .btn-checkout:hover {
      background: var(--brown-hover);
    }

    .checkout-toast {
      position: fixed;
      right: 24px;
      top: 24px;
      background: #1f1f1f;
      color: #ffffff;
      padding: 12px 16px;
      border-radius: 10px;
      box-shadow: 0 10px 24px rgba(0, 0, 0, 0.2);
      opacity: 0;
      transform: translateX(16px);
      transition: opacity 0.2s ease, transform 0.2s ease;
      z-index: 9999;
      pointer-events: none;
    }

    .checkout-toast.is-visible {
      opacity: 1;
      transform: translateX(0);
    }

    @media (max-width: 900px) {
      .cart-layout {
        grid-template-columns: 1fr;
        gap: 40px;
      }

      .cart-forms {
        flex-direction: column;
      }
    }

    @media (max-width: 600px) {
      .page-wrapper {
        padding: 0 4%;
      }

      .cart-header {
        display: none;
      }

      .cart-item {
        grid-template-columns: 1fr;
        gap: 15px;
        border-bottom: 2px solid var(--border);
        padding: 5% 4%;
      }

      .cart-pickup-time-card,
      .cart-total-card,
      .cart-table-card {
        padding: 5%;
      }

      .pickup-days,
      .pickup-times {
        flex-wrap: wrap;
        gap: 3%;
      }

      .pickup-pill {
        flex: 1 1 45%;
        font-size: 0.85rem;
        padding: 2.5em 0;
        margin-bottom: 2%;
      }

      .coupon-wrap {
        flex-direction: column;
        width: 100%;
        max-width: 100%;
      }

      .coupon-input {
        border-radius: var(--radius-md);
        margin-bottom: 10px;
        width: 100%;
      }

      .btn-apply-coupon {
        border-radius: var(--radius-md);
        width: 100%;
      }

      .paypal-box {
        width: 100%;
        justify-content: center;
      }

      .btn-pickup {
        font-size: 0.85rem;
        padding: 1em;
      }
      .change-address a{
        color: #333;
      }
    }
  </style>
</head>

<body>

  <div class="checkout-toast" id="checkoutToast">Proceeding to Collection Slot Selection...</div>

  <main class="page-wrapper">
    <div class="cart-layout">

      <!-- LEFT COLUMN -->
      <div class="cart-left">
        <!-- Table Card -->
        <div class="cart-table-card">
          <div class="cart-header">
            <div>PRODUCT</div>
            <div>PRICE</div>
            <div>QUANTITY</div>
            <div>SUBTOTAL</div>
          </div>

          <div id="cart-items-list" class="cart-list">
            <!-- Rendered by JS -->
          </div>

          <div class="cart-actions-bottom">
            <button class="btn-shop-further" onclick="window.location.href='/cleckbasket/includes/pages/shop.php';">Shop Further</button>
          </div>
        </div>

        <!-- Coupon -->
        <div class="cart-forms">
          <div class="coupon-wrap">
            <input type="text" class="coupon-input" id="coupon-input" placeholder="Have A Coupon Code?">
            <button class="btn-apply-coupon" id="coupon-btn">Apply Coupon</button>
          </div>
        </div>
          <div class="right-section cart-total-card">
          <h3>Cart Total</h3>
          <br>
          <div class="summary-row">
            <span>Sub Total :</span>
            <span class="val" id="summary-subtotal">$106.00</span>
          </div>
          <div class="summary-row">
            <span>Shipping :</span>
            <span class="val" id="summary-delivery">$0.00</span>
          </div>
          <div class="summary-divider"></div>
          <div class="summary-row">
            <span>Total :</span>
            <span class="val" id="summary-grand-total">$106.00</span>
          </div>

          <button class="btn-checkout" id="btn-checkout">Proceed to Checkout</button>
        </div>

        <!-- PayPal -->
        <div class="paypal-box">
          <span>Pay with PayPal</span>
          <img src="https://upload.wikimedia.org/wikipedia/commons/b/b5/PayPal.svg" alt="PayPal">
        </div>
      </div>

      <!-- RIGHT COLUMN -->
      <div class="cart-right">

        <div class="right-section">
          <div class="address-header" style="margin-bottom: 12px;">
            <h3>Is This Your Address?</h3>
            <span class="change-address"><a href="../pages/profile.php">Change Address</a></span>
          </div>
          <div class="address-content"
            style="background: #fdfdfd; border: 1px solid var(--border); padding: 15px; border-radius: var(--radius-md); color: var(--text-mid); font-size: 0.95rem; line-height: 1.6;">
            <?php if ($cartUserName): ?>
                <strong><?= htmlspecialchars($cartUserName) ?></strong><br>
            <?php endif; ?>
            Collection only — Cleckheaton Market, BD19 3RH
          </div>
        </div>

        <div class="right-section">
          <h3>Self Pick-up Outlets</h3>
          <button class="btn-pickup">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M9 18h6" />
              <path d="M10 14h4" />
              <path d="M12 10v4" />
              <circle cx="12" cy="12" r="10" />
            </svg>
            Cleckheaton Central Station
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="6 9 12 15 18 9" />
            </svg>
          </button>
        </div>

        

      

      </div>

    </div>
  </main>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const list = document.getElementById('cart-items-list');
      if (!list) {
        return;
      }

      const checkoutBtn = document.getElementById('btn-checkout');
      if (checkoutBtn) {
        checkoutBtn.addEventListener('click', () => {
          const toast = document.getElementById('checkoutToast');
          if (toast) {
            toast.classList.add('is-visible');
          }

          const cartItems = getCartItems();
          localStorage.setItem('invoice_items', JSON.stringify(cartItems));
          saveCartItems([]);
          localStorage.removeItem('coupon_code');

          setTimeout(() => {
            window.location.href = '/cleckbasket/includes/pages/collection.php';
          }, 1200);
        });
      }

      list.addEventListener('click', (event) => {
        const btn = event.target.closest('.qty-btn');
        if (!btn) {
          return;
        }

        const name = btn.dataset.name;
        const action = btn.dataset.action;
        if (!name || !action) {
          return;
        }

        const cart = getCartItems();
        const item = cart.find((entry) => entry.name === name);
        if (!item) {
          return;
        }

        if (action === 'inc') {
          item.quantity += 1;
        } else if (action === 'dec') {
          item.quantity = Math.max(0, item.quantity - 1);
          if (item.quantity === 0) {
            const index = cart.findIndex((entry) => entry.name === name);
            cart.splice(index, 1);
          }
        }

        saveCartItems(cart);
        renderCart();
      });

      const couponBtn = document.getElementById('coupon-btn');
      if (couponBtn) {
        couponBtn.addEventListener('click', (event) => {
          event.preventDefault();
          applyCoupon();
        });
      }

      renderCart();
    });

    function getCartItems() {
      const cart = localStorage.getItem('cart');
      return cart ? JSON.parse(cart) : [];
    }

    function saveCartItems(cart) {
      localStorage.setItem('cart', JSON.stringify(cart));
    }

    function renderCart() {
      const list = document.getElementById('cart-items-list');
      const cart = getCartItems();
      list.innerHTML = '';

      if (cart.length === 0) {
        const empty = document.createElement('div');
        empty.className = 'cart-item';
        empty.textContent = 'Your cart is empty.';
        list.appendChild(empty);
        updateSummary(0);
        return;
      }

      cart.forEach((item) => {
        const sub = item.price * item.quantity;
        const row = document.createElement('div');
        row.className = 'cart-item';
        row.innerHTML = `
          <div class="cart-item__product">
            <img class="cart-item__img" src="${item.image}" alt="${item.name}" onerror="this.src='https://cdn-icons-png.flaticon.com/128/3143/3143645.png'">
            <span class="cart-item__name">${item.name}</span>
          </div>
          <div class="cart-item__price">${formatPriceSubscript(item.price)}</div>

          <div class="qty-selector">
             <button class="qty-btn" data-name="${item.name}" data-action="dec">−</button>
             <span class="qty-value">${item.quantity}</span>
             <button class="qty-btn" data-name="${item.name}" data-action="inc">+</button>
          </div>

          <div class="cart-item__subtotal">${formatPriceSubscript(sub)}</div>
        `;
        list.appendChild(row);
      });

      const subtotal = cart.reduce(
        (acc, item) => acc + item.price * item.quantity,
        0,
      );
      updateSummary(subtotal);
    }

    function updateSummary(subtotal) {
      const delivery = subtotal > 0 ? 0 : 0;
      const discount = getCouponDiscount(subtotal);
      const total = Math.max(0, subtotal + delivery - discount);

      const subtotalEl = document.getElementById('summary-subtotal');
      const deliveryEl = document.getElementById('summary-delivery');
      const totalEl = document.getElementById('summary-grand-total');

      if (subtotalEl) {
        subtotalEl.textContent = formatPriceTotal(subtotal);
      }
      if (deliveryEl) {
        deliveryEl.textContent = formatPriceTotal(delivery);
      }
      if (totalEl) {
        totalEl.textContent = formatPriceTotal(total);
      }
    }

    function getCouponDiscount(subtotal) {
      const saved = localStorage.getItem('coupon_code') || '';
      if (!saved) {
        return 0;
      }

      if (saved.toUpperCase() === 'SAVE10') {
        return Math.min(10, subtotal);
      }

      return 0;
    }

    function applyCoupon() {
      const input = document.getElementById('coupon-input');
      if (!input) {
        return;
      }

      const code = input.value.trim().toUpperCase();
      if (!code) {
        return;
      }

      if (code === 'SAVE10') {
        localStorage.setItem('coupon_code', code);
      } else {
        localStorage.removeItem('coupon_code');
      }

      renderCart();
    }

    function formatPriceSubscript(value) {
      const fixed = Number(value).toFixed(2);
      const parts = fixed.split('.');
      return `£${parts[0]}<span style="font-size:0.7em;">.${parts[1]}</span>`;
    }

    function formatPriceTotal(value) {
      return `£${Number(value).toFixed(2)}`;
    }
  </script>


</body>

</html>

<?php include('../footer.php'); ?>