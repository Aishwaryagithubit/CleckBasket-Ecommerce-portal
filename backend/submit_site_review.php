<?php
ob_start();
error_reporting(0);
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to submit a review.']);
    exit;
}

$input  = json_decode(file_get_contents('php://input'), true);
$rating = (int)($input['rating'] ?? 0);
$title  = trim($input['title'] ?? '');
$text   = trim($input['text'] ?? '');

if ($rating < 1 || $rating > 5) {
    echo json_encode(['success' => false, 'message' => 'Please select a rating between 1 and 5.']);
    exit;
}
if (strlen($title) < 2) {
    echo json_encode(['success' => false, 'message' => 'Please enter a review title.']);
    exit;
}
if (strlen($title) > 100) {
    echo json_encode(['success' => false, 'message' => 'Title is too long (max 100 characters).']);
    exit;
}
if (strlen($text) < 5) {
    echo json_encode(['success' => false, 'message' => 'Review message is too short.']);
    exit;
}
if (strlen($text) > 500) {
    echo json_encode(['success' => false, 'message' => 'Review message is too long (max 500 characters).']);
    exit;
}

require_once __DIR__ . '/connect.php';
$conn = getDBConnection();
if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit;
}

// Omit product_id — nullable column defaults to NULL (site-level review)
$user_id = (int)$_SESSION['user_id'];
$sql  = "INSERT INTO review (review_title, review, review_rating, user_id, created_date)
         VALUES (:review_title, :review_text, :review_rating, :user_id, SYSDATE)";
$stmt = oci_parse($conn, $sql);
oci_bind_by_name($stmt, ':review_title',  $title);
oci_bind_by_name($stmt, ':review_text',   $text);
oci_bind_by_name($stmt, ':review_rating', $rating);
oci_bind_by_name($stmt, ':user_id',       $user_id);

if (oci_execute($stmt)) {
    oci_commit($conn);
    echo json_encode(['success' => true, 'message' => 'Thank you! Your review has been submitted.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to save review. Please try again.']);
}

oci_free_statement($stmt);
oci_close($conn);
