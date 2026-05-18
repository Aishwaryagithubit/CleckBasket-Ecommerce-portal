<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$defaultCategories = ['Butcher', 'Greengrocer', 'Delicatessen', 'Fishmonger', 'Bakery'];
$categories = isset($_SESSION['admin_categories']) ? array_column($_SESSION['admin_categories'], 'name') : $defaultCategories;
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uploads_dir = dirname(__DIR__) . '/assets/images/products';
    if (!is_dir($uploads_dir)) {
        mkdir($uploads_dir, 0755, true);
    }

    if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['image'];
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $error = 'Image upload failed. Please try again.';
        } elseif (!in_array($file['type'], $allowed_types, true)) {
            $error = 'Invalid image type. Allowed types: JPG, PNG, GIF, WEBP.';
        } else {
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $image_name = 'product_' . time() . '_' . uniqid() . '.' . $ext;
            $upload_path = $uploads_dir . '/' . $image_name;

            if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                // $image_path can be stored in the database when saving the product
                $image_path = '/cleckbasket/assets/images/products/' . $image_name;
            } else {
                $error = 'Failed to save uploaded image.';
            }
        }
    }

    if (!$error) {
        // TODO: Save product to database including $image_path if available
        $message = 'Product added successfully!';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - Admin Panel</title>
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
                <h1>Add New Product</h1>
            </header>

            <div class="content">
                <?php if ($message): ?>
                    <div class="success-message"><?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <div class="form-container">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="name">Product Name</label>
                            <input type="text" id="name" name="name" required>
                        </div>

                        <div class="form-group">
                            <label for="category">Category</label>
                            <select id="category" name="category" required>
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo htmlspecialchars($cat); ?>"><?php echo htmlspecialchars($cat); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="price">Price (£)</label>
                            <input type="number" id="price" name="price" step="0.01" required>
                        </div>

                        <div class="form-group">
                            <label for="stock">Stock Quantity</label>
                            <input type="number" id="stock" name="stock" required>
                        </div>

                        <div class="form-group">
                            <label for="image">Product Image</label>
                            <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/gif,image/webp">
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea id="description" name="description"></textarea>
                        </div>

                        <div class="form-buttons">
                            <button type="submit" class="btn-primary">Add Product</button>
                            <a href="products.php" class="btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
