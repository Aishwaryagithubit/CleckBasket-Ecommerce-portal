<?php
require_once 'trader_menu.php';

$userId  = $_SESSION['user_id'];
$orderId = (int)($_GET['order_id'] ?? 0);

$order     = null;
$items     = [];
$shop      = null;
$customer  = null;
$payment   = null;

if ($orderId) {
    $conn = getDBConnection();
    if ($conn) {
        $traderShop = getTraderShop($conn, $userId);
        if ($traderShop) {
            $shop = $traderShop;

            // Local vars for OCI8 reference binding
            $bind_order_id = $orderId;
            $bind_shop_id  = $traderShop['SHOP_ID'];

            // Verify this order contains products from this trader's shop
            $chk = oci_parse($conn,
                "SELECT COUNT(*) AS cnt FROM order_product op
                 JOIN product p ON op.product_id = p.product_id
                 WHERE op.order_id = :order_id AND p.shop_id = :shop_id");
            oci_bind_by_name($chk, ':order_id', $bind_order_id);
            oci_bind_by_name($chk, ':shop_id',  $bind_shop_id);
            oci_execute($chk);
            $row = oci_fetch_assoc($chk);
            oci_free_statement($chk);

            if ((int)($row['CNT'] ?? 0) > 0) {
                // Order header
                $sql = "SELECT o.order_id, o.order_amount, o.order_status,
                               TO_CHAR(o.order_date,'DD Mon YYYY') AS order_date,
                               u.firstname || ' ' || u.lastname AS customer_name,
                               u.email, u.contact_no
                        FROM orders o
                        JOIN users u ON o.user_id = u.user_id
                        WHERE o.order_id = :order_id";
                $stmt = oci_parse($conn, $sql);
                oci_bind_by_name($stmt, ':order_id', $bind_order_id);
                oci_execute($stmt);
                $order = oci_fetch_assoc($stmt);
                oci_free_statement($stmt);

                // Only this trader's items
                $sql2 = "SELECT p.product_name, op.price_at_purchase AS price, op.quantity,
                                (op.price_at_purchase * op.quantity) AS subtotal
                         FROM order_product op
                         JOIN product p ON op.product_id = p.product_id
                         WHERE op.order_id = :order_id AND p.shop_id = :shop_id";
                $stmt2 = oci_parse($conn, $sql2);
                oci_bind_by_name($stmt2, ':order_id', $bind_order_id);
                oci_bind_by_name($stmt2, ':shop_id',  $bind_shop_id);
                oci_execute($stmt2);
                while ($r = oci_fetch_assoc($stmt2)) $items[] = $r;
                oci_free_statement($stmt2);

                // Payment info
                $sql3 = "SELECT payment_method, payment_amount FROM payment WHERE order_id = :order_id";
                $stmt3 = oci_parse($conn, $sql3);
                oci_bind_by_name($stmt3, ':order_id', $bind_order_id);
                oci_execute($stmt3);
                $payment = oci_fetch_assoc($stmt3);
                oci_free_statement($stmt3);
            }
        }
        oci_close($conn);
    }
}

render_trader_shell_start('Invoice', 'invoice', 'TRADER PORTAL › PAYMENTS › INVOICE', 'Trader Invoice');
?>

<?php if (!$order): ?>
    <div class="card" style="padding:2rem;text-align:center;color:#888;">
        Invoice not found. <a href="trader_invoice.php">Back to Payments</a>
    </div>
<?php else: ?>
<div class="invoice-box">
    <h1>TRADER INVOICE</h1>
    <p>Order #<?= h($order['ORDER_ID']) ?> &nbsp;·&nbsp; <?= h($order['ORDER_DATE']) ?></p>
    <hr>

    <div class="invoice-grid">
        <div>
            <h3>Billed To</h3>
            <p><?= h($order['CUSTOMER_NAME']) ?><br>
               <?= h($order['EMAIL']) ?><br>
               <?= h($order['CONTACT_NO'] ?? '') ?>
            </p>
        </div>
        <div>
            <h3>Shop Details</h3>
            <p><?= h($shop['SHOP_NAME']) ?><br><?= h($shop['DESCRIPTION'] ?? '') ?></p>
        </div>
    </div>

    <h3>Order Summary</h3>
    <table class="table">
        <tr><th>Product</th><th>Price</th><th>Qty</th><th>Subtotal</th></tr>
        <?php foreach ($items as $item): ?>
            <tr>
                <td><?= h($item['PRODUCT_NAME']) ?></td>
                <td>£<?= number_format((float)$item['PRICE'], 2) ?></td>
                <td><?= h($item['QUANTITY']) ?></td>
                <td>£<?= number_format((float)$item['SUBTOTAL'], 2) ?></td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <td colspan="3"><b>Total</b></td>
            <td><b>£<?= number_format((float)$order['ORDER_AMOUNT'], 2) ?></b></td>
        </tr>
    </table>

    <?php if ($payment): ?>
        <p><b>Payment Method:</b> <?= h($payment['PAYMENT_METHOD']) ?></p>
        <p><b>Amount Paid:</b> £<?= number_format((float)$payment['PAYMENT_AMOUNT'], 2) ?></p>
    <?php endif; ?>
    <p><b>Status:</b> <?= h($order['ORDER_STATUS']) ?></p>

    <button class="btn" onclick="window.print()">Print Invoice</button>
    <a class="btn light" href="trader_invoice.php">Back</a>
</div>
<?php endif; ?>

<?php render_trader_shell_end(); ?>
