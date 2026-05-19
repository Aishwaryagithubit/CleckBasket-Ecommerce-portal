<?php
require_once 'trader_menu.php';

$userId   = $_SESSION['user_id'];
$payments = [];

$conn = getDBConnection();
if ($conn) {
    $shop = getTraderShop($conn, $userId);
    if ($shop) {
        $shopId = $shop['SHOP_ID'];
        $sql = "SELECT DISTINCT pay.payment_id, pay.order_id,
                       TO_CHAR(pay.payment_date, 'YYYY-MM-DD') AS payment_date,
                       pay.payment_amount, pay.payment_method,
                       u.firstname || ' ' || u.lastname AS customer_name,
                       u.email
                FROM payment pay
                JOIN orders o         ON pay.order_id = o.order_id
                JOIN order_product op ON o.order_id = op.order_id
                JOIN product p        ON op.product_id = p.product_id
                JOIN users u          ON o.user_id = u.user_id
                WHERE p.shop_id = :shop_id
                ORDER BY TO_CHAR(pay.payment_date, 'YYYY-MM-DD') DESC";
        $bind_shop_id = $shopId;
        $stmt = oci_parse($conn, $sql);
        oci_bind_by_name($stmt, ':shop_id', $bind_shop_id);
        oci_execute($stmt);
        while ($row = oci_fetch_assoc($stmt)) $payments[] = $row;
        oci_free_statement($stmt);
    }
    oci_close($conn);
}

render_trader_shell_start('Trader Invoices', 'invoice', 'TRADER PORTAL › PAYMENTS', 'Payment Records');
?>

<?php if (empty($payments)): ?>
    <div class="card" style="padding:2rem;text-align:center;color:#888;">No payment records found.</div>
<?php else: ?>
<div class="card">
    <table class="table">
        <tr>
            <th>Payment ID</th><th>Order ID</th><th>Date</th>
            <th>Customer</th><th>Amount</th><th>Method</th><th>Action</th>
        </tr>
        <?php foreach ($payments as $o): ?>
            <tr>
                <td>#<?= h($o['PAYMENT_ID']) ?></td>
                <td>#<?= h($o['ORDER_ID']) ?></td>
                <td><?= h($o['PAYMENT_DATE']) ?></td>
                <td><?= h($o['CUSTOMER_NAME']) ?><br><small><?= h($o['EMAIL']) ?></small></td>
                <td>£<?= number_format((float)$o['PAYMENT_AMOUNT'], 2) ?></td>
                <td><?= h($o['PAYMENT_METHOD']) ?></td>
                <td><a class="btn light" href="view_invoice.php?order_id=<?= (int)$o['ORDER_ID'] ?>">View</a></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
<?php endif; ?>

<?php render_trader_shell_end(); ?>
