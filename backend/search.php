<?php
require_once __DIR__ . '/connect.php';
header('Content-Type: application/json');

$q         = trim($_GET['q']          ?? '');
$catsParam = trim($_GET['cats']       ?? '');
$minRating = (float)($_GET['min_rating'] ?? 0);
$minPrice  = isset($_GET['min_price']) && $_GET['min_price'] !== '' ? (float)$_GET['min_price'] : null;
$maxPrice  = isset($_GET['max_price']) && $_GET['max_price'] !== '' ? (float)$_GET['max_price'] : null;

$conn = getDBConnection();
if (!$conn) {
    echo json_encode(['products' => [], 'categories' => []]);
    exit;
}

// Always return categories (used by filter chips on init and after searches)
$categories = [];
$cs = oci_parse($conn, "SELECT category_type FROM product_category ORDER BY category_type");
oci_execute($cs);
while ($row = oci_fetch_assoc($cs)) $categories[] = $row['CATEGORY_TYPE'];
oci_free_statement($cs);

if ($q === '') {
    oci_close($conn);
    echo json_encode(['products' => [], 'categories' => $categories]);
    exit;
}

$likeQ = '%' . strtoupper($q) . '%';

$sql = "SELECT p.product_id, p.product_name, pc.category_type,
               p.price, p.product_image,
               ROUND(NVL(AVG(r.review_rating), 0), 1) AS avg_rating,
               COUNT(r.review_id) AS review_count
        FROM product p
        JOIN product_category pc ON p.product_category_id = pc.product_category_id
        LEFT JOIN review r ON p.product_id = r.product_id
        WHERE (UPPER(p.product_name) LIKE :p_q1 OR UPPER(pc.category_type) LIKE :p_q2)";

if ($minPrice !== null) $sql .= " AND p.price >= :p_min_price";
if ($maxPrice !== null) $sql .= " AND p.price <= :p_max_price";

$sql .= " GROUP BY p.product_id, p.product_name, pc.category_type, p.price, p.product_image";
$sql .= " ORDER BY avg_rating DESC, p.product_name";
$sql .= " FETCH FIRST 30 ROWS ONLY";

$stmt = oci_parse($conn, $sql);
oci_bind_by_name($stmt, ':p_q1', $likeQ);
oci_bind_by_name($stmt, ':p_q2', $likeQ);
if ($minPrice !== null) oci_bind_by_name($stmt, ':p_min_price', $minPrice);
if ($maxPrice !== null) oci_bind_by_name($stmt, ':p_max_price', $maxPrice);

$products = [];
if (oci_execute($stmt)) {
    while ($row = oci_fetch_assoc($stmt)) {
        $products[] = [
            'id'           => (int)$row['PRODUCT_ID'],
            'name'         => $row['PRODUCT_NAME'],
            'category'     => $row['CATEGORY_TYPE'],
            'price'        => (float)$row['PRICE'],
            'rating'       => (float)$row['AVG_RATING'],
            'review_count' => (int)$row['REVIEW_COUNT'],
            'image'        => $row['PRODUCT_IMAGE'] ?? '',
        ];
    }
}
oci_free_statement($stmt);
oci_close($conn);

// Category filter applied in PHP (simpler than a dynamic IN clause)
if ($catsParam) {
    $catList = array_filter(array_map('trim', explode(',', $catsParam)));
    if ($catList) {
        $products = array_values(array_filter($products, fn($p) => in_array($p['category'], $catList)));
    }
}

// Rating filter applied in PHP (HAVING with conditional bind is messier)
if ($minRating > 0) {
    $products = array_values(array_filter($products, fn($p) => $p['rating'] >= $minRating));
}

echo json_encode(['products' => $products, 'categories' => $categories]);
