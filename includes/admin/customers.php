<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

if (!isset($_SESSION['customers'])) {
    $_SESSION['customers'] = [
        ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com', 'status' => 'Active', 'registered' => '2025-11-12', 'orders' => 12, 'valuation' => 1500],
        ['id' => 2, 'name' => 'Jane Smith', 'email' => 'jane@example.com', 'status' => 'Active', 'registered' => '2026-01-05', 'orders' => 8, 'valuation' => 850],
        ['id' => 3, 'name' => 'Mike Johnson', 'email' => 'mike@example.com', 'status' => 'Inactive', 'registered' => '2025-08-22', 'orders' => 2, 'valuation' => 120],
        ['id' => 4, 'name' => 'Emily Davis', 'email' => 'emily@example.com', 'status' => 'Active', 'registered' => '2026-05-14', 'orders' => 1, 'valuation' => 150],
    ];
}

$customers = &$_SESSION['customers'];
$message = isset($_GET['message']) ? trim($_GET['message']) : '';
$error = '';
$showAddCustomerForm = isset($_GET['new_customer']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'add_customer') {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $status = ($_POST['status'] === 'Inactive') ? 'Inactive' : 'Active';
        $orders = max(0, (int)($_POST['orders'] ?? 0));
        $valuation = max(0, (int)($_POST['valuation'] ?? 0));

        if ($name === '' || $email === '') {
            $error = 'Name and email are required to add a customer.';
            $showAddCustomerForm = true;
        } else {
            $newId = 1;
            foreach ($customers as $customer) {
                $newId = max($newId, $customer['id'] + 1);
            }
            $customers[] = [
                'id' => $newId,
                'name' => $name,
                'email' => $email,
                'status' => $status,
                'registered' => date('Y-m-d'),
                'orders' => $orders,
                'valuation' => $valuation,
            ];
            header('Location: customers.php?message=' . rawurlencode('Customer added successfully.'));
            exit;
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'delete_customer' && isset($_POST['customer_id'])) {
        $customerId = (int)$_POST['customer_id'];
        foreach ($customers as $index => $customer) {
            if ($customer['id'] === $customerId) {
                array_splice($customers, $index, 1);
                header('Location: customers.php?message=' . rawurlencode('Customer deleted successfully.'));
                exit;
            }
        }
    }
}

$activeCustomers = count(array_filter($customers, function ($customer) {
    return $customer['status'] === 'Active';
}));

$todayNew = count(array_filter($customers, function ($customer) {
    return $customer['registered'] === date('Y-m-d');
}));

$customerCount = count($customers);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Directory - Admin Panel</title>
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
                <a href="customers.php" class="nav-item active">
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

        <main class="main-content">
            <header class="top-bar">
                <h1>Customer Directory</h1>
                <div class="user-info">
                    <a href="customers.php?new_customer=1" class="btn-primary">+ Add Customer</a>
                    <span><?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
                </div>
            </header>

            <div class="content">
                <?php if ($message): ?>
                    <div style="background: #e8f5e9; color: #2e7d32; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div style="background: #ffebee; color: #c62828; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <?php if ($showAddCustomerForm): ?>
                <div class="card" id="add-customer-form" style="margin-bottom: 30px;">
                    <div class="card-header">
                        <h2>Add New Customer</h2>
                    </div>
                    <form method="POST" style="display: grid; gap: 18px; padding: 20px;">
                        <input type="hidden" name="action" value="add_customer">
                        <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px;">
                            <div class="form-group">
                                <label for="name">Full Name</label>
                                <input type="text" id="name" name="name" required placeholder="Customer name">
                            </div>
                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <input type="email" id="email" name="email" required placeholder="customer@example.com">
                            </div>
                        </div>
                        <div style="display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px;">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select id="status" name="status">
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="orders">Orders</label>
                                <input type="number" id="orders" name="orders" min="0" value="0">
                            </div>
                            <div class="form-group">
                                <label for="valuation">Valuation (£)</label>
                                <input type="number" id="valuation" name="valuation" min="0" value="0">
                            </div>
                        </div>
                        <div style="display: flex; gap: 12px; justify-content: flex-end;">
                            <a href="customers.php" class="btn-secondary" style="padding: 10px 16px; display: inline-flex; align-items: center; justify-content: center;">Cancel</a>
                            <button type="submit" class="btn-primary" style="padding: 10px 16px;">Save Customer</button>
                        </div>
                    </form>
                </div>
                <?php endif; ?>

                <!-- Metrics Cards -->
                <section class="metrics" style="grid-template-columns: repeat(3, 1fr);">
                    <div class="metric-card">
                        <div class="metric-icon customers">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <div class="metric-details">
                            <p>Total Active Customers</p>
                            <h3><?php echo $activeCustomers; ?></h3>
                        </div>
                    </div>

                    <div class="metric-card">
                        <div class="metric-icon sales">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <div class="metric-details">
                            <p>New Today</p>
                            <h3><?php echo $todayNew; ?></h3>
                        </div>
                    </div>

                    <div class="metric-card">
                        <div class="metric-icon products">
                            <i class="fas fa-heartbeat"></i>
                        </div>
                        <div class="metric-details">
                            <p>Total Customers</p>
                            <h3><?php echo $customerCount; ?></h3>
                        </div>
                    </div>
                </section>

                <div class="card">
                    <div class="action-bar">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" placeholder="Search customers by name or email...">
                        </div>
                        <div style="display: flex; gap: 10px;">
                            <button class="filter-btn"><i class="fas fa-filter"></i> Filter</button>
                            <button class="filter-btn"><i class="fas fa-file-export"></i> Export</button>
                        </div>
                    </div>

                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Customer Name</th>
                                    <th>Status</th>
                                    <th>Registered Date</th>
                                    <th>Order History</th>
                                    <th>Valuation</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($customers as $customer): ?>
                                <tr>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 12px;">
                                            <div style="width: 36px; height: 36px; border-radius: 50%; background: var(--light-bg); display: flex; align-items: center; justify-content: center; font-weight: 600; color: var(--primary-brown);">
                                                <?php echo substr($customer['name'], 0, 1); ?>
                                            </div>
                                            <div>
                                                <div style="font-weight: 500; color: var(--text-main);"><?php echo htmlspecialchars($customer['name']); ?></div>
                                                <div style="font-size: 12px; color: var(--text-muted);"><?php echo htmlspecialchars($customer['email']); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="status <?php echo strtolower($customer['status']); ?>">
                                            <?php echo htmlspecialchars($customer['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($customer['registered']); ?></td>
                                    <td><?php echo $customer['orders']; ?> orders</td>
                                    <td style="font-weight: 500;">£<?php echo number_format($customer['valuation']); ?></td>
                                    <td>
                                        <form method="POST" style="display:inline-block; margin:0;">
                                            <input type="hidden" name="action" value="delete_customer">
                                            <input type="hidden" name="customer_id" value="<?php echo $customer['id']; ?>">
                                            <button type="submit" class="filter-btn" style="padding: 8px 10px; font-size: 13px;">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
