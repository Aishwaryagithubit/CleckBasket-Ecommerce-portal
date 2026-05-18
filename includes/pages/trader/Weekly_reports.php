<?php
require_once 'trader_menu.php';

$weeklyStats = array(
    array(
        'ORDER_DATE' => 'Week of May 10',
        'TOTAL_ORDERS' => 24,
        'TOTAL_SALES' => 420,
        'COMPLETED_ORDERS' => 18,
        'PENDING_ORDERS' => 6,
        'COMPLETED_SALES' => 355
    ),
    array(
        'ORDER_DATE' => 'Week of May 03',
        'TOTAL_ORDERS' => 19,
        'TOTAL_SALES' => 312,
        'COMPLETED_ORDERS' => 15,
        'PENDING_ORDERS' => 4,
        'COMPLETED_SALES' => 250
    ),
    array(
        'ORDER_DATE' => 'Week of Apr 26',
        'TOTAL_ORDERS' => 28,
        'TOTAL_SALES' => 510,
        'COMPLETED_ORDERS' => 21,
        'PENDING_ORDERS' => 7,
        'COMPLETED_SALES' => 430
    )
);

$right = '<a class="btn" href="traderdashboard.php">Back to Dashboard</a>';

render_trader_shell_start(
    'Weekly Reports',
    'weekly',
    'TRADER PORTAL › REPORTS',
    'Weekly Reports',
    $right
);
?>

<div class="report-grid">
    <div class="metric">
        <span>Total Orders</span>
        <strong><?= array_sum(array_column($weeklyStats, 'TOTAL_ORDERS')) ?></strong>
    </div>

    <div class="metric">
        <span>Total Sales</span>
        <strong>£<?= number_format(array_sum(array_column($weeklyStats, 'TOTAL_SALES')), 2) ?></strong>
    </div>

    <div class="metric">
        <span>Completed</span>
        <strong><?= array_sum(array_column($weeklyStats, 'COMPLETED_ORDERS')) ?></strong>
    </div>
</div>

<div class="card">
    <h2>Weekly Orders Details</h2>

    <table class="table">
        <tr>
            <th>Date</th>
            <th>Total Orders</th>
            <th>Total Sales</th>
            <th>Completed</th>
            <th>Pending</th>
        </tr>

        <?php foreach($weeklyStats as $s): ?>
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
