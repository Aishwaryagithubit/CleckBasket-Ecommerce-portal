<?php
require_once 'trader_menu.php';

$userId     = $_SESSION['user_id'];
$categories = [];
$notice     = '';
$error      = '';

// Check trader status — must be ACTIVE to add products
$traderStatus = '';
$connStatus = getDBConnection();
if ($connStatus) {
    $stmtS = oci_parse($connStatus, "SELECT status FROM users WHERE user_id = :p_user_id");
    oci_bind_by_name($stmtS, ':p_user_id', $userId);
    if (oci_execute($stmtS)) {
        $rowS = oci_fetch_assoc($stmtS);
        $traderStatus = strtoupper(trim($rowS['STATUS'] ?? ''));
    }

    oci_free_statement($stmtS);
    oci_close($connStatus);
}

// Fetch categories for dropdown
$conn = getDBConnection();
if ($conn) {
    $stmt = oci_parse($conn, "SELECT product_category_id, category_type FROM product_category ORDER BY category_type");
    oci_execute($stmt);
    while ($row = oci_fetch_assoc($stmt)) {
        $categories[] = $row;
    }
    oci_free_statement($stmt);

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add']) && $traderStatus === 'ACTIVE') {
        $shop = getTraderShop($conn, $userId);

        if (!$shop) {
            $error = 'No shop found for your account. Please contact support.';
        } else {
            $shopId      = $shop['SHOP_ID'];
            $name        = trim($_POST['product_name'] ?? '');
            $desc        = trim($_POST['description'] ?? '');
            $price       = (float)($_POST['price'] ?? 0);
            $stock       = (int)($_POST['stock'] ?? 0);
            $minOrder    = (int)($_POST['min_order'] ?? 1);
            $maxOrder    = (int)($_POST['max_order'] ?? 50);
            $allergy     = trim($_POST['allergy_information'] ?? 'None');
            $categoryId  = (int)($_POST['category_id'] ?? 0);

            if (!$name || $price <= 0 || !$categoryId) {
                $error = 'Product name, price, and category are required.';
            } else {
                // Handle image upload
                $imageName = 'default_product.png';
                if (!empty($_FILES['product_image']['name'])) {
                    $ext       = strtolower(pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION));
                    $allowed   = ['jpg','jpeg','png','gif','webp'];
                    if (in_array($ext, $allowed) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
                        $imageName = 'product_' . time() . '_' . uniqid() . '.' . $ext;
                        $dest      = __DIR__ . '/../../../assets/images/' . $imageName;
                        if (!move_uploaded_file($_FILES['product_image']['tmp_name'], $dest)) {
                            $imageName = 'default_product.png';
                        }
                    }
                }

                $sql = "INSERT INTO product (product_name, description, price, stock_quantity,
                                             min_order, max_order, allergy_information, product_image,
                                             shop_id, product_category_id)
                        VALUES (:prod_name, :prod_desc, :price, :stock, :min_order, :max_order,
                                :allergy, :image, :shop_id, :cat_id)";
                $stmt = oci_parse($conn, $sql);
                oci_bind_by_name($stmt, ':prod_name', $name);
                oci_bind_by_name($stmt, ':prod_desc', $desc);
                oci_bind_by_name($stmt, ':price',     $price);
                oci_bind_by_name($stmt, ':stock',     $stock);
                oci_bind_by_name($stmt, ':min_order', $minOrder);
                oci_bind_by_name($stmt, ':max_order', $maxOrder);
                oci_bind_by_name($stmt, ':allergy',   $allergy);
                oci_bind_by_name($stmt, ':image',     $imageName);
                oci_bind_by_name($stmt, ':shop_id',   $shopId);
                oci_bind_by_name($stmt, ':cat_id',    $categoryId);

                if (oci_execute($stmt)) {
                    oci_commit($conn);
                    $notice = 'Product "' . h($name) . '" added successfully!';
                    // Clear POST so form resets
                    $_POST = [];
                } else {
                    $err   = oci_error($stmt);
                    $error = 'Failed to add product: ' . $err['message'];
                }
                oci_free_statement($stmt);
            }
        }
    }
    oci_close($conn);
}

render_trader_shell_start('Add Product', 'add', 'TRADER PORTAL › INVENTORY › ADD PRODUCT', 'Add Product');
?>

<?php if ($traderStatus !== 'ACTIVE'): ?>
    <div class="notice danger" style="margin-bottom:24px;">
        <i class="fa-solid fa-clock"></i>
        Your account is <strong>pending admin approval</strong>. You cannot add products until your account is approved.
    </div>
<?php else: ?>

<?php if ($notice): ?>
    <div class="notice success"><i class="fa-solid fa-check"></i> <?= h($notice) ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="notice danger"><i class="fa-solid fa-circle-exclamation"></i> <?= h($error) ?></div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data" class="harvest-form">
    <section class="card core-card">
        <h2><i class="fa-solid fa-basket-shopping"></i> Core Information</h2>
        <div class="two-cols">
            <label>Product Name
                <input type="text" name="product_name" placeholder="e.g. Heirloom Tomatoes"
                       value="<?= h($_POST['product_name'] ?? '') ?>" required>
            </label>
            <label>Category
                <select name="category_id" required>
                    <option value="">Select category</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= (int)$cat['PRODUCT_CATEGORY_ID'] ?>"
                            <?= (($_POST['category_id'] ?? '') == $cat['PRODUCT_CATEGORY_ID']) ? 'selected' : '' ?>>
                            <?= h($cat['CATEGORY_TYPE']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>
        </div>
        <label>Product Description
            <textarea name="description" placeholder="Describe the product..."><?= h($_POST['description'] ?? '') ?></textarea>
        </label>
        <label>Allergy Information
            <input type="text" name="allergy_information" placeholder="e.g. Gluten, Dairy or None"
                   value="<?= h($_POST['allergy_information'] ?? 'None') ?>">
        </label>
    </section>

    <section class="card image-card">
        <h2><i class="fa-regular fa-camera"></i> Product Image</h2>
        <label class="upload-box">
            <input type="file" name="product_image" accept="image/*">
            <i class="fa-regular fa-file-image"></i>
            <strong>Click to upload photo</strong>
            <span>PNG or JPG (Max 10MB)</span>
        </label>
    </section>

    <section class="card metrics-card">
        <h2><i class="fa-solid fa-money-bill-wave"></i> Commercial Metrics</h2>
        <div class="two-cols">
            <label>Price per Unit (£)
                <input type="number" name="price" step="0.01" placeholder="0.00"
                       value="<?= h($_POST['price'] ?? '') ?>" required>
            </label>
            <label>Stock Quantity
                <input type="number" name="stock" placeholder="Units in storage" min="0"
                       value="<?= h($_POST['stock'] ?? '') ?>" required>
            </label>
        </div>
    </section>

    <section class="card order-card">
        <h2><i class="fa-solid fa-basket-shopping"></i> Order Limits</h2>
        <div class="limit-row">
            <div><strong>Minimum Order</strong><span>Lowest qty per customer</span></div>
            <div class="stepper">
                <button type="button" onclick="changeVal('min_order',-1)">−</button>
                <input id="min_order" name="min_order" value="<?= h($_POST['min_order'] ?? 1) ?>" min="1">
                <button type="button" onclick="changeVal('min_order',1)">+</button>
            </div>
        </div>
        <div class="limit-row">
            <div><strong>Maximum Order</strong><span>Highest qty per customer</span></div>
            <div class="stepper">
                <button type="button" onclick="changeVal('max_order',-1)">−</button>
                <input id="max_order" name="max_order" value="<?= h($_POST['max_order'] ?? 50) ?>" min="1">
                <button type="button" onclick="changeVal('max_order',1)">+</button>
            </div>
        </div>
    </section>

    <div class="form-actions">
        <a href="My_products.php" class="brown-btn">Cancel</a>
        <button type="submit" name="add" class="brown-btn">
            <i class="fa-regular fa-circle-check"></i> Add Product
        </button>
    </div>
</form>

<script>
function changeVal(id, delta) {
    const el = document.getElementById(id);
    el.value = Math.max(1, (parseInt(el.value) || 1) + delta);
}
</script>

<?php endif; // end ACTIVE check ?>

<?php render_trader_shell_end(); ?>
