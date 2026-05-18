<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Mock product data
$products = [
    ['id' => 1, 'name' => 'Buffalo Meat', 'category' => 'Butcher', 'price' => 250, 'stock' => 45],
    ['id' => 2, 'name' => 'Bitter Gourd', 'category' => 'Greengrocer', 'price' => 120, 'stock' => 78],
    ['id' => 3, 'name' => 'Fresh Passionfruits', 'category' => 'Greengrocer', 'price' => 180, 'stock' => 32],
    ['id' => 4, 'name' => 'Aged Gouda', 'category' => 'Delicatessen', 'price' => 90, 'stock' => 55],
    ['id' => 5, 'name' => 'Salmon Fillet', 'category' => 'Fishmonger', 'price' => 110, 'stock' => 28],
];

$action = $_GET['action'] ?? null;
$product_id = $_GET['id'] ?? null;

// Handle delete action
if ($action === 'delete' && $product_id) {
    // TODO: Delete from database
    header('Location: products.php?message=Product deleted successfully');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin-style.css">
    <link rel="stylesheet" href="css/table-style.css">
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
                <a href="index.php" class="nav-item">
                    <i class="fas fa-chart-line"></i>
                    <span>Dashboard</span>
                </a>
                <a href="products.php" class="nav-item active">
                    <i class="fas fa-box"></i>
                    <span>Products</span>
                </a>
                <a href="orders.php" class="nav-item">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Orders</span>
                </a>
                <a href="customers.php" class="nav-item">
                    <i class="fas fa-users"></i>
                    <span>Customers</span>
                </a>
                <a href="categories.php" class="nav-item">
                    <i class="fas fa-tags"></i>
                    <span>Categories</span>
                </a>
                <a href="reports.php" class="nav-item">
                    <i class="fas fa-chart-bar"></i>
                    <span>Reports</span>
                </a>
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
                <h1>Products</h1>
                <div class="user-info">
                    <span>Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
                </div>
            </header>

            <div class="content">
                <?php if (isset($_GET['message'])): ?>
                    <div class="success-message">
                        <?php echo htmlspecialchars($_GET['message']); ?>
                    </div>
                <?php endif; ?>

                <div class="page-header">
                    <h2>Product Management</h2>
                    <a href="product-add.php" class="btn-primary">
                        <i class="fas fa-plus"></i> Add Product
                    </a>
                </div>

                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                <td><?php echo htmlspecialchars($product['category']); ?></td>
                                <td>£<?php echo number_format($product['price']); ?></td>
                                <td>
                                    <span class="stock-badge <?php echo $product['stock'] < 30 ? 'low' : 'good'; ?>">
                                        <?php echo $product['stock']; ?> units
                                    </span>
                                </td>
                                <td class="actions">
                                    <a href="product-edit.php?id=<?php echo $product['id']; ?>" class="btn-edit">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="products.php?action=delete&id=<?php echo $product['id']; ?>" class="btn-delete" onclick="return confirm('Delete this product?');">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                </td>
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
