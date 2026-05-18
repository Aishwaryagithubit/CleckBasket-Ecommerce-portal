<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$message = '';
$trader_requests = &$_SESSION['trader_requests'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['trader_id'], $_POST['trader_action'])) {
        $traderId = $_POST['trader_id'];
        $action = $_POST['trader_action'];

        foreach ($trader_requests as &$request) {
            if ($request['id'] === $traderId && $request['status'] === 'Pending') {
                if ($action === 'approve') {
                    $request['status'] = 'Approved';
                    $message = 'Trader registration approved.';
                } elseif ($action === 'reject') {
                    $request['status'] = 'Rejected';
                    $message = 'Trader registration rejected.';
                }
                break;
            }
        }
        unset($request);
    } else {
        $message = 'New merchant added successfully!';
    }
}

$pending_trader_requests = array_filter($trader_requests, function($request) {
    return $request['status'] === 'Pending';
});

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trader Management - Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin-style.css">
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="logo">
                <a href="index.php" style="text-decoration:none; color: inherit;">
                    <img src="../assets/images/logo.png" alt="CleckBasket" style="height: 40px; width: auto;">
                </a>
            </div>

            <nav class="nav-menu">
                <div class="nav-section-title">Overview</div>
                <a href="index.php" class="nav-item">
                    <i class="fas fa-chart-line"></i>
                    <span>Dashboard</span>
                </a>
                
                <div class="nav-section-title">Management</div>
                <a href="customers.php" class="nav-item">
                    <i class="fas fa-users"></i>
                    <span>Customer Directory</span>
                </a>
                <a href="products.php" class="nav-item">
                    <i class="fas fa-box"></i>
                    <span>Products</span>
                </a>
                <a href="orders.php" class="nav-item">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Orders</span>
                </a>
                <a href="add-trader.php" class="nav-item active">
                    <i class="fas fa-user-plus"></i>
                    <span>Trader Management</span>
                </a>
                <a href="categories.php" class="nav-item">
                    <i class="fas fa-tags"></i>
                    <span>Categories</span>
                </a>
                
                <div class="nav-section-title">System</div>
                <a href="settings.php" class="nav-item">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a>
                <a href="logout.php" class="nav-item logout">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </nav>
        </aside>

        <main class="main-content">
            <header class="top-bar">
                <h1>Trader Management</h1>
                <div class="user-info">
                    <a href="index.php" class="filter-btn" style="text-decoration:none;"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
                    <span><?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
                </div>
            </header>

            <div class="content">
                <?php if ($message): ?>
                    <div style="background: #e8f5e9; color: #2e7d32; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <div class="dashboard-grid">
                    <!-- Main Form Column -->
                    <div class="left-column">
                        <form method="POST" action="add-trader.php">
                            <!-- Merchant Details -->
                            <div class="card">
                                <div class="card-header">
                                    <h2>Merchant Details</h2>
                                </div>
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label>Business Name</label>
                                        <input type="text" name="business_name" required placeholder="e.g. Sunny Vale Farms">
                                    </div>
                                    <div class="form-group">
                                        <label>Contact Person</label>
                                        <input type="text" name="contact_person" required placeholder="Full Name">
                                    </div>
                                    <div class="form-group">
                                        <label>Email Address</label>
                                        <input type="email" name="email" required placeholder="contact@business.com">
                                    </div>
                                    <div class="form-group">
                                        <label>Phone Number</label>
                                        <input type="text" name="phone" required placeholder="+44 123 456 7890">
                                    </div>
                                </div>
                            </div>

                            <!-- Initial Setup Options -->
                            <div class="card">
                                <div class="card-header">
                                    <h2>Initial Setup Options</h2>
                                </div>
                                <div class="form-group">
                                    <label>Business Category</label>
                                    <div class="pill-options">
                                        <input type="radio" id="cat_organic" name="category" value="Organic Produce" class="pill-checkbox" checked>
                                        <label for="cat_organic" class="pill-label">Organic Produce</label>
                                        
                                        <input type="radio" id="cat_bakery" name="category" value="Bakery" class="pill-checkbox">
                                        <label for="cat_bakery" class="pill-label">Bakery & Sweets</label>
                                        
                                        <input type="radio" id="cat_dairy" name="category" value="Dairy" class="pill-checkbox">
                                        <label for="cat_dairy" class="pill-label">Dairy Products</label>
                                        
                                        <input type="radio" id="cat_meat" name="category" value="Meat" class="pill-checkbox">
                                        <label for="cat_meat" class="pill-label">Ethical Meats</label>
                                    </div>
                                </div>
                                <div class="form-group" style="margin-top: 25px;">
                                    <label>Store Configuration</label>
                                    <div class="pill-options">
                                        <input type="checkbox" id="conf_pickup" name="config[]" value="Click & Collect" class="pill-checkbox" checked>
                                        <label for="conf_pickup" class="pill-label">Click & Collect Enabled</label>
                                        
                                        <input type="checkbox" id="conf_delivery" name="config[]" value="Local Delivery" class="pill-checkbox">
                                        <label for="conf_delivery" class="pill-label">Local Delivery</label>
                                        
                                        <input type="checkbox" id="conf_inventory" name="config[]" value="Auto Inventory" class="pill-checkbox" checked>
                                        <label for="conf_inventory" class="pill-label">Auto Inventory Tracking</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Quality Standards Compliance -->
                            <div class="card dark-box" style="margin-bottom: 20px;">
                                <h3>Quality Standards Compliance</h3>
                                <p style="color: #bcaaa4; font-size: 13px; margin-bottom: 20px;">Ensure the merchant meets our community and quality guidelines.</p>
                                
                                <div class="form-group">
                                    <label>Certification Number (if applicable)</label>
                                    <input type="text" name="cert_number" placeholder="Enter certificate ID">
                                </div>
                                
                                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; margin-top: 15px;">
                                    <input type="checkbox" required style="width: auto;">
                                    <span style="font-size: 14px; color: var(--white);">I confirm this merchant has been vetted for organic/local standards.</span>
                                </label>
                            </div>

                            <div style="display: flex; justify-content: flex-end; margin-bottom: 40px;">
                                <button type="submit" class="btn-submit">Register Merchant</button>
                            </div>
                        </form>
                    </div>

                    <!-- Right Column -->
                    <div class="right-column">
                        <!-- Pending Trader Requests -->
                        <div class="card">
                            <div class="card-header">
                                <h2>Pending Trader Requests</h2>
                            </div>
                            <?php if (count($pending_trader_requests) > 0): ?>
                                <?php foreach ($pending_trader_requests as $request): ?>
                                    <div class="list-item" style="display: flex; align-items: center; justify-content: space-between; gap: 12px;">
                                        <div class="list-info" style="flex: 1;">
                                            <h4><?php echo htmlspecialchars($request['name']); ?></h4>
                                            <p style="margin: 4px 0 0; color: var(--text-muted); font-size: 13px;">
                                                <?php echo htmlspecialchars($request['category']); ?> · <?php echo htmlspecialchars($request['email']); ?>
                                            </p>
                                            <p style="margin: 4px 0 0; color: var(--text-muted); font-size: 12px;">Submitted: <?php echo htmlspecialchars($request['submitted']); ?></p>
                                        </div>
                                        <div style="display: flex; gap: 8px; flex-shrink: 0;">
                                            <form method="POST" style="margin:0;">
                                                <input type="hidden" name="trader_id" value="<?php echo htmlspecialchars($request['id']); ?>">
                                                <button type="submit" name="trader_action" value="approve" class="btn-primary" style="padding: 8px 12px; font-size: 13px;">Approve</button>
                                            </form>
                                            <form method="POST" style="margin:0;">
                                                <input type="hidden" name="trader_id" value="<?php echo htmlspecialchars($request['id']); ?>">
                                                <button type="submit" name="trader_action" value="reject" class="btn-secondary" style="padding: 8px 12px; font-size: 13px;">Reject</button>
                                            </form>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p style="color: var(--text-muted); font-size: 14px;">No pending trader requests.</p>
                            <?php endif; ?>
                        </div>

                        <!-- Pro Tip Card -->
                        <div class="card" style="background: var(--accent-green-soft); border-color: var(--accent-green-soft);">
                            <div style="display: flex; gap: 15px;">
                                <i class="fas fa-lightbulb" style="color: var(--accent-green); font-size: 24px; margin-top: 5px;"></i>
                                <div>
                                    <h3 style="color: var(--accent-green); font-size: 16px; margin-bottom: 10px;">Pro Tip</h3>
                                    <p style="color: #2e7d32; font-size: 13px; line-height: 1.5;">
                                        Ensure new merchants complete their public profile fully before going live. Merchants with high-quality photos and detailed business stories see 40% more initial sales on CleckBasket.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
