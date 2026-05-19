<?php
ob_start();
error_reporting(0);
session_start();
require_once __DIR__ . '/connect.php';
require_once __DIR__ . '/send_otp_email.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$email = strtolower(trim($input['email'] ?? ''));

if (!$email) {
    echo json_encode(['success' => false, 'message' => 'Email is required']);
    exit;
}

$conn = getDBConnection();
if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

$bind_email = $email;
$stmt = oci_parse($conn, "SELECT user_id, firstname, lastname FROM users WHERE LOWER(email) = :email");
oci_bind_by_name($stmt, ':email', $bind_email);
oci_execute($stmt);
$user = oci_fetch_assoc($stmt);
oci_free_statement($stmt);

if (!$user) {
    oci_close($conn);
    echo json_encode(['success' => false, 'message' => 'Account not found']);
    exit;
}

$new_otp  = strtoupper(bin2hex(random_bytes(3)));
$bind_uid = $user['USER_ID'];

$upd = oci_parse($conn, "UPDATE users SET verification_code = :otp WHERE user_id = :user_id");
oci_bind_by_name($upd, ':otp',     $new_otp);
oci_bind_by_name($upd, ':user_id', $bind_uid);
oci_execute($upd);
oci_commit($conn);
oci_free_statement($upd);
oci_close($conn);

$fullName = trim($user['FIRSTNAME'] . ' ' . $user['LASTNAME']);
$result   = sendOtpEmail($email, $fullName ?: 'User', $new_otp);

if ($result === true) {
    echo json_encode(['success' => true, 'message' => 'A new OTP has been sent to your email.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to send email: ' . $result]);
}
