<?php
$order_id = isset($_GET['order_id']) ? trim($_GET['order_id']) : null;
$homeUrl = '/cleckbasket/index.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful</title>
    <style>
        body { margin: 0; font-family: Arial, sans-serif; background: #f8f9fb; color: #212529; }
        .page-wrapper { display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 24px; }
        .card { background: #fff; border: 1px solid #e5e7eb; border-radius: 16px; max-width: 520px; width: 100%; box-shadow: 0 20px 50px rgba(15, 23, 42, 0.08); padding: 36px; text-align: center; }
        .card h1 { margin: 0 0 18px; font-size: 30px; color: #111827; }
        .card p { margin: 0 0 24px; font-size: 16px; color: #4b5563; line-height: 1.6; }
        .btn-primary { display: inline-flex; align-items: center; justify-content: center; background: #2563eb; color: #fff; border: none; border-radius: 9999px; padding: 14px 28px; font-size: 16px; text-decoration: none; cursor: pointer; }
        .btn-primary:hover { background: #1d4ed8; }
        .order-id { margin-bottom: 12px; font-weight: 600; color: #374151; }
    </style>
</head>
<body>
    <div class="page-wrapper">
        <div class="card">
            <div class="order-id">Payment completed<?php echo $order_id ? ' for order #' . htmlspecialchars($order_id) : ''; ?></div>
            <h1>Thank you for your order!</h1>
            <p>Your payment was successful. Click the button below to return to the CleckBasket homepage and continue shopping.</p>
            <a class="btn-primary" href="<?php echo htmlspecialchars($homeUrl); ?>">Return to Homepage</a>
        </div>
    </div>
</body>
</html>
