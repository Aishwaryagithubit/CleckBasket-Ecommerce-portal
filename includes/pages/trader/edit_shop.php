<?php
require_once 'trader_menu.php';

$saved = $_SERVER['REQUEST_METHOD'] === 'POST';
$shop = [
    'shop_name' => $_POST['shop_name'] ?? 'Mir Farm',
    'shop_category' => $_POST['shop_category'] ?? 'Vegetables',
    'description' => $_POST['description'] ?? 'Fresh local farm produce and seasonal harvest items.'
];

$right = '<a class="btn" href="traderdashboard.php">Back to Dashboard</a>';
render_trader_shell_start('Edit Shop', 'profile', 'TRADER PORTAL › SHOP SETTINGS', 'Edit Shop', $right);
?>

<?php if($saved): ?><div class="card ok">Front-end demo: shop details updated locally only.</div><?php endif; ?>

<div class="edit-card">
    <form method="POST" class="edit-form">
        <label>Shop Name<input name="shop_name" value="<?= h($shop['shop_name']) ?>"></label>
        <label>Shop Category
            <select name="shop_category">
                <option>Vegetables</option>
                <option>Bakery</option>
                <option>Dairy</option>
                <option>Meat</option>
                <option>Fish</option>
            </select>
        </label>
        <label class="full-row">Description<textarea name="description"><?= h($shop['description']) ?></textarea></label>
        <button class="btn save-btn" type="submit">Save Shop Details</button>
    </form>
</div>

<?php render_trader_shell_end(); ?>