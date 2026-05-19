<?php
require_once 'trader_menu.php';

$userId      = $_SESSION['user_id'];
$weeklyStats = [];

$conn = getDBConnection();
if ($conn) {
    $shop = getTraderShop($conn, $userId);
    if ($shop) {
        $shopId = $shop['SHOP_ID'];
        $sql = "SELECT TO_CHAR(TRUNC(o.order_date, 'IW'), 'YYYY-MM-DD') AS week_start,
                       COUNT(DISTINCT o.order_id) AS total_orders,
                       NVL(SUM(op.price_at_purchase * op.quantity), 0) AS total_sales,
                       COUNT(DISTINCT CASE WHEN o.order_status = 'COMPLETED' THEN o.order_id END) AS completed_orders,
                       COUNT(DISTINCT CASE WHEN o.order_status = 'PENDING'   THEN o.order_id END) AS pending_orders
                FROM orders o
                JOIN order_product op ON o.order_id = op.order_id
                JOIN product p        ON op.product_id = p.product_id
                WHERE p.shop_id = :sid
                GROUP BY TRUNC(o.order_date, 'IW')
                ORDER BY TRUNC(o.order_date, 'IW') DESC
                FETCH FIRST 8 ROWS ONLY";
        $stmt = oci_parse($conn, $sql);
        oci_bind_by_name($stmt, ':sid', $shopId);
        oci_execute($stmt);
        while ($row = oci_fetch_assoc($stmt)) {
            $row['ORDER_DATE'] = 'Week of ' . $row['WEEK_START'];
            $weeklyStats[] = $row;
        }
        oci_free_statement($stmt);
    }
    oci_close($conn);
}

$right = '<a class="btn" href="traderdashboard.php">Back to Dashboard</a>';
render_trader_shell_start('Weekly Reports', 'weekly', 'TRADER PORTAL › REPORTS', 'Weekly Reports', $right);
?>

<?php if (empty($weeklyStats)): ?>
    <div class="card" style="padding:2rem;text-align:center;color:#888;">No weekly data available yet.</div>
<?php else: ?>
<div class="report-grid">
    <div class="metric"><span>Total Orders</span>
        <strong><?= array_sum(array_column($weeklyStats, 'TOTAL_ORDERS')) ?></strong></div>
    <div class="metric"><span>Total Sales</span>
        <strong>£<?= number_format(array_sum(array_column($weeklyStats, 'TOTAL_SALES')), 2) ?></strong></div>
    <div class="metric"><span>Completed</span>
        <strong><?= array_sum(array_column($weeklyStats, 'COMPLETED_ORDERS')) ?></strong></div>
</div>

<div class="card">
    <h2>Weekly Orders</h2>
    <table class="table">
        <tr><th>Week</th><th>Total Orders</th><th>Total Sales</th><th>Completed</th><th>Pending</th></tr>
        <?php foreach ($weeklyStats as $s): ?>
            <tr>
                <td><?= h($s['ORDER_DATE']) ?></td>
                <td><?= h($s['TOTAL_ORDERS']) ?></td>
                <td>£<?= number_format((float)$s['TOTAL_SALES'], 2) ?></td>
                <td><?= h($s['COMPLETED_ORDERS']) ?></td>
                <td><?= h($s['PENDING_ORDERS']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
<?php endif; ?>

<?php render_trader_shell_end(); ?>
