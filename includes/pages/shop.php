<?php
/**
 * shop.php — Unified Shop/Category Page
 */

$per_page = 9;
$page     = max(1, (int)($_GET['page'] ?? 1));

$categories = [
    'all'         => __DIR__ . '/all.php',
    'butcher'     => __DIR__ . '/butcher.php',
    'bakery'      => __DIR__ . '/bakery.php',
    'fishmonger'  => __DIR__ . '/fishmonger.php',
    'greengrocer' => __DIR__ . '/greengrocer.php',
    'delicatessen' => __DIR__ . '/delicatessen.php',
];

$cat = isset($_GET['cat']) ? strtolower(trim($_GET['cat'])) : 'all';
if (!array_key_exists($cat, $categories)) {
    $cat = 'all';
}

include $categories[$cat];
// $products, $category_name, $hero_title, $hero_subtitle now set

// Always use product_ images from assets/images as hero slides
$_img_dir    = realpath(__DIR__ . '/../../assets/images');
$_img_files  = glob($_img_dir . DIRECTORY_SEPARATOR . 'product_*');
$hero_images = [];
if (!empty($_img_files)) {
    foreach ($_img_files as $_path) {
        $hero_images[] = [
            'src' => '/cleckbasket/assets/images/' . basename($_path),
            'alt' => 'Product',
        ];
    }
}

// Pagination
$total_products = count($products);
$total_pages    = max(1, (int)ceil($total_products / $per_page));
$page           = min($page, $total_pages);
$paged_products = array_slice($products, ($page - 1) * $per_page, $per_page);

// Dynamic New Products — 3 newest from DB
require_once __DIR__ . '/../../backend/connect.php';
$new_products = [];
$conn_np = getDBConnection();
if ($conn_np) {
    $sql_np = "SELECT p.product_id, p.product_name, p.price, p.product_image
               FROM product p
               ORDER BY p.product_id DESC
               FETCH FIRST 3 ROWS ONLY";
    $stmt_np = oci_parse($conn_np, $sql_np);
    oci_execute($stmt_np);
    while ($row = oci_fetch_assoc($stmt_np)) {
        $new_products[] = [
            'id'    => (int)$row['PRODUCT_ID'],
            'name'  => $row['PRODUCT_NAME'],
            'price' => number_format((float)$row['PRICE'], 2),
            'image' => '/cleckbasket/assets/images/' . $row['PRODUCT_IMAGE'],
        ];
    }
    oci_free_statement($stmt_np);
    oci_close($conn_np);
}

function shop_page_url($cat, $pg) {
    return '/cleckbasket/includes/pages/shop.php?cat=' . urlencode($cat) . '&page=' . $pg;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo htmlspecialchars($category_name); ?> — CleckBasket Shop</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;900&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="/cleckbasket/assets/css/bakery.css" />
    <style>
        .discount-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            background: #e74c3c;
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 20px;
            letter-spacing: 0.02em;
            z-index: 1;
            pointer-events: none;
        }

        .price-block {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }

        .price-original {
            font-size: 13px;
            color: #aaa;
            text-decoration: line-through;
        }

        .price-discounted {
            font-size: 15px;
            font-weight: 700;
            color: #e74c3c;
        }

        .price-normal {
            font-size: 15px;
            font-weight: 700;
            color: #333;
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin-top: 48px;
            flex-wrap: wrap;
        }

        .pagination a,
        .pagination span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 40px;
            height: 40px;
            padding: 0 12px;
            border-radius: 10px;
            font-family: "DM Sans", sans-serif;
            font-size: 15px;
            font-weight: 600;
            text-decoration: none;
            border: 1.5px solid #e0e0e0;
            color: #380000;
            background: #fff;
            transition: background 0.18s, color 0.18s, border-color 0.18s;
        }

        .pagination a:hover {
            background: #616834;
            color: #fff;
            border-color: #616834;
        }

        .pagination .current {
            background: #616834;
            color: #fff;
            border-color: #616834;
        }

        .pagination .disabled {
            color: #ccc;
            border-color: #f0f0f0;
            cursor: default;
            pointer-events: none;
        }

        .new-prod-item {
            text-decoration: none;
        }
        
        /* Fix: ensure page is tall enough to push footer down */
        .category-page-wrapper {
        min-height: calc(100vh - 200px);
        }

        /* Hero slider */
        .hero-img-wrap {
            display: none;
            opacity: 0;
            transition: opacity 0.4s ease;
        }
        .hero-img-wrap.slider-visible {
            display: flex;
            opacity: 1;
        }
        .hero-images {
            position: relative;
        }
    </style>
</head>

<body>
    <?php include __DIR__ . '/../header.php'; ?>

    <main class="category-page-wrapper">

        <!-- Top Hero Section -->
        <section class="category-hero">
            <h1 class="hero-title"><?php echo htmlspecialchars($hero_title); ?></h1>
            <h2 class="hero-subtitle"><?php echo htmlspecialchars($hero_subtitle); ?></h2>

            <div class="hero-images">
                <button class="arrow-btn arrow-prev" aria-label="Previous">
                    <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"></polyline></svg>
                </button>
                <?php foreach ($hero_images as $img): ?>
                    <div class="hero-img-wrap">
                        <img src="<?php echo htmlspecialchars($img['src']); ?>"
                            alt="<?php echo htmlspecialchars($img['alt']); ?>"
                            onerror="this.src='https://placehold.co/350x350/F6F6F6/ccc?text=<?php echo urlencode($img['alt']); ?>'" />
                    </div>
                <?php endforeach; ?>
                <button class="arrow-btn arrow-next" aria-label="Next">
                    <svg viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </button>
            </div>
        </section>

        <section class="category-main">
            <!-- Sidebar -->
            <aside class="sidebar">

                <!-- Category List -->
                <div class="widget-shop-category">
                    <h3 class="widget-title">Shop By Category</h3>
                    <ul class="cat-list">
                        <?php
                        $cat_labels = [
                            'all'         => ['All',          '/cleckbasket/assets/images/grocery2.png'],
                            'butcher'     => ['Butcher',      '/cleckbasket/assets/images/beefs.png'],
                            'greengrocer' => ['Green Grocer', '/cleckbasket/assets/images/greengrocers.png'],
                            'bakery'      => ['Bakery',       '/cleckbasket/assets/images/bakerys.png'],
                            'fishmonger'  => ['Fish Monger',  '/cleckbasket/assets/images/fishmongers.png'],
                            'delicatessen'=> ['Delicatessen', '/cleckbasket/assets/images/nutritions.png'],
                        ];
                        foreach ($cat_labels as $key => $info):
                            $active = ($key === $cat) ? ' active' : '';
                        ?>
                            <li>
                                <a href="/cleckbasket/includes/pages/shop.php?cat=<?php echo $key; ?>"
                                    class="cat-item<?php echo $active; ?>">
                                    <img src="<?php echo $info[1]; ?>" alt="<?php echo $info[0]; ?>" class="cat-icon-img" />
                                    <?php echo $info[0]; ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- New Products (Dynamic) -->
                <div class="widget-new-products">
                    <h3 class="widget-title-lg">New Products</h3>
                    <div class="new-prod-list">
                        <?php if (empty($new_products)): ?>
                            <p style="color:#888;font-size:14px;">No products yet.</p>
                        <?php else: ?>
                            <?php foreach ($new_products as $np): ?>
                                <a class="new-prod-item"
                                   href="/cleckbasket/includes/pages/product_detail.php?id=<?php echo $np['id']; ?>">
                                    <img src="<?php echo htmlspecialchars($np['image']); ?>"
                                         alt="<?php echo htmlspecialchars($np['name']); ?>"
                                         class="new-prod-img"
                                         onerror="this.src='https://placehold.co/70x70/f6f6f6/ccc?text=Img'" />
                                    <div class="new-prod-info">
                                        <p class="new-prod-name"><?php echo htmlspecialchars($np['name']); ?></p>
                                        <p class="new-prod-price">£<?php echo $np['price']; ?></p>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

            </aside>

            <!-- Product Display Area -->
            <div class="product-area">
                <h2 class="section-main-title"><?php echo htmlspecialchars($category_name); ?> Products</h2>

                <?php if (empty($paged_products)): ?>
                    <p style="color:#888;padding:2rem;text-align:center;">No products found.</p>
                <?php else: ?>
                <div class="products-grid">
                    <?php foreach ($paged_products as $index => $product):
                        $discount   = isset($product['discount']) ? (int)$product['discount'] : 0;
                        $original   = $product['price'];
                        $discounted = $discount > 0 ? round($original * (1 - $discount / 100), 2) : null;
                        $cart_price = $discounted ?? $original;
                    ?>
                        <div class="product-card"
                            data-product-id="<?php echo isset($product['id']) ? (int)$product['id'] : 0; ?>"
                            data-name="<?php echo htmlspecialchars($product['name']); ?>"
                            data-price="<?php echo $cart_price; ?>"
                            data-image="<?php echo htmlspecialchars($product['image']); ?>"
                            data-desc="<?php echo htmlspecialchars($product['desc']); ?>">

                            <div class="product-card-top">
                                <?php if ($discount > 0): ?>
                                    <span class="discount-badge">-<?php echo $discount; ?>%</span>
                                <?php endif; ?>
                                <img src="<?php echo htmlspecialchars($product['image']); ?>"
                                    alt="<?php echo htmlspecialchars($product['name']); ?>"
                                    onerror="this.src='https://placehold.co/300x200/F6F6F6/ccc?text=<?php echo urlencode($product['name']); ?>'" />
                            </div>

                            <div class="product-card-bottom">
                                <div>
                                    <h3 class="product-card-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                                    <div class="price-block">
                                        <?php if ($discount > 0): ?>
                                            <span class="price-original">£<?php echo number_format($original, 2); ?></span>
                                            <span class="price-discounted">£<?php echo number_format($discounted, 2); ?></span>
                                        <?php else: ?>
                                            <span class="price-normal">£<?php echo number_format($original, 2); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="product-card-actions">
                                    <button class="btn-add-cart">Add to Cart</button>
                                    <button class="btn-fav" aria-label="Add to wishlist">
                                        <svg viewBox="0 0 24 24">
                                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                <nav class="pagination" aria-label="Product pages">
                    <?php if ($page > 1): ?>
                        <a href="<?php echo shop_page_url($cat, $page - 1); ?>">&laquo; Prev</a>
                    <?php else: ?>
                        <span class="disabled">&laquo; Prev</span>
                    <?php endif; ?>

                    <?php
                    $range = 2;
                    $start = max(1, $page - $range);
                    $end   = min($total_pages, $page + $range);
                    if ($start > 1): ?>
                        <a href="<?php echo shop_page_url($cat, 1); ?>">1</a>
                        <?php if ($start > 2): ?><span class="disabled">&hellip;</span><?php endif; ?>
                    <?php endif; ?>

                    <?php for ($p = $start; $p <= $end; $p++): ?>
                        <?php if ($p === $page): ?>
                            <span class="current"><?php echo $p; ?></span>
                        <?php else: ?>
                            <a href="<?php echo shop_page_url($cat, $p); ?>"><?php echo $p; ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if ($end < $total_pages): ?>
                        <?php if ($end < $total_pages - 1): ?><span class="disabled">&hellip;</span><?php endif; ?>
                        <a href="<?php echo shop_page_url($cat, $total_pages); ?>"><?php echo $total_pages; ?></a>
                    <?php endif; ?>

                    <?php if ($page < $total_pages): ?>
                        <a href="<?php echo shop_page_url($cat, $page + 1); ?>">Next &raquo;</a>
                    <?php else: ?>
                        <span class="disabled">Next &raquo;</span>
                    <?php endif; ?>
                </nav>
                <?php endif; ?>

            </div>
        </section>

    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // --- ADD TO CART ---
            document.querySelectorAll('.btn-add-cart').forEach(function (btn) {
                btn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    var card = btn.closest('.product-card');
                    var product = {
                        id: parseInt(card.dataset.productId) || 0,
                        name: card.dataset.name,
                        price: parseFloat(card.dataset.price),
                        image: card.dataset.image,
                        quantity: 1
                    };
                    var cart = JSON.parse(localStorage.getItem('cart') || '[]');
                    var existing = cart.find(function (item) { return item.name === product.name; });
                    if (existing) {
                        existing.quantity += 1;
                    } else {
                        if (cart.length >= 20) {
                            btn.textContent = 'Cart Full (max 20)';
                            btn.style.background = '#fdecea';
                            setTimeout(function () {
                                btn.textContent = 'Add to Cart';
                                btn.style.background = '';
                            }, 1800);
                            return;
                        }
                        cart.push(product);
                    }
                    localStorage.setItem('cart', JSON.stringify(cart));
                    var badge = document.getElementById('cartCount');
                    if (badge) {
                        var total = cart.reduce(function (s, i) { return s + i.quantity; }, 0);
                        badge.textContent = total;
                        badge.style.display = total > 0 ? 'flex' : 'none';
                    }
                    btn.textContent = 'Added ✓';
                    btn.style.background = '#D5E8CD';
                    setTimeout(function () {
                        btn.textContent = 'Add to Cart';
                        btn.style.background = '';
                    }, 1200);
                });
            });

            // --- WISHLIST TOGGLE ---
            document.querySelectorAll('.btn-fav').forEach(function (btn) {
                btn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    var card = btn.closest('.product-card');
                    var name = card.dataset.name;
                    var wishlist = JSON.parse(localStorage.getItem('wishlist') || '[]');
                    var idx = wishlist.indexOf(name);
                    if (idx > -1) {
                        wishlist.splice(idx, 1);
                        btn.classList.remove('wishlisted');
                    } else {
                        wishlist.push(name);
                        btn.classList.add('wishlisted');
                    }
                    localStorage.setItem('wishlist', JSON.stringify(wishlist));
                });
            });

            // Restore wishlist state on load
            var savedWishlist = JSON.parse(localStorage.getItem('wishlist') || '[]');
            document.querySelectorAll('.product-card').forEach(function (card) {
                if (savedWishlist.indexOf(card.dataset.name) > -1) {
                    var favBtn = card.querySelector('.btn-fav');
                    if (favBtn) favBtn.classList.add('wishlisted');
                }
            });

            // --- PRODUCT CARD → DETAIL ---
            document.querySelectorAll('.product-card').forEach(function (card) {
                card.style.cursor = 'pointer';
                card.addEventListener('click', function () {
                    var pid = card.dataset.productId;
                    if (pid && parseInt(pid) > 0) {
                        window.location.href = '/cleckbasket/includes/pages/product_detail.php?id=' + pid;
                    }
                });
            });

            // --- HERO SLIDER ---
            (function () {
                var slides   = Array.from(document.querySelectorAll('.hero-img-wrap'));
                var prevBtn  = document.querySelector('.arrow-prev');
                var nextBtn  = document.querySelector('.arrow-next');
                if (!slides.length) return;

                var VISIBLE  = Math.min(3, slides.length);
                var current  = 0;
                var timer    = null;

                function show(idx) {
                    slides.forEach(function (s) { s.classList.remove('slider-visible'); });
                    for (var i = 0; i < VISIBLE; i++) {
                        slides[(idx + i) % slides.length].classList.add('slider-visible');
                    }
                    current = idx;
                }

                function goNext() { show((current + 1) % slides.length); }
                function goPrev() { show((current - 1 + slides.length) % slides.length); }

                function startAuto() { timer = setInterval(goNext, 3000); }
                function resetAuto() { clearInterval(timer); startAuto(); }

                if (nextBtn) nextBtn.addEventListener('click', function () { goNext(); resetAuto(); });
                if (prevBtn) prevBtn.addEventListener('click', function () { goPrev(); resetAuto(); });

                show(0);
                startAuto();
            })();

            // --- CART BADGE ON LOAD ---
            var cart = JSON.parse(localStorage.getItem('cart') || '[]');
            var badge = document.getElementById('cartCount');
            if (badge) {
                var total = cart.reduce(function (s, i) { return s + i.quantity; }, 0);
                badge.textContent = total;
                badge.style.display = total > 0 ? 'flex' : 'none';
            }
        });
    </script>

    <?php include __DIR__ . '/../footer.php'; ?>
</body>

</html>
