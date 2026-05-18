<?php
require_once 'trader_menu.php';

$userName = $_SESSION['full_name'] ?? 'Unus';
$shopName = 'Mir Farm';
$orderStats = ['TOTAL_ORDERS' => 24, 'TOTAL_SALES' => 1420, 'COMPLETED_ORDERS' => 18, 'PENDING_ORDERS' => 8];

$topProducts = [
    ['name' => 'Heirloom Carrots (1kg)', 'category' => 'Vegetables', 'qty' => 84, 'status' => 'In Stock', 'image' => 'https://images.unsplash.com/photo-1447175008436-054170c2e979?auto=format&fit=crop&w=80&q=80'],
    ['name' => 'Artisan Sourdough', 'category' => 'Bakery', 'qty' => 12, 'status' => 'Low Stock', 'image' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?auto=format&fit=crop&w=80&q=80'],
    ['name' => 'Whole Farm Milk (2L)', 'category' => 'Dairy', 'qty' => 45, 'status' => 'In Stock', 'image' => 'https://images.unsplash.com/photo-1563636619-e9143da7973b?auto=format&fit=crop&w=80&q=80']
];

$right = '<a href="edit_shop.php" class="btn"><i class="fa-regular fa-circle-user"></i> ' . h($shopName) . '</a>';
render_trader_shell_start('Trader Dashboard', 'dashboard', 'TRADER PORTAL › DASHBOARD', 'Trader Dashboard', $right);
?>

<section class="dashboard-grid">
    <article class="metric-card stock-card">
        <div class="metric-top">
            <span class="metric-icon"><i class="fa-regular fa-clipboard"></i></span>
            <span class="alert-pill">ALERT</span>
        </div>
        <h3>STOCK HEALTH</h3>
        <p class="big-number orange">142 <span>SKUs Active</span></p>
        <p class="metric-note danger"><i class="fa-solid fa-triangle-exclamation"></i> 8 Needs Refill (Low Stock)</p>
    </article>

    <article class="metric-card revenue-card">
        <div class="metric-top">
            <span class="metric-icon green"><i class="fa-regular fa-money-bill-1"></i></span>
            <span class="growth-pill">+12% TODAY</span>
        </div>
        <h3>DAILY PROJECTED REVENUE</h3>
        <p class="big-number green-text">£<?= number_format($orderStats['TOTAL_SALES'], 0) ?></p>
        <p class="metric-note success">Based on 32 active orders today</p>
    </article>

    <article class="slot-card">
        <div class="slot-header">
            <h3>Next Slot Arrival</h3>
            <span>LIVE</span>
        </div>
        <p class="slot-time">10:00 – 13:00</p>
        <p class="slot-sub"><strong>24 Orders</strong> pending collection</p>
        <div class="progress-row">
            <span>PREPARATION PROGRESS</span>
            <strong>65%</strong>
        </div>
        <div class="progress-track"><div></div></div>
    </article>
</section>

<section class="content-grid">
    <article class="products-block">
        <div class="section-title-row">
            <h2>Top Products Performance</h2>
            <a href="My_products.php">View All <i class="fa-solid fa-angle-right"></i></a>
        </div>

        <div class="product-table-card">
            <div class="table-head">
                <span>Product</span>
                <span>Category</span>
                <span>Qty</span>
                <span>Status</span>
            </div>

            <?php foreach ($topProducts as $product): ?>
                <div class="product-row">
                    <div class="product-name">
                        <img src="<?= h($product['image']) ?>" alt="<?= h($product['name']) ?>">
                        <strong><?= h($product['name']) ?></strong>
                    </div>
                    <span class="category-chip"><?= h($product['category']) ?></span>
                    <strong><?= h($product['qty']) ?></strong>
                    <span class="status-dot <?= strtolower(str_replace(' ', '-', $product['status'])) ?>">
                        <?= h($product['status']) ?>
                    </span>
                </div>
            <?php endforeach; ?>
        </div>
    </article>

    <aside class="queue-block">
        <h2>Collection Queue</h2>
        <div class="queue-card">
            <div class="queue-date">
                <strong>Wed, Oct 23</strong>
                <i class="fa-regular fa-calendar"></i>
            </div>
            <div class="queue-slot">
                <span>MORNING</span>
                <strong>10:00 – 13:00</strong>
                <em>Total<br>24 Orders</em>
            </div>
            <div class="queue-slot selected-slot">
                <span>AFTERNOON</span>
                <strong>13:00 – 16:00</strong>
                <em>Total<br>18 Orders</em>
            </div>
            <div class="queue-slot disabled-slot">
                <span>EVENING</span>
                <strong>16:00 – 19:00</strong>
                <em>Total<br>09 Orders</em>
            </div>
        </div>

        <a class="schedule-btn" href="Weekly_reports.php">
            <i class="fa-regular fa-calendar-days"></i>
            <span>View Full Weekly Schedule</span>
            <i class="fa-solid fa-arrow-right"></i>
        </a>
    </aside>
</section>

<?php render_trader_shell_end(); ?>