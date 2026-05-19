<?php
require_once 'admin_auth.php';

// ── Live metrics from DB ──────────────────────────────────────────────────────
$metrics = [
    'total_revenue'   => 0,
    'total_orders'    => 0,
    'total_customers' => 0,
    'pending_traders' => 0,
];
$recent_orders  = [];
$monthly_chart  = array_fill(0, 12, 0); // index 0=Jan … 11=Dec

$conn = getDBConnection();
if ($conn) {
    // Revenue + orders
    $s = oci_parse($conn,
        "SELECT COUNT(*) AS total_orders,
                NVL(SUM(order_amount), 0) AS total_revenue
         FROM orders");
    oci_execute($s);
    $r = oci_fetch_assoc($s);
    oci_free_statement($s);
    $metrics['total_orders']  = (int)($r['TOTAL_ORDERS']  ?? 0);
    $metrics['total_revenue'] = (float)($r['TOTAL_REVENUE'] ?? 0);

    // Customers
    $s = oci_parse($conn, "SELECT COUNT(*) AS cnt FROM users WHERE UPPER(role) = 'CUSTOMER'");
    oci_execute($s);
    $r = oci_fetch_assoc($s);
    oci_free_statement($s);
    $metrics['total_customers'] = (int)($r['CNT'] ?? 0);

    // Pending traders (PENDING status)
    $s = oci_parse($conn,
        "SELECT COUNT(*) AS cnt FROM users WHERE UPPER(role) = 'TRADER' AND UPPER(status) = 'PENDING'");
    oci_execute($s);
    $r = oci_fetch_assoc($s);
    oci_free_statement($s);
    $metrics['pending_traders'] = (int)($r['CNT'] ?? 0);

    // Recent 5 orders
    $s = oci_parse($conn,
        "SELECT o.order_id,
                u.firstname || ' ' || u.lastname AS customer,
                o.order_amount,
                o.order_status,
                TO_CHAR(o.order_date, 'YYYY-MM-DD') AS order_date
         FROM orders o
         JOIN users u ON o.user_id = u.user_id
         ORDER BY o.order_date DESC
         FETCH FIRST 5 ROWS ONLY");
    oci_execute($s);
    while ($row = oci_fetch_assoc($s)) $recent_orders[] = $row;
    oci_free_statement($s);

    // Monthly revenue for chart (current year)
    $s = oci_parse($conn,
        "SELECT TO_NUMBER(TO_CHAR(order_date, 'MM')) AS month_num,
                NVL(SUM(order_amount), 0) AS rev
         FROM orders
         WHERE TO_CHAR(order_date, 'YYYY') = TO_CHAR(SYSDATE, 'YYYY')
         GROUP BY TO_CHAR(order_date, 'MM')");
    oci_execute($s);
    while ($row = oci_fetch_assoc($s)) {
        $idx = (int)$row['MONTH_NUM'] - 1;
        if ($idx >= 0 && $idx < 12) $monthly_chart[$idx] = (float)$row['REV'];
    }
    oci_free_statement($s);

    // Pending trader list (up to 3)
    $pendingTraders = [];
    $s = oci_parse($conn,
        "SELECT user_id, firstname || ' ' || lastname AS name, email,
                TO_CHAR(created_date, 'YYYY-MM-DD') AS submitted
         FROM users
         WHERE UPPER(role) = 'TRADER' AND UPPER(status) = 'PENDING'
         ORDER BY created_date DESC
         FETCH FIRST 3 ROWS ONLY");
    oci_execute($s);
    while ($row = oci_fetch_assoc($s)) $pendingTraders[] = $row;
    oci_free_statement($s);

    oci_close($conn);
}

$maxRev = max(array_merge($monthly_chart, [1]));
$monthLabels = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - CleckBasket</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin-style.css">
</head>
<body>
<div class="admin-container">
    <?php admin_sidebar('dashboard'); ?>

    <main class="main-content">
        <header class="top-bar">
            <h1>Admin Management Overview</h1>
            <div class="user-info">
                <a href="product-add.php" class="btn-primary">+ New Product</a>
                <span><?= h($_SESSION['admin_username']) ?></span>
            </div>
        </header>

        <div class="content">
            <!-- Metrics -->
            <section class="metrics">
                <div class="metric-card">
                    <div class="metric-icon sales"><i class="fas fa-pound-sign"></i></div>
                    <div class="metric-details">
                        <p>Total Revenue</p>
                        <h3>£<?= number_format($metrics['total_revenue'], 0) ?></h3>
                    </div>
                </div>
                <div class="metric-card">
                    <div class="metric-icon orders"><i class="fas fa-shopping-cart"></i></div>
                    <div class="metric-details">
                        <p>Total Orders</p>
                        <h3><?= $metrics['total_orders'] ?></h3>
                    </div>
                </div>
                <div class="metric-card">
                    <div class="metric-icon customers"><i class="fas fa-users"></i></div>
                    <div class="metric-details">
                        <p>Customers</p>
                        <h3><?= $metrics['total_customers'] ?></h3>
                    </div>
                </div>
                <div class="metric-card">
                    <div class="metric-icon products"><i class="fas fa-user-check"></i></div>
                    <div class="metric-details">
                        <p>Pending Traders</p>
                        <h3><a href="add-trader.php" style="color:inherit;text-decoration:none;"><?= $metrics['pending_traders'] ?></a></h3>
                    </div>
                </div>
            </section>

            <div class="dashboard-grid">
                <div class="left-column">
                    <!-- Monthly Sales Chart -->
                    <div class="card">
                        <div class="card-header">
                            <h2>Monthly Sales Statistics (<?= date('Y') ?>)</h2>
                        </div>
                        <div class="chart-container">
                            <?php foreach ($monthly_chart as $i => $rev): ?>
                                <div class="chart-bar"
                                     style="height: <?= $maxRev > 0 ? round(($rev / $maxRev) * 100) : 0 ?>%;"
                                     data-label="<?= $monthLabels[$i] ?>"
                                     title="<?= $monthLabels[$i] ?>: £<?= number_format($rev, 0) ?>">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Recent Transactions -->
                    <div class="card">
                        <div class="card-header">
                            <h2>Recent Transactions</h2>
                            <a href="orders.php" style="color:var(--accent-green);text-decoration:none;font-size:14px;font-weight:500;">View All</a>
                        </div>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Order ID</th><th>Customer</th><th>Amount</th>
                                        <th>Status</th><th>Date</th><th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($recent_orders)): ?>
                                        <tr><td colspan="6" style="text-align:center;color:#999;">No orders yet.</td></tr>
                                    <?php else: ?>
                                    <?php foreach ($recent_orders as $o): ?>
                                    <tr>
                                        <td>#<?= h($o['ORDER_ID']) ?></td>
                                        <td><?= h($o['CUSTOMER']) ?></td>
                                        <td>£<?= number_format((float)$o['ORDER_AMOUNT'], 2) ?></td>
                                        <td><span class="status <?= strtolower(h($o['ORDER_STATUS'])) ?>"><?= h($o['ORDER_STATUS']) ?></span></td>
                                        <td><?= h($o['ORDER_DATE']) ?></td>
                                        <td><a href="orders.php" class="btn-edit" style="font-size:12px;"><i class="fas fa-eye"></i></a></td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="right-column">
                    <!-- Pending Approvals -->
                    <div class="card">
                        <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;">
                            <h2>Pending Trader Approvals</h2>
                            <a href="add-trader.php" style="font-size:14px;color:var(--accent-green);text-decoration:none;">Manage &rarr;</a>
                        </div>
                        <?php if (empty($pendingTraders)): ?>
                            <p style="color:var(--text-muted);font-size:14px;">No pending approvals.</p>
                        <?php else: ?>
                        <?php foreach ($pendingTraders as $pt): ?>
                            <a href="add-trader.php" class="list-item" style="display:flex;align-items:center;text-decoration:none;color:inherit;">
                                <div class="list-icon"><i class="fas fa-store"></i></div>
                                <div class="list-info">
                                    <h4><?= h($pt['NAME']) ?></h4>
                                    <p><?= h($pt['EMAIL']) ?></p>
                                </div>
                                <span class="status pending">Pending</span>
                            </a>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <!-- Quick stats -->
                    <div class="card dark-box">
                        <h3>Platform Summary</h3>
                        <div class="list-item" style="border-bottom-color:rgba(255,255,255,0.1);">
                            <div class="list-info" style="flex:1;">
                                <h4 style="color:white;">Total Revenue</h4>
                            </div>
                            <span style="font-weight:600;">£<?= number_format($metrics['total_revenue'], 0) ?></span>
                        </div>
                        <div class="list-item" style="border-bottom-color:rgba(255,255,255,0.1);">
                            <div class="list-info" style="flex:1;">
                                <h4 style="color:white;">Orders Placed</h4>
                            </div>
                            <span style="font-weight:600;"><?= $metrics['total_orders'] ?></span>
                        </div>
                        <div class="list-item" style="border-bottom:none;">
                            <div class="list-info" style="flex:1;">
                                <h4 style="color:white;">Registered Customers</h4>
                            </div>
                            <span style="font-weight:600;"><?= $metrics['total_customers'] ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
</body>
</html>
