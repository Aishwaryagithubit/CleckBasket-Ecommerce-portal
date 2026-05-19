<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../backend/connect.php';

$dbUser = ['firstname' => '', 'lastname' => '', 'email' => '', 'contact_no' => ''];
$recentOrders = [];
$orderCount   = 0;

if (!empty($_SESSION['user_id'])) {
    $bind_uid = $_SESSION['user_id'];   // local var — OCI8 needs a real reference
    $conn = getDBConnection();
    if ($conn) {
        // Load user profile
        $s = oci_parse($conn, "SELECT firstname, lastname, email, contact_no FROM users WHERE user_id = :user_id");
        oci_bind_by_name($s, ':user_id', $bind_uid);
        oci_execute($s);
        $row = oci_fetch_assoc($s);
        if ($row) {
            $dbUser = [
                'firstname'  => $row['FIRSTNAME'] ?? '',
                'lastname'   => $row['LASTNAME']  ?? '',
                'email'      => $row['EMAIL']      ?? '',
                'contact_no' => $row['CONTACT_NO'] ?? '',
            ];
        }
        oci_free_statement($s);

        // Load recent orders (up to 5 most recent orders with their products)
        $uid_val = $bind_uid;
        $sql_o = "SELECT o.order_id, o.order_status, o.order_amount,
                         TO_CHAR(o.order_date, 'DD Mon YYYY') AS order_date_fmt,
                         cs.slot_day, TO_CHAR(cs.slot_date, 'DD Mon YYYY') AS slot_date_fmt, cs.slot_time,
                         p.product_name, p.product_image, op.quantity, op.price_at_purchase
                  FROM (
                      SELECT order_id, user_id, order_status, order_amount, order_date, collection_slot_id
                      FROM orders WHERE user_id = :user_id
                      ORDER BY order_date DESC FETCH FIRST 5 ROWS ONLY
                  ) o
                  JOIN order_product op ON o.order_id = op.order_id
                  JOIN product p ON op.product_id = p.product_id
                  LEFT JOIN collection_slot cs ON o.collection_slot_id = cs.collection_slot_id
                  ORDER BY o.order_date DESC, o.order_id DESC";
        $so = oci_parse($conn, $sql_o);
        oci_bind_by_name($so, ':user_id', $uid_val);
        oci_execute($so);
        while ($r = oci_fetch_assoc($so)) {
            $oid = (int)$r['ORDER_ID'];
            if (!isset($recentOrders[$oid])) {
                $recentOrders[$oid] = [
                    'order_id'  => $oid,
                    'status'    => $r['ORDER_STATUS'] ?? 'Pending',
                    'amount'    => $r['ORDER_AMOUNT'] ?? 0,
                    'date'      => $r['ORDER_DATE_FMT'] ?? '',
                    'slot_day'  => $r['SLOT_DAY'] ?? '',
                    'slot_date' => $r['SLOT_DATE_FMT'] ?? '',
                    'slot_time' => $r['SLOT_TIME'] ?? '',
                    'products'  => [],
                ];
            }
            $recentOrders[$oid]['products'][] = [
                'name'  => $r['PRODUCT_NAME']       ?? '',
                'image' => $r['PRODUCT_IMAGE']      ?? '',
                'qty'   => (int)($r['QUANTITY']     ?? 1),
                'price' => (float)($r['PRICE_AT_PURCHASE'] ?? 0),
            ];
        }
        oci_free_statement($so);

        // Total order count for badge
        $sc = oci_parse($conn, "SELECT COUNT(*) AS cnt FROM orders WHERE user_id = :user_id");
        oci_bind_by_name($sc, ':user_id', $uid_val);
        oci_execute($sc);
        $rc = oci_fetch_assoc($sc);
        $orderCount = (int)($rc['CNT'] ?? 0);
        oci_free_statement($sc);

        oci_close($conn);
    }
}
$fullName = trim($dbUser['firstname'] . ' ' . $dbUser['lastname']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>My Account — FreshCart</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="/cleckbasket/assets/css/profile.css" />
</head>

<body>
    <?php include __DIR__ . '/../header.php'; ?>

    <main class="profile-page-wrapper">
        <div class="profile-container" style="max-width: 1440px; margin: 0 auto;">
            
            <header class="profile-main-header">
                <h1 class="welcome-text" id="profile-welcome">Welcome, <?= htmlspecialchars($dbUser['firstname'] ?: 'User') ?></h1>
                <p class="cur-date" id="profile-date"></p>
            </header>

            <div class="profile-grid">
                
                <!-- LEFT COLUMN: Profile Form -->
                <div class="profile-left-col">
                    <div class="profile-card profile-form-card">
                        
                        <div class="profile-cover-brown"></div>
                        
                        <div class="profile-user-bar">
                            <div class="user-avatar">
                                <svg width="90" height="90" viewBox="0 0 90 90" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="45" cy="45" r="45" fill="#666666"/>
                                    <path d="M45 42C51.6274 42 57 36.6274 57 30C57 23.3726 51.6274 18 45 18C38.3726 18 33 23.3726 33 30C33 36.6274 38.3726 42 45 42Z" fill="#CCCCCC"/>
                                    <path d="M21.5 66.5C21.5 56.835 32.0213 49 45 49C57.9787 49 68.5 56.835 68.5 66.5C68.5 69.5376 66.0376 72 63 72H27C23.9624 72 21.5 69.5376 21.5 66.5Z" fill="#CCCCCC"/>
                                </svg>
                            </div>
                            
                            <div class="user-info-text">
                                <h2 id="profile-name"><?= htmlspecialchars($fullName) ?></h2>
                                <p id="profile-email"><?= htmlspecialchars($dbUser['email']) ?></p>
                            </div>

                            <button class="btn-edit-profile-blue" id="profile-edit-btn">Edit</button>
                        </div>

                        <div class="profile-form-wrap">
                            
                            <div class="form-group">
                                <label>Full Name</label>
                                <input type="text" class="input-grey" id="profile-full-name" value="<?= htmlspecialchars($fullName) ?>" readonly />
                            </div>

                            <div class="form-group">
                                <label>Contact Number</label>
                                <div class="input-with-icon">
                                    <input type="text" class="input-grey" id="profile-phone" value="<?= htmlspecialchars($dbUser['contact_no']) ?>" readonly />
                                    <svg class="chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Email Address</label>
                                <div class="input-with-icon">
                                    <input type="text" class="input-grey" id="profile-email-input" value="<?= htmlspecialchars($dbUser['email']) ?>" readonly />
                                    <svg class="chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Default Address</label>
                                <div class="input-with-icon">
                                    <input type="text" class="input-grey" id="profile-address" value="" readonly />
                                    <svg class="chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                                </div>
                            </div>

                            <h3 class="subsection-title">My email Address</h3>
                            
                            <div class="email-address-row">
                                <div class="email-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="2">
                                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                        <polyline points="22,6 12,13 2,6"/>
                                    </svg>
                                </div>
                                <div class="email-details">
                                    <p class="email-val" id="profile-email-secondary"><?= htmlspecialchars($dbUser['email']) ?></p>
                                    <p class="email-time">1 month ago</p>
                                </div>
                            </div>

                            <div id="profile-alert" style="display:none;padding:12px 16px;border-radius:8px;font-size:14px;margin-bottom:12px;"></div>
                            <button class="btn-add-email" id="profile-save-btn" type="button">Save Changes</button>
                            
                        </div>
                    </div>
                </div>

                <!-- RIGHT COLUMN: Orders and Activities -->
                <div class="profile-right-col">
                    
                    <!-- Recent Orders -->
                    <div class="profile-card recent-orders-card">
                        <h2 class="card-title">Recent Orders</h2>

                        <div class="order-list" id="profile-orders">
                        <?php if (empty($recentOrders)): ?>
                            <p class="order-empty">No recent orders yet.</p>
                        <?php else: ?>
                            <?php foreach ($recentOrders as $ord): ?>
                            <div class="order-group" style="border:1px solid #f0f0f0;border-radius:10px;margin-bottom:16px;overflow:hidden;">
                                <!-- Order header -->
                                <div style="background:#f8f9fa;padding:10px 14px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:6px;">
                                    <div style="font-size:13px;color:#555;">
                                        <strong style="color:#222;">Order #<?= (int)$ord['order_id'] ?></strong>
                                        &nbsp;·&nbsp; <?= htmlspecialchars($ord['date']) ?>
                                    </div>
                                    <?php
                                    $statusColors = [
                                        'Pending'   => ['#fff8e1','#f57f17'],
                                        'Confirmed' => ['#e3f2fd','#1565c0'],
                                        'Ready'     => ['#e8f5e9','#2e7d32'],
                                        'Collected' => ['#ede7f6','#4527a0'],
                                        'Cancelled' => ['#ffebee','#c62828'],
                                    ];
                                    $st   = $ord['status'] ?? 'Pending';
                                    $scol = $statusColors[$st] ?? ['#f5f5f5','#555'];
                                    ?>
                                    <span style="background:<?= $scol[0] ?>;color:<?= $scol[1] ?>;font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;">
                                        <?= htmlspecialchars($st) ?>
                                    </span>
                                </div>
                                <!-- Collection slot -->
                                <?php if ($ord['slot_day'] || $ord['slot_date']): ?>
                                <div style="padding:8px 14px;background:#fffde7;font-size:12px;color:#795548;border-bottom:1px solid #f0f0f0;">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:4px;"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                    Collection: <?= htmlspecialchars($ord['slot_day']) ?>
                                    <?php if ($ord['slot_date']): ?>, <?= htmlspecialchars($ord['slot_date']) ?><?php endif; ?>
                                    <?php if ($ord['slot_time']): ?> &nbsp;·&nbsp; <?= htmlspecialchars($ord['slot_time']) ?><?php endif; ?>
                                </div>
                                <?php endif; ?>
                                <!-- Products -->
                                <div style="padding:10px 14px;">
                                    <?php foreach ($ord['products'] as $prod): ?>
                                    <div class="order-item" style="border-bottom:1px solid #f5f5f5;padding-bottom:10px;margin-bottom:10px;">
                                        <?php
                                        $imgSrc = $prod['image']
                                            ? '/cleckbasket/assets/images/' . htmlspecialchars(basename($prod['image']))
                                            : '/cleckbasket/assets/images/logo.png';
                                        ?>
                                        <img src="<?= $imgSrc ?>" alt="<?= htmlspecialchars($prod['name']) ?>" class="order-img" onerror="this.src='/cleckbasket/assets/images/logo.png'" />
                                        <div class="order-info">
                                            <h4 class="order-name"><?= htmlspecialchars($prod['name']) ?></h4>
                                            <p class="order-price">£<?= number_format($prod['price'], 2) ?> &times; <?= (int)$prod['qty'] ?></p>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                    <div style="text-align:right;font-size:13px;font-weight:600;color:#333;padding-top:2px;">
                                        Total: £<?= number_format($ord['amount'], 2) ?>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </div>
                    </div>

                    <!-- Activities -->
                    <div class="profile-card activities-card">
                        <h2 class="card-title">Activities</h2>
                        
                        <ul class="activities-list">
                            <li>
                                <div class="activity-left">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#555" stroke-width="2">
                                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                    <span>Recent Orders</span>
                                </div>
                                <span class="badge-green" id="badge-orders"><?= $orderCount ?></span>
                            </li>
                            <li>
                                <div class="activity-left">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#555" stroke-width="2">
                                        <circle cx="9" cy="21" r="1"/>
                                        <circle cx="20" cy="21" r="1"/>
                                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                                    </svg>
                                    <span>My Cart</span>
                                </div>
                                <span class="badge-green" id="badge-cart">6</span>
                            </li>
                            <li>
                                <div class="activity-left">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="#E53935" stroke="#E53935" stroke-width="2">
                                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                                    </svg>
                                    <span>My Wishlist</span>
                                </div>
                                <span class="badge-green" id="badge-wishlist">7</span>
                            </li>
                            <li style="cursor: pointer;" id="logout-btn">
                                <div class="activity-left">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#E53935" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                        <polyline points="16 17 21 12 16 7"></polyline>
                                        <line x1="21" y1="12" x2="9" y2="12"></line>
                                    </svg>
                                    <span style="color: #E53935; font-weight: 500;">Logout</span>
                                </div>
                            </li>
                        </ul>
                    </div>

                </div>
            </div>
            
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (!isLoggedIn()) {
                window.location.href = '/cleckbasket/includes/pages/login.php';
                return;
            }

            hydrateDate();
            hydrateBadges();
            setEditingState(false);

            const editBtn = document.getElementById('profile-edit-btn');
            const saveBtn = document.getElementById('profile-save-btn');
            const alertEl = document.getElementById('profile-alert');

            if (editBtn) {
                editBtn.addEventListener('click', () => {
                    const isEditing = editBtn.dataset.editing === 'true';
                    setEditingState(!isEditing);
                    if (alertEl) alertEl.style.display = 'none';
                });
            }

            if (saveBtn) {
                saveBtn.addEventListener('click', async () => {
                    const payload = {
                        name:  getInputValue('profile-full-name'),
                        email: getInputValue('profile-email-input'),
                        phone: getInputValue('profile-phone'),
                    };

                    saveBtn.disabled = true;
                    saveBtn.textContent = 'Saving…';

                    try {
                        const res  = await fetch('/cleckbasket/backend/update_profile.php', {
                            method:  'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body:    JSON.stringify(payload),
                        });
                        const json = await res.json();

                        if (alertEl) {
                            alertEl.textContent   = json.message;
                            alertEl.style.display = 'block';
                            alertEl.style.background = json.success ? '#e8f5e9' : '#ffebee';
                            alertEl.style.color      = json.success ? '#2e7d32' : '#c62828';
                        }

                        if (json.success) {
                            // Update displayed name/email without page reload
                            setText('profile-name', payload.name);
                            setText('profile-welcome', 'Welcome, ' + payload.name);
                            setText('profile-email', payload.email);
                            setText('profile-email-secondary', payload.email);
                            setEditingState(false);
                        }
                    } catch (err) {
                        if (alertEl) {
                            alertEl.textContent      = 'Network error. Please try again.';
                            alertEl.style.display    = 'block';
                            alertEl.style.background = '#ffebee';
                            alertEl.style.color      = '#c62828';
                        }
                    } finally {
                        saveBtn.disabled    = false;
                        saveBtn.textContent = 'Save Changes';
                    }
                });
            }

            const logoutBtn = document.getElementById('logout-btn');
            if (logoutBtn) {
                logoutBtn.addEventListener('click', () => {
                    localStorage.removeItem('is_login');
                    window.location.href = '/cleckbasket/backend/logout.php';
                });
            }
        });

        function isLoggedIn() {
            return localStorage.getItem('is_login') === 'true';
        }

        function hydrateDate() {
            const dateEl = document.getElementById('profile-date');
            if (!dateEl) return;
            const now = new Date();
            dateEl.textContent = now.toLocaleDateString('en-GB', {
                weekday: 'short', day: '2-digit', month: 'long', year: 'numeric'
            });
        }

        function hydrateBadges() {
            const cart     = JSON.parse(localStorage.getItem('cart')     || '[]');
            const wishlist = JSON.parse(localStorage.getItem('wishlist') || '[]');
            setText('badge-cart',     Array.isArray(cart)     ? cart.reduce((s, i) => s + (i.quantity || 0), 0) : 0);
            setText('badge-wishlist', Array.isArray(wishlist) ? wishlist.length : 0);
        }

        function setEditingState(isEditing) {
            const editBtn = document.getElementById('profile-edit-btn');
            const saveBtn = document.getElementById('profile-save-btn');
            if (editBtn) {
                editBtn.dataset.editing = isEditing ? 'true' : 'false';
                editBtn.textContent     = isEditing ? 'Cancel' : 'Edit';
            }
            if (saveBtn) {
                saveBtn.disabled      = !isEditing;
                saveBtn.style.opacity = isEditing ? '1' : '0.6';
            }
            ['profile-full-name', 'profile-email-input', 'profile-phone', 'profile-address'].forEach(id => {
                const input = document.getElementById(id);
                if (input) {
                    input.readOnly = !isEditing;
                    input.classList.toggle('is-editing', isEditing);
                }
            });
        }

        function setText(id, value) {
            const el = document.getElementById(id);
            if (el) el.textContent = value;
        }

        function getInputValue(id) {
            const input = document.getElementById(id);
            return input ? input.value.trim() : '';
        }
    </script>

    <?php include __DIR__ . '/../footer.php'; ?>
</body>
</html>