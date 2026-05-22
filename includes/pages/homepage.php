<?php
require_once '../../backend/connect.php';

$featured_products = [];
$fruits_data       = [];

$conn = getDBConnection();

if ($conn) {
    // Featured products — 6 most recently added products
    $sql = "SELECT p.product_id, p.product_name, p.price, p.product_image
            FROM product p
            ORDER BY p.product_id DESC
            FETCH FIRST 6 ROWS ONLY";
    $stmt = oci_parse($conn, $sql);
    oci_execute($stmt);
    while ($row = oci_fetch_assoc($stmt)) {
        $featured_products[] = [
            'id'    => $row['PRODUCT_ID'],
            'name'  => $row['PRODUCT_NAME'],
            'price' => $row['PRICE'],
            'image' => '/cleckbasket/assets/images/' . $row['PRODUCT_IMAGE'],
        ];
    }
    oci_free_statement($stmt);

    // Top rated products — up to 8, sorted by average review rating
    $bg_colors = ['#FDF49B','#FFC594','#FDADA3','#FF9A9B','#C6D37B','#FADC54','#DDF1A1','#F6F6F6'];
    $sql2 = "SELECT p.product_id, p.product_name, p.price, p.product_image,
                    ROUND(NVL(AVG(r.review_rating), 0), 1) AS avg_rating,
                    COUNT(r.review_id) AS review_count
             FROM product p
             LEFT JOIN review r ON p.product_id = r.product_id
             GROUP BY p.product_id, p.product_name, p.price, p.product_image
             ORDER BY avg_rating DESC
             FETCH FIRST 8 ROWS ONLY";
    $stmt2 = oci_parse($conn, $sql2);
    oci_execute($stmt2);
    $i = 0;
    while ($row = oci_fetch_assoc($stmt2)) {
        $fruits_data[] = [
            'id'      => $row['PRODUCT_ID'],
            'name'    => $row['PRODUCT_NAME'],
            'price'   => number_format((float)$row['PRICE'], 2),
            'rating'  => $row['AVG_RATING'],
            'reviews' => (int)$row['REVIEW_COUNT'],
            'bgColor' => $bg_colors[$i % count($bg_colors)],
            'image'   => '/cleckbasket/assets/images/' . $row['PRODUCT_IMAGE'],
            'inCart'  => 0,
        ];
        $i++;
    }
    oci_free_statement($stmt2);
    oci_close($conn);
}

$show_fruit_slider = count($fruits_data) > 5;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CleckBasket — Shop Local, Eat Fresh</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="/cleckbasket/assets/css/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/cleckbasket/assets/css/style.css">
    <link rel="stylesheet" href="/cleckbasket/assets/css/aboutus.css">
    <link rel="stylesheet" href="/cleckbasket/assets/css/productcard.css">
</head>

<body>

    <!-- ===== NAVBAR ===== -->
    <?php include '../header.php'; ?>


    <!-- =====================================================
         SECTION 1: BANNER HERO
         Big full-width banner — text left, image right
         (adapted from your senior's homepage.php)
    ====================================================== -->
    <section class="banner-hero">
        <div class="banner-text">
            <h1>FRESH, LOCAL, YOURS</h1>
            <h2>Your Neighborhood Market, <span class="banner-highlight">Online</span></h2>
            <p>Shop from your favorite local traders online and pick up fresh goods with ease</p>
            <a href="/cleckbasket/includes/pages/shop.php" class="banner-btn">SHOP NOW</a>
        </div>
        <div class="banner-image">
            <!--  image path -->
            <img src="/cleckbasket/assets/images/banner1.png" alt="Fresh groceries">

        </div>
    </section>


    <!-- =====================================================
         SECTION 2: PROMO CARDS
         4 cards in a row — green & brown alternating
    ====================================================== -->
    <section class="promo-section">
        <div class="promo-container">

            <!-- Card 1 — Light Green -->
            <div class="promo-card card-green">
                <div class="promo-text">
                    <h3>Buy Online</h3>
                    <p> Collect With Convenience</p>
                    <a href="/cleckbasket/includes/pages/shop.php" class="promo-btn btn-dark">Shop Now</a>
                </div>
                <div class="promo-img">
                    <img src="/cleckbasket/assets/images/grocery2.png" alt="Same Day Delivery">
                </div>
            </div>

            <!-- Card 2 — Brown -->
            <div class="promo-card card-brown">
                <div class="promo-text">
                    <h3>Up to 25% off</h3>
                    <p>For first buyers</p>
                    <a href="/cleckbasket/includes/pages/login.php" class="promo-btn btn-light">Login Now</a>
                </div>
                <div class="promo-img">
                    <img src="/cleckbasket/assets/images/grocery3.png" alt="25% Off">
                </div>
            </div>

            <!-- Card 3 — Light Green -->
            <div class="promo-card card-green">
                <div class="promo-text">
                    <h3>Fresh Picks</h3>
                    <p>One-day deals for you</p>
                    <a href="/cleckbasket/includes/pages/shop.php" class="promo-btn btn-dark">Pick Now</a>
                </div>
                <div class="promo-img">
                    <img src="/cleckbasket/assets/images/grocery2.png" alt="Fresh Picks">
                </div>
            </div>

            <!-- Card 4 — Brown -->
            <div class="promo-card card-brown">
                <div class="promo-text">
                    <h3>Farmer's Pick</h3>
                    <p>On first buyers</p>
                    <a href="/cleckbasket/includes/pages/about.php" class="promo-btn btn-light">About Us</a>
                </div>
                <div class="promo-img">
                    <img src="/cleckbasket/assets/images/grocery4.png" alt="Farmer's Pick">
                </div>
            </div>

        </div>
    </section>


    <!-- =====================================================
         SECTION 3: BROWSE CATEGORIES
         Circle icons with category name below
    ====================================================== -->
    <section class="categories-section">
        <div class="categories-wrap">

            <!-- Header row -->
            <div class="categories-header">
                <h2>Browse by Categories</h2>
                <a href="/cleckbasket/includes/pages/shop.php">See all</a>
            </div>

            <!-- Category items -->
            <div class="categories-list">

                <a href="/cleckbasket/includes/pages/shop.php?cat=butcher" class="category-card">
                    <div class="category-circle">
                        <img src="/cleckbasket/assets/images/beefs.png" alt="Butcher">
                    </div>
                    <p>Butcher</p>
                </a>

                <a href="/cleckbasket/includes/pages/shop.php?cat=bakery" class="category-card">
                    <div class="category-circle">
                        <img src="/cleckbasket/assets/images/bakerys.png" alt="Bakery">
                    </div>
                    <p>Bakery</p>
                </a>

                <a href="/cleckbasket/includes/pages/shop.php?cat=fishmonger" class="category-card">
                    <div class="category-circle">
                        <img src="/cleckbasket/assets/images/fishmongers.png" alt="Fishmonger">
                    </div>
                    <p>Fishmonger</p>
                </a>

                <a href="/cleckbasket/includes/pages/shop.php?cat=greengrocer" class="category-card">
                    <div class="category-circle">
                        <img src="/cleckbasket/assets/images/greengrocers.png" alt="Greengrocer">
                    </div>
                    <p>Greengrocer</p>
                </a>

                <a href="/cleckbasket/includes/pages/shop.php?cat=delicatessen" class="category-card">
                    <div class="category-circle">
                        <img src="/cleckbasket/assets/images/nutritions.png" alt="Delicatessen">
                    </div>
                    <p>Delicatessen</p>
                </a>

            </div>
        </div>
    </section>

    <!-- =====================================================
         SECTION 3.5: FRUITS
    ====================================================== -->
    <section class="fruits-section">
        <div class="fruits-wrap">
            <div class="fruits-header">
                <h2>Top Rated Products</h2>
                <a href="/cleckbasket/includes/pages/shop.php?cat=fruits">See all</a>
            </div>

            <div class="fruits-slider<?php echo $show_fruit_slider ? ' has-controls' : ''; ?>">
                <button class="fruit-nav fruit-prev<?php echo $show_fruit_slider ? ' is-visible' : ''; ?>" type="button" aria-label="Scroll fruits left">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                </button>
                <div class="fruits-list" data-fruits-list>
                <?php if (empty($fruits_data)): ?>
                    <p style="color:#888;padding:1rem;">No products available.</p>
                <?php else: ?>
                <?php foreach ($fruits_data as $fruit): ?>
                    <div class="fruit-card" data-product-id="<?php echo $fruit['id']; ?>">
                        <div class="fruit-img-box" style="background-color: <?php echo $fruit['bgColor']; ?>;">
                            <img src="<?php echo htmlspecialchars($fruit['image']); ?>"
                                 alt="<?php echo htmlspecialchars($fruit['name']); ?>"
                                 onerror="this.onerror=null;this.style.opacity='0'">
                            <div class="fruit-add-btn circle-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            </div>
                        </div>
                        <div class="fruit-info">
                            <h3><?php echo htmlspecialchars($fruit['name']); ?></h3>
                            <div class="fruit-rating">
                                <span class="star">⭐</span>
                                <?php echo $fruit['rating'] > 0 ? $fruit['rating'] . ' (' . $fruit['reviews'] . ' reviews)' : 'No reviews yet'; ?>
                            </div>
                            <p class="fruit-price">£<?php echo $fruit['price']; ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php endif; ?>
                </div>
                <button class="fruit-nav fruit-next<?php echo $show_fruit_slider ? ' is-visible' : ''; ?>" type="button" aria-label="Scroll fruits right">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                </button>
            </div>
        </div>
    </section>


    <section class="about-us" id="about-us">
        <div class="container">

            <!-- LEFT TEXT -->
            <div class="about-us-text">
                <h2>About Us</h2>
                <h3>Trust in our experience</h3>
                <p>
                    At CleckBasket, we bring the freshness of local farms straight to your doorstep. Our mission is to connect communities with trusted local growers and suppliers, ensuring every product you receive is fresh, high-quality, and sustainably sourced. From everyday essentials to farm-fresh produce, we make grocery shopping simple, convenient, and reliable. With a commitment to supporting local businesses and delivering exceptional service, CleckBasket is your neighborhood market—online and always within reach.
                </p>
                <button class="see-more" onclick="location.href='/cleckbasket/includes/pages/about.php'">See More</button>
            </div>

            <!-- RIGHT IMAGE -->
            <div class="about-us-image">
                <img src="/cleckbasket/assets/images/about.png" alt="About us">
            </div>

        </div>
    </section>


    <!-- FEATURED HEADER -->
    <section class="featured-header-section">
        <div class="container">
            <div class="featured-header">
                <h2>FEATURED PRODUCTS</h2>
            </div>
            <div class="view-all-products">
                <a href="/cleckbasket/includes/pages/shop.php">View all products ></a>
            </div>
        </div>
    </section>

    <!-- PRODUCT SECTION -->
    <section class="product-section">
        <div class="container">
            <div class="product-container">

                <?php if (empty($featured_products)): ?>
                    <p style="color:#888;padding:2rem;">No products available at the moment.</p>
                <?php else: ?>
                <?php foreach ($featured_products as $product): ?>
                    <div class="product-card" data-product-id="<?php echo $product['id']; ?>">
                        <div class="image-container">
                            <img src="<?php echo htmlspecialchars($product['image']); ?>"
                                 alt="<?php echo htmlspecialchars($product['name']); ?>"
                                 onerror="this.onerror=null;this.style.opacity='0'">
                        </div>
                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p class="price">£<?php echo number_format((float)$product['price'], 2); ?></p>
                        <div class="card-actions">
                            <button class="add-to-cart">Add to Cart</button>
                            <div class="quantity-container">
                                <input type="text" value="1" class="quantity-input">
                                <div class="quantity-buttons">
                                    <button class="quantity-btn">+</button>
                                    <button class="quantity-btn">−</button>
                                </div>
                            </div>
                            <button class="wishlist">
                                <svg viewBox="0 0 24 24">
                                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php endif; ?>

            </div>
        </div>
    </section>

    <button class="scroll-top-btn" id="scrollTopBtn" onclick="window.scrollTo({top: 0, behavior: 'smooth'})">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="19" x2="12" y2="5"/><polyline points="5 12 12 5 19 12"/></svg>
    </button>

    <!-- ===== FOOTER ===== -->
    <?php include '../footer.php'; ?>

</body>
<script>
    const btn = document.getElementById('scrollTopBtn');
    window.addEventListener('scroll', () => {
        btn.classList.toggle('visible', window.scrollY > 300);
    });
</script>

</html>