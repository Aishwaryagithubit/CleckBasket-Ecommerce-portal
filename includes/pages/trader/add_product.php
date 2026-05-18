<?php
require_once 'trader_menu.php';
render_trader_shell_start('Register New Harvest', 'add', 'TRADER PORTAL › INVENTORY › REGISTER NEW HARVEST', 'Register New Harvest');
?>

<?php if($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
    <div class="notice success"><i class="fa-solid fa-check"></i> Front-end demo: product form submitted locally. No database was used.</div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data" class="harvest-form">
    <section class="card core-card">
        <h2><i class="fa-solid fa-basket-shopping"></i> Core Information</h2>
        <div class="two-cols">
            <label>Product Name<input type="text" name="product_name" placeholder="e.g. Heirloom Tomatoes" required></label>
            <label>Product ID (SKU)<input type="text" name="rfid" placeholder="TOM-HR-001"></label>
        </div>
        <label>Product Description<textarea name="description" placeholder="Describe the origin, flavor profile, and harvest method..." required></textarea></label>
    </section>

    <section class="card image-card">
        <h2><i class="fa-regular fa-camera"></i> Harvest Imagery</h2>
        <label class="upload-box">
            <input type="file" name="product_image" accept="image/*">
            <i class="fa-regular fa-file-image"></i>
            <strong>Click to upload photos</strong>
            <span>High-res PNG or JPG<br>(Max 10MB)</span>
        </label>
        <div class="thumb-row">
            <div><i class="fa-regular fa-image"></i></div>
            <div><i class="fa-regular fa-image"></i></div>
            <div><i class="fa-regular fa-image"></i></div>
        </div>
    </section>

    <section class="card metrics-card">
        <h2><i class="fa-solid fa-money-bill-wave"></i> Commercial Metrics</h2>
        <div class="two-cols">
            <label>Price per Unit (GBP)<input type="number" name="price" step="0.01" placeholder="£  0.00" required></label>
            <label>Stock Level<input type="number" name="stock" placeholder="Units in storage" required></label>
        </div>
        <label>Initial Status
            <select name="product_status">
                <option>Pending Update</option>
                <option>In Stock</option>
                <option>Low Stock</option>
                <option>Out of Stock</option>
            </select>
        </label>
    </section>

    <section class="card order-card">
        <h2><i class="fa-solid fa-basket-shopping"></i> Order & Limits</h2>
        <div class="limit-row">
            <div><strong>Minimum Order</strong><span>Lowest qty per customer</span></div>
            <div class="stepper"><button type="button" onclick="changeVal('min_order',-1)">−</button><input id="min_order" name="min_order" value="1" min="1"><button type="button" onclick="changeVal('min_order',1)">+</button></div>
        </div>
        <div class="limit-row">
            <div><strong>Maximum Order</strong><span>Highest qty per customer</span></div>
            <div class="stepper"><button type="button" onclick="changeVal('max_order',-1)">−</button><input id="max_order" name="max_order" value="50" min="1"><button type="button" onclick="changeVal('max_order',1)">+</button></div>
        </div>
    </section>

    <div class="form-actions">
        <button type="button" class="brown-btn">Save as Draft</button>
        <button type="submit" name="add" class="brown-btn"><i class="fa-regular fa-circle-check"></i> Add Product</button>
    </div>
</form>

<script>
function changeVal(id, delta) {
    const el = document.getElementById(id);
    el.value = Math.max(1, (parseInt(el.value) || 1) + delta);
}
</script>

<?php render_trader_shell_end(); ?>