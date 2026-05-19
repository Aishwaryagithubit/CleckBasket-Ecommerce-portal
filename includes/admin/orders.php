<?php
require_once 'admin_auth.php';

$message = '';

// Status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['new_status'])) {
    $oid       = (int)$_POST['order_id'];
    $newStatus = strtoupper(trim($_POST['new_status']));
    $allowed   = ['PENDING','PROCESSING','COMPLETED','CANCELLED'];
    if (in_array($newStatus, $allowed)) {
        $conn = getDBConnection();
        if ($conn) {
            $upd = oci_parse($conn, "UPDATE orders SET order_status = :st WHERE order_id = :oid");
            oci_bind_by_name($upd, ':st',  $newStatus);
            oci_bind_by_name($upd, ':oid', $oid);
            if (oci_execute($upd)) { oci_commit($conn); $message = 'Order status updated.'; }
            oci_free_statement($upd);
            oci_close($conn);
        }
    }
}

// Load orders
$orders = [];
$conn = getDBConnection();
if ($conn) {
    $sql = "SELECT o.order_id,
                   u.firstname || ' ' || u.lastname AS customer,
                   u.email,
                   o.order_amount,
                   o.order_status,
                   TO_CHAR(o.order_date, 'YYYY-MM-DD HH24:MI') AS order_date,
                   NVL(cs.slot_time, 'N/A') AS slot_time,
                   NVL(cs.slot_day,  'N/A') AS slot_day
            FROM orders o
            JOIN users u ON o.user_id = u.user_id
            LEFT JOIN collection_slot cs ON o.collection_slot_id = cs.collection_slot_id
            ORDER BY o.order_date DESC";
    $stmt = oci_parse($conn, $sql);
    oci_execute($stmt);
    while ($row = oci_fetch_assoc($stmt)) $orders[] = $row;
    oci_free_statement($stmt);
    oci_close($conn);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders - Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin-style.css">
    <link rel="stylesheet" href="css/table-style.css">
</head>
<body>
<div class="admin-container">
    <?php admin_sidebar('orders'); ?>

    <main class="main-content">
        <header class="top-bar">
            <h1>Orders</h1>
            <div class="user-info"><span><?= h($_SESSION['admin_username']) ?></span></div>
        </header>

        <div class="content">
            <?php if ($message): ?>
                <div style="background:#e8f5e9;color:#2e7d32;padding:15px;border-radius:8px;margin-bottom:20px;"><?= h($message) ?></div>
            <?php endif; ?>

            <div class="page-header"><h2>Order Management</h2></div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th><th>Customer</th><th>Amount</th>
                            <th>Collection Slot</th><th>Status</th><th>Date</th><th>Update</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($orders)): ?>
                        <tr><td colspan="7" style="text-align:center;color:#999;">No orders found.</td></tr>
                    <?php else: ?>
                    <?php foreach ($orders as $o): ?>
                        <tr>
                            <td>#<?= h($o['ORDER_ID']) ?></td>
                            <td>
                                <?= h($o['CUSTOMER']) ?><br>
                                <small style="color:var(--text-muted);"><?= h($o['EMAIL']) ?></small>
                            </td>
                            <td>£<?= number_format((float)$o['ORDER_AMOUNT'], 2) ?></td>
                            <td><?= h($o['SLOT_DAY']) ?> <?= h($o['SLOT_TIME']) ?></td>
                            <td><span class="status <?= strtolower(h($o['ORDER_STATUS'])) ?>"><?= h($o['ORDER_STATUS']) ?></span></td>
                            <td><?= h($o['ORDER_DATE']) ?></td>
                            <td>
                                <form method="POST" style="display:flex;gap:4px;">
                                    <input type="hidden" name="order_id" value="<?= (int)$o['ORDER_ID'] ?>">
                                    <select name="new_status" style="font-size:12px;padding:4px;border-radius:4px;border:1px solid #ddd;">
                                        <?php foreach (['PENDING','PROCESSING','COMPLETED','CANCELLED'] as $s): ?>
                                            <option <?= $o['ORDER_STATUS'] === $s ? 'selected' : '' ?>><?= $s ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button class="btn-edit" type="submit" style="font-size:12px;padding:4px 8px;">Save</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
</body>
</html>
