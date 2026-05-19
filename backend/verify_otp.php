<?php
session_start();
require_once __DIR__ . '/connect.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$email = strtolower(trim($input['email'] ?? ''));
$otp   = strtoupper(trim($input['otp'] ?? ''));

if (!$email || !$otp) {
    echo json_encode(['success' => false, 'message' => 'Email and OTP are required']);
    exit;
}

$conn = getDBConnection();
if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

$sql  = "SELECT USER_ID, FIRSTNAME, LASTNAME, EMAIL, ROLE, STATUS, VERIFICATION_CODE FROM USERS
         WHERE LOWER(EMAIL) = :email";
$stmt = oci_parse($conn, $sql);
oci_bind_by_name($stmt, ':email', $email);
oci_execute($stmt);
$user = oci_fetch_assoc($stmt);
oci_free_statement($stmt);

if (!$user) {
    oci_close($conn);
    echo json_encode(['success' => false, 'message' => 'Account not found']);
    exit;
}

if (strtoupper($user['VERIFICATION_CODE']) !== $otp) {
    oci_close($conn);
    echo json_encode(['success' => false, 'message' => 'Invalid OTP. Please try again.']);
    exit;
}

// Clear the used verification code
$clr = oci_parse($conn, "UPDATE users SET verification_code = NULL WHERE user_id = :p_user_id");
oci_bind_by_name($clr, ':p_user_id', $user['USER_ID']);
oci_execute($clr);
oci_commit($conn);
oci_free_statement($clr);
oci_close($conn);

$status = strtoupper(trim($user['STATUS'] ?? ''));
$role   = strtolower(trim($user['ROLE']));

// PENDING traders have verified their email but still need admin approval
if ($status === 'PENDING') {
    echo json_encode([
        'success'  => true,
        'redirect' => '/cleckbasket/includes/pages/login.php?success=' . urlencode('Email verified! Your account is awaiting admin approval. You will be able to log in once approved.')
    ]);
    exit;
}

// ACTIVE users — create session and redirect
$_SESSION['user_id']   = $user['USER_ID'];
$_SESSION['email']     = $user['EMAIL'];
$_SESSION['user_name'] = $user['FIRSTNAME'];
$_SESSION['full_name'] = $user['FIRSTNAME'] . ' ' . $user['LASTNAME'];
$_SESSION['role']      = $role;

$redirect = $role === 'trader'
    ? '/cleckbasket/includes/pages/trader/traderdashboard.php'
    : '/cleckbasket/includes/pages/homepage.php';

echo json_encode(['success' => true, 'redirect' => $redirect]);
