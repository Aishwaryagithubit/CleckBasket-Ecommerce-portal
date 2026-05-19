<?php
require_once 'trader_menu.php';

$userId   = $_SESSION['user_id'];
$shopName = 'My Shop';
$shopId   = null;
$orderStats  = ['TOTAL_ORDERS' => 0, 'TOTAL_SALES' => 0, 'COMPLETED_ORDERS' => 0, 'PENDING_ORDERS' => 0];
$topProducts = [];
$slots       = [];

$conn = getDBConnection();
if ($conn) {
    $shop = getTraderShop($conn, $userId);
    if ($shop) {
        $shopId   = $shop['SHOP_ID'];
        $shopName = $shop['SHOP_NAME'];

        // Order stats for this trader's products
        $sql = "SELECT COUNT(DISTINCT o.order_id) AS total_orders,
                       NVL(SUM(op.price_at_purchase * op.quantity), 0) AS total_sales,
                       COUNT(DISTINCT CASE WHEN o.order_status = 'COMPLETED' THEN o.order_id END) AS completed_orders,
                       COUNT(DISTINCT CASE WHEN o.order_status = 'PENDING'   THEN o.order_id END) AS pending_orders
                FROM orders o
                JOIN order_product op ON o.order_id = op.order_id
                JOIN product p        ON op.product_id = p.product_id
                WHERE p.shop_id = :sid";
        $stmt = oci_parse($conn, $sql);
        oci_bind_by_name($stmt, ':sid', $shopId);
        oci_execute($stmt);
        $r = oci_fetch_assoc($stmt);
        if ($r) $orderStats = $r;
        oci_free_statement($stmt);

        // Top 3 products by order quantity sold
        $sql2 = "SELECT p.product_id, p.product_name, p.stock_quantity, p.product_image,
                        NVL(pc.category_type, 'General') AS category_type,
                        NVL(SUM(op.quantity), 0) AS total_sold
                 FROM product p
                 LEFT JOIN product_category pc ON p.product_category_id = pc.product_category_id
                 LEFT JOIN order_product op     ON p.product_id = op.product_id
                 WHERE p.shop_id = :sid
                 GROUP BY p.product_id, p.product_name, p.stock_quantity, p.product_image, pc.category_type
                 ORDER BY total_sold DESC
                 FETCH FIRST 3 ROWS ONLY";
        $stmt2 = oci_parse($conn, $sql2);
        oci_bind_by_name($stmt2, ':sid', $shopId);
        oci_execute($stmt2);
        while ($row = oci_fetch_assoc($stmt2)) {
            $qty    = (int)$row['STOCK_QUANTITY'];
            $status = $qty <= 0 ? 'Out of Stock' : ($qty <= 10 ? 'Low Stock' : 'In Stock');
            $topProducts[] = [
                'name'     => $row['PRODUCT_NAME'],
                'category' => $row['CATEGORY_TYPE'],
                'qty'      => $qty,
                'status'   => $status,
                'image'    => '/cleckbasket/assets/images/' . $row['PRODUCT_IMAGE'],
            ];
        }
        oci_free_statement($stmt2);
    }

    // Upcoming collection slots
    $sql3 = "SELECT slot_day, TO_CHAR(slot_date,'DD Mon YYYY') AS slot_date_fmt, slot_time
             FROM collection_slot
             WHERE slot_date >= TRUNC(SYSDATE)
             ORDER BY slot_date, slot_time
             FETCH FIRST 3 ROWS ONLY";
    $stmt3 = oci_parse($conn, $sql3);
    oci_execute($stmt3);
    while ($row = oci_fetch_assoc($stmt3)) {
        $slots[] = $row;
    }
    oci_free_statement($stmt3);
    oci_close($conn);
}

$right = '<a href="edit_shop.php" class="btn"><i class="fa-regular fa-circle-user"></i> ' . h($shopName) . '</a>';
render_trader_shell_start('Trader Dashboard', 'dashboard', 'TRADER PORTAL › DASHBOARD', 'Trader Dashboard', $right);
?>

<section class="dashboard-grid">
    <article class="metric-card stock-card">
        <div class="metric-top">
            <span class="metric-icon"><i class="fa-regular fa-clipboard"></i></span>
            <span class="alert-pill">ORDERS</span>
        </div>
        <h3>TOTAL ORDERS</h3>
        <p class="big-number orange"><?= (int)$orderStats['TOTAL_ORDERS'] ?> <span>Orders</span></p>
        <p class="metric-note danger"><i class="fa-solid fa-triangle-exclamation"></i> <?= (int)$orderStats['PENDING_ORDERS'] ?> Pending</p>
    </article>

    <article class="metric-card revenue-card">
        <div class="metric-top">
            <span class="metric-icon green"><i class="fa-regular fa-money-bill-1"></i></span>
            <span class="growth-pill">TOTAL REVENUE</span>
        </div>
        <h3>TOTAL SALES</h3>
        <p class="big-number green-text">£<?= number_format((float)$orderStats['TOTAL_SALES'], 0) ?></p>
        <p class="metric-note success"><?= (int)$orderStats['COMPLETED_ORDERS'] ?> orders completed</p>
    </article>

    <article class="slot-card">
        <div class="slot-header">
            <h3>Upcoming Collection Slots</h3>
            <span>LIVE</span>
        </div>
        <?php if (empty($slots)): ?>
            <p class="slot-sub">No upcoming slots scheduled.</p>
        <?php else: ?>
            <?php foreach ($slots as $slot): ?>
                <p class="slot-time"><?= h($slot['SLOT_TIME']) ?></p>
                <p class="slot-sub"><strong><?= h($slot['SLOT_DAY']) ?></strong> — <?= h($slot['SLOT_DATE_FMT']) ?></p>
            <?php endforeach; ?>
        <?php endif; ?>
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
                <span>Stock</span>
                <span>Status</span>
            </div>

            <?php if (empty($topProducts)): ?>
                <p style="padding:1rem;color:#888;">No products found. <a href="add_product.php">Add your first product.</a></p>
            <?php else: ?>
                <?php foreach ($topProducts as $product): ?>
                    <div class="product-row">
                        <div class="product-name">
                            <img src="<?= h($product['image']) ?>" alt="<?= h($product['name']) ?>"
                                 onerror="this.onerror=null;this.style.opacity='0'">
                            <strong><?= h($product['name']) ?></strong>
                        </div>
                        <span class="category-chip"><?= h($product['category']) ?></span>
                        <strong><?= h($product['qty']) ?></strong>
                        <span class="status-dot <?= strtolower(str_replace(' ', '-', $product['status'])) ?>">
                            <?= h($product['status']) ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </article>

    <aside class="queue-block">
        <h2>Collection Queue</h2>
        <?php if (empty($slots)): ?>
            <div class="queue-card"><p style="padding:1rem;color:#888;">No upcoming slots.</p></div>
        <?php else: ?>
            <div class="queue-card">
                <?php foreach ($slots as $slot): ?>
                    <div class="queue-slot">
                        <span><?= h($slot['SLOT_DAY']) ?></span>
                        <strong><?= h($slot['SLOT_TIME']) ?></strong>
                        <em><?= h($slot['SLOT_DATE_FMT']) ?></em>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <a class="schedule-btn" href="Weekly_reports.php">
            <i class="fa-regular fa-calendar-days"></i>
            <span>View Full Weekly Schedule</span>
            <i class="fa-solid fa-arrow-right"></i>
        </a>
    </aside>
</section>

<?php render_trader_shell_end(); ?>
