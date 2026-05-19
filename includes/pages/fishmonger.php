<?php
/**
 * Fishmonger — loads products from DB (category: Seafood)
 */

$category_key  = 'fishmonger';
$category_name = 'Fishmonger';
$hero_title    = 'Seafood — Straight From the Harbour, Onto Your Plate.';
$hero_subtitle = 'Wild Caught. Sustainably Sourced. Ocean Fresh.';
$hero_images   = [
    ['src' => '/cleckbasket/assets/images/salmonfillet.png', 'alt' => 'Salmon Fillet'],
    ['src' => '/cleckbasket/assets/images/fishmongers.png',  'alt' => 'Fresh Fish'],
    ['src' => '/cleckbasket/assets/images/salmonfillet.png', 'alt' => 'Prawns'],
];

require_once __DIR__ . '/../../backend/connect.php';

$products = [];
$conn = getDBConnection();
if ($conn) {
    $sql = "SELECT p.product_id, p.product_name, p.price, p.product_image,
                   NVL(p.description, '') AS description,
                   NVL(d.discount_percentage, 0) AS discount_pct
            FROM product p
            JOIN product_category pc ON p.product_category_id = pc.product_category_id
            LEFT JOIN discount d ON p.product_id = d.product_id
                AND d.start_date <= SYSDATE AND d.end_date >= SYSDATE
            WHERE pc.category_type = 'Seafood'
            ORDER BY p.product_id DESC";
    $stmt = oci_parse($conn, $sql);
    oci_execute($stmt);
    while ($row = oci_fetch_assoc($stmt)) {
        $products[] = [
            'id'       => (int)$row['PRODUCT_ID'],
            'name'     => $row['PRODUCT_NAME'],
            'price'    => (float)$row['PRICE'],
            'image'    => '/cleckbasket/assets/images/' . $row['PRODUCT_IMAGE'],
            'desc'     => $row['DESCRIPTION'],
            'discount' => (int)$row['DISCOUNT_PCT'],
        ];
    }
    oci_free_statement($stmt);
    oci_close($conn);
}
