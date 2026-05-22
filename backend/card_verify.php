<?php
/**
 * Card Verification Endpoint
 * POST /cleckbasket/backend/card_verify.php
 *
 * Accepts JSON: { "card_uid": "<any format of A3 38 A7 13>" }
 *
 * Normalisation strips every non-hex character then uppercases,
 * so ALL of these match correctly:
 *   "A338A713"        (no separator  — many USB readers)
 *   "A3 38 A7 13"     (space-separated)
 *   "A3:38:A7:13"     (colon-separated)
 *   "A3-38-A7-13"     (dash-separated)
 *   "a3 38 a7 13"     (lower-case)
 *
 * Rules:
 *  - User must be logged in (valid PHP session)
 *  - No DB schema changes / relationship changes / connect.php changes
 */

if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json; charset=utf-8');

/* ── Only allow POST ──────────────────────────────────────────── */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'method_not_allowed']);
    exit;
}

/* ── Auth guard ───────────────────────────────────────────────── */
if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'error'   => 'not_logged_in',
        'message' => 'Please log in to verify your card.'
    ]);
    exit;
}

require_once __DIR__ . '/connect.php';

/* ── Normalise helper: keep only hex digits, uppercase ────────── */
function normaliseUid(string $raw): string {
    return strtoupper(preg_replace('/[^0-9A-Fa-f]/', '', $raw));
}

/* ── Registered card — stored as plain hex after normalisation ── */
$VALID_UID = normaliseUid('A3 38 A7 13');   // → 'A338A713'

/* ── Parse input ─────────────────────────────────────────────── */
$raw     = file_get_contents('php://input');
$input   = json_decode($raw, true);
$cardRaw = trim($input['card_uid'] ?? '');
$cardNorm = normaliseUid($cardRaw);          // strip separators, uppercase

/* ── Card check ──────────────────────────────────────────────── */
if ($cardNorm === '') {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error'   => 'empty_uid',
        'message' => 'No card UID received. Please scan or enter the card.'
    ]);
    exit;
}

if ($cardNorm !== $VALID_UID) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'error'   => 'invalid_card',
        'message' => 'Card not recognised. Please ensure you are using the correct card.'
    ]);
    exit;
}

/* ── DB queries (read-only — no schema changes) ──────────────── */
$userId = (int)$_SESSION['user_id'];
$conn   = getDBConnection();

if (!$conn) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'db_error', 'message' => 'Database unavailable.']);
    exit;
}

/* 1. User details ─────────────────────────────────────────────── */
$sqlUser = "SELECT user_id,
                   firstname || ' ' || lastname AS full_name,
                   email,
                   contact_no,
                   status,
                   TO_CHAR(created_date, 'DD Mon YYYY') AS member_since
            FROM   users
            WHERE  user_id = :uid";
$stmtUser = oci_parse($conn, $sqlUser);
oci_bind_by_name($stmtUser, ':uid', $userId);
oci_execute($stmtUser);
$userData = oci_fetch_assoc($stmtUser);
oci_free_statement($stmtUser);

if (!$userData) {
    oci_close($conn);
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'user_not_found',
                      'message' => 'User account not found.']);
    exit;
}

/* 2. Pending / Confirmed / Processing orders with slot info ───── */
$sqlOrders = "SELECT o.order_id,
                     o.order_status,
                     o.order_amount,
                     TO_CHAR(cs.slot_date, 'DD Mon YYYY') AS slot_date,
                     cs.slot_time,
                     RTRIM(cs.slot_day)                   AS slot_day
              FROM   orders o
              LEFT JOIN collection_slot cs
                     ON o.collection_slot_id = cs.collection_slot_id
              WHERE  o.user_id = :uid
                AND  UPPER(o.order_status) IN ('PENDING','CONFIRMED','PROCESSING')
              ORDER  BY o.order_id DESC";
$stmtOrders = oci_parse($conn, $sqlOrders);
oci_bind_by_name($stmtOrders, ':uid', $userId);
oci_execute($stmtOrders);

$orders = [];
while ($oRow = oci_fetch_assoc($stmtOrders)) {
    $oid = (int)$oRow['ORDER_ID'];

    /* 3. Line items for each order ─────────────────────────────── */
    $sqlItems = "SELECT p.product_name,
                        op.quantity,
                        op.price_at_purchase
                 FROM   order_product op
                 LEFT JOIN product p ON op.product_id = p.product_id
                 WHERE  op.order_id = :oid";
    $stmtItems = oci_parse($conn, $sqlItems);
    oci_bind_by_name($stmtItems, ':oid', $oid);
    oci_execute($stmtItems);

    $items = [];
    while ($iRow = oci_fetch_assoc($stmtItems)) {
        $items[] = [
            'name'     => $iRow['PRODUCT_NAME']      ?? 'Item',
            'quantity' => (int)($iRow['QUANTITY']     ?? 1),
            'price'    => (float)($iRow['PRICE_AT_PURCHASE'] ?? 0)
        ];
    }
    oci_free_statement($stmtItems);

    $orders[] = [
        'order_id'  => $oid,
        'status'    => $oRow['ORDER_STATUS'] ?? '',
        'amount'    => (float)($oRow['ORDER_AMOUNT'] ?? 0),
        'slot_date' => $oRow['SLOT_DATE']    ?? '',
        'slot_time' => $oRow['SLOT_TIME']    ?? '',
        'slot_day'  => $oRow['SLOT_DAY']     ?? '',
        'items'     => $items
    ];
}
oci_free_statement($stmtOrders);
oci_close($conn);

/* ── Success response ─────────────────────────────────────────── */
echo json_encode([
    'success'  => true,
    'card_uid' => 'A3 38 A7 13',          // always return human-readable form
    'user'     => [
        'name'         => $userData['FULL_NAME']    ?? '',
        'email'        => $userData['EMAIL']        ?? '',
        'contact'      => $userData['CONTACT_NO']   ?? '',
        'status'       => $userData['STATUS']       ?? '',
        'member_since' => $userData['MEMBER_SINCE'] ?? ''
    ],
    'orders'   => $orders
], JSON_UNESCAPED_UNICODE);
