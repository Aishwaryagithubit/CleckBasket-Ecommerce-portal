<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // TODO: Save settings to database
    $message = 'Settings updated successfully!';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin-style.css">
    <link rel="stylesheet" href="css/table-style.css">
</head>
<body>
    <div class="admin-container">
        <aside class="sidebar">
            <div class="logo"><a href="index.php" style="text-decoration:none; color: inherit;"><img src="../assets/images/logo.png" alt="CleckBasket" style="height: 40px; width: auto;"></a></div>
            <nav class="nav-menu">
                <a href="index.php" class="nav-item"><i class="fas fa-chart-line"></i><span>Dashboard</span></a>
                <a href="products.php" class="nav-item"><i class="fas fa-box"></i><span>Products</span></a>
                <a href="orders.php" class="nav-item"><i class="fas fa-shopping-cart"></i><span>Orders</span></a>
                <a href="customers.php" class="nav-item"><i class="fas fa-users"></i><span>Customers</span></a>
                <a href="categories.php" class="nav-item"><i class="fas fa-tags"></i><span>Categories</span></a>
                <a href="reports.php" class="nav-item"><i class="fas fa-chart-bar"></i><span>Reports</span></a>
                <a href="settings.php" class="nav-item active"><i class="fas fa-cog"></i><span>Settings</span></a>
                <a href="logout.php" class="nav-item logout"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
            </nav>
        </aside>

        <main class="main-content">
            <header class="top-bar">
                <h1>Settings</h1>
            </header>

            <div class="content">
                <?php if ($message): ?>
                    <div class="success-message"><?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>

                <div class="form-container">
                    <h2>Admin Configuration</h2>
                    <form method="POST">
                        <div class="form-group">
                            <label for="site_name">Site Name</label>
                            <input type="text" id="site_name" name="site_name" value="CleckBasket" required>
                        </div>

                        <div class="form-group">
                            <label for="site_email">Site Email</label>
                            <input type="email" id="site_email" name="site_email" value="admin@cleckbasket.com" required>
                        </div>

                        <div class="form-group">
                            <label for="currency">Currency</label>
                            <select id="currency" name="currency" required>
                                <option value="GBP" selected>British Pound (£)</option>
                                <option value="INR">Indian Rupee (₹)</option>
                                <option value="USD">US Dollar ($)</option>
                                <option value="EUR">Euro (€)</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="items_per_page">Items Per Page</label>
                            <input type="number" id="items_per_page" name="items_per_page" value="10" required>
                        </div>

                        <div class="form-group">
                            <label for="maintenance_mode">
                                <input type="checkbox" id="maintenance_mode" name="maintenance_mode">
                                Enable Maintenance Mode
                            </label>
                        </div>

                        <div class="form-buttons">
                            <button type="submit" class="btn-primary">Save Settings</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
