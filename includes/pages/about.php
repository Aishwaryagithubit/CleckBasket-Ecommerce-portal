<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../backend/connect.php';

$is_logged_in = isset($_SESSION['user_id']);

$site_reviews = [];
$conn = getDBConnection();
if ($conn) {
    $sql = "SELECT r.review_title, r.review, r.review_rating, r.created_date,
                   u.firstname, u.lastname
            FROM review r
            JOIN users u ON r.user_id = u.user_id
            WHERE r.product_id IS NULL
            ORDER BY r.created_date DESC
            FETCH FIRST 6 ROWS ONLY";
    $stmt = oci_parse($conn, $sql);
    oci_execute($stmt);
    while ($row = oci_fetch_assoc($stmt)) {
        $site_reviews[] = [
            'title'  => $row['REVIEW_TITLE'],
            'text'   => $row['REVIEW'],
            'rating' => (int)$row['REVIEW_RATING'],
            'name'   => htmlspecialchars(trim($row['FIRSTNAME'] . ' ' . $row['LASTNAME'])),
            'date'   => date('M Y', strtotime($row['CREATED_DATE'])),
        ];
    }
    oci_free_statement($stmt);
    oci_close($conn);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>About Us - CleckBasket</title>

    <!-- Existing CSS Path -->
    <link rel="stylesheet" href="/cleckbasket/assets/css/aboutus.css" />

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #f7f9f4;
            color: #1d1d1d;
            overflow-x: hidden;
        }

        .about-us-page {
            width: 100%;
            overflow: hidden;
        }

        /* ================= HERO SECTION ================= */

        .about-hero-banner {
            position: relative;
            height: 92vh;
            min-height: 760px;
            overflow: hidden;
        }

        .about-hero-banner img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            transform: scale(1.04);
            filter: brightness(0.82);
        }

        .hero-overlay {
            position: absolute;
            inset: 0;
            background:
                linear-gradient(to right,
                    rgba(0, 0, 0, 0.82) 10%,
                    rgba(0, 0, 0, 0.48) 50%,
                    rgba(0, 0, 0, 0.10) 100%);
            display: flex;
            align-items: center;
            padding: 0 8%;
        }

        .hero-content {
            max-width: 720px;
            color: white;
        }

        .hero-tag {
            display: inline-block;
            background: rgba(109, 190, 69, 0.18);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: white;
            padding: 12px 24px;
            border-radius: 60px;
            font-size: 13px;
            margin-bottom: 28px;
            letter-spacing: 2px;
            font-weight: 600;
        }

        .hero-content h1 {
            font-size: clamp(52px, 7vw, 82px);
            line-height: 1.04;
            margin-bottom: 28px;
            font-weight: 800;
            letter-spacing: -2px;
        }

        .hero-content p {
            font-size: 18px;
            line-height: 2;
            color: rgba(255, 255, 255, 0.92);
            margin-bottom: 40px;
            max-width: 620px;
        }

        .hero-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #6DBE45, #4d972d);
            color: white;
            height: 58px;
            padding: 0 34px;
            border-radius: 16px;
            text-decoration: none;
            font-weight: 600;
            transition: 0.35s ease;
            box-shadow: 0 15px 35px rgba(109, 190, 69, 0.35);
        }

        .hero-btn:hover {
            transform: translateY(-5px);
        }

        /* ================= ABOUT SECTION ================= */

        .premium-about-section {
            padding: 130px 8%;
            background: white;
        }

        .premium-about-wrapper {
            display: grid;
            grid-template-columns: 1.05fr 1fr;
            gap: 90px;
            align-items: center;
        }

        .premium-about-image {
            position: relative;
        }

        .premium-about-image img {
            width: 100%;
            height: 700px;
            object-fit: cover;
            border-radius: 34px;
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.12);
        }

        .experience-badge {
            position: absolute;
            bottom: 35px;
            left: -35px;
            width: 210px;
            height: 210px;
            background: linear-gradient(135deg, #6DBE45, #4d972d);
            border-radius: 30px;
            color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            box-shadow: 0 18px 45px rgba(0, 0, 0, 0.18);
        }

        .experience-badge h3 {
            font-size: 64px;
            line-height: 1;
            margin-bottom: 10px;
            font-weight: 800;
        }

        .experience-badge span {
            font-size: 16px;
            text-align: center;
            width: 80%;
            line-height: 1.5;
        }

        .premium-about-content h5 {
            color: #6DBE45;
            font-size: 15px;
            margin-bottom: 18px;
            text-transform: uppercase;
            letter-spacing: 3px;
            font-weight: 700;
        }

        .premium-about-content h2 {
            font-size: clamp(42px, 5vw, 64px);
            line-height: 1.15;
            margin-bottom: 30px;
            color: #111;
            letter-spacing: -1.5px;
        }

        .premium-about-content p {
            font-size: 17px;
            line-height: 2;
            color: #666;
            margin-bottom: 24px;
        }

        .about-features {
            margin-top: 48px;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 24px;
        }

        .feature-box {
            background: #f7f9f4;
            padding: 32px;
            border-radius: 24px;
            transition: 0.35s ease;
        }

        .feature-box:hover {
            transform: translateY(-8px);
            box-shadow: 0 18px 35px rgba(0, 0, 0, 0.06);
        }

        .feature-box h4 {
            margin-bottom: 14px;
            font-size: 22px;
            color: #171717;
        }

        .feature-box p {
            font-size: 15px;
            line-height: 1.8;
            margin: 0;
        }

        /* ================= FARMERS SECTION ================= */

        .farmers-section {
            position: relative;
            padding: 140px 8%;
            text-align: center;
            overflow: hidden;
        }

        .farmers-section::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(rgba(0, 0, 0, 0.76),
                    rgba(0, 0, 0, 0.76)),
                url('/cleckbasket/assets/images/aboutbanner2.png');
            background-size: cover;
            background-position: center;
            transform: scale(1.06);
        }

        .farmers-section>* {
            position: relative;
            z-index: 2;
        }

        .farmers-section h2 {
            font-size: clamp(42px, 5vw, 68px);
            color: white;
            margin-bottom: 24px;
            letter-spacing: -1px;
        }

        .farmers-section p {
            max-width: 900px;
            margin: auto;
            line-height: 2;
            font-size: 18px;
            color: rgba(255, 255, 255, 0.9);
        }

        .farmers-grid {
            margin-top: 80px;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 32px;
        }

        .farmer-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(16px);
            border-radius: 28px;
            padding: 50px 38px;
            transition: 0.35s ease;
            border: 1px solid rgba(255, 255, 255, 0.12);
        }

        .farmer-card:hover {
            transform: translateY(-10px);
            background: rgba(255, 255, 255, 0.12);
        }

        .farmer-icon {
            width: 95px;
            height: 95px;
            margin: auto;
            background: linear-gradient(135deg, #6DBE45, #4d972d);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 42px;
            box-shadow: 0 12px 25px rgba(109, 190, 69, 0.35);
        }

        .farmer-card h3 {
            margin: 28px 0 18px;
            font-size: 28px;
            color: white;
        }

        .farmer-card p {
            font-size: 15px;
            line-height: 1.9;
        }

        /* ================= REVIEWS ================= */

        .reviews-section {
            padding: 130px 8%;
            background: #f7f9f4;
        }

        .reviews-section-title {
            text-align: center;
        }

        .reviews-section-title h2 {
            font-size: clamp(42px, 5vw, 60px);
            margin-bottom: 70px;
            color: #111;
            letter-spacing: -1px;
        }

        .reviews-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 34px;
        }

        .review-card {
            background: white;
            border-radius: 30px;
            padding: 42px;
            text-align: left;
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.06);
            transition: 0.35s ease;
        }

        .review-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.1);
        }

        .review-rating {
            color: #ffc107;
            font-size: 24px;
            letter-spacing: 3px;
        }

        .review-body h3 {
            margin: 20px 0 14px;
            font-size: 28px;
            color: #171717;
        }

        .review-body p {
            color: #666;
            line-height: 1.9;
            font-size: 16px;
        }

        .reviewer-info {
            margin-top: 24px;
            padding-top: 18px;
            border-top: 1px solid #f0f0f0;
        }

        .reviewer-name {
            display: block;
            font-size: 16px;
            font-weight: 700;
            color: #222;
        }

        .reviewer-date {
            font-size: 13px;
            color: #aaa;
            margin-top: 3px;
            display: block;
        }

        .no-reviews-msg {
            grid-column: 1 / -1;
            text-align: center;
            color: #aaa;
            font-size: 17px;
            padding: 40px 0;
        }

        /* ================= REVIEW FORM SECTION ================= */

        .add-review-section {
            padding: 130px 8%;
            background: white;
        }

        .review-form-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 70px;
            align-items: center;
            background: linear-gradient(135deg, #f7f9f4, #ffffff);
            border-radius: 36px;
            padding: 70px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.06);
        }

        .review-small-title {
            display: inline-block;
            color: #6DBE45;
            font-size: 15px;
            font-weight: 700;
            letter-spacing: 3px;
            margin-bottom: 18px;
        }

        .review-left h2 {
            font-size: clamp(40px, 4vw, 60px);
            line-height: 1.15;
            color: #151515;
            margin-bottom: 24px;
        }

        .review-left p {
            font-size: 17px;
            line-height: 2;
            color: #666;
            margin-bottom: 35px;
            max-width: 540px;
        }

        .review-highlight {
            background: white;
            padding: 18px 22px;
            border-radius: 18px;
            margin-bottom: 16px;
            font-weight: 600;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        }

        .review-right {
            background: white;
            border-radius: 30px;
            padding: 45px;
            box-shadow: 0 18px 45px rgba(0, 0, 0, 0.08);
        }

        .input-group {
            margin-bottom: 28px;
        }

        .input-group label {
            display: block;
            margin-bottom: 12px;
            font-size: 15px;
            font-weight: 600;
        }

        .input-group input,
        .input-group textarea {
            width: 100%;
            border: 1px solid #e2e2e2;
            border-radius: 16px;
            padding: 18px 20px;
            font-size: 15px;
            font-family: 'Poppins', sans-serif;
            background: #fafafa;
            outline: none;
            transition: 0.3s ease;
        }

        .input-group input:focus,
        .input-group textarea:focus {
            border-color: #6DBE45;
            background: white;
            box-shadow: 0 0 0 4px rgba(109, 190, 69, 0.12);
        }

        .input-group textarea {
            height: 170px;
            resize: none;
        }

        .star-rating {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
        }

        .rating-star {
            font-size: 42px;
            color: #d9d9d9;
            cursor: pointer;
            transition: 0.25s ease;
        }

        .rating-star:hover,
        .rating-star.active {
            color: #ffc107;
            transform: scale(1.08);
        }

        .rating-text {
            font-size: 14px;
            color: #777;
        }

        .submit-review-btn {
            width: 100%;
            height: 62px;
            border: none;
            border-radius: 18px;
            background: linear-gradient(135deg, #6DBE45, #4d972d);
            color: white;
            font-size: 17px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.35s ease;
            box-shadow: 0 12px 28px rgba(109, 190, 69, 0.28);
        }

        .submit-review-btn:hover {
            transform: translateY(-4px);
        }

        #reviewSuccessMessage {
            margin-top: 24px;
            padding: 18px;
            border-radius: 16px;
            background: rgba(109, 190, 69, 0.12);
            color: #2d7d13;
            font-weight: 600;
            display: none;
        }

        /* ================= STATS ================= */

        .stats-bar {
            background: linear-gradient(135deg, #6DBE45, #4d972d);
            color: white;
            padding: 90px 8%;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 40px;
            text-align: center;
        }

        .stat-item h2 {
            font-size: clamp(44px, 5vw, 70px);
            margin-bottom: 12px;
            font-weight: 800;
        }

        .stat-item p {
            font-size: 18px;
        }

        /* ================= RESPONSIVE ================= */

        @media(max-width: 992px) {

            .premium-about-wrapper,
            .reviews-grid,
            .farmers-grid,
            .stats-bar,
            .review-form-container {
                grid-template-columns: 1fr;
            }

            .hero-content h1 {
                font-size: 52px;
            }

            .premium-about-image img {
                height: auto;
            }

            .experience-badge {
                width: 170px;
                height: 170px;
                left: 20px;
                bottom: 20px;
            }

            .experience-badge h3 {
                font-size: 48px;
            }
        }

        @media(max-width: 768px) {

            .premium-about-section,
            .farmers-section,
            .reviews-section,
            .add-review-section {
                padding: 90px 6%;
            }

            .review-form-container {
                padding: 40px 30px;
            }

            .hero-content h1 {
                font-size: 44px;
            }

            .about-features {
                grid-template-columns: 1fr;
            }
        }

        @media(max-width: 480px) {

            .hero-content h1 {
                font-size: 36px;
            }

            .hero-content p {
                font-size: 15px;
            }

            .review-right {
                padding: 28px;
            }

            .rating-star {
                font-size: 34px;
            }

            .experience-badge {
                width: 140px;
                height: 140px;
            }

            .experience-badge h3 {
                font-size: 36px;
            }

            .experience-badge span {
                font-size: 13px;
            }
        }
    </style>
</head>

<body>

    <?php include '../header.php'; ?>

    <div class="about-us-page">

        <!-- HERO SECTION -->

        <section class="about-hero-banner">

            <img src="/cleckbasket/assets/images/aboutbanner.png"
                alt="Organic fresh produce from farmers">

            <div class="hero-overlay">

                <div class="hero-content">

                    <span class="hero-tag">
                        100% ORGANIC • FARM FRESH
                    </span>

                    <h1>
                        Farmer’s Picked Organic Goodness Delivered Daily
                    </h1>

                    <p>
                        CleckBasket connects local organic farmers directly to your table.
                        Experience hand-picked vegetables, naturally grown fruits, premium dairy,
                        and wholesome groceries harvested fresh every morning.
                    </p>

                    <a href="/cleckbasket/includes/pages/shop.php" class="hero-btn">
                        Explore Fresh Collection
                    </a>

                </div>

            </div>

        </section>

        <!-- ABOUT SECTION -->

        <section class="premium-about-section">

            <div class="premium-about-wrapper">

                <div class="premium-about-image">

                    <img src="/cleckbasket/assets/images/aboutbanner2.png"
                        alt="Organic Farmers">

                    <div class="experience-badge">
                        <h3>15+</h3>
                        <span>Years Of Organic Trust</span>
                    </div>

                </div>

                <div class="premium-about-content">

                    <h5>ABOUT CLECKBASKET</h5>

                    <h2>
                        Premium Organic Marketplace Built With Farmers & Families In Mind
                    </h2>

                    <p>
                        We proudly partner with trusted local farmers who cultivate crops naturally,
                        ensuring freshness, nutrition, and sustainability in every order.
                        From crisp vegetables to handselected fruits, every product is carefully
                        inspected before delivery for maximum quality and freshness.
                    </p>

                    <div class="about-features">

                        <div class="feature-box">
                            <h4>Farm Fresh </h4>
                            <p>Freshly harvested</p>
                        </div>

                        <div class="feature-box">
                            <h4>100% Organic</h4>
                            <p>Naturally grown without harmful chemicals.</p>
                        </div>

                        <div class="feature-box">
                            <h4>Trusted Farmers</h4>
                            <p>Partnering with experienced local organic growers.</p>
                        </div>

                        <div class="feature-box">
                            <h4>Pick Up</h4>
                            <p>Quick pickup with premium freshness.</p>
                        </div>

                    </div>

                </div>

            </div>

        </section>

        <!-- FARMERS SECTION -->

        <section class="farmers-section">

            <h2>Picked With Care From Organic Farms</h2>

            <p>
                Every basket is thoughtfully curated with naturally grown produce sourced
                from local farms committed to sustainability and superior quality.
            </p>

            <div class="farmers-grid">

                <div class="farmer-card">
                    <div class="farmer-icon">🌱</div>
                    <h3>Natural Farming</h3>
                    <p>Eco-friendly farming methods preserving natural ecosystems.</p>
                </div>

                <div class="farmer-card">
                    <div class="farmer-icon">🏪</div>
                    <h3>Pick Up</h3>
                    <p> Premium freshness.</p>
                </div>

                <div class="farmer-card">
                    <div class="farmer-icon">❤️</div>
                    <h3>Healthy Lifestyle</h3>
                    <p>Healthy organic products for better living and wellness.</p>
                </div>

            </div>

        </section>

        <!-- REVIEWS -->

        <section class="reviews-section">

            <div class="reviews-section-title">
                <h2>What Our Customers Say</h2>
            </div>

            <div class="reviews-grid">

                <?php if (empty($site_reviews)): ?>
                    <p class="no-reviews-msg">No reviews yet. Be the first to share your experience!</p>
                <?php else: ?>
                    <?php foreach ($site_reviews as $rev): ?>
                        <div class="review-card">
                            <div class="review-rating">
                                <?php echo str_repeat('★', $rev['rating']) . str_repeat('☆', 5 - $rev['rating']); ?>
                            </div>
                            <div class="review-body">
                                <?php if (!empty($rev['title'])): ?>
                                    <h3><?php echo htmlspecialchars($rev['title']); ?></h3>
                                <?php endif; ?>
                                <p><?php echo htmlspecialchars($rev['text']); ?></p>
                            </div>
                            <div class="reviewer-info">
                                <span class="reviewer-name"><?php echo $rev['name']; ?></span>
                                <span class="reviewer-date"><?php echo $rev['date']; ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

            </div>

        </section>

        <!-- REVIEW FORM -->

        <section class="add-review-section">

            <div class="review-form-container">

                <div class="review-left">

                    <span class="review-small-title">
                        CUSTOMER FEEDBACK
                    </span>

                    <h2>
                        Share Your Experience With CleckBasket
                    </h2>

                    <p>
                        Your feedback helps us improve our service and continue
                        delivering premium farm-fresh organic products.
                    </p>

                    <div class="review-highlight">
                        🌿 Fresh Organic Products
                    </div>

                    <div class="review-highlight">
                        🕒 Pick Up
                    </div>

                    <div class="review-highlight">
                        ❤️ Trusted By Thousands
                    </div>

                </div>

                <div class="review-right">

                    <?php if ($is_logged_in): ?>
                    <form id="customerReviewForm">

                        <div class="input-group">
                            <label>Your Rating</label>
                            <div class="star-rating">
                                <span class="rating-star" data-value="1">★</span>
                                <span class="rating-star" data-value="2">★</span>
                                <span class="rating-star" data-value="3">★</span>
                                <span class="rating-star" data-value="4">★</span>
                                <span class="rating-star" data-value="5">★</span>
                            </div>
                            <input type="hidden" id="selectedRating" value="0">
                            <span class="rating-text">Click stars to rate</span>
                        </div>

                        <div class="input-group">
                            <label>Review Title</label>
                            <input type="text" id="reviewTitle"
                                placeholder="e.g. Absolutely Fresh!"
                                maxlength="100" required>
                        </div>

                        <div class="input-group">
                            <label>Your Message</label>
                            <textarea id="reviewMessage"
                                placeholder="Write your review here..."
                                required></textarea>
                        </div>

                        <button type="submit" class="submit-review-btn">
                            Submit Review
                        </button>

                        <div id="reviewSuccessMessage"></div>

                    </form>
                    <?php else: ?>
                    <div style="text-align:center;padding:40px 20px;">
                        <p style="font-size:18px;color:#555;margin-bottom:24px;">
                            Please log in to leave a review.
                        </p>
                        <a href="/cleckbasket/includes/pages/login.php"
                           style="display:inline-block;background:linear-gradient(135deg,#6DBE45,#4d972d);
                                  color:white;padding:14px 36px;border-radius:14px;
                                  font-weight:600;font-size:16px;text-decoration:none;">
                            Log In
                        </a>
                    </div>
                    <?php endif; ?>

                </div>

            </div>

        </section>

    

    <?php include '../footer.php'; ?>

    <script>
        const stars       = document.querySelectorAll(".rating-star");
        const ratingInput = document.getElementById("selectedRating");
        const ratingText  = document.querySelector(".rating-text");
        const reviewForm  = document.getElementById("customerReviewForm");
        const statusMsg   = document.getElementById("reviewSuccessMessage");

        if (stars.length) {
            stars.forEach(star => {
                star.addEventListener("click", () => {
                    const rating = star.getAttribute("data-value");
                    ratingInput.value = rating;
                    stars.forEach(s => {
                        s.classList.toggle("active", s.getAttribute("data-value") <= rating);
                    });
                    ratingText.textContent = rating + " Star" + (rating > 1 ? "s" : "") + " Selected";
                });
            });
        }

        if (reviewForm) {
            reviewForm.addEventListener("submit", async function (e) {
                e.preventDefault();

                const rating  = parseInt(ratingInput.value);
                const title   = document.getElementById("reviewTitle").value.trim();
                const text    = document.getElementById("reviewMessage").value.trim();
                const btn     = reviewForm.querySelector(".submit-review-btn");

                if (!rating) {
                    showStatus("Please select a star rating.", false);
                    return;
                }
                if (title.length < 2) {
                    showStatus("Please enter a review title.", false);
                    return;
                }
                if (text.length < 5) {
                    showStatus("Please write a longer review.", false);
                    return;
                }

                btn.disabled = true;
                btn.textContent = "Submitting…";

                try {
                    const res  = await fetch("/cleckbasket/backend/submit_site_review.php", {
                        method: "POST",
                        headers: { "Content-Type": "application/json" },
                        body: JSON.stringify({ rating, title, text })
                    });
                    const data = await res.json();

                    if (data.success) {
                        showStatus("✅ " + data.message, true);
                        reviewForm.reset();
                        stars.forEach(s => s.classList.remove("active"));
                        ratingInput.value = "0";
                        ratingText.textContent = "Click stars to rate";
                    } else {
                        showStatus("❌ " + data.message, false);
                    }
                } catch (err) {
                    showStatus("❌ Network error. Please try again.", false);
                } finally {
                    btn.disabled = false;
                    btn.textContent = "Submit Review";
                }
            });
        }

        function showStatus(msg, success) {
            if (!statusMsg) return;
            statusMsg.textContent = msg;
            statusMsg.style.display  = "block";
            statusMsg.style.background = success ? "rgba(109,190,69,0.12)" : "rgba(231,76,60,0.1)";
            statusMsg.style.color      = success ? "#2d7d13" : "#c0392b";
            statusMsg.style.padding    = "18px";
            statusMsg.style.borderRadius = "16px";
            statusMsg.style.fontWeight = "600";
            statusMsg.style.marginTop  = "24px";
        }
    </script>

</body>

</html>