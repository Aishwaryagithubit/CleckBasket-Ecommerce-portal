<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Initialize trader registration requests in session so approvals persist during the session
if (!isset($_SESSION['trader_requests'])) {
    $_SESSION['trader_requests'] = [
        ['id' => 'TR-1001', 'name' => 'Sunny Vale Farms', 'category' => 'Organic Produce', 'email' => 'hello@sunnyvale.co.uk', 'status' => 'Pending', 'submitted' => '2026-05-13'],
        ['id' => 'TR-1002', 'name' => 'Baker Street Bakes', 'category' => 'Bakery', 'email' => 'contact@bakerstreet.co.uk', 'status' => 'Pending', 'submitted' => '2026-05-12'],
        ['id' => 'TR-1003', 'name' => 'Riverbank Fishery', 'category' => 'Fishmonger', 'email' => 'admin@riverbankfishery.co.uk', 'status' => 'Approved', 'submitted' => '2026-05-10'],
    ];
}

$trader_requests = &$_SESSION['trader_requests'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['trader_id'], $_POST['action'])) {
    $traderId = $_POST['trader_id'];
    $action = $_POST['action'];

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
}

$pending_traders = count(array_filter($trader_requests, function($request) {
    return $request['status'] === 'Pending';
}));

// Mock data for dashboard metrics
$dashboard_data = [
    'total_sales' => 45000,
    'total_orders' => 234,
    'total_customers' => 567,
    'total_products' => 89,
    'recent_orders' => [
        ['id' => '001', 'customer' => 'John Doe', 'amount' => 2500, 'status' => 'Completed', 'date' => '2026-05-10'],
        ['id' => '002', 'customer' => 'Jane Smith', 'amount' => 1800, 'status' => 'Pending', 'date' => '2026-05-09'],
        ['id' => '003', 'customer' => 'Mike Johnson', 'amount' => 3200, 'status' => 'Completed', 'date' => '2026-05-09'],
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - CleckBasket</title>
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
                <a href="index.php" class="nav-item active">
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
                <a href="add-trader.php" class="nav-item">
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

        <!-- Main Content -->
        <main class="main-content">
            <header class="top-bar">
                <h1>Admin Management Overview</h1>
                <div class="user-info">
                    <a href="product-add.php" class="btn-primary">+ New Action</a>
                    <span><?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
                </div>
            </header>

            <div class="content">
                <?php if ($message): ?>
                    <div style="background: #e8f5e9; color: #2e7d32; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <!-- Metrics Cards -->
                <section class="metrics">
                    <div class="metric-card">
                        <div class="metric-icon sales">
                            <i class="fas fa-pound-sign"></i>
                        </div>
                        <div class="metric-details">
                            <p>Total Revenue</p>
                            <h3>£<?php echo number_format($dashboard_data['total_sales']); ?></h3>
                        </div>
                    </div>

                    <div class="metric-card">
                        <div class="metric-icon orders">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="metric-details">
                            <p>Total Orders</p>
                            <h3><?php echo $dashboard_data['total_orders']; ?></h3>
                        </div>
                    </div>

                    <div class="metric-card">
                        <div class="metric-icon customers">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="metric-details">
                            <p>Registrations</p>
                            <h3><?php echo $dashboard_data['total_customers']; ?></h3>
                        </div>
                    </div>

                    <div class="metric-card">
                        <div class="metric-icon products">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <div class="metric-details">
                            <p>Pending Requests</p>
                            <h3><a href="add-trader.php" style="color: inherit; text-decoration: none;"><?php echo $pending_traders; ?></a></h3>
                        </div>
                    </div>
                </section>

                <div class="dashboard-grid">
                    <!-- Left Column -->
                    <div class="left-column">
                        <div class="card">
                            <div class="card-header">
                                <h2>Monthly Sales Statistics</h2>
                                <select style="padding: 5px 10px; border-radius: 20px; border: 1px solid #e8e8e8; outline: none;">
                                    <option>This Year</option>
                                    <option>Last Year</option>
                                </select>
                            </div>
                            <div class="chart-container">
                                <div class="chart-bar" style="height: 40%;" data-label="Jan"></div>
                                <div class="chart-bar" style="height: 60%;" data-label="Feb"></div>
                                <div class="chart-bar" style="height: 45%;" data-label="Mar"></div>
                                <div class="chart-bar" style="height: 80%;" data-label="Apr"></div>
                                <div class="chart-bar" style="height: 100%; background: var(--accent-green);" data-label="May"></div>
                                <div class="chart-bar" style="height: 70%;" data-label="Jun"></div>
                                <div class="chart-bar" style="height: 50%;" data-label="Jul"></div>
                                <div class="chart-bar" style="height: 85%;" data-label="Aug"></div>
                                <div class="chart-bar" style="height: 65%;" data-label="Sep"></div>
                                <div class="chart-bar" style="height: 90%;" data-label="Oct"></div>
                                <div class="chart-bar" style="height: 75%;" data-label="Nov"></div>
                                <div class="chart-bar" style="height: 95%;" data-label="Dec"></div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h2>Recent Transactions</h2>
                                <a href="orders.php" style="color: var(--accent-green); text-decoration: none; font-size: 14px; font-weight: 500;">View All</a>
                            </div>
                            <div class="table-container">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Customer</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($dashboard_data['recent_orders'] as $order): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($order['id']); ?></td>
                                            <td><?php echo htmlspecialchars($order['customer']); ?></td>
                                            <td>£<?php echo number_format($order['amount']); ?></td>
                                            <td>
                                                <span class="status <?php echo strtolower($order['status']); ?>">
                                                    <?php echo htmlspecialchars($order['status']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo htmlspecialchars($order['date']); ?></td>
                                            <td><i class="fas fa-ellipsis-h action-menu"></i></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="right-column">
                        <div class="card dark-box">
                            <h3>Performance by Category</h3>
                            <div class="list-item" style="border-bottom-color: rgba(255,255,255,0.1);">
                                <div class="list-info" style="flex:1;">
                                    <h4 style="color:white;">Organic Produce</h4>
                                    <div style="width: 100%; height: 6px; background: rgba(255,255,255,0.2); border-radius: 3px; margin-top: 8px;">
                                        <div style="width: 75%; height: 100%; background: var(--accent-green-light); border-radius: 3px;"></div>
                                    </div>
                                </div>
                                <span style="font-weight: 600;">75%</span>
                            </div>
                            <div class="list-item" style="border-bottom-color: rgba(255,255,255,0.1);">
                                <div class="list-info" style="flex:1;">
                                    <h4 style="color:white;">Bakery & Sweets</h4>
                                    <div style="width: 100%; height: 6px; background: rgba(255,255,255,0.2); border-radius: 3px; margin-top: 8px;">
                                        <div style="width: 60%; height: 100%; background: var(--accent-green-light); border-radius: 3px;"></div>
                                    </div>
                                </div>
                                <span style="font-weight: 600;">60%</span>
                            </div>
                            <div class="list-item" style="border-bottom:none;">
                                <div class="list-info" style="flex:1;">
                                    <h4 style="color:white;">Dairy Products</h4>
                                    <div style="width: 100%; height: 6px; background: rgba(255,255,255,0.2); border-radius: 3px; margin-top: 8px;">
                                        <div style="width: 45%; height: 100%; background: var(--accent-green-light); border-radius: 3px;"></div>
                                    </div>
                                </div>
                                <span style="font-weight: 600;">45%</span>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h2>Weekly Financials</h2>
                            </div>
                            <div class="list-item">
                                <div class="list-icon"><i class="fas fa-arrow-up" style="color: #4caf50;"></i></div>
                                <div class="list-info">
                                    <h4>Income</h4>
                                    <p>Total revenue this week</p>
                                </div>
                                <div style="margin-left: auto; font-weight: 600; color: #2e7d32;">+£12,450</div>
                            </div>
                            <div class="list-item">
                                <div class="list-icon"><i class="fas fa-arrow-down" style="color: #f44336;"></i></div>
                                <div class="list-info">
                                    <h4>Expenses</h4>
                                    <p>Operating costs</p>
                                </div>
                                <div style="margin-left: auto; font-weight: 600; color: #c62828;">-£3,200</div>
                            </div>
                            <div class="list-item" style="border-bottom:none;">
                                <div class="list-icon"><i class="fas fa-wallet"></i></div>
                                <div class="list-info">
                                    <h4>Net Profit</h4>
                                    <p>After all deductions</p>
                                </div>
                                <div style="margin-left: auto; font-weight: 700;">£9,250</div>
                            </div>
                        </div>
                        
                        <div class="card">
                            <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; gap: 12px;">
                                <h2>Pending Approvals</h2>
                                <a href="add-trader.php" style="font-size: 14px; color: var(--accent-green); text-decoration: none;">Manage &rarr;</a>
                            </div>
                            <?php 
                            $count = 0;
                            foreach ($trader_requests as $request): 
                                if ($request['status'] === 'Pending' && $count < 3):
                                    $count++;
                            ?>
                            <a href="add-trader.php" class="list-item" style="display: flex; align-items: center; text-decoration: none; color: inherit;">
                                <div class="list-icon"><i class="fas fa-store"></i></div>
                                <div class="list-info">
                                    <h4><?php echo htmlspecialchars($request['name']); ?></h4>
                                    <p><?php echo htmlspecialchars($request['category']); ?></p>
                                </div>
                                <span class="status pending">Pending</span>
                            </a>
                            <?php 
                                endif;
                            endforeach; 
                            if ($count === 0):
                            ?>
                            <p style="color: var(--text-muted); font-size: 14px;">No pending approvals.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
