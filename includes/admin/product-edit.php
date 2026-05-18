<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$product_id = $_GET['id'] ?? null;
$defaultCategories = ['Butcher', 'Greengrocer', 'Delicatessen', 'Fishmonger', 'Bakery'];
$categories = isset($_SESSION['admin_categories']) ? array_column($_SESSION['admin_categories'], 'name') : $defaultCategories;

// Mock product data
$product = [
    'id' => 1,
    'name' => 'Buffalo Meat',
    'category' => 'Butcher',
    'price' => 250,
    'stock' => 45,
    'description' => 'Fresh buffalo meat'
];

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // TODO: Update in database
    $message = 'Product updated successfully!';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin-style.css">
    <link rel="stylesheet" href="css/table-style.css">
</head>
<body>
    <div class="admin-container">
        <aside class="sidebar">
            <div class="logo">
                <a href="index.php" style="text-decoration:none; color: inherit;">
                    <img src="../assets/images/logo.png" alt="CleckBasket" style="height: 40px; width: auto;">
                </a>
            </div>
            <nav class="nav-menu">
                <a href="index.php" class="nav-item"><i class="fas fa-chart-line"></i><span>Dashboard</span></a>
                <a href="products.php" class="nav-item active"><i class="fas fa-box"></i><span>Products</span></a>
                <a href="orders.php" class="nav-item"><i class="fas fa-shopping-cart"></i><span>Orders</span></a>
                <a href="customers.php" class="nav-item"><i class="fas fa-users"></i><span>Customers</span></a>
                <a href="categories.php" class="nav-item"><i class="fas fa-tags"></i><span>Categories</span></a>
                <a href="reports.php" class="nav-item"><i class="fas fa-chart-bar"></i><span>Reports</span></a>
                <a href="settings.php" class="nav-item"><i class="fas fa-cog"></i><span>Settings</span></a>
                <a href="logout.php" class="nav-item logout"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
            </nav>
        </aside>

        <main class="main-content">
            <header class="top-bar">
                <h1>Edit Product</h1>
            </header>

            <div class="content">
                <?php if ($message): ?>
                    <div class="success-message"><?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>

                <div class="form-container">
                    <form method="POST">
                        <div class="form-group">
                            <label for="name">Product Name</label>
                            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="category">Category</label>
                            <select id="category" name="category" required>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo $product['category'] === $cat ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cat); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="price">Price (£)</label>
                            <input type="number" id="price" name="price" step="0.01" value="<?php echo htmlspecialchars($product['price']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="stock">Stock Quantity</label>
                            <input type="number" id="stock" name="stock" value="<?php echo htmlspecialchars($product['stock']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea id="description" name="description"><?php echo htmlspecialchars($product['description']); ?></textarea>
                        </div>

                        <div class="form-buttons">
                            <button type="submit" class="btn-primary">Update Product</button>
                            <a href="products.php" class="btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
