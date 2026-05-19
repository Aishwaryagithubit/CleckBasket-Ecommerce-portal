<?php
require_once 'trader_menu.php';

$userId       = $_SESSION['user_id'];
$monthlyStats = [];

$conn = getDBConnection();
if ($conn) {
    $shop = getTraderShop($conn, $userId);
    if ($shop) {
        $shopId = $shop['SHOP_ID'];
        $sql = "SELECT TO_CHAR(TRUNC(o.order_date, 'MM'), 'Month YYYY') AS month_label,
                       TRUNC(o.order_date, 'MM') AS month_sort,
                       COUNT(DISTINCT o.order_id) AS total_orders,
                       NVL(SUM(op.price_at_purchase * op.quantity), 0) AS total_sales,
                       COUNT(DISTINCT CASE WHEN o.order_status = 'COMPLETED' THEN o.order_id END) AS completed_orders,
                       COUNT(DISTINCT CASE WHEN o.order_status = 'PENDING'   THEN o.order_id END) AS pending_orders
                FROM orders o
                JOIN order_product op ON o.order_id = op.order_id
                JOIN product p        ON op.product_id = p.product_id
                WHERE p.shop_id = :sid
                GROUP BY TRUNC(o.order_date, 'MM')
                ORDER BY month_sort DESC
                FETCH FIRST 12 ROWS ONLY";
        $stmt = oci_parse($conn, $sql);
        oci_bind_by_name($stmt, ':sid', $shopId);
        oci_execute($stmt);
        while ($row = oci_fetch_assoc($stmt)) {
            $row['ORDER_DATE'] = trim($row['MONTH_LABEL']);
            $monthlyStats[] = $row;
        }
        oci_free_statement($stmt);
    }
    oci_close($conn);
}

$right = '<a class="btn" href="traderdashboard.php">Back to Dashboard</a>';
render_trader_shell_start('Monthly Reports', 'monthly', 'TRADER PORTAL › REPORTS', 'Monthly Reports', $right);
?>

<?php if (empty($monthlyStats)): ?>
    <div class="card" style="padding:2rem;text-align:center;color:#888;">No monthly data available yet.</div>
<?php else: ?>
<div class="report-grid">
    <div class="metric"><span>Total Orders</span>
        <strong><?= array_sum(array_column($monthlyStats, 'TOTAL_ORDERS')) ?></strong></div>
    <div class="metric"><span>Total Sales</span>
        <strong>£<?= number_format(array_sum(array_column($monthlyStats, 'TOTAL_SALES')), 2) ?></strong></div>
    <div class="metric"><span>Completed</span>
        <strong><?= array_sum(array_column($monthlyStats, 'COMPLETED_ORDERS')) ?></strong></div>
</div>

<div class="card">
    <h2>Monthly Orders</h2>
    <table class="table">
        <tr><th>Month</th><th>Total Orders</th><th>Total Sales</th><th>Completed</th><th>Pending</th></tr>
        <?php foreach ($monthlyStats as $s): ?>
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
