<?php
require_once 'trader_menu.php';

$product = ['PRODUCT_NAME'=>'Heirloom Tomatoes','DESCRIPTION'=>'Fresh locally grown heirloom tomatoes.','PRICE'=>'3.50','STOCK'=>'84','PRODUCT_STATUS'=>'In Stock'];
$saved = $_SERVER['REQUEST_METHOD'] === 'POST';

$right = '<a class="btn" href="My_products.php">Back to Products</a>';
render_trader_shell_start('Edit Product', 'products', 'TRADER PORTAL › INVENTORY', 'Edit Product', $right);
?>

<?php if($saved): ?><div class="card ok">Front-end demo: product changes displayed locally only.</div><?php endif; ?>

<div class="edit-card">
    <form method="POST" class="edit-form">
        <label>Product Name<input name="product_name" value="<?= h($_POST['product_name'] ?? $product['PRODUCT_NAME']) ?>"></label>
        <label class="full-row">Description<textarea name="description"><?= h($_POST['description'] ?? $product['DESCRIPTION']) ?></textarea></label>
        <label>Price<input type="number" step="0.01" name="price" value="<?= h($_POST['price'] ?? $product['PRICE']) ?>"></label>
        <label>Stock<input type="number" name="stock" value="<?= h($_POST['stock'] ?? $product['STOCK']) ?>"></label>
        <label class="full-row">Status
            <select name="product_status">
                <option>In Stock</option>
                <option>Low Stock</option>
                <option>Out of Stock</option>
            </select>
        </label>
        <button class="btn save-btn" type="submit">Save Changes</button>
    </form>
</div>

<?php render_trader_shell_end(); ?>