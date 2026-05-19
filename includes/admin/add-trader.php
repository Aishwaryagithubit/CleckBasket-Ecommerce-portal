<?php
require_once 'admin_auth.php';

$message = '';
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Approve / Reject existing trader
    if (isset($_POST['trader_id'], $_POST['trader_action'])) {
        $tid    = (int)$_POST['trader_id'];
        $action = $_POST['trader_action'];
        if (in_array($action, ['approve', 'reject'], true)) {
            $newStatus = $action === 'approve' ? 'ACTIVE' : 'REJECTED';
            $conn = getDBConnection();
            if ($conn) {
                $upd = oci_parse($conn, "UPDATE users SET status = :st WHERE user_id = :id AND UPPER(role) = 'TRADER'");
                oci_bind_by_name($upd, ':st', $newStatus);
                oci_bind_by_name($upd, ':id', $tid);
                if (oci_execute($upd)) {
                    oci_commit($conn);
                    $message = $action === 'approve' ? 'Trader approved.' : 'Trader rejected.';
                } else {
                    $err   = oci_error($upd);
                    $error = $err['message'];
                }
                oci_free_statement($upd);
                oci_close($conn);
            }
        }
    } elseif (isset($_POST['business_name'])) {
        // Register new merchant
        $businessName  = trim($_POST['business_name']    ?? '');
        $contactPerson = trim($_POST['contact_person']   ?? '');
        $email         = trim($_POST['email']            ?? '');
        $phone         = trim($_POST['phone']            ?? '');

        if ($businessName === '' || $contactPerson === '' || $email === '') {
            $error = 'Business name, contact person, and email are required.';
        } else {
            // Split contact person into first/last name
            $parts     = explode(' ', $contactPerson, 2);
            $firstname = $parts[0];
            $lastname  = $parts[1] ?? '';
            $tempPw    = password_hash(bin2hex(random_bytes(8)), PASSWORD_DEFAULT);

            $conn = getDBConnection();
            if ($conn) {
                // Insert user
                $insUser = oci_parse($conn,
                    "INSERT INTO users (firstname, lastname, email, contact_no, password_hash, role, status)
                     VALUES (:p_firstname, :p_lastname, :p_email, :p_phone, :p_pwhash, 'TRADER', 'ACTIVE')
                     RETURNING user_id INTO :p_uid");
                $newUid = 0;
                oci_bind_by_name($insUser, ':p_firstname', $firstname);
                oci_bind_by_name($insUser, ':p_lastname',  $lastname);
                oci_bind_by_name($insUser, ':p_email',     $email);
                oci_bind_by_name($insUser, ':p_phone',     $phone);
                oci_bind_by_name($insUser, ':p_pwhash',    $tempPw);
                oci_bind_by_name($insUser, ':p_uid',       $newUid, 20, SQLT_INT);

                if (oci_execute($insUser, OCI_NO_AUTO_COMMIT)) {
                    // Insert shop
                    $insShop = oci_parse($conn,
                        "INSERT INTO shop (shop_name, user_id) VALUES (:p_shopname, :p_shopuid)");
                    oci_bind_by_name($insShop, ':p_shopname', $businessName);
                    oci_bind_by_name($insShop, ':p_shopuid',  $newUid);
                    oci_execute($insShop, OCI_NO_AUTO_COMMIT);
                    oci_free_statement($insShop);
                    oci_commit($conn);
                    $message = 'Merchant "' . htmlspecialchars($businessName, ENT_QUOTES) . '" registered successfully.';
                } else {
                    $err   = oci_error($insUser);
                    $error = 'Registration failed: ' . $err['message'];
                    oci_rollback($conn);
                }
                oci_free_statement($insUser);
                oci_close($conn);
            }
        }
    }
}

// Load pending traders
$pendingTraders = [];
$conn = getDBConnection();
if ($conn) {
    $sql = "SELECT u.user_id, u.firstname || ' ' || u.lastname AS name, u.email, u.contact_no,
                   TO_CHAR(u.created_date, 'YYYY-MM-DD') AS submitted,
                   NVL(s.shop_name, 'N/A') AS shop_name
            FROM users u
            LEFT JOIN shop s ON u.user_id = s.user_id
            WHERE UPPER(u.role) = 'TRADER' AND UPPER(u.status) = 'PENDING'
            ORDER BY u.created_date DESC";
    $stmt = oci_parse($conn, $sql);
    oci_execute($stmt);
    while ($row = oci_fetch_assoc($stmt)) $pendingTraders[] = $row;
    oci_free_statement($stmt);
    oci_close($conn);
}

// Load all traders (active + rejected) for the summary table
$allTraders = [];
$conn = getDBConnection();
if ($conn) {
    $sql = "SELECT u.user_id, u.firstname || ' ' || u.lastname AS name, u.email,
                   u.status, TO_CHAR(u.created_date, 'YYYY-MM-DD') AS joined,
                   NVL(s.shop_name, 'N/A') AS shop_name
            FROM users u
            LEFT JOIN shop s ON u.user_id = s.user_id
            WHERE UPPER(u.role) = 'TRADER'
            ORDER BY u.created_date DESC";
    $stmt = oci_parse($conn, $sql);
    oci_execute($stmt);
    while ($row = oci_fetch_assoc($stmt)) $allTraders[] = $row;
    oci_free_statement($stmt);
    oci_close($conn);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trader Management - Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin-style.css">
</head>
<body>
<div class="admin-container">
    <?php admin_sidebar('traders'); ?>

    <main class="main-content">
        <header class="top-bar">
            <h1>Trader Management</h1>
            <div class="user-info"><span><?= h($_SESSION['admin_username']) ?></span></div>
        </header>

        <div class="content">
            <?php if ($message): ?>
                <div style="background:#e8f5e9;color:#2e7d32;padding:15px;border-radius:8px;margin-bottom:20px;"><?= h($message) ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div style="background:#ffebee;color:#c62828;padding:15px;border-radius:8px;margin-bottom:20px;"><?= h($error) ?></div>
            <?php endif; ?>

            <div class="dashboard-grid">
                <!-- Register Merchant Form -->
                <div class="left-column">
                    <form method="POST">
                        <div class="card">
                            <div class="card-header"><h2>Register New Merchant</h2></div>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label>Business Name *</label>
                                    <input type="text" name="business_name" required placeholder="e.g. Sunny Vale Farms">
                                </div>
                                <div class="form-group">
                                    <label>Contact Person *</label>
                                    <input type="text" name="contact_person" required placeholder="Full Name">
                                </div>
                                <div class="form-group">
                                    <label>Email Address *</label>
                                    <input type="email" name="email" required placeholder="contact@business.com">
                                </div>
                                <div class="form-group">
                                    <label>Phone Number</label>
                                    <input type="text" name="phone" placeholder="+44 123 456 7890">
                                </div>
                            </div>
                        </div>

                        <div style="display:flex;justify-content:flex-end;margin-bottom:24px;">
                            <button type="submit" class="btn-primary" style="padding:10px 24px;">Register Merchant</button>
                        </div>
                    </form>

                    <!-- All Traders Table -->
                    <div class="card">
                        <div class="card-header"><h2>All Traders</h2></div>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Shop</th>
                                        <th>Status</th>
                                        <th>Joined</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php if (empty($allTraders)): ?>
                                    <tr><td colspan="4" style="text-align:center;color:#999;">No traders yet.</td></tr>
                                <?php else: ?>
                                <?php foreach ($allTraders as $t): ?>
                                    <tr>
                                        <td>
                                            <div style="font-weight:500;"><?= h($t['NAME']) ?></div>
                                            <div style="font-size:12px;color:var(--text-muted);"><?= h($t['EMAIL']) ?></div>
                                        </td>
                                        <td><?= h($t['SHOP_NAME']) ?></td>
                                        <td><span class="status <?= strtolower(h($t['STATUS'] ?? 'pending')) ?>"><?= h($t['STATUS'] ?? 'Pending') ?></span></td>
                                        <td><?= h($t['JOINED']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Pending Approvals -->
                <div class="right-column">
                    <div class="card">
                        <div class="card-header">
                            <h2>Pending Approvals</h2>
                            <span style="font-size:14px;color:var(--text-muted);"><?= count($pendingTraders) ?> pending</span>
                        </div>
                        <?php if (empty($pendingTraders)): ?>
                            <p style="color:var(--text-muted);font-size:14px;">No pending trader requests.</p>
                        <?php else: ?>
                        <?php foreach ($pendingTraders as $pt): ?>
                            <div class="list-item" style="display:flex;align-items:center;justify-content:space-between;gap:12px;">
                                <div class="list-info" style="flex:1;">
                                    <h4><?= h($pt['NAME']) ?></h4>
                                    <p style="margin:4px 0 0;color:var(--text-muted);font-size:13px;">
                                        <?= h($pt['SHOP_NAME']) ?> &middot; <?= h($pt['EMAIL']) ?>
                                    </p>
                                    <p style="margin:4px 0 0;color:var(--text-muted);font-size:12px;">Submitted: <?= h($pt['SUBMITTED']) ?></p>
                                </div>
                                <div style="display:flex;gap:8px;flex-shrink:0;">
                                    <form method="POST" style="margin:0;">
                                        <input type="hidden" name="trader_id" value="<?= (int)$pt['USER_ID'] ?>">
                                        <button type="submit" name="trader_action" value="approve"
                                                class="btn-primary" style="padding:6px 12px;font-size:13px;">Approve</button>
                                    </form>
                                    <form method="POST" style="margin:0;"
                                          onsubmit="return confirm('Reject this trader application?');">
                                        <input type="hidden" name="trader_id" value="<?= (int)$pt['USER_ID'] ?>">
                                        <button type="submit" name="trader_action" value="reject"
                                                class="btn-delete" style="padding:6px 12px;font-size:13px;">Reject</button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <div class="card" style="background:var(--accent-green-soft);border-color:var(--accent-green-soft);">
                        <div style="display:flex;gap:15px;">
                            <i class="fas fa-lightbulb" style="color:var(--accent-green);font-size:24px;margin-top:5px;"></i>
                            <div>
                                <h3 style="color:var(--accent-green);font-size:16px;margin-bottom:10px;">Pro Tip</h3>
                                <p style="color:#2e7d32;font-size:13px;line-height:1.5;">
                                    Traders registered here are given ACTIVE status immediately.
                                    Traders who self-register via the website appear as PENDING and need your approval.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
</body>
</html>
