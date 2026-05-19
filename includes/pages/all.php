<?php
/**
 * All — loads every product from DB across all categories
 */

$category_key  = 'all';
$category_name = 'All Products';
$hero_title    = 'Everything Fresh, All In One Place.';
$hero_subtitle = 'Explore our full range from local traders.';
$hero_images   = [
    ['src' => '/cleckbasket/assets/images/bakerys.png',     'alt' => 'Bakery'],
    ['src' => '/cleckbasket/assets/images/beefs.png',       'alt' => 'Butcher'],
    ['src' => '/cleckbasket/assets/images/fishmongers.png', 'alt' => 'Fishmonger'],
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
