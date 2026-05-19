<?php
require_once __DIR__ . '/connect.php';
header('Content-Type: text/plain');

$conn = getDBConnection();
if (!$conn) {
    echo "FAIL: Could not connect to database.\n";
    exit;
}

// Which schema is PHP connected as?
$stmt = oci_parse($conn, "SELECT USER FROM DUAL");
oci_execute($stmt);
$row = oci_fetch_assoc($stmt);
echo "PHP connected as schema: " . $row['USER'] . "\n\n";
oci_free_statement($stmt);

// How many products exist in this schema?
$stmt = oci_parse($conn, "SELECT COUNT(*) AS cnt FROM product");
oci_execute($stmt);
$row = oci_fetch_assoc($stmt);
echo "Total products in PHP schema: " . $row['CNT'] . "\n\n";
oci_free_statement($stmt);

// List all products with id and name
$stmt = oci_parse($conn, "SELECT product_id, product_name FROM product ORDER BY product_id");
oci_execute($stmt);
echo "Products:\n";
while ($row = oci_fetch_assoc($stmt)) {
    echo "  [{$row['PRODUCT_ID']}] {$row['PRODUCT_NAME']}\n";
}
oci_free_statement($stmt);
oci_close($conn);
