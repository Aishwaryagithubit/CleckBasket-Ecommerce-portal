<?php
/**
 * shop.php — Unified Shop/Category Page
 */

$categories = [
    'butcher'     => __DIR__ . '/butcher.php',
    'bakery'      => __DIR__ . '/bakery.php',
    'fishmonger'  => __DIR__ . '/fishmonger.php',
    'greengrocer' => __DIR__ . '/greengrocer.php',
    'delicatessen' => __DIR__ . '/delicatessen.php',
];

$cat = isset($_GET['cat']) ? strtolower(trim($_GET['cat'])) : 'butcher';
if (!array_key_exists($cat, $categories)) {
    $cat = 'butcher';
}

include $categories[$cat];
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
        /* Discount pricing styles */
        .product-card-top {
            position: relative;
        }

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
                    <svg viewBox="0 0 24 24">
                        <polyline points="15 18 9 12 15 6"></polyline>
                    </svg>
                </button>

                <?php foreach ($hero_images as $img): ?>
                    <div class="hero-img-wrap">
                        <img src="<?php echo htmlspecialchars($img['src']); ?>"
                            alt="<?php echo htmlspecialchars($img['alt']); ?>"
                            onerror="this.src='https://placehold.co/350x350/F6F6F6/ccc?text=<?php echo urlencode($img['alt']); ?>'" />
                    </div>
                <?php endforeach; ?>

                <button class="arrow-btn arrow-next" aria-label="Next">
                    <svg viewBox="0 0 24 24">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
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

                <!-- New Products -->
                <div class="widget-new-products">
                    <h3 class="widget-title-lg">New Products</h3>
                    <div class="new-prod-list">
                        <a class="new-prod-item" href="/cleckbasket/includes/pages/product_detail.php">
                            <img src="/cleckbasket/assets/images/cupcake.png" alt="Fruit Cupcakes" class="new-prod-img"
                                onerror="this.src='https://placehold.co/70x70/ffe/f00?text=Cupcake'" />
                            <div class="new-prod-info">
                                <p class="new-prod-name">Fruit Cupcakes</p>
                                <p class="new-prod-price">£3.50</p>
                            </div>
                        </a>
                        <a class="new-prod-item" href="/cleckbasket/includes/pages/product_detail.php">
                            <img src="/cleckbasket/assets/images/stripsteak.png" alt="NY Strip Steak"
                                class="new-prod-img"
                                onerror="this.src='https://placehold.co/70x70/fee/f00?text=Steak'" />
                            <div class="new-prod-info">
                                <p class="new-prod-name">NY Strip Steak</p>
                                <p class="new-prod-price">£11.75</p>
                            </div>
                        </a>
                    </div>
                </div>

            </aside>

            <!-- Product Display Area -->
            <div class="product-area">
                <h2 class="section-main-title"><?php echo htmlspecialchars($category_name); ?> Products</h2>

                <div class="products-grid">
                    <?php foreach ($products as $index => $product):
                        $discount      = isset($product['discount']) ? (int)$product['discount'] : 0;
                        $original      = $product['price'];
                        $discounted    = $discount > 0
                            ? round($original * (1 - $discount / 100), 2)
                            : null;
                        // Pass the final payable price into data-price for cart
                        $cart_price    = $discounted ?? $original;
                    ?>
                        <div class="product-card"
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
                                    <button class="btn-add-cart" data-id="<?php echo $index; ?>">Add to Cart</button>
                                    <button class="btn-fav" aria-label="Add to wishlist" data-id="<?php echo $index; ?>">
                                        <svg viewBox="0 0 24 24">
                                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
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
                    sessionStorage.setItem('selected_product', JSON.stringify({
                        name: card.dataset.name,
                        price: '£' + parseFloat(card.dataset.price).toFixed(2),
                        image: card.dataset.image,
                        desc: card.dataset.desc
                    }));
                    window.location.href = '/cleckbasket/includes/pages/product_detail.php';
                });
            });

            // --- HERO IMAGE CAROUSEL ---
            var slides = document.querySelectorAll('.hero-img-wrap');
            var currentSlide = 0;
            function showSlide(n) {
                if (slides.length <= 3) return;
                slides.forEach(function (s, i) {
                    s.style.display = (i >= n && i < n + 3) ? 'flex' : 'none';
                });
            }
            document.querySelector('.arrow-prev')?.addEventListener('click', function () {
                currentSlide = Math.max(0, currentSlide - 1);
                showSlide(currentSlide);
            });
            document.querySelector('.arrow-next')?.addEventListener('click', function () {
                currentSlide = Math.min(slides.length - 3, currentSlide + 1);
                showSlide(currentSlide);
            });

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