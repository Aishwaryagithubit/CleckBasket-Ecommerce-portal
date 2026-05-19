<?php
require_once 'admin_auth.php';

// Monthly sales for current year
$reports     = [];
$topProducts = [];
$topTraders  = [];

$conn = getDBConnection();
if ($conn) {
    // Monthly breakdown
    $s = oci_parse($conn,
        "SELECT TO_CHAR(o.order_date, 'YYYY-MM') AS yr_month,
                TO_CHAR(o.order_date, 'Month YYYY') AS month_label,
                COUNT(o.order_id) AS order_count,
                NVL(SUM(o.order_amount), 0) AS revenue,
                COUNT(DISTINCT o.user_id) AS unique_customers
         FROM orders o
         WHERE TO_CHAR(o.order_date, 'YYYY') = TO_CHAR(SYSDATE, 'YYYY')
         GROUP BY TO_CHAR(o.order_date, 'YYYY-MM'), TO_CHAR(o.order_date, 'Month YYYY')
         ORDER BY yr_month DESC");
    oci_execute($s);
    while ($row = oci_fetch_assoc($s)) $reports[] = $row;
    oci_free_statement($s);

    // Top 5 products by quantity sold
    $s = oci_parse($conn,
        "SELECT p.product_name, pc.category_type,
                NVL(SUM(op.quantity), 0) AS qty_sold,
                NVL(SUM(op.quantity * op.price_at_purchase), 0) AS revenue
         FROM product p
         JOIN product_category pc ON p.product_category_id = pc.product_category_id
         LEFT JOIN order_product op ON p.product_id = op.product_id
         GROUP BY p.product_name, pc.category_type
         ORDER BY qty_sold DESC
         FETCH FIRST 5 ROWS ONLY");
    oci_execute($s);
    while ($row = oci_fetch_assoc($s)) $topProducts[] = $row;
    oci_free_statement($s);

    // Totals for the year
    $s = oci_parse($conn,
        "SELECT NVL(SUM(order_amount), 0) AS total_rev,
                COUNT(*) AS total_orders
         FROM orders
         WHERE TO_CHAR(order_date, 'YYYY') = TO_CHAR(SYSDATE, 'YYYY')");
    oci_execute($s);
    $totals = oci_fetch_assoc($s);
    oci_free_statement($s);

    oci_close($conn);
}

$totalRev    = (float)($totals['TOTAL_REV']    ?? 0);
$totalOrders = (int)($totals['TOTAL_ORDERS'] ?? 0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin-style.css">
    <link rel="stylesheet" href="css/table-style.css">
</head>
<body>
<div class="admin-container">
    <?php admin_sidebar('reports'); ?>

    <main class="main-content">
        <header class="top-bar">
            <h1>Reports &amp; Analytics</h1>
            <div class="user-info"><span><?= h($_SESSION['admin_username']) ?></span></div>
        </header>

        <div class="content">
            <div class="page-header">
                <h2>Sales Analytics — <?= date('Y') ?></h2>
            </div>

            <!-- Year totals -->
            <section class="metrics" style="grid-template-columns:repeat(2,1fr);margin-bottom:24px;">
                <div class="metric-card">
                    <div class="metric-icon sales"><i class="fas fa-pound-sign"></i></div>
                    <div class="metric-details">
                        <p>Revenue This Year</p>
                        <h3>£<?= number_format($totalRev, 0) ?></h3>
                    </div>
                </div>
                <div class="metric-card">
                    <div class="metric-icon orders"><i class="fas fa-shopping-cart"></i></div>
                    <div class="metric-details">
                        <p>Orders This Year</p>
                        <h3><?= $totalOrders ?></h3>
                    </div>
                </div>
            </section>

            <!-- Monthly breakdown -->
            <div class="card" style="margin-bottom:24px;">
                <div class="card-header"><h2>Monthly Breakdown</h2></div>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th>Revenue</th>
                                <th>Orders</th>
                                <th>Unique Customers</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($reports)): ?>
                            <tr><td colspan="4" style="text-align:center;color:#999;">No sales data for <?= date('Y') ?>.</td></tr>
                        <?php else: ?>
                        <?php foreach ($reports as $r): ?>
                            <tr>
                                <td><?= h(trim($r['MONTH_LABEL'])) ?></td>
                                <td>£<?= number_format((float)$r['REVENUE'], 2) ?></td>
                                <td><?= (int)$r['ORDER_COUNT'] ?></td>
                                <td><?= (int)$r['UNIQUE_CUSTOMERS'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Top products -->
            <?php if (!empty($topProducts)): ?>
            <div class="card">
                <div class="card-header"><h2>Top 5 Products by Units Sold</h2></div>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Category</th>
                                <th>Units Sold</th>
                                <th>Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($topProducts as $tp): ?>
                            <tr>
                                <td><?= h($tp['PRODUCT_NAME']) ?></td>
                                <td><?= h($tp['CATEGORY_TYPE']) ?></td>
                                <td><?= (int)$tp['QTY_SOLD'] ?></td>
                                <td>£<?= number_format((float)$tp['REVENUE'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </main>
</div>
</body>
</html>
