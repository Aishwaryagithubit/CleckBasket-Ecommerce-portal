<?php
require_once 'trader_menu.php';

$userId   = $_SESSION['user_id'];
$products = [];
$shopId   = null;
$error    = '';

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $deleteId = (int)$_POST['delete_id'];
    $conn = getDBConnection();
    if ($conn) {
        $shop = getTraderShop($conn, $userId);
        if ($shop) {
            $shopId = $shop['SHOP_ID'];
            // Verify product belongs to this trader's shop before deleting
            $chk = oci_parse($conn, "SELECT product_id FROM product WHERE product_id = :pid AND shop_id = :sid");
            oci_bind_by_name($chk, ':pid', $deleteId);
            oci_bind_by_name($chk, ':sid', $shopId);
            oci_execute($chk);
            if (oci_fetch($chk)) {
                oci_free_statement($chk);

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
                    oci_bind_by_name($s, ':pid', $deleteId);
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
                }

            } else {
                oci_free_statement($chk);
                $error = 'Product not found.';
            }
        }
        oci_close($conn);
    }
}

$conn = getDBConnection();
if ($conn) {
    $shop = getTraderShop($conn, $userId);
    if ($shop) {
        $shopId = $shop['SHOP_ID'];
        $sql = "SELECT p.product_id, p.product_name, p.price, p.stock_quantity, p.product_image,
                       NVL(pc.category_type, 'General') AS category_type
                FROM product p
                LEFT JOIN product_category pc ON p.product_category_id = pc.product_category_id
                WHERE p.shop_id = :sid
                ORDER BY p.product_id DESC";
        $stmt = oci_parse($conn, $sql);
        oci_bind_by_name($stmt, ':sid', $shopId);
        oci_execute($stmt);
        while ($row = oci_fetch_assoc($stmt)) {
            $qty    = (int)$row['STOCK_QUANTITY'];
            $status = $qty <= 0 ? 'Out of Stock' : ($qty <= 10 ? 'Low Stock' : 'In Stock');
            $products[] = [
                'PRODUCT_ID'     => $row['PRODUCT_ID'],
                'PRODUCT_NAME'   => $row['PRODUCT_NAME'],
                'SHOP_CATEGORY'  => $row['CATEGORY_TYPE'],
                'PRICE'          => $row['PRICE'],
                'STOCK'          => $qty,
                'PRODUCT_STATUS' => $status,
                'IMAGE'          => '/cleckbasket/assets/images/' . $row['PRODUCT_IMAGE'],
            ];
        }
        oci_free_statement($stmt);
    }
    oci_close($conn);
}

$right = '<a class="btn" href="add_product.php"><i class="fa-solid fa-plus"></i> Add Product</a>';
render_trader_shell_start('My Products', 'products', 'TRADER PORTAL › INVENTORY', 'My Products', $right);
?>

<?php if ($error): ?>
    <div class="notice danger"><i class="fa-solid fa-circle-exclamation"></i> <?= h($error) ?></div>
<?php endif; ?>

<?php if (empty($products)): ?>
    <div class="card" style="text-align:center;padding:2rem;">
        <p>No products yet. <a href="add_product.php">Add your first product</a>.</p>
    </div>
<?php else: ?>
<div class="products-grid">
    <?php foreach ($products as $p): ?>
        <div class="product-card">
            <div class="product-image-placeholder">
                <img src="<?= h($p['IMAGE']) ?>" alt="<?= h($p['PRODUCT_NAME']) ?>"
                     style="width:100%;height:79%;object-fit:cover;border-radius:8px;"
                     onerror="this.onerror=null;this.style.display='none';this.previousElementSibling&&(this.previousElementSibling.style.display='flex')">
            </div>
            <h2><?= h($p['PRODUCT_NAME']) ?></h2>
            <p><?= h($p['SHOP_CATEGORY']) ?></p>
            <h2>£<?= number_format((float)$p['PRICE'], 2) ?></h2>
            <p class="<?= strtolower(str_replace(' ', '-', $p['PRODUCT_STATUS'])) ?>">
                <?= h($p['STOCK']) ?> units · <?= h($p['PRODUCT_STATUS']) ?>
            </p>
            <a class="btn light" href="edit_product.php?id=<?= (int)$p['PRODUCT_ID'] ?>">Edit</a>
            <form method="POST" style="display:inline;"
                  onsubmit="return confirm('Delete <?= h($p['PRODUCT_NAME']) ?>?')">
                <input type="hidden" name="delete_id" value="<?= (int)$p['PRODUCT_ID'] ?>">
                <button class="btn" type="submit">Delete</button>
            </form>
        </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php render_trader_shell_end(); ?>