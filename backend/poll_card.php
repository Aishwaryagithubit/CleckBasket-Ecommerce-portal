<?php
/**
 * CleckBasket — Browser Poll Endpoint
 * GET /cleckbasket/backend/poll_card.php
 *
 * The browser calls this every 500 ms while waiting on checkout_success.php.
 * Reads temp/card_scan.json written by receive_card.php.
 * Returns success only if:
 *   1. User is logged in (valid PHP session)
 *   2. The UID in the file matches A3 38 A7 13 (normalised → A338A713)
 *   3. The scan happened within the last 30 seconds
 *
 * On success the temp file is cleared to prevent the same scan triggering twice.
 *
 * No DB changes. No relationship changes. No connect.php changes.
 */

if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json; charset=utf-8');

/* ── Registered card (normalised hex) ─────────────────────────── */
$VALID_UID = strtoupper(preg_replace('/[^0-9A-Fa-f]/', '', 'A3 38 A7 13')); // A338A713
$MAX_AGE   = 30; // seconds — scan must be fresher than this

/* ── Auth guard ───────────────────────────────────────────────── */
if (empty($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'status' => 'not_logged_in']);
    exit;
}

$tempFile = __DIR__ . '/../temp/card_scan.json';

/* ── No scan file yet ─────────────────────────────────────────── */
if (!file_exists($tempFile)) {
    echo json_encode(['success' => false, 'status' => 'waiting']);
    exit;
}

/* ── Read & decode the temp file ──────────────────────────────── */
$content = file_get_contents($tempFile);
$data    = $content ? json_decode($content, true) : null;

if (!$data || empty($data['uid']) || empty($data['scanned_at'])) {
    echo json_encode(['success' => false, 'status' => 'waiting']);
    exit;
}

$uid       = $data['uid']        ?? '';
$scannedAt = (int)($data['scanned_at'] ?? 0);
$age       = time() - $scannedAt;

/* ── Too old ──────────────────────────────────────────────────── */
if ($age > $MAX_AGE) {
    echo json_encode(['success' => false, 'status' => 'waiting', 'note' => 'scan_expired']);
    exit;
}

/* ── Wrong card ───────────────────────────────────────────────── */
if ($uid !== $VALID_UID) {
    echo json_encode(['success' => false, 'status' => 'wrong_card']);
    exit;
}

/* ── SUCCESS — clear temp so the same tap doesn't fire twice ──── */
file_put_contents($tempFile, json_encode([
    'uid'        => '',
    'raw'        => '',
    'scanned_at' => 0
]), LOCK_EX);

echo json_encode([
    'success'  => true,
    'status'   => 'verified',
    'uid'      => 'A3 38 A7 13',
    'redirect' => '/cleckbasket/includes/cart/invoice.php'
]);
