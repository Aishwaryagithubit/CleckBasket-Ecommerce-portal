<?php
require_once 'trader_menu.php';

$monthlyStats = array(
    array(
        'ORDER_DATE' => 'May 2026',
        'TOTAL_ORDERS' => 24,
        'TOTAL_SALES' => 420,
        'COMPLETED_ORDERS' => 18,
        'PENDING_ORDERS' => 6,
        'COMPLETED_SALES' => 355
    ),
    array(
        'ORDER_DATE' => 'April 2026',
        'TOTAL_ORDERS' => 19,
        'TOTAL_SALES' => 312,
        'COMPLETED_ORDERS' => 15,
        'PENDING_ORDERS' => 4,
        'COMPLETED_SALES' => 250
    ),
    array(
        'ORDER_DATE' => 'March 2026',
        'TOTAL_ORDERS' => 28,
        'TOTAL_SALES' => 510,
        'COMPLETED_ORDERS' => 21,
        'PENDING_ORDERS' => 7,
        'COMPLETED_SALES' => 430
    )
);

$right = '<a class="btn" href="traderdashboard.php">Back to Dashboard</a>';

render_trader_shell_start(
    'Monthly Reports',
    'monthly',
    'TRADER PORTAL › REPORTS',
    'Monthly Reports',
    $right
);
?>

<div class="report-grid">
    <div class="metric">
        <span>Total Orders</span>
        <strong><?= array_sum(array_column($monthlyStats, 'TOTAL_ORDERS')) ?></strong>
    </div>

    <div class="metric">
        <span>Total Sales</span>
        <strong>£<?= number_format(array_sum(array_column($monthlyStats, 'TOTAL_SALES')), 2) ?></strong>
    </div>

    <div class="metric">
        <span>Completed</span>
        <strong><?= array_sum(array_column($monthlyStats, 'COMPLETED_ORDERS')) ?></strong>
    </div>
</div>

<div class="card">
    <h2>Monthly Orders Details</h2>

    <table class="table">
        <tr>
            <th>Date</th>
            <th>Total Orders</th>
            <th>Total Sales</th>
            <th>Completed</th>
            <th>Pending</th>
        </tr>

        <?php foreach($monthlyStats as $s): ?>
            <tr>
                <td><?= h($s['ORDER_DATE']) ?></td>
                <td><?= h($s['TOTAL_ORDERS']) ?></td>
                <td>£<?= number_format($s['TOTAL_SALES'], 2) ?></td>
                <td><?= h($s['COMPLETED_ORDERS']) ?></td>
                <td><?= h($s['PENDING_ORDERS']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

<?php render_trader_shell_end(); ?>
