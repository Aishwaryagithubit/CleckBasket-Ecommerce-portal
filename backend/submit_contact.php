<?php
ob_start();
error_reporting(0);
header('Content-Type: application/json');

$input          = json_decode(file_get_contents('php://input'), true);
$first_name     = trim($input['first_name']     ?? '');
$last_name      = trim($input['last_name']      ?? '');
$email          = trim($input['email']          ?? '');
$contact_number = trim($input['contact_number'] ?? '');
$subject        = trim($input['subject']        ?? 'general');
$message        = trim($input['message']        ?? '');

if (!$first_name || !$email || !$message) {
    echo json_encode(['success' => false, 'message' => 'Please fill in your name, email, and message.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
    exit;
}

if (strlen($message) > 2000) {
    echo json_encode(['success' => false, 'message' => 'Message is too long (max 2000 characters).']);
    exit;
}

require_once __DIR__ . '/connect.php';
$conn = getDBConnection();
if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit;
}

$sql  = "INSERT INTO contact_message
           (first_name, last_name, email, contact_number, subject, message, sent_date)
         VALUES
           (:first_name, :last_name, :email, :contact_number, :subject, :message, SYSDATE)";
$stmt = oci_parse($conn, $sql);
oci_bind_by_name($stmt, ':first_name',     $first_name);
oci_bind_by_name($stmt, ':last_name',      $last_name);
oci_bind_by_name($stmt, ':email',          $email);
oci_bind_by_name($stmt, ':contact_number', $contact_number);
oci_bind_by_name($stmt, ':subject',        $subject);
oci_bind_by_name($stmt, ':message',        $message);

if (oci_execute($stmt)) {
    oci_commit($conn);
    echo json_encode(['success' => true, 'message' => 'Your message has been sent! We\'ll get back to you soon.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to send message. Please try again.']);
}

oci_free_statement($stmt);
oci_close($conn);
