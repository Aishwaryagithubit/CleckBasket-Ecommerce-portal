<?php /* Product Detail — layout updated to match provided reference */ ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Fresh Blueberries - Cleck Basket</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&family=Inter:wght@400;500;600&family=Roboto:wght@400&display=swap" rel="stylesheet" />
  <style>
    *,
    *::before,
    *::after {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    :root {
      --text: #1E1E1E;
      --muted: #757575;
      --border: #F1DFDF;
      --bg: #FFFFFF;
      --card: #FFFFFF;
      --primary: #5A331C;
      --accent: #CFF7D3;
      --accent-text: #02542D;
      --star: #2C2C2C;
    }

    body {
      font-family: 'Inter', sans-serif;
      color: var(--text);
      background: var(--bg);
      line-height: 1.4;
    }

    img {
      max-width: 100%;
      display: block;
    }

    a {
      color: inherit;
      text-decoration: none;
    }

    button {
      font: inherit;
      border: 0;
      background: transparent;
      cursor: pointer;
    }

    .wrapper {
      width: min(1220px, calc(100% - 38px));
      margin: 34px auto 48px;
    }

    .layout {
      display: grid;
      grid-template-columns: minmax(0, 1fr) 280px;
      gap: 36px;
      margin-top: 8px;
    }

    .main {
      display: grid;
      grid-template-columns: minmax(250px, 1fr) minmax(300px, 1fr);
      gap: 34px;
      align-items: center;
    }

    .product-image-wrap {
      min-height: 320px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .product-image {
      width: min(100%, 360px);
      max-height: 360px;
      object-fit: contain;
      filter: drop-shadow(0 24px 30px rgba(0, 0, 0, 0.12));
      animation: floatin .45s ease;
    }

    @keyframes floatin {
      from {
        opacity: 0;
        transform: translateY(18px) scale(0.96);
      }

      to {
        opacity: 1;
        transform: translateY(0) scale(1);
      }
    }

    .details h1 {
      font-family: 'DM Sans', sans-serif;
      font-size: 36px;
      font-weight: 400;
      line-height: 1.4;
      margin-bottom: 11px;
      color: #000000;
      letter-spacing: -0.04em;
    }

    .pill {
      display: inline-flex;
      border-radius: 8px;
      background: var(--accent);
      color: var(--accent-text);
      font-size: 16px;
      font-weight: 400;
      font-family: 'Inter', sans-serif;
      padding: 8px;
      margin-bottom: 24px;
      line-height: 1;
    }

    .rating {
      display: flex;
      align-items: center;
      gap: 4px;
      margin-bottom: 10px;
      font-family: 'Inter', sans-serif;
      color: var(--muted);
      font-size: 16px;
    }

    .stars {
      color: var(--star);
      letter-spacing: 2px;
      font-size: 20px;
    }

    .price {
      font-family: 'DM Sans', sans-serif;
      font-size: 48px;
      font-weight: 700;
      line-height: 1.4;
      margin: 10px 0 4px;
      color: #000000;
      letter-spacing: -0.04em;
    }

    .price small {
      font-size: 0.5em;
      font-weight: 700;
      letter-spacing: -0.04em;
    }

    .desc {
      color: var(--muted);
      max-width: 504px;
      margin: 8px 0 18px;
      font-family: 'DM Sans', sans-serif;
      font-size: 16px;
      line-height: 1.4;
    }

    .size-label {
      color: var(--muted);
      font-family: 'DM Sans', sans-serif;
      font-size: 16px;
      margin-bottom: 8px;
    }

    .sizes {
      display: flex;
      gap: 13px;
      margin-bottom: 24px;
    }

    .sizes button {
      border: 1px solid var(--border);
      border-radius: 4px;
      width: 37px;
      height: 35px;
      color: var(--muted);
      font-weight: 400;
      font-size: 16px;
      font-family: 'DM Sans', sans-serif;
      transition: 0.2s ease;
      background: #FFFFFF;
    }

    .sizes button.active {
      background: var(--primary);
      border-color: var(--border);
      color: #FFFFFF;
    }

    .cta-row {
      display: flex;
      flex-wrap: wrap;
      gap: 17px;
      margin-top: 10px;
    }

    .btn {
      border-radius: 8px;
      height: 40px;
      min-width: 171px;
      font-weight: 400;
      font-size: 16px;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: transform .2s ease, box-shadow .2s ease;
    }

    .btn:hover {
      transform: translateY(-1px);
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    }

    .btn-cart {
      background: var(--primary);
      color: #F5F5F5;
      font-family: 'DM Sans', sans-serif;
      border: 1px solid #2C2C2C;
    }

    .btn-wish {
      background: #E9FCE0;
      color: #5A331C;
      font-family: 'Inter', sans-serif;
      border: none;
    }

    .btn-feedback {
      background: #F4F1ED;
      color: #5A331C;
      font-family: 'Inter', sans-serif;
      border: 1px solid #E3DDDD;
    }

    .sidebar {
      display: flex;
      flex-direction: column;
      gap: 24px;
    }

    .card {
      background: var(--card);
      border: 1px solid #E3DDDD;
      border-radius: 17px;
      padding: 23px 22px;
    }

    .card h3 {
      font-family: 'DM Sans', sans-serif;
      font-size: 16px;
      margin-bottom: 14px;
      font-weight: 700;
      color: #380000;
    }

    .cat-item,
    .mini-product {
      border: 1px solid rgba(241, 231, 231, 0.5);
      border-radius: 13px;
      min-height: 48px;
      display: flex;
      align-items: center;
      padding: 0 16px;
      gap: 16px;
      margin-bottom: 11px;
      background: #FFFFFF;
      font-weight: 400;
      font-size: 16px;
      color: #380000;
      font-family: 'DM Sans', sans-serif;
    }

    .mini-product {
      min-height: 84px;
      padding: 15px 11px;
      gap: 8px;
      align-items: flex-start;
      flex-direction: row;
    }

    .mini-prod-info {
      display: flex;
      flex-direction: column;
      gap: 4px;
    }

    .mini-prod-info .prod-title {
      font-family: 'DM Sans', sans-serif;
      font-weight: 700;
      color: #5A331C;
      font-size: 16px;
    }

    .mini-prod-info .prod-price {
      font-family: 'DM Sans', sans-serif;
      font-weight: 700;
      color: #575757;
      font-size: 16px;
    }

    .cat-item img {
      width: 34px;
      height: 34px;
      object-fit: contain;
    }

    .mini-product img {
      width: 55px;
      height: 55px;
      object-fit: contain;
      border-radius: 10px;
    }

    .latest {
      margin-top: 60px;
    }

    .latest h2 {
      font-family: 'Inter', sans-serif;
      font-weight: 600;
      font-size: 24px;
      margin-bottom: 48px;
      color: #1E1E1E;
      letter-spacing: -0.02em;
    }

    .review-grid {
      display: flex;
      flex-wrap: wrap;
      gap: 48px;
    }

    .review {
      background: #FFFFFF;
      border: 1px solid #D9D9D9;
      border-radius: 8px;
      padding: 24px;
      min-width: 240px;
      width: calc((100% - 96px) / 3);
      display: flex;
      flex-direction: column;
      gap: 24px;
    }

    .review .stars {
      display: flex;
      gap: 4px;
      font-size: 20px;
      margin-bottom: 0px;
      color: #F5A623;
      letter-spacing: 0;
    }

    .review-body {
      display: flex;
      flex-direction: column;
      gap: 4px;
      flex-grow: 1;
    }

    .review h4 {
      font-family: 'Inter', sans-serif;
      font-weight: 600;
      font-size: 24px;
      line-height: 1.2;
      color: #1E1E1E;
      letter-spacing: -0.02em;
      margin-bottom: 0px;
    }

    .review p {
      font-family: 'Inter', sans-serif;
      font-weight: 400;
      font-size: 16px;
      color: #1E1E1E;
      line-height: 1.4;
      margin-bottom: 0px;
    }

    .review-user {
      display: flex;
      align-items: flex-start;
      gap: 12px;
      margin-top: auto;
    }

    .review-user img {
      width: 40px;
      height: 40px;
      border-radius: 9999px;
      object-fit: cover;
    }

    .review-user div strong {
      font-family: 'Inter', sans-serif;
      font-weight: 600;
      font-size: 16px;
      color: #757575;
      display: block;
      line-height: 1.4;
    }

    .review-user div span {
      font-family: 'Inter', sans-serif;
      font-weight: 400;
      font-size: 16px;
      color: #B3B3B3;
      line-height: 1.4;
    }

    @media (max-width: 1150px) {
      .layout {
        grid-template-columns: 1fr;
      }

      .sidebar {
        display: grid;
        grid-template-columns: 1fr 1fr;
      }
    }

    @media (max-width: 900px) {
      .main {
        grid-template-columns: 1fr;
        gap: 18px;
      }

      .details h1 {
        font-size: 2.35rem;
      }

      .cta-row {
        flex-direction: column;
      }

      .btn {
        width: 100%;
      }

      .review-grid {
        grid-template-columns: 1fr;
      }
    }

    @media (max-width: 620px) {
      .wrapper {
        width: calc(100% - 22px);
        margin-top: 14px;
      }

      .sidebar {
        grid-template-columns: 1fr;
      }

      .price {
        font-size: 2.4rem;
      }
    }
  </style>
</head>

<body>
  <?php include __DIR__ . '/../header.php'; ?>

  <div class="wrapper">
    <section class="layout" aria-label="Product detail layout">
      <div>
        <div class="main">
          <div class="product-image-wrap">
            <img class="product-image" id="main-product-image" src="../../assets/images/passionfruit.png" alt="Fresh Blueberries" />
          </div>

          <div class="details">
            <h1>Fresh Blueberries</h1>
            <span class="pill">Fruit</span>

            <div class="rating" aria-label="Product rating">
              <span class="stars">★★★★☆</span>
              <span>(221)</span>
            </div>

            <div class="price">&#163;5<small>.60 / lb</small></div>

            <p class="desc">
              Fresh blueberries from Cleckheaton, Huddersfield - sweet, juicy, and
              perfect for everyday snacking.
            </p>

            <p class="size-label">Size/Weight</p>
            <div class="sizes" id="size-options">
              <button data-size="1lb">1lb</button>
              <button data-size="2lb">2lb</button>
              <button class="active" data-size="3lb">3lb</button>
              <button data-size="4lb">4lb</button>
            </div>

            <div class="cta-row">
              <button class="btn btn-cart" id="add-cart-btn">Add To Cart</button>
              <button class="btn btn-wish" id="wishlist-btn">Add To Wishlist</button>
              <a class="btn btn-feedback" href="/cleckbasket/includes/pages/product_feedback.php">Product Feedback</a>
            </div>

          </div>
        </div>

        <section class="latest" aria-label="Latest reviews">
          <h2>Latest reviews</h2>
          <div class="review-grid">
            <article class="review">
              <div class="stars">
                <span>★</span><span>★</span><span>★</span><span>★</span><span>★</span>
              </div>
              <div class="review-body">
                <h4>Absolutely love these!</h4>
                <p>The blueberries arrived super fresh and were perfectly sweet. Great size too — not the tiny ones you get at supermarkets. Will definitely be ordering again.</p>
              </div>
              <div class="review-user">
                <img src="../../assets/images/about.png" alt="Sarah M." />
                <div>
                  <strong>Sarah M.</strong>
                  <span>12 May 2025</span>
                </div>
              </div>
            </article>

            <article class="review">
              <div class="stars">
                <span>★</span><span>★</span><span>★</span><span>★</span><span>☆</span>
              </div>
              <div class="review-body">
                <h4>Fresh and great value</h4>
                <p>Really happy with the quality. Came well packaged and tasted great. Took one star off only because a small handful were a bit soft, but overall brilliant.</p>
              </div>
              <div class="review-user">
                <img src="../../assets/images/about.png" alt="James R." />
                <div>
                  <strong>James R.</strong>
                  <span>28 April 2025</span>
                </div>
              </div>
            </article>

            <article class="review">
              <div class="stars">
                <span>★</span><span>★</span><span>★</span><span>★</span><span>★</span>
              </div>
              <div class="review-body">
                <h4>Best local blueberries I've had</h4>
                <p>Proper local taste — you can tell these aren't shipped from miles away. My kids demolished them in a day. Buying the 3lb pack again next week!</p>
              </div>
              <div class="review-user">
                <img src="../../assets/images/about.png" alt="Priya K." />
                <div>
                  <strong>Priya K.</strong>
                  <span>3 May 2025</span>
                </div>
              </div>
            </article>
          </div>
        </section>
      </div>

      <aside class="sidebar" aria-label="Sidebar">
        <section class="card">
          <h3>Shop By Category</h3>
          <a class="cat-item" href="#"><img src="../../assets/images/buffalomeat.png" alt="Butcher" /> Butcher</a>
          <a class="cat-item" href="#"><img src="../../assets/images/greengrocers.png" alt="Green Grocer" /> Green Grocer</a>
          <a class="cat-item" href="#"><img src="../../assets/images/bakerys.png" alt="Bakery" /> Bakery</a>
          <a class="cat-item" href="#"><img src="../../assets/images/fishmongers.png" alt="Fish Monger" /> Fish Monger</a>
          <a class="cat-item" href="#"><img src="../../assets/images/nutritions.png" alt="Delicatessen" /> Delicatessen</a>
        </section>

        <section class="card">
          <h3>New Products</h3>
          <a class="mini-product" href="#">
            <img src="../../assets/images/passionfruit.png" alt="Fruit Cupcakes" />
            <div class="mini-prod-info">
              <span class="prod-title">Fruit Cupcakes</span>
              <span class="prod-price">&#163;3.50</span>
            </div>
          </a>
          <a class="mini-product" href="#">
            <img src="../../assets/images/beefs.png" alt="NY Strip Steak" />
            <div class="mini-prod-info">
              <span class="prod-title">NY Strip Steak</span>
              <span class="prod-price">&#163;11.75</span>
            </div>
          </a>
        </section>
      </aside>
    </section>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const sizeWrap = document.getElementById('size-options');
      const cartBtn = document.getElementById('add-cart-btn');

      if (sizeWrap) {
        sizeWrap.addEventListener('click', (event) => {
          const btn = event.target.closest('button[data-size]');
          if (!btn) {
            return;
          }
          sizeWrap.querySelectorAll('button').forEach((b) => b.classList.remove('active'));
          btn.classList.add('active');
        });
      }

      if (cartBtn) {
        cartBtn.addEventListener('click', () => {
          const product = getDetailProduct();
          if (!product) {
            return;
          }

          const cart = getCartItems();
          const existing = cart.find((item) => item.name === product.name);
          if (existing) {
            existing.quantity += 1;
          } else {
            cart.push({
              ...product,
              quantity: 1
            });
          }

          saveCartItems(cart);
          cartBtn.textContent = 'Added';
          setTimeout(() => {
            cartBtn.textContent = 'Add To Cart';
          }, 1300);
        });
      }
    });

    function getCartItems() {
      const cart = localStorage.getItem('cart');
      return cart ? JSON.parse(cart) : [];
    }

    function saveCartItems(cart) {
      localStorage.setItem('cart', JSON.stringify(cart));
    }

    function getDetailProduct() {
      const nameEl = document.querySelector('.details h1');
      const priceEl = document.querySelector('.details .price');
      const imgEl = document.querySelector('.product-image');

      if (!nameEl || !priceEl || !imgEl) {
        return null;
      }

      return {
        name: nameEl.textContent.trim(),
        price: parsePrice(priceEl.textContent),
        image: imgEl.src,
      };
    }

    function parsePrice(text) {
      if (!text) {
        return 0;
      }
      const value = parseFloat(text.replace(/[^0-9.]/g, ''));
      return Number.isFinite(value) ? value : 0;
    }
  </script>

  <?php include __DIR__ . '/../footer.php'; ?>
  <script src="/cleckbasket/assets/js/main.js" defer></script>
</body>

</html>