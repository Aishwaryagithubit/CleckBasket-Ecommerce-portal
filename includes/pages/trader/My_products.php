<?php
require_once 'trader_menu.php';

$products = [
    ['PRODUCT_ID'=>1,'PRODUCT_NAME'=>'Heirloom Tomatoes','SHOP_NAME'=>'Mir Farm','SHOP_CATEGORY'=>'Vegetables','PRICE'=>3.50,'STOCK'=>84,'PRODUCT_STATUS'=>'In Stock'],
    ['PRODUCT_ID'=>2,'PRODUCT_NAME'=>'Artisan Sourdough','SHOP_NAME'=>'Mir Farm','SHOP_CATEGORY'=>'Bakery','PRICE'=>4.20,'STOCK'=>12,'PRODUCT_STATUS'=>'Low Stock'],
    ['PRODUCT_ID'=>3,'PRODUCT_NAME'=>'Whole Farm Milk (2L)','SHOP_NAME'=>'Mir Farm','SHOP_CATEGORY'=>'Dairy','PRICE'=>2.30,'STOCK'=>45,'PRODUCT_STATUS'=>'In Stock']
];

$right = '<a class="btn" href="add_product.php"><i class="fa-solid fa-plus"></i> Add Product</a>';
render_trader_shell_start('My Products', 'products', 'TRADER PORTAL › INVENTORY', 'My Products', $right);
?>

<div class="products-grid">
    <?php foreach($products as $p): ?>
        <div class="product-card">
            <div class="product-image-placeholder"><i class="fa-regular fa-image"></i></div>
            <h2><?= h($p['PRODUCT_NAME']) ?></h2>
            <p><?= h($p['SHOP_NAME']) ?> · <?= h($p['SHOP_CATEGORY']) ?></p>
            <h2>£<?= number_format($p['PRICE'], 2) ?></h2>
            <p class="<?= strtolower(str_replace(' ', '-', $p['PRODUCT_STATUS'])) ?>">
                <?= h($p['STOCK']) ?> units · <?= h($p['PRODUCT_STATUS']) ?>
            </p>
            <a class="btn light" href="edit_product.php?id=<?= h($p['PRODUCT_ID']) ?>">Edit</a>
            <button class="btn" onclick="alert('Front-end demo only: delete disabled')">Delete</button>
        </div>
    <?php endforeach; ?>
</div>

<?php render_trader_shell_end(); ?>