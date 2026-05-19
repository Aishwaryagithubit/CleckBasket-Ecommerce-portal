<?php
require_once 'trader_menu.php';

$userId = $_SESSION['user_id'];
$orders = [];

$conn = getDBConnection();
if ($conn) {
    $shop = getTraderShop($conn, $userId);
    if ($shop) {
        $shopId = $shop['SHOP_ID'];

        // Handle status update
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['new_status'])) {
            $orderId   = (int)$_POST['order_id'];
            $newStatus = strtoupper(trim($_POST['new_status']));
            $allowed   = ['PENDING', 'PROCESSING', 'COMPLETED', 'CANCELLED'];
            if (in_array($newStatus, $allowed)) {
                $upd = oci_parse($conn, "UPDATE orders SET order_status = :st WHERE order_id = :oid");
                oci_bind_by_name($upd, ':st',  $newStatus);
                oci_bind_by_name($upd, ':oid', $orderId);
                oci_execute($upd);
                oci_commit($conn);
                oci_free_statement($upd);
            }
        }

        $sql = "SELECT DISTINCT o.order_id,
                       u.firstname || ' ' || u.lastname AS customer_name,
                       u.email,
                       TO_CHAR(o.order_date, 'YYYY-MM-DD HH24:MI') AS order_date,
                       NVL(cs.slot_time, 'N/A') AS collection_time,
                       o.order_amount,
                       o.order_status
                FROM orders o
                JOIN users u          ON o.user_id = u.user_id
                JOIN order_product op ON o.order_id = op.order_id
                JOIN product p        ON op.product_id = p.product_id
                LEFT JOIN collection_slot cs ON o.collection_slot_id = cs.collection_slot_id
                WHERE p.shop_id = :sid
                ORDER BY o.order_date DESC";
        $stmt = oci_parse($conn, $sql);
        oci_bind_by_name($stmt, ':sid', $shopId);
        oci_execute($stmt);
        while ($row = oci_fetch_assoc($stmt)) $orders[] = $row;
        oci_free_statement($stmt);
    }
    oci_close($conn);
}

render_trader_shell_start('All Orders', 'invoice', 'TRADER PORTAL › ORDERS', 'All Orders');
?>

<?php if (empty($orders)): ?>
    <div class="card" style="padding:2rem;text-align:center;color:#888;">No orders found for your shop.</div>
<?php else: ?>
<div class="card">
    <table class="table">
        <tr>
            <th>Order</th><th>Customer</th><th>Date</th>
            <th>Collection</th><th>Total</th><th>Status</th><th>Action</th>
        </tr>
        <?php foreach ($orders as $o): ?>
            <tr>
                <td>#<?= h($o['ORDER_ID']) ?></td>
                <td><?= h($o['CUSTOMER_NAME']) ?><br><small><?= h($o['EMAIL']) ?></small></td>
                <td><?= h($o['ORDER_DATE']) ?></td>
                <td><?= h($o['COLLECTION_TIME']) ?></td>
                <td>£<?= number_format((float)$o['ORDER_AMOUNT'], 2) ?></td>
                <td><span class="status <?= strtolower(h($o['ORDER_STATUS'])) ?>"><?= h($o['ORDER_STATUS']) ?></span></td>
                <td>
                    <form method="POST" style="display:flex;gap:4px;">
                        <input type="hidden" name="order_id" value="<?= (int)$o['ORDER_ID'] ?>">
                        <select name="new_status" style="font-size:12px;padding:4px;">
                            <?php foreach (['PENDING','PROCESSING','COMPLETED','CANCELLED'] as $s): ?>
                                <option <?= $o['ORDER_STATUS'] === $s ? 'selected' : '' ?>><?= $s ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button class="btn light" type="submit">Update</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
<?php endif; ?>

<?php render_trader_shell_end(); ?>
