<?php
require_once 'admin_auth.php';

$message = '';
$error   = '';

// Delete customer
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['customer_id'])
    && $_POST['action'] === 'delete_customer') {
    $cid  = (int)$_POST['customer_id'];
    $conn = getDBConnection();
    if ($conn) {

        // FIXED: Delete child records first before deleting user
        $delQueries = [
            "DELETE FROM payment          WHERE user_id = :id",
            "DELETE FROM favourite_product WHERE favourite_id IN (SELECT favourite_id FROM favourite WHERE user_id = :id)",
            "DELETE FROM favourite        WHERE user_id = :id",
            "DELETE FROM review           WHERE user_id = :id",
            "DELETE FROM order_product    WHERE order_id IN (SELECT order_id FROM orders WHERE user_id = :id)",
            "DELETE FROM orders           WHERE user_id = :id",
            "DELETE FROM cart_product     WHERE cart_id IN (SELECT cart_id FROM cart WHERE user_id = :id)",
            "DELETE FROM cart             WHERE user_id = :id",
            "DELETE FROM report           WHERE user_id = :id",
            "DELETE FROM users            WHERE user_id = :id AND UPPER(role) = 'CUSTOMER'",
        ];

        $failed = false;
        foreach ($delQueries as $q) {
            $s = oci_parse($conn, $q);
            oci_bind_by_name($s, ':id', $cid);
            if (!oci_execute($s)) {
                $err   = oci_error($s);
                $error = 'Delete failed: ' . $err['message'];
                $failed = true;
                oci_free_statement($s);
                break;
            }
            oci_free_statement($s);
        }
        if (!$failed) {
            oci_commit($conn);
            $message = 'Customer deleted successfully.';
        }
        oci_close($conn);
    }
}

// Load customers
$customers = [];
$conn = getDBConnection();
if ($conn) {
    $sql = "SELECT u.user_id, u.firstname || ' ' || u.lastname AS full_name,
                   u.email, u.contact_no, u.status,
                   TO_CHAR(u.created_date, 'YYYY-MM-DD') AS registered,
                   COUNT(o.order_id) AS order_count,
                   NVL(SUM(o.order_amount), 0) AS total_spent
            FROM users u
            LEFT JOIN orders o ON u.user_id = o.user_id
            WHERE UPPER(u.role) = 'CUSTOMER'
            GROUP BY u.user_id, u.firstname, u.lastname, u.email, u.contact_no, u.status, u.created_date
            ORDER BY u.created_date DESC";
    $stmt = oci_parse($conn, $sql);
    oci_execute($stmt);
    while ($row = oci_fetch_assoc($stmt)) $customers[] = $row;
    oci_free_statement($stmt);
    oci_close($conn);
}

$activeCount = count(array_filter($customers, fn($c) => strtoupper($c['STATUS'] ?? '') === 'ACTIVE'));
$todayNew    = count(array_filter($customers, fn($c) => ($c['REGISTERED'] ?? '') === date('Y-m-d')));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Directory - Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin-style.css">
</head>
<body>
<div class="admin-container">
    <?php admin_sidebar('customers'); ?>

    <main class="main-content">
        <header class="top-bar">
            <h1>Customer Directory</h1>
            <div class="user-info"><span><?= h($_SESSION['admin_username']) ?></span></div>
        </header>

        <div class="content">
            <?php if ($message): ?>
                <div style="background:#e8f5e9;color:#2e7d32;padding:15px;border-radius:8px;margin-bottom:20px;"><?= h($message) ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div style="background:#ffebee;color:#c62828;padding:15px;border-radius:8px;margin-bottom:20px;"><?= h($error) ?></div>
            <?php endif; ?>

            <section class="metrics" style="grid-template-columns:repeat(3,1fr);">
                <div class="metric-card">
                    <div class="metric-icon customers"><i class="fas fa-user-check"></i></div>
                    <div class="metric-details"><p>Active Customers</p><h3><?= $activeCount ?></h3></div>
                </div>
                <div class="metric-card">
                    <div class="metric-icon sales"><i class="fas fa-user-plus"></i></div>
                    <div class="metric-details"><p>New Today</p><h3><?= $todayNew ?></h3></div>
                </div>
                <div class="metric-card">
                    <div class="metric-icon products"><i class="fas fa-heartbeat"></i></div>
                    <div class="metric-details"><p>Total Customers</p><h3><?= count($customers) ?></h3></div>
                </div>
            </section>

            <div class="card">
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Customer Name</th>
                                <th>Status</th>
                                <th>Registered</th>
                                <th>Orders</th>
                                <th>Total Spent</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($customers)): ?>
                            <tr><td colspan="6" style="text-align:center;color:#999;">No customers registered yet.</td></tr>
                        <?php else: ?>
                        <?php foreach ($customers as $c): ?>
                            <tr>
                                <td>
                                    <div style="display:flex;align-items:center;gap:12px;">
                                        <div style="width:36px;height:36px;border-radius:50%;background:var(--light-bg);display:flex;align-items:center;justify-content:center;font-weight:600;color:var(--primary-brown);">
                                            <?= h(mb_substr($c['FULL_NAME'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <div style="font-weight:500;"><?= h($c['FULL_NAME']) ?></div>
                                            <div style="font-size:12px;color:var(--text-muted);"><?= h($c['EMAIL']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="status <?= strtolower(h($c['STATUS'] ?? 'active')) ?>"><?= h($c['STATUS'] ?? 'Active') ?></span></td>
                                <td><?= h($c['REGISTERED']) ?></td>
                                <td><?= (int)$c['ORDER_COUNT'] ?> orders</td>
                                <td>£<?= number_format((float)$c['TOTAL_SPENT'], 2) ?></td>
                                <td>
                                    <form method="POST" style="display:inline-block;margin:0;"
                                          onsubmit="return confirm('Delete <?= h($c['FULL_NAME']) ?>? This cannot be undone.');">
                                        <input type="hidden" name="action" value="delete_customer">
                                        <input type="hidden" name="customer_id" value="<?= (int)$c['USER_ID'] ?>">
                                        <button type="submit" class="btn-delete" style="padding:6px 10px;font-size:12px;">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>
</body>
</html>