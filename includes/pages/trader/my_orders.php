<?php
require_once 'trader_menu.php';

$orders = [
    ['ORDER_ID'=>1001,'CUSTOMER_NAME'=>'Aarav Sharma','CUSTOMER_EMAIL'=>'aarav@example.com','ORDER_DATE'=>'2026-05-10 10:30','COLLECTION_TIME'=>'10:00 - 13:00','TOTAL_AMOUNT'=>42.50,'STATUS'=>'PENDING'],
    ['ORDER_ID'=>1002,'CUSTOMER_NAME'=>'Maya Patel','CUSTOMER_EMAIL'=>'maya@example.com','ORDER_DATE'=>'2026-05-09 14:15','COLLECTION_TIME'=>'13:00 - 16:00','TOTAL_AMOUNT'=>31.20,'STATUS'=>'PROCESSING'],
    ['ORDER_ID'=>1003,'CUSTOMER_NAME'=>'Tom Wilson','CUSTOMER_EMAIL'=>'tom@example.com','ORDER_DATE'=>'2026-05-08 09:00','COLLECTION_TIME'=>'16:00 - 19:00','TOTAL_AMOUNT'=>19.99,'STATUS'=>'COMPLETED']
];

render_trader_shell_start('All Orders', 'invoice', 'TRADER PORTAL › ORDERS', 'All Orders');
?>

<div class="card">
    <table class="table">
        <tr><th>Order</th><th>Customer</th><th>Date</th><th>Collection</th><th>Total</th><th>Status</th><th>Action</th></tr>
        <?php foreach($orders as $o): ?>
            <tr>
                <td>#<?= h($o['ORDER_ID']) ?></td>
                <td><?= h($o['CUSTOMER_NAME']) ?><br><small><?= h($o['CUSTOMER_EMAIL']) ?></small></td>
                <td><?= h($o['ORDER_DATE']) ?></td>
                <td><?= h($o['COLLECTION_TIME']) ?></td>
                <td>£<?= number_format($o['TOTAL_AMOUNT'], 2) ?></td>
                <td><span class="status <?= strtolower(h($o['STATUS'])) ?>"><?= h($o['STATUS']) ?></span></td>
                <td><button class="btn light" onclick="alert('Front-end demo only')">Update</button></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

<?php render_trader_shell_end(); ?>