<?php /* Product Feedback Page */ ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Product Feedback - CleckBasket</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="../../assets/css/style.css" />
    <link rel="stylesheet" href="../../assets/css/product_feedback.css" />
</head>

<body>
    <?php include '../header.php'; ?>

    <div class="feedback-page">

        <!-- ===== PAGE HEADER ===== -->
        <div class="feedback-page-header">
            <h1>PRODUCT FEEDBACK</h1>
            <p>Your opinion matters. Help us improve our curated selections.</p>
        </div>

        <!-- ===== MAIN CONTENT ===== -->
        <div class="feedback-main">
            
            <div class="feedback-content-wrapper">
                <!-- LEFT: INFO PANEL -->
                <div class="feedback-info-panel">
                    <div class="info-badge">
                        <span>Quality Matters</span>
                    </div>
                    <h2 class="info-heading">Share Your Experience</h2>
                    <p class="info-description">
                        At CleckBasket, we pride ourselves on providing the finest, freshest, and most carefully curated products. Whether it's our organic vegetables, artisan bakery, or exquisite delicatessen, your feedback helps us maintain the highest standards.
                    </p>

                    <div class="info-highlights">
                        <div class="highlight-item">
                            <div class="highlight-icon">
                                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M9 12L11 14L15 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <div class="highlight-text">Directly impacts our curations</div>
                        </div>
                        <div class="highlight-item">
                            <div class="highlight-icon">
                                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M12 16V12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M12 8H12.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <div class="highlight-text">Helps other shoppers decide</div>
                        </div>
                    </div>
                </div>

                <!-- RIGHT: FORM CARD -->
                <div class="feedback-form-card">
                    <h2 class="form-card-title">Leave a Review</h2>

                    <form class="feedback-form" action="#" method="POST" enctype="multipart/form-data">

                        <div class="form-row">
                            <div class="form-field">
                                <label for="fullName">Full Name</label>
                                <input type="text" id="fullName" name="full_name" placeholder="John Doe" required />
                            </div>
                            <div class="form-field">
                                <label for="emailAddress">Email Address</label>
                                <input type="email" id="emailAddress" name="email" placeholder="john@example.com" required />
                            </div>
                        </div>

                        <div class="form-field">
                            <label for="productName">Product Name</label>
                            <input type="text" id="productName" name="product_name" placeholder="e.g. Organic Avocados" required />
                        </div>

                        <div class="form-field">
                            <label>Product Rating</label>
                            <div class="star-rating">
                                <input type="radio" id="star5" name="rating" value="5" />
                                <label for="star5" title="5 stars">★</label>
                                <input type="radio" id="star4" name="rating" value="4" />
                                <label for="star4" title="4 stars">★</label>
                                <input type="radio" id="star3" name="rating" value="3" />
                                <label for="star3" title="3 stars">★</label>
                                <input type="radio" id="star2" name="rating" value="2" />
                                <label for="star2" title="2 stars">★</label>
                                <input type="radio" id="star1" name="rating" value="1" />
                                <label for="star1" title="1 star">★</label>
                            </div>
                        </div>

                        <div class="form-field">
                            <label for="review">Your Feedback</label>
                            <textarea id="review" name="review" placeholder="Tell us what you liked or how we can improve..." required></textarea>
                        </div>

                        <div class="form-field file-upload">
                            <label for="productImage">Attach an Image (Optional)</label>
                            <input type="file" id="productImage" name="product_image" accept="image/*" />
                        </div>

                        <button type="submit" class="btn-submit">Submit Feedback</button>

                    </form>
                </div>
            </div>

        </div>

    </div>

    <?php include '../footer.php'; ?>
</body>
</html>
