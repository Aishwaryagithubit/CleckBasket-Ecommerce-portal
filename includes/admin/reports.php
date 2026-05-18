<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$reports = [
    ['month' => 'January 2026', 'sales' => 85000, 'orders' => 234, 'customers' => 156],
    ['month' => 'February 2026', 'sales' => 92000, 'orders' => 267, 'customers' => 178],
    ['month' => 'March 2026', 'sales' => 78000, 'orders' => 210, 'customers' => 142],
    ['month' => 'April 2026', 'sales' => 95000, 'orders' => 280, 'customers' => 195],
    ['month' => 'May 2026', 'sales' => 45000, 'orders' => 125, 'customers' => 89],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Admin Panel</title>
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
                <a href="reports.php" class="nav-item active"><i class="fas fa-chart-bar"></i><span>Reports</span></a>
                <a href="settings.php" class="nav-item"><i class="fas fa-cog"></i><span>Settings</span></a>
                <a href="logout.php" class="nav-item logout"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
            </nav>
        </aside>

        <main class="main-content">
            <header class="top-bar">
                <h1>Reports & Analytics</h1>
            </header>

            <div class="content">
                <div class="page-header">
                    <h2>Sales Analytics</h2>
                </div>

                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th>Sales</th>
                                <th>Orders</th>
                                <th>Customers</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reports as $report): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($report['month']); ?></td>
                                <td>£<?php echo number_format($report['sales']); ?></td>
                                <td><?php echo $report['orders']; ?></td>
                                <td><?php echo $report['customers']; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
