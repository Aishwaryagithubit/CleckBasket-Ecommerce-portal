<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../backend/connect.php';

$product_id = (int)($_GET['id'] ?? 0);
if ($product_id <= 0) {
    header('Location: /cleckbasket/includes/pages/shop.php');
    exit;
}

$product      = null;
$reviews      = [];
$related      = [];
$avg_rating   = 0;
$review_count = 0;

$conn = getDBConnection();
if ($conn) {
    // ── Main product ──
    $sql  = "SELECT p.product_id, p.product_name, p.description, p.price,
                    p.stock_quantity, p.allergy_information, p.product_image,
                    p.product_category_id,
                    pc.category_type, s.shop_name
             FROM product p
             JOIN product_category pc ON p.product_category_id = pc.product_category_id
             JOIN shop s              ON p.shop_id             = s.shop_id
             WHERE p.product_id = :id";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ':id', $product_id);
    if (oci_execute($stmt)) {
        $row = oci_fetch_assoc($stmt);
        if ($row) {
            $product = [
                'id'          => (int)$row['PRODUCT_ID'],
                'name'        => $row['PRODUCT_NAME'],
                'description' => $row['DESCRIPTION'] ?? '',
                'price'       => (float)$row['PRICE'],
                'stock'       => (int)$row['STOCK_QUANTITY'],
                'allergy'     => $row['ALLERGY_INFORMATION'] ?? '',
                'image'       => $row['PRODUCT_IMAGE'],
                'category_id' => (int)$row['PRODUCT_CATEGORY_ID'],
                'category'    => $row['CATEGORY_TYPE'],
                'shop'        => $row['SHOP_NAME'],
            ];
        }
    }
    oci_free_statement($stmt);

    if (!$product) {
        oci_close($conn);
        header('Location: /cleckbasket/includes/pages/shop.php');
        exit;
    }

    // ── Reviews ──
    $sql2  = "SELECT r.review, r.review_rating, r.created_date,
                     u.firstname, u.lastname
              FROM review r
              LEFT JOIN users u ON r.user_id = u.user_id
              WHERE r.product_id = :id
              ORDER BY r.created_date DESC
              FETCH FIRST 6 ROWS ONLY";
    $stmt2 = oci_parse($conn, $sql2);
    oci_bind_by_name($stmt2, ':id', $product_id);
    if (oci_execute($stmt2)) {
        while ($row = oci_fetch_assoc($stmt2)) {
            $first = $row['FIRSTNAME'] ?? 'Anonymous';
            $last  = !empty($row['LASTNAME']) ? $row['LASTNAME'][0] . '.' : '';
            $reviews[] = [
                'text'   => $row['REVIEW'],
                'rating' => max(1, min(5, (int)$row['REVIEW_RATING'])),
                'date'   => $row['CREATED_DATE'],
                'name'   => trim($first . ' ' . $last),
            ];
        }
    }
    oci_free_statement($stmt2);

    if (!empty($reviews)) {
        $avg_rating   = round(array_sum(array_column($reviews, 'rating')) / count($reviews), 1);
        $review_count = count($reviews);
    }

    // ── Related products (same category) ──
    $cat_id = $product['category_id'];
    $sql3   = "SELECT product_id, product_name, price, product_image
               FROM product
               WHERE product_category_id = :cat AND product_id != :id
               ORDER BY product_id DESC
               FETCH FIRST 3 ROWS ONLY";
    $stmt3  = oci_parse($conn, $sql3);
    oci_bind_by_name($stmt3, ':cat', $cat_id);
    oci_bind_by_name($stmt3, ':id',  $product_id);
    if (oci_execute($stmt3)) {
        while ($row = oci_fetch_assoc($stmt3)) {
            $related[] = [
                'id'    => (int)$row['PRODUCT_ID'],
                'name'  => $row['PRODUCT_NAME'],
                'price' => (float)$row['PRICE'],
                'image' => $row['PRODUCT_IMAGE'],
            ];
        }
    }
    oci_free_statement($stmt3);
    oci_close($conn);
}

// Helper: render filled/empty stars
function starStr(float $r, int $max = 5): string {
    $full = (int)round($r);
    return str_repeat('★', $full) . str_repeat('☆', $max - $full);
}

// Category → shop URL map
$cat_links = [
    'butcher'     => '/cleckbasket/includes/pages/shop.php?cat=butcher',
    'greengrocer' => '/cleckbasket/includes/pages/shop.php?cat=greengrocer',
    'bakery'      => '/cleckbasket/includes/pages/shop.php?cat=bakery',
    'fishmonger'  => '/cleckbasket/includes/pages/shop.php?cat=fishmonger',
    'delicatessen'=> '/cleckbasket/includes/pages/shop.php?cat=delicatessen',
];
$cat_images = [
    'butcher'     => 'buffalomeat.png',
    'greengrocer' => 'greengrocers.png',
    'bakery'      => 'bakerys.png',
    'fishmonger'  => 'fishmongers.png',
    'delicatessen'=> 'nutritions.png',
];

$image_url = '/cleckbasket/assets/images/' . htmlspecialchars($product['image']);
$name_safe = htmlspecialchars($product['name']);
$desc_safe = $product['description']
    ? htmlspecialchars($product['description'])
    : 'Fresh, high-quality ' . $name_safe . ' from ' . htmlspecialchars($product['shop']) . ' — sourced locally and delivered with care.';

// ── CHANGE 1: Bakery discount variables ──
$is_bakery   = strtolower($product['category']) === 'bakery';
$final_price = $is_bakery ? round($product['price'] * 0.85, 2) : $product['price'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?php echo $name_safe; ?> - CleckBasket</title>
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

    img { max-width: 100%; display: block; }
    a   { color: inherit; text-decoration: none; }
    button { font: inherit; border: 0; background: transparent; cursor: pointer; }

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
      background: #f9f9f9;
      border-radius: 18px;
      position: relative; /* CHANGE 2: needed for badge positioning */
    }

    /* CHANGE 2: discount badge on image */
    .image-discount-badge {
      position: absolute;
      top: 14px;
      left: 14px;
      background: #e74c3c;
      color: #fff;
      font-size: 13px;
      font-weight: 700;
      padding: 5px 11px;
      border-radius: 20px;
      font-family: 'DM Sans', sans-serif;
      z-index: 2;
    }

    .product-image {
      width: min(100%, 360px);
      max-height: 360px;
      object-fit: contain;
      filter: drop-shadow(0 24px 30px rgba(0,0,0,.12));
      animation: floatin .45s ease;
    }

    @keyframes floatin {
      from { opacity: 0; transform: translateY(18px) scale(.96); }
      to   { opacity: 1; transform: translateY(0)   scale(1);   }
    }

    .details h1 {
      font-family: 'DM Sans', sans-serif;
      font-size: 36px;
      font-weight: 400;
      line-height: 1.4;
      margin-bottom: 11px;
      color: #000;
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

    .shop-label {
      display: block;
      font-size: 13px;
      color: var(--muted);
      margin-bottom: 14px;
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

    .stars { color: var(--star); letter-spacing: 2px; font-size: 20px; }

    .price {
      font-family: 'DM Sans', sans-serif;
      font-size: 48px;
      font-weight: 700;
      line-height: 1.4;
      margin: 10px 0 4px;
      color: #000;
      letter-spacing: -0.04em;
    }

    /* CHANGE 3: styles for strikethrough + badge */
    .price-was {
      font-family: 'DM Sans', sans-serif;
      font-size: 22px;
      font-weight: 400;
      color: #aaa;
      text-decoration: line-through;
      margin-left: 6px;
      vertical-align: middle;
    }

    .price-save-badge {
      display: inline-block;
      background: #e8f5e9;
      color: #2e7d32;
      font-size: 13px;
      font-weight: 700;
      padding: 4px 10px;
      border-radius: 20px;
      margin-left: 8px;
      vertical-align: middle;
      font-family: 'Inter', sans-serif;
    }

    .desc {
      color: var(--muted);
      max-width: 504px;
      margin: 8px 0 18px;
      font-family: 'DM Sans', sans-serif;
      font-size: 16px;
      line-height: 1.6;
    }

    .allergy-note {
      font-size: 13px;
      color: #c0392b;
      margin-bottom: 18px;
      font-style: italic;
    }

    .stock-note {
      font-size: 13px;
      color: var(--muted);
      margin-bottom: 18px;
    }

    .qty-label {
      color: var(--muted);
      font-family: 'DM Sans', sans-serif;
      font-size: 16px;
      margin-bottom: 8px;
    }

    .qty-row {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 24px;
    }

    .qty-btn {
      width: 37px; height: 35px;
      border: 1px solid var(--border);
      border-radius: 4px;
      font-size: 18px;
      color: var(--primary);
      background: #fff;
      transition: .2s;
    }

    .qty-btn:hover { background: var(--accent); }

    .qty-display {
      width: 52px;
      text-align: center;
      font-size: 16px;
      font-family: 'DM Sans', sans-serif;
      border: 1px solid var(--border);
      border-radius: 4px;
      padding: 6px;
      color: var(--primary);
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
      cursor: pointer;
    }

    .btn:hover { transform: translateY(-1px); box-shadow: 0 4px 6px rgba(0,0,0,.05); }

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

    /* ── Sidebar ── */
    .sidebar { display: flex; flex-direction: column; gap: 24px; }

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

    .cat-item, .mini-product {
      border: 1px solid rgba(241,231,231,.5);
      border-radius: 13px;
      min-height: 48px;
      display: flex;
      align-items: center;
      padding: 0 16px;
      gap: 16px;
      margin-bottom: 11px;
      background: #fff;
      font-weight: 400;
      font-size: 16px;
      color: #380000;
      font-family: 'DM Sans', sans-serif;
      transition: background .2s;
    }

    .cat-item:hover, .mini-product:hover { background: #fdf5f5; }

    .mini-product {
      min-height: 84px;
      padding: 15px 11px;
      gap: 8px;
      align-items: flex-start;
      flex-direction: row;
    }

    .mini-prod-info { display: flex; flex-direction: column; gap: 4px; }
    .mini-prod-info .prod-title { font-family: 'DM Sans', sans-serif; font-weight: 700; color: #5A331C; font-size: 16px; }
    .mini-prod-info .prod-price { font-family: 'DM Sans', sans-serif; font-weight: 700; color: #575757; font-size: 16px; }

    .cat-item img  { width: 34px; height: 34px; object-fit: contain; }
    .mini-product img { width: 55px; height: 55px; object-fit: cover; border-radius: 10px; }

    /* ── Reviews ── */
    .latest { margin-top: 60px; }

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
      background: #fff;
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
      color: #F5A623;
      letter-spacing: 0;
    }

    .review-body { display: flex; flex-direction: column; gap: 4px; flex-grow: 1; }

    .review h4 {
      font-family: 'Inter', sans-serif;
      font-weight: 600;
      font-size: 18px;
      line-height: 1.3;
      color: #1E1E1E;
      letter-spacing: -0.02em;
    }

    .review p {
      font-family: 'Inter', sans-serif;
      font-weight: 400;
      font-size: 15px;
      color: #555;
      line-height: 1.6;
    }

    .review-user { display: flex; align-items: flex-start; gap: 12px; margin-top: auto; }

    .review-user img { width: 40px; height: 40px; border-radius: 9999px; object-fit: cover; }

    .review-user div strong { font-family: 'Inter', sans-serif; font-weight: 600; font-size: 14px; color: #757575; display: block; }
    .review-user div span  { font-family: 'Inter', sans-serif; font-weight: 400; font-size: 13px; color: #B3B3B3; }

    .no-reviews { color: var(--muted); font-style: italic; padding: 16px 0; }

    /* ── Responsive ── */
    @media (max-width: 1150px) {
      .layout { grid-template-columns: 1fr; }
      .sidebar { display: grid; grid-template-columns: 1fr 1fr; }
    }

    @media (max-width: 900px) {
      .main { grid-template-columns: 1fr; gap: 18px; }
      .details h1 { font-size: 2.35rem; }
      .cta-row { flex-direction: column; }
      .btn { width: 100%; }
    }

    @media (max-width: 620px) {
      .wrapper { width: calc(100% - 22px); margin-top: 14px; }
      .sidebar { grid-template-columns: 1fr; }
      .price { font-size: 2.4rem; }
      .review { width: 100%; }
    }
  </style>
</head>

<body>
  <?php include __DIR__ . '/../header.php'; ?>

  <div class="wrapper">
    <section class="layout" aria-label="Product detail layout">

      <!-- ── LEFT: main content ── -->
      <div>
        <div class="main">

          <div class="product-image-wrap">
            <?php if ($is_bakery): /* CHANGE 2: badge on image */ ?>
              <span class="image-discount-badge">15% OFF</span>
            <?php endif; ?>
            <img class="product-image"
                 src="<?php echo $image_url; ?>"
                 alt="<?php echo $name_safe; ?>"
                 onerror="this.onerror=null;this.style.opacity='.4'">
          </div>

          <div class="details">
            <h1><?php echo $name_safe; ?></h1>

            <span class="pill"><?php echo htmlspecialchars($product['category']); ?></span>
            <span class="shop-label">Sold by <?php echo htmlspecialchars($product['shop']); ?></span>

            <?php if ($review_count > 0): ?>
            <div class="rating" aria-label="Product rating">
              <span class="stars"><?php echo starStr($avg_rating); ?></span>
              <span>(<?php echo $review_count; ?> review<?php echo $review_count !== 1 ? 's' : ''; ?>)</span>
            </div>
            <?php else: ?>
            <div class="rating"><span style="color:#bbb">No reviews yet</span></div>
            <?php endif; ?>

            <?php /* CHANGE 3: price display — bakery shows discounted + strikethrough, others unchanged */ ?>
            <?php if ($is_bakery): ?>
              <div class="price">
                &#163;<?php echo number_format($final_price, 2); ?>
                <span class="price-was">&#163;<?php echo number_format($product['price'], 2); ?></span>
                <span class="price-save-badge">15% OFF</span>
              </div>
            <?php else: ?>
              <div class="price">&#163;<?php echo number_format($product['price'], 2); ?></div>
            <?php endif; ?>

            <p class="desc"><?php echo $desc_safe; ?></p>

            <?php if ($product['allergy']): ?>
            <p class="allergy-note">Allergy info: <?php echo htmlspecialchars($product['allergy']); ?></p>
            <?php endif; ?>

            <p class="stock-note"><?php echo $product['stock'] > 0
                ? htmlspecialchars($product['stock']) . ' in stock'
                : 'Currently out of stock'; ?></p>

            <p class="qty-label">Quantity</p>
            <div class="qty-row">
              <button class="qty-btn" id="qty-minus" aria-label="Decrease quantity">−</button>
              <input class="qty-display" id="qty-value" type="text" value="1" readonly>
              <button class="qty-btn" id="qty-plus"  aria-label="Increase quantity">+</button>
            </div>

            <div class="cta-row">
              <button class="btn btn-cart" id="add-cart-btn"
                      <?php echo $product['stock'] <= 0 ? 'disabled style="opacity:.5;cursor:not-allowed"' : ''; ?>>
                Add To Cart
              </button>
              <button class="btn btn-wish" id="wishlist-btn">Add To Wishlist</button>
              <a class="btn btn-feedback"
                 href="/cleckbasket/includes/pages/product_feedback.php?id=<?php echo $product['id']; ?>">
                Product Feedback
              </a>
            </div>
          </div>

        </div>

        <!-- ── Reviews ── -->
        <section class="latest" aria-label="Product reviews">
          <h2>Latest reviews</h2>

          <?php if (empty($reviews)): ?>
            <p class="no-reviews">No reviews yet for this product. Be the first to leave one!</p>
          <?php else: ?>
          <div class="review-grid">
            <?php foreach ($reviews as $r): ?>
            <article class="review">
              <div class="stars">
                <?php for ($s = 1; $s <= 5; $s++): ?>
                  <span><?php echo $s <= $r['rating'] ? '★' : '☆'; ?></span>
                <?php endfor; ?>
              </div>
              <div class="review-body">
                <p><?php echo htmlspecialchars($r['text']); ?></p>
              </div>
              <div class="review-user">
                <img src="/cleckbasket/assets/images/Avatar.png"
                     alt="<?php echo htmlspecialchars($r['name']); ?>"
                     onerror="this.onerror=null;this.style.display='none'">
                <div>
                  <strong><?php echo htmlspecialchars($r['name']); ?></strong>
                  <span><?php echo htmlspecialchars($r['date']); ?></span>
                </div>
              </div>
            </article>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>
        </section>
      </div>

      <!-- ── Sidebar ── -->
      <aside class="sidebar" aria-label="Sidebar">

        <section class="card">
          <h3>Shop By Category</h3>
          <?php foreach ($cat_links as $slug => $url): ?>
          <a class="cat-item" href="<?php echo $url; ?>">
            <img src="/cleckbasket/assets/images/<?php echo $cat_images[$slug]; ?>"
                 alt="<?php echo ucfirst($slug); ?>"
                 onerror="this.style.display='none'">
            <?php echo ucfirst($slug); ?>
          </a>
          <?php endforeach; ?>
        </section>

        <section class="card">
          <h3>More from <?php echo htmlspecialchars($product['category']); ?></h3>
          <?php if (empty($related)): ?>
            <p style="color:#aaa;font-size:14px;">No other products in this category yet.</p>
          <?php else: ?>
            <?php foreach ($related as $rel): ?>
            <a class="mini-product"
               href="/cleckbasket/includes/pages/product_detail.php?id=<?php echo $rel['id']; ?>">
              <img src="/cleckbasket/assets/images/<?php echo htmlspecialchars($rel['image']); ?>"
                   alt="<?php echo htmlspecialchars($rel['name']); ?>"
                   onerror="this.onerror=null;this.style.opacity='.3'">
              <div class="mini-prod-info">
                <span class="prod-title"><?php echo htmlspecialchars($rel['name']); ?></span>
                <span class="prod-price">&#163;<?php echo number_format($rel['price'], 2); ?></span>
              </div>
            </a>
            <?php endforeach; ?>
          <?php endif; ?>
        </section>

      </aside>

    </section>
  </div>

  <?php include __DIR__ . '/../footer.php'; ?>

  <script>
    /* ── Product data from PHP ── */
    const PRODUCT = <?php echo json_encode([
        'id'    => $product['id'],
        'name'  => $product['name'],
        'price' => $final_price, /* CHANGE 4: cart uses discounted price for bakery */
        'image' => '/cleckbasket/assets/images/' . $product['image'],
    ]); ?>;

    /* ── Quantity ── */
    const qtyDisplay = document.getElementById('qty-value');
    let qty = 1;

    document.getElementById('qty-plus').addEventListener('click', () => {
      qty = Math.min(qty + 1, <?php echo max(1, $product['stock']); ?>);
      qtyDisplay.value = qty;
    });

    document.getElementById('qty-minus').addEventListener('click', () => {
      qty = Math.max(qty - 1, 1);
      qtyDisplay.value = qty;
    });

    /* ── Add to Cart ── */
    document.getElementById('add-cart-btn').addEventListener('click', () => {
      const cart     = JSON.parse(localStorage.getItem('cart') || '[]');
      const existing = cart.find(i => i.id === PRODUCT.id);
      if (existing) {
        existing.quantity += qty;
      } else {
        cart.push({ ...PRODUCT, quantity: qty });
      }
      localStorage.setItem('cart', JSON.stringify(cart));

      const btn = document.getElementById('add-cart-btn');
      btn.textContent = 'Added ✓';
      setTimeout(() => btn.textContent = 'Add To Cart', 1400);
    });

    /* ── Wishlist ── */
    document.getElementById('wishlist-btn').addEventListener('click', () => {
      const wishlist = JSON.parse(localStorage.getItem('wishlist') || '[]');
      const idx      = wishlist.findIndex(i => i.id === PRODUCT.id);
      const btn      = document.getElementById('wishlist-btn');

      if (idx >= 0) {
        wishlist.splice(idx, 1);
        btn.textContent = 'Add To Wishlist';
      } else {
        wishlist.push(PRODUCT);
        btn.textContent = 'Saved ✓';
      }
      localStorage.setItem('wishlist', JSON.stringify(wishlist));
    });

    /* ── Init wishlist button state ── */
    (function () {
      const wishlist = JSON.parse(localStorage.getItem('wishlist') || '[]');
      if (wishlist.some(i => i.id === PRODUCT.id)) {
        document.getElementById('wishlist-btn').textContent = 'Saved ✓';
      }
    })();
  </script>

  <script src="/cleckbasket/assets/js/main.js" defer></script>
</body>
</html>