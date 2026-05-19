<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/backend/connect.php';

$order_id   = isset($_GET['order_id']) ? trim($_GET['order_id']) : null;
$homeUrl    = '/cleckbasket/index.php';
$invoiceUrl = '/cleckbasket/includes/cart/invoice.php';

// Finalise the pending order placed before PayPal redirect
if (!empty($_SESSION['pending_order_id']) && !empty($_SESSION['user_id'])) {
    $bind_order_id = $_SESSION['pending_order_id'];   // local vars for OCI8 reference binding
    $bind_user_id  = $_SESSION['user_id'];

    $conn = getDBConnection();
    if ($conn) {
        // Fetch order amount
        $s = oci_parse($conn, "SELECT order_amount FROM orders WHERE order_id = :order_id AND user_id = :user_id");
        oci_bind_by_name($s, ':order_id', $bind_order_id);
        oci_bind_by_name($s, ':user_id',  $bind_user_id);
        oci_execute($s);
        $orderRow = oci_fetch_assoc($s);
        oci_free_statement($s);

        if ($orderRow) {
            $bind_amount = (float)$orderRow['ORDER_AMOUNT'];

            // Insert payment record
            $ins = oci_parse($conn,
                "INSERT INTO payment (payment_amount, payment_method, user_id, order_id)
                 VALUES (:payment_amount, 'PayPal', :user_id, :order_id)");
            oci_bind_by_name($ins, ':payment_amount', $bind_amount);
            oci_bind_by_name($ins, ':user_id',        $bind_user_id);
            oci_bind_by_name($ins, ':order_id',       $bind_order_id);
            oci_execute($ins, OCI_NO_AUTO_COMMIT);
            oci_free_statement($ins);

            // Update order status to Confirmed
            $upd = oci_parse($conn, "UPDATE orders SET order_status = 'Confirmed' WHERE order_id = :order_id");
            oci_bind_by_name($upd, ':order_id', $bind_order_id);
            oci_execute($upd, OCI_NO_AUTO_COMMIT);
            oci_free_statement($upd);

            oci_commit($conn);
        }
        oci_close($conn);
    }
    unset($_SESSION['pending_order_id']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful — CleckBasket</title>
    <style>
        body { margin: 0; font-family: 'Segoe UI', Arial, sans-serif; background: #FFF8F5; color: #29170C; }
        .page-wrapper { display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 24px; }
        .card {
            background: #fff;
            border-radius: 24px;
            max-width: 480px;
            width: 100%;
            box-shadow: 0 16px 64px -12px rgba(41,23,12,0.10);
            padding: 48px 36px;
            text-align: center;
        }
        .check-circle {
            width: 72px; height: 72px;
            background: #D5E8CD;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 24px;
        }
        h1 { margin: 0 0 12px; font-size: 26px; font-weight: 700; color: #29170C; }
        .subtitle { margin: 0 0 32px; font-size: 15px; color: #51443E; line-height: 1.6; }
        .payer-id { margin-bottom: 28px; font-size: 12px; color: #865135; }
        .btn-row { display: flex; flex-direction: column; gap: 12px; }
        .btn-invoice {
            display: block; width: 100%;
            padding: 14px;
            background: #572B12;
            color: #fff;
            border: none; border-radius: 14px;
            font-size: 14px; font-weight: 700;
            letter-spacing: 0.8px; text-transform: uppercase;
            text-decoration: none; cursor: pointer;
            transition: opacity 0.2s;
        }
        .btn-invoice:hover { opacity: 0.85; }
        .btn-home {
            display: block; width: 100%;
            padding: 14px;
            background: #FFE3D3;
            color: #5A331C;
            border: none; border-radius: 14px;
            font-size: 14px; font-weight: 700;
            letter-spacing: 0.8px; text-transform: uppercase;
            text-decoration: none; cursor: pointer;
            transition: opacity 0.2s;
        }
        .btn-home:hover { opacity: 0.8; }
    </style>
</head>
<body>
    <div class="page-wrapper">
        <div class="card">
            <div class="check-circle">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none">
                    <path d="M5 13l4 4L19 7" stroke="#101F0E" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <h1>Payment Successful!</h1>
            <p class="subtitle">Your order has been confirmed. You can view your invoice or return to the homepage.</p>
            <?php if ($order_id): ?>
                <div class="payer-id">Transaction ref: <?= htmlspecialchars($order_id) ?></div>
            <?php endif; ?>
            <div class="btn-row">
                <a class="btn-invoice" href="<?= htmlspecialchars($invoiceUrl) ?>">View Invoice</a>
                <a class="btn-home"    href="<?= htmlspecialchars($homeUrl) ?>">Return to Homepage</a>
            </div>
        </div>
    </div>
</body>
</html>
