<?php
// user_profile.php
// Including main header
include('../includes/header.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile — CleckBasket</title>
    <!-- CSS for profile page -->
    <link rel="stylesheet" href="/cleckbasket/assets/css/profile.css">
    <!-- FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>

    <div class="profile-container">

        <!-- Sidebar -->
        <aside class="profile-sidebar">
            <div class="user-info-card">
                <div class="user-avatar">
                    <!-- Using a nice placeholder avatar generator -->
                    <img src="https://ui-avatars.com/api/?name=Anoushka+M&background=F1F8F1&color=2E7D32&rounded=true&size=100&bold=true"
                        alt="Anoushka's Avatar">
                </div>
                <h3>Anoushka M.</h3>
                <p>anoushka.m@example.com</p>
            </div>

            <nav class="profile-nav">
                <a href="user_profile.php" class="active"><i class="fa-solid fa-user"></i> My Profile</a>
                <a href="recent_orders.php"><i class="fa-solid fa-box"></i> Recent Orders</a>
                <a href="edit_profile.php"><i class="fa-solid fa-pen-to-square"></i> Edit Profile</a>
                <a href="/cleckbasket/includes/pages/wishlist.php"><i class="fa-solid fa-heart"></i> Wishlist</a>
                <a href="#" class="logout-link"><i class="fa-solid fa-arrow-right-from-bracket"></i> Logout</a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="profile-content">
            <h2 class="content-title">My Dashboard</h2>

            <!-- Quick Stats -->
            <div class="dashboard-cards">
                <div class="dash-card">
                    <div class="dash-icon"><i class="fa-solid fa-bag-shopping"></i></div>
                    <div class="dash-info">
                        <h4>12</h4>
                        <p>Total Orders</p>
                    </div>
                </div>
                <div class="dash-card">
                    <div class="dash-icon"><i class="fa-solid fa-wallet"></i></div>
                    <div class="dash-info">
                        <h4>$ 450.00</h4>
                        <p>Total Spent</p>
                    </div>
                </div>
                <div class="dash-card">
                    <div class="dash-icon"><i class="fa-solid fa-star"></i></div>
                    <div class="dash-info">
                        <h4>3</h4>
                        <p>Reviews Given</p>
                    </div>
                </div>
            </div>

            <!-- Personal Info -->
            <section class="profile-details-card">
                <div class="card-header">
                    <h3>Personal Information</h3>
                    <a href="edit_profile.php" class="btn-action"><i class="fa-solid fa-pencil"></i> Edit</a>
                </div>
                <div class="card-body">
                    <div class="detail-row">
                        <span class="detail-label">Full Name</span>
                        <span class="detail-value">Anoushka M.</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Email Address</span>
                        <span class="detail-value">anoushka.m@example.com</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Phone Number</span>
                        <span class="detail-value">+977 980-0000000</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Date of Birth</span>
                        <span class="detail-value">January 15, 1995</span>
                    </div>
                </div>
            </section>

            <!-- Address Book -->
            <section class="profile-details-card">
                <div class="card-header">
                    <h3>Address Book</h3>
                    <button class="btn-action"><i class="fa-solid fa-plus"></i> Add New</button>
                </div>
                <div class="card-body">
                    <div class="address-item">
                        <div class="address-header">
                            <span class="address-badge">Default Delivery</span>
                            <div class="address-actions">
                                <button aria-label="Edit Address"><i class="fa-solid fa-pen"></i></button>
                                <button aria-label="Delete Address"><i class="fa-solid fa-trash"></i></button>
                            </div>
                        </div>
                        <h4>Anoushka's Home</h4>
                        <p>123 Cleck Street, Bakery District<br>Kathmandu, Nepal<br>Bagmati Province, 44600</p>
                    </div>
                </div>
            </section>

        </main>

    </div>

    <?php
    // Include main footer
    include('../includes/footer.php');
    ?>
</body>

</html>