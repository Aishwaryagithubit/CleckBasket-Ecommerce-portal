<?php
ob_start();
error_reporting(0);
if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json');

if (empty($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$name  = trim($input['name']  ?? '');
$email = trim($input['email'] ?? '');
$phone = trim($input['phone'] ?? '');

if (!$name || !$email) {
    echo json_encode(['success' => false, 'message' => 'Name and email are required.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email address.']);
    exit;
}

$parts     = explode(' ', $name, 2);
$firstname = $parts[0];
$lastname  = $parts[1] ?? '';

require_once __DIR__ . '/connect.php';
$conn = getDBConnection();
if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit;
}

$bind_uid = $_SESSION['user_id'];   // local var for OCI8 reference binding
$sql = "UPDATE users SET firstname = :firstname, lastname = :lastname, email = :email, contact_no = :phone WHERE user_id = :user_id";
$stmt = oci_parse($conn, $sql);
oci_bind_by_name($stmt, ':firstname', $firstname);
oci_bind_by_name($stmt, ':lastname',  $lastname);
oci_bind_by_name($stmt, ':email',     $email);
oci_bind_by_name($stmt, ':phone',     $phone);
oci_bind_by_name($stmt, ':user_id',   $bind_uid);

if (oci_execute($stmt)) {
    oci_commit($conn);
    echo json_encode(['success' => true, 'message' => 'Profile updated successfully.']);
} else {
    $err = oci_error($stmt);
    echo json_encode(['success' => false, 'message' => 'Update failed: ' . ($err['message'] ?? 'unknown error')]);
}

oci_free_statement($stmt);
oci_close($conn);
