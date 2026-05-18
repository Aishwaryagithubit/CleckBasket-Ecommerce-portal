<?php
require_once 'trader_menu.php';

$orders = [
    ['PAYMENT_ID'=>501,'ORDER_ID'=>1001,'PAYMENT_DATE'=>'2026-05-10','CUSTOMER_NAME'=>'Aarav Sharma','EMAIL'=>'aarav@example.com','ORDER_ITEMS'=>'Tomatoes x2, Milk x1','TRADER_TOTAL'=>42.50,'PAYMENT_METHOD'=>'Card','PAYMENT_STATUS'=>'completed'],
    ['PAYMENT_ID'=>502,'ORDER_ID'=>1002,'PAYMENT_DATE'=>'2026-05-09','CUSTOMER_NAME'=>'Maya Patel','EMAIL'=>'maya@example.com','ORDER_ITEMS'=>'Sourdough x3','TRADER_TOTAL'=>12.60,'PAYMENT_METHOD'=>'PayPal','PAYMENT_STATUS'=>'pending']
];

render_trader_shell_start('Trader Invoices', 'invoice', 'TRADER PORTAL › PAYMENTS', 'Payment Records');
?>

<div class="card">
    <table class="table">
        <tr><th>Payment ID</th><th>Order ID</th><th>Date</th><th>Customer</th><th>Items</th><th>Amount</th><th>Method</th><th>Status</th><th>Action</th></tr>
        <?php foreach($orders as $o): ?>
            <tr>
                <td>#<?= h($o['PAYMENT_ID']) ?></td>
                <td>#<?= h($o['ORDER_ID']) ?></td>
                <td><?= h($o['PAYMENT_DATE']) ?></td>
                <td><?= h($o['CUSTOMER_NAME']) ?><br><small><?= h($o['EMAIL']) ?></small></td>
                <td><?= h($o['ORDER_ITEMS']) ?></td>
                <td>£<?= number_format($o['TRADER_TOTAL'], 2) ?></td>
                <td><?= h($o['PAYMENT_METHOD']) ?></td>
                <td><span class="status <?= h($o['PAYMENT_STATUS']) ?>"><?= h($o['PAYMENT_STATUS']) ?></span></td>
                <td><a class="btn light" href="view_invoice.php?order_id=<?= h($o['ORDER_ID']) ?>">View</a></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

<?php render_trader_shell_end(); ?>