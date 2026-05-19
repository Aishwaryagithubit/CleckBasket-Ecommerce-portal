<?php
ob_start();
error_reporting(0);
if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json');

if (empty($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

require_once __DIR__ . '/connect.php';

$body   = json_decode(file_get_contents('php://input'), true);
$slotId = (int)($body['slot_id'] ?? 0);
$items  = $body['cart_items'] ?? [];

$bind_user_id = $_SESSION['user_id'];   // local var — OCI8 needs a real reference

if (!$slotId || empty($items)) {
    echo json_encode(['success' => false, 'error' => 'Missing slot or cart items']);
    exit;
}

$conn = getDBConnection();
if (!$conn) {
    echo json_encode(['success' => false, 'error' => 'DB connection failed']);
    exit;
}

// Calculate total (including 5% tax)
$total = 0;
foreach ($items as $item) {
    $total += (float)($item['price'] ?? 0) * (int)($item['quantity'] ?? 1);
}
$total = round($total * 1.05, 2);

// Insert order, get back new order_id
$orderId  = 0;
$insOrder = oci_parse($conn,
    "INSERT INTO orders (user_id, order_status, order_amount, collection_slot_id)
     VALUES (:user_id, 'Pending', :order_amount, :slot_id)
     RETURNING order_id INTO :order_id");
oci_bind_by_name($insOrder, ':user_id',      $bind_user_id);
oci_bind_by_name($insOrder, ':order_amount', $total);
oci_bind_by_name($insOrder, ':slot_id',      $slotId);
oci_bind_by_name($insOrder, ':order_id',     $orderId, 20, SQLT_INT);

if (!oci_execute($insOrder, OCI_NO_AUTO_COMMIT)) {
    $e = oci_error($insOrder);
    oci_close($conn);
    echo json_encode(['success' => false, 'error' => $e['message'] ?? 'Insert failed']);
    exit;
}
oci_free_statement($insOrder);

// Insert one row per cart item
foreach ($items as $item) {
    $productId = (int)($item['id'] ?? 0);
    $qty       = (int)($item['quantity'] ?? 1);
    $price     = (float)($item['price'] ?? 0);

    if (!$productId) continue;

    $insItem = oci_parse($conn,
        "INSERT INTO order_product (order_id, product_id, quantity, price_at_purchase)
         VALUES (:order_id, :product_id, :quantity, :unit_price)");
    oci_bind_by_name($insItem, ':order_id',   $orderId);
    oci_bind_by_name($insItem, ':product_id', $productId);
    oci_bind_by_name($insItem, ':quantity',   $qty);
    oci_bind_by_name($insItem, ':unit_price', $price);
    oci_execute($insItem, OCI_NO_AUTO_COMMIT);
    oci_free_statement($insItem);
}

oci_commit($conn);
oci_close($conn);

$_SESSION['pending_order_id'] = $orderId;

echo json_encode(['success' => true, 'order_id' => $orderId]);
