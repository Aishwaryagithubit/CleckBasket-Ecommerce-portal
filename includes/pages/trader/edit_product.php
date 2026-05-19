<?php
require_once 'trader_menu.php';

$userId     = $_SESSION['user_id'];
$productId  = (int)($_GET['id'] ?? 0);
$product    = null;
$categories = [];
$notice     = '';
$error      = '';

if (!$productId) {
    header('Location: My_products.php');
    exit;
}

$conn = getDBConnection();
if ($conn) {
    $shop = getTraderShop($conn, $userId);
    $shopId = $shop ? $shop['SHOP_ID'] : null;

    // Fetch categories
    $stmt = oci_parse($conn, "SELECT product_category_id, category_type FROM product_category ORDER BY category_type");
    oci_execute($stmt);
    while ($row = oci_fetch_assoc($stmt)) $categories[] = $row;
    oci_free_statement($stmt);

    if ($shopId) {
        // Load product — verify it belongs to this trader
        $sql  = "SELECT p.product_id, p.product_name, p.description, p.price, p.stock_quantity,
                        p.min_order, p.max_order, p.allergy_information, p.product_image,
                        p.product_category_id
                 FROM product p
                 WHERE p.product_id = :pid AND p.shop_id = :sid";
        $stmt = oci_parse($conn, $sql);
        oci_bind_by_name($stmt, ':pid', $productId);
        oci_bind_by_name($stmt, ':sid', $shopId);
        oci_execute($stmt);
        $product = oci_fetch_assoc($stmt);
        oci_free_statement($stmt);

        if (!$product) {
            oci_close($conn);
            header('Location: My_products.php');
            exit;
        }

        // Handle update
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name       = trim($_POST['product_name'] ?? '');
            $desc       = trim($_POST['description'] ?? '');
            $price      = (float)($_POST['price'] ?? 0);
            $stock      = (int)($_POST['stock'] ?? 0);
            $minOrder   = (int)($_POST['min_order'] ?? 1);
            $maxOrder   = (int)($_POST['max_order'] ?? 50);
            $allergy    = trim($_POST['allergy_information'] ?? 'None');
            $categoryId = (int)($_POST['category_id'] ?? 0);

            if (!$name || $price <= 0 || !$categoryId) {
                $error = 'Product name, price, and category are required.';
            } else {
                // Handle image update
                $imageName = $product['PRODUCT_IMAGE'];
                if (!empty($_FILES['product_image']['name']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
                    $ext     = strtolower(pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION));
                    $allowed = ['jpg','jpeg','png','gif','webp'];
                    if (in_array($ext, $allowed)) {
                        $newName = 'product_' . time() . '_' . uniqid() . '.' . $ext;
                        $dest    = __DIR__ . '/../../../assets/images/' . $newName;
                        if (move_uploaded_file($_FILES['product_image']['tmp_name'], $dest)) {
                            $imageName = $newName;
                        }
                    }
                }

                $sql2 = "UPDATE product SET
                            product_name = :prod_name,
                            description  = :prod_desc,
                            price        = :price,
                            stock_quantity = :stock,
                            min_order    = :min_order,
                            max_order    = :max_order,
                            allergy_information = :allergy,
                            product_image = :image,
                            product_category_id = :cat_id
                         WHERE product_id = :pid AND shop_id = :sid";
                $stmt2 = oci_parse($conn, $sql2);
                oci_bind_by_name($stmt2, ':prod_name', $name);
                oci_bind_by_name($stmt2, ':prod_desc', $desc);
                oci_bind_by_name($stmt2, ':price',     $price);
                oci_bind_by_name($stmt2, ':stock',     $stock);
                oci_bind_by_name($stmt2, ':min_order', $minOrder);
                oci_bind_by_name($stmt2, ':max_order', $maxOrder);
                oci_bind_by_name($stmt2, ':allergy',   $allergy);
                oci_bind_by_name($stmt2, ':image',     $imageName);
                oci_bind_by_name($stmt2, ':cat_id',    $categoryId);
                oci_bind_by_name($stmt2, ':pid',       $productId);
                oci_bind_by_name($stmt2, ':sid',       $shopId);

                if (oci_execute($stmt2)) {
                    oci_commit($conn);
                    $notice = 'Product updated successfully.';
                    // Refresh product data
                    $product['PRODUCT_NAME']        = $name;
                    $product['DESCRIPTION']         = $desc;
                    $product['PRICE']               = $price;
                    $product['STOCK_QUANTITY']      = $stock;
                    $product['MIN_ORDER']           = $minOrder;
                    $product['MAX_ORDER']           = $maxOrder;
                    $product['ALLERGY_INFORMATION'] = $allergy;
                    $product['PRODUCT_IMAGE']       = $imageName;
                    $product['PRODUCT_CATEGORY_ID'] = $categoryId;
                } else {
                    $err   = oci_error($stmt2);
                    $error = 'Update failed: ' . $err['message'];
                }
                oci_free_statement($stmt2);
            }
        }
    }
    oci_close($conn);
}

$right = '<a class="btn" href="My_products.php">Back to Products</a>';
render_trader_shell_start('Edit Product', 'products', 'TRADER PORTAL › INVENTORY', 'Edit Product', $right);
?>

<?php if ($notice): ?>
    <div class="notice success"><i class="fa-solid fa-check"></i> <?= h($notice) ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="notice danger"><i class="fa-solid fa-circle-exclamation"></i> <?= h($error) ?></div>
<?php endif; ?>

<div class="edit-card">
    <form method="POST" enctype="multipart/form-data" class="edit-form">
        <label>Product Name
            <input name="product_name" value="<?= h($product['PRODUCT_NAME'] ?? '') ?>" required>
        </label>
        <label>Category
            <select name="category_id" required>
                <option value="">Select category</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= (int)$cat['PRODUCT_CATEGORY_ID'] ?>"
                        <?= ($cat['PRODUCT_CATEGORY_ID'] == ($product['PRODUCT_CATEGORY_ID'] ?? '')) ? 'selected' : '' ?>>
                        <?= h($cat['CATEGORY_TYPE']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <label class="full-row">Description
            <textarea name="description"><?= h($product['DESCRIPTION'] ?? '') ?></textarea>
        </label>
        <label>Price (£)
            <input type="number" step="0.01" name="price" value="<?= h($product['PRICE'] ?? '') ?>" required>
        </label>
        <label>Stock Quantity
            <input type="number" name="stock" value="<?= h($product['STOCK_QUANTITY'] ?? '') ?>" min="0" required>
        </label>
        <label>Min Order
            <input type="number" name="min_order" value="<?= h($product['MIN_ORDER'] ?? 1) ?>" min="1">
        </label>
        <label>Max Order
            <input type="number" name="max_order" value="<?= h($product['MAX_ORDER'] ?? 50) ?>" min="1">
        </label>
        <label class="full-row">Allergy Information
            <input name="allergy_information" value="<?= h($product['ALLERGY_INFORMATION'] ?? 'None') ?>">
        </label>
        <label class="full-row">Replace Image (optional)
            <input type="file" name="product_image" accept="image/*">
        </label>
        <button class="btn save-btn" type="submit">Save Changes</button>
    </form>
</div>

<?php render_trader_shell_end(); ?>
