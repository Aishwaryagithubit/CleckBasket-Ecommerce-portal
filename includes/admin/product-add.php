<?php
require_once 'admin_auth.php';

$message = '';
$error   = '';

// Load categories for the dropdown
$categories = [];
$conn = getDBConnection();
if ($conn) {
    $s = oci_parse($conn, "SELECT product_category_id, category_type FROM product_category ORDER BY category_type");
    oci_execute($s);
    while ($row = oci_fetch_assoc($s)) $categories[] = $row;
    oci_free_statement($s);
    oci_close($conn);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name      = trim($_POST['name']        ?? '');
    $catId     = (int)($_POST['category_id'] ?? 0);
    $price     = (float)($_POST['price']     ?? 0);
    $stock     = (int)($_POST['stock']       ?? 0);
    $desc      = trim($_POST['description'] ?? '');
    $imageName = '';

    if ($name === '' || $catId === 0 || $price <= 0) {
        $error = 'Product name, category, and price are required.';
    }

    // Handle optional image upload
    if (!$error && isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['image'];
        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $error = 'Image upload failed.';
        } elseif (!in_array($file['type'], $allowed, true)) {
            $error = 'Invalid image type. Use JPG, PNG, GIF, or WebP.';
        } else {
            $ext       = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $imageName = 'product_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            $dest      = __DIR__ . '/../../assets/images/' . $imageName;
            if (!move_uploaded_file($file['tmp_name'], $dest)) {
                $error = 'Failed to save image.';
                $imageName = '';
            }
        }
    }

    if (!$error) {
        $conn = getDBConnection();
        if ($conn) {
            $ins = oci_parse($conn,
                "INSERT INTO product (product_name, description, price, stock_quantity, product_image, product_category_id)
                 VALUES (:pname, :pdesc, :price, :stock, :img, :catid)");
            oci_bind_by_name($ins, ':pname', $name);
            oci_bind_by_name($ins, ':pdesc', $desc);
            oci_bind_by_name($ins, ':price', $price);
            oci_bind_by_name($ins, ':stock', $stock);
            oci_bind_by_name($ins, ':img',   $imageName);
            oci_bind_by_name($ins, ':catid', $catId);
            if (oci_execute($ins)) {
                oci_commit($conn);
                $message = 'Product added successfully.';
            } else {
                $err   = oci_error($ins);
                $error = 'Save failed: ' . $err['message'];
            }
            oci_free_statement($ins);
            oci_close($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin-style.css">
    <link rel="stylesheet" href="css/table-style.css">
</head>
<body>
<div class="admin-container">
    <?php admin_sidebar('products'); ?>

    <main class="main-content">
        <header class="top-bar">
            <h1>Add New Product</h1>
            <div class="user-info"><span><?= h($_SESSION['admin_username']) ?></span></div>
        </header>

        <div class="content">
            <?php if ($message): ?>
                <div style="background:#e8f5e9;color:#2e7d32;padding:15px;border-radius:8px;margin-bottom:20px;">
                    <?= h($message) ?> <a href="products.php" style="color:#2e7d32;">View all products &rarr;</a>
                </div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div style="background:#ffebee;color:#c62828;padding:15px;border-radius:8px;margin-bottom:20px;"><?= h($error) ?></div>
            <?php endif; ?>

            <div class="form-container">
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="name">Product Name *</label>
                        <input type="text" id="name" name="name"
                               value="<?= h($_POST['name'] ?? '') ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="category_id">Category *</label>
                        <select id="category_id" name="category_id" required>
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $c): ?>
                                <option value="<?= (int)$c['PRODUCT_CATEGORY_ID'] ?>"
                                    <?= (int)($_POST['category_id'] ?? 0) === (int)$c['PRODUCT_CATEGORY_ID'] ? 'selected' : '' ?>>
                                    <?= h($c['CATEGORY_TYPE']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="price">Price (£) *</label>
                        <input type="number" id="price" name="price" step="0.01" min="0.01"
                               value="<?= h($_POST['price'] ?? '') ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="stock">Stock Quantity</label>
                        <input type="number" id="stock" name="stock" min="0"
                               value="<?= h($_POST['stock'] ?? '0') ?>">
                    </div>

                    <div class="form-group">
                        <label for="image">Product Image</label>
                        <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/gif,image/webp">
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description"><?= h($_POST['description'] ?? '') ?></textarea>
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
