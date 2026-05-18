<?php
// Junior Dev Logic: Defining a simple Product Array to build our interface 
// before hooking up the real database!
$featured_products = [
    ["name" => "Buffalo Meat", "price" => 250, "image" => "/cleckbasket/assets/images/buffalomeat.png"],
    ["name" => "Bitter Gourd", "price" => 120, "image" => "/cleckbasket/assets/images/BitterGourd.png"],
    ["name" => "Fresh Passionfruits", "price" => 180, "image" => "/cleckbasket/assets/images/passionfruit.png"],
    ["name" => "Aged Gouda", "price" => 90, "image" => "/cleckbasket/assets/images/agedgouda.png"],
    ["name" => "Salmon Fillet", "price" => 110, "image" => "/cleckbasket/assets/images/salmonfillet.png"],
    ["name" => "Whole Chicken", "price" => 80, "image" => "/cleckbasket/assets/images/wholechicken.png"]
];

$fruits_data = [
    ["name" => "Buffalo Meat", "price" => "3.99", "rating" => "4.9", "reviews" => 287, "bgColor" => "#FDF49B", "image" => "/cleckbasket/assets/images/buffalomeat.png", "inCart" => 1],
        ["name" => "Salmon Fillet", "price" => "3.99", "rating" => "4.7", "reviews" => 300, "bgColor" => "#FFC594", "image" => "/cleckbasket/assets/images/salmonfillet.png", "inCart" => 0],
    ["name" => "Cheese", "price" => "1.99", "rating" => "4.5", "reviews" => 100, "bgColor" => "#FDADA3", "image" => "/cleckbasket/assets/images/agedgouda.png", "inCart" => 0],
    ["name" => "Bread", "price" => "1.99", "rating" => "4.4", "reviews" => 587, "bgColor" => "#FF9A9B", "image" => "/cleckbasket/assets/images/bread.png", "inCart" => 0],
    ["name" => "Bitter Gourd", "price" => "1.99", "rating" => "4.4", "reviews" => 387, "bgColor" => "#C6D37B", "image" => "/cleckbasket/assets/images/BitterGourd.png", "inCart" => 0],
    ["name" => "Mango", "price" => "4.99", "rating" => "4.3", "reviews" => 127, "bgColor" => "#FADC54", "image" => "/cleckbasket/assets/images/mango.png", "inCart" => 0],
    ["name" => "Grapes", "price" => "3.45", "rating" => "4.2", "reviews" => 117, "bgColor" => "#DDF1A1", "image" => "/cleckbasket/assets/images/grapes.png", "inCart" => 0],
    ["name" => "Paw Paw", "price" => "2.89", "rating" => "4.2", "reviews" => 23, "bgColor" => "#F6F6F6", "image" => "/cleckbasket/assets/images/pawpaw.png", "inCart" => 0]
];
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
            <h1>FRESH, LOCAL, YOURS.</h1>
            <h2>Your Neighborhood Market, <span class="banner-highlight">Online</span></h2>
            <p>Shop from your favorite local traders online and pick up fresh goods with ease.</p>
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
                    <h3>Same Day Delivery</h3>
                    <p>On orders above Rs. 1000</p>
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
                    <a href="/cleckbasket/includes/pages/shop.php" class="promo-btn btn-light">Shop Now</a>
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
                    <i class="fa-solid fa-chevron-left"></i>
                </button>
                <div class="fruits-list" data-fruits-list>
                <?php foreach ($fruits_data as $fruit): ?>
                    <div class="fruit-card">
                        <div class="fruit-img-box" style="background-color: <?php echo $fruit['bgColor']; ?>;">
                            <img src="<?php echo $fruit['image']; ?>" alt="<?php echo $fruit['name']; ?>" onerror="this.style.display='none'">
                            <?php if (isset($fruit['inCart']) && $fruit['inCart'] > 0): ?>
                                <div class="fruit-add-btn pill-btn">
                                    <i class="fa-solid fa-trash-can"></i>
                                    <span><?php echo $fruit['inCart']; ?></span>
                                    <i class="fa-solid fa-plus"></i>
                                </div>
                            <?php else: ?>
                                <div class="fruit-add-btn circle-btn">
                                    <i class="fa-solid fa-plus"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="fruit-info">
                            <h3><?php echo $fruit['name']; ?></h3>
                            <div class="fruit-rating">
                                <span class="star">⭐</span> <?php echo $fruit['rating']; ?> (<?php echo $fruit['reviews']; ?>)
                            </div>
                            <p class="fruit-price">$<?php echo $fruit['price']; ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
                </div>
                <button class="fruit-nav fruit-next<?php echo $show_fruit_slider ? ' is-visible' : ''; ?>" type="button" aria-label="Scroll fruits right">
                    <i class="fa-solid fa-chevron-right"></i>
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

                <!-- DYNAMIC PRODUCTS RENDERED VIA PHP FOREACH -->
                <?php foreach ($featured_products as $product): ?>
                    <div class="product-card">
                        <div class="image-container">
                            <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                        </div>
                        <h3><?php echo $product['name']; ?></h3>
                        <p class="price">£ <?php echo $product['price']; ?></p>
                        <div class="card-actions">
                            <button class="add-to-cart">Add TO Cart</button>
                            <div class="quantity-container">
                                <input type="text" value="1" class="quantity-input">
                                <div class="quantity-buttons">
                                    <button class="quantity-btn">+</button>
                                    <button class="quantity-btn">−</button>
                                </div>
                            </div>
                            <button class="wishlist"><svg viewBox="0 0 24 24">
                                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z">
                                            </path>
                                        </svg></button>
                        </div>
                    </div>
                <?php endforeach; ?>

            </div>
        </div>
    </section>
    <button class="scroll-top-btn" id="scrollTopBtn" onclick="window.scrollTo({top: 0, behavior: 'smooth'})">
    <i class="fa-solid fa-arrow-up"></i>
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