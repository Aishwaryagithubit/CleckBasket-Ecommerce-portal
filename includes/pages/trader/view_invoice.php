<?php
require_once 'trader_menu.php';

render_trader_shell_start('Invoice', 'invoice', 'TRADER PORTAL › PAYMENTS › INVOICE', 'Trader Invoice');
?>

<div class="invoice-box">
    <h1>TRADER INVOICE</h1>
    <p>Order #<?= h($_GET['order_id'] ?? '1001') ?></p>
    <hr>

    <div class="invoice-grid">
        <div>
            <h3>Billed To</h3>
            <p>Aarav Sharma<br>aarav@example.com<br>07123456789</p>
        </div>
        <div>
            <h3>Shop Details</h3>
            <p>Mir Farm<br>Category: Vegetables</p>
        </div>
    </div>

    <h3>Order Summary</h3>
    <table class="table">
        <tr><th>Product</th><th>Price</th><th>Qty</th><th>Subtotal</th></tr>
        <tr><td>Heirloom Tomatoes</td><td>£3.50</td><td>4</td><td>£14.00</td></tr>
        <tr><td>Whole Farm Milk</td><td>£2.30</td><td>2</td><td>£4.60</td></tr>
        <tr><td colspan="3"><b>Total</b></td><td><b>£18.60</b></td></tr>
    </table>

    <p><b>Payment Method:</b> CARD</p>
    <p><b>Status:</b> Completed</p>

    <button class="btn" onclick="window.print()">Print Invoice</button>
    <a class="btn light" href="trader_invoice.php">Back</a>
</div>

<?php render_trader_shell_end(); ?>