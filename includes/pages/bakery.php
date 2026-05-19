<?php
/**
 * Bakery — loads products from DB (category: Bakery)
 */

$category_key  = 'bakery';
$category_name = 'Bakery';
$hero_title    = 'Artisan Baked Goods — Fresh From the Oven, Daily.';
$hero_subtitle = 'Handcrafted. Wholesome. Irresistible.';
$hero_images   = [
    ['src' => '/cleckbasket/assets/images/bread.png',    'alt' => 'Sourdough Bread'],
    ['src' => '/cleckbasket/assets/images/croissant.jpg', 'alt' => 'Butter Croissant'],
    ['src' => '/cleckbasket/assets/images/fruitcupcake.png',   'alt' => 'Cupcakes'],
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
            WHERE pc.category_type = 'Bakery'
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
