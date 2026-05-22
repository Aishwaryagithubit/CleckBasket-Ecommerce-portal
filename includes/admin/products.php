<?php
require_once 'admin_auth.php';

$message = '';
$error   = '';

// Delete product (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['product_id'])
    && $_POST['action'] === 'delete_product') {
    $pid  = (int)$_POST['product_id'];
    $conn = getDBConnection();
    if ($conn) {

        // FIXED: Delete child records first before deleting product
        $delQueries = [
            "DELETE FROM product_report    WHERE product_id = :pid",
            "DELETE FROM discount          WHERE product_id = :pid",
            "DELETE FROM favourite_product WHERE product_id = :pid",
            "DELETE FROM cart_product      WHERE product_id = :pid",
            "DELETE FROM order_product     WHERE product_id = :pid",
            "DELETE FROM review            WHERE product_id = :pid",
            "DELETE FROM product           WHERE product_id = :pid",
        ];

        $failed = false;
        foreach ($delQueries as $q) {
            $s = oci_parse($conn, $q);
            oci_bind_by_name($s, ':pid', $pid);
            if (!oci_execute($s)) {
                $err   = oci_error($s);
                $error = 'Delete failed: ' . $err['message'];
                $failed = true;
                oci_free_statement($s);
                break;
            }
            oci_free_statement($s);
        }
        if (!$failed) {
            oci_commit($conn);
            $message = 'Product deleted successfully.';
        }
        oci_close($conn);
    }
}

// Load products
$products = [];
$conn = getDBConnection();
if ($conn) {
    $sql = "SELECT p.product_id, p.product_name, pc.category_type,
                   p.price, p.stock_quantity, p.product_image
            FROM product p
            JOIN product_category pc ON p.product_category_id = pc.product_category_id
            ORDER BY p.product_id DESC";
    $stmt = oci_parse($conn, $sql);
    oci_execute($stmt);
    while ($row = oci_fetch_assoc($stmt)) $products[] = $row;
    oci_free_statement($stmt);
    oci_close($conn);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin-style.css">
    <link rel="stylesheet" href="css/table-style.css">
</head>
<body>
<div class="admin-container">
    <?php admin_sidebar('products'); ?>

    <main class="main-content">
        <header class="top-bar">
            <h1>Products</h1>
            <div class="user-info"><span><?= h($_SESSION['admin_username']) ?></span></div>
        </header>

        <div class="content">
            <?php if ($message): ?>
                <div style="background:#e8f5e9;color:#2e7d32;padding:15px;border-radius:8px;margin-bottom:20px;"><?= h($message) ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div style="background:#ffebee;color:#c62828;padding:15px;border-radius:8px;margin-bottom:20px;"><?= h($error) ?></div>
            <?php endif; ?>

            <div class="page-header">
                <h2>Product Management</h2>
                <a href="product-add.php" class="btn-primary"><i class="fas fa-plus"></i> Add Product</a>
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
                    <?php if (empty($products)): ?>
                        <tr><td colspan="5" style="text-align:center;color:#999;">No products found.</td></tr>
                    <?php else: ?>
                    <?php foreach ($products as $p): ?>
                        <tr>
                            <td><?= h($p['PRODUCT_NAME']) ?></td>
                            <td><?= h($p['CATEGORY_TYPE']) ?></td>
                            <td>£<?= number_format((float)$p['PRICE'], 2) ?></td>
                            <td>
                                <span class="stock-badge <?= (int)$p['STOCK_QUANTITY'] < 10 ? 'low' : 'good' ?>">
                                    <?= (int)$p['STOCK_QUANTITY'] ?> units
                                </span>
                            </td>
                            <td class="actions">
                                <a href="product-edit.php?id=<?= (int)$p['PRODUCT_ID'] ?>" class="btn-edit">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form method="POST" style="display:inline-block;margin:0;"
                                      onsubmit="return confirm('Delete <?= h($p['PRODUCT_NAME']) ?>? This cannot be undone.');">
                                    <input type="hidden" name="action" value="delete_product">
                                    <input type="hidden" name="product_id" value="<?= (int)$p['PRODUCT_ID'] ?>">
                                    <button type="submit" class="btn-delete" style="padding:6px 10px;font-size:12px;">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
</body>
</html>
