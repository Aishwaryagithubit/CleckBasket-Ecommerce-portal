<?php
/**
 * CleckBasket — Receive Card UID from Arduino Serial Bridge
 * POST /cleckbasket/backend/receive_card.php
 *
 * Called by serial_bridge.py (not the browser).
 * Validates a shared secret key, normalises the UID,
 * then writes it to temp/card_scan.json for poll_card.php to read.
 *
 * No session needed. No DB changes. No relationship changes.
 */

header('Content-Type: application/json; charset=utf-8');

/* ── Secret key — must match serial_bridge.py ─────────────────── */
define('BRIDGE_SECRET', 'CB-ARDUINO-2024');

/* ── Only POST ────────────────────────────────────────────────── */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'method_not_allowed']);
    exit;
}

/* ── Parse body ───────────────────────────────────────────────── */
$raw    = file_get_contents('php://input');
$input  = json_decode($raw, true);
$secret = trim($input['secret']   ?? '');
$uid    = trim($input['card_uid'] ?? '');

/* ── Validate secret ──────────────────────────────────────────── */
if ($secret !== BRIDGE_SECRET) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'invalid_secret']);
    exit;
}

/* ── Validate UID ─────────────────────────────────────────────── */
if ($uid === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'empty_uid']);
    exit;
}

/* ── Normalise: strip non-hex chars, uppercase ────────────────── */
$normUid = strtoupper(preg_replace('/[^0-9A-Fa-f]/', '', $uid));

if (strlen($normUid) < 4) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'uid_too_short']);
    exit;
}

/* ── Write to temp file ───────────────────────────────────────── */
$tempDir  = __DIR__ . '/../temp';
$tempFile = $tempDir . '/card_scan.json';

if (!is_dir($tempDir)) {
    mkdir($tempDir, 0755, true);
}

$payload = json_encode([
    'uid'        => $normUid,   // e.g. "A338A713"
    'raw'        => $uid,       // e.g. "A3 38 A7 13" (original from Arduino)
    'scanned_at' => time()      // Unix timestamp for freshness check
]);

$written = file_put_contents($tempFile, $payload, LOCK_EX);

if ($written === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'write_failed',
                      'message' => 'Could not write to temp folder.']);
    exit;
}

echo json_encode([
    'success' => true,
    'uid'     => $normUid,
    'message' => 'Card UID stored successfully'
]);
