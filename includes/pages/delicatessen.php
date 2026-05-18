<?php
/**
 * Delicatessen — Data Only
 * This file returns product data for the Delicatessen category.
 * It is included by shop.php, which handles the layout/CSS/JS.
 */

$category_key   = 'delicatessen';
$category_name  = 'Delicatessen';
$hero_title     = 'Gourmet Delicacies — Curated for the Discerning Palate.';
$hero_subtitle  = 'Artisan. Imported. Exquisite.';
$hero_images    = [
    ['src' => '/cleckbasket/assets/images/nutritions.png', 'alt' => 'Gourmet Selection'],
    ['src' => '/cleckbasket/assets/images/agedgouda.png',  'alt' => 'Aged Gouda'],
    ['src' => '/cleckbasket/assets/images/nutritions.png', 'alt' => 'Fine Cheese'],
];

$products = [
    [
        'name'  => 'Truffle Oil',
        'price' => 14.50,
        'image' => '/cleckbasket/assets/images/nutritions.png',
        'desc'  => 'Italian black truffle infused extra virgin olive oil.',
    ],
    [
        'name'  => 'Aged Gouda',
        'price' => 9.00,
        'image' => '/cleckbasket/assets/images/agedgouda.png',
        'desc'  => 'Matured for 18 months, rich and crystalline.',
    ],
    [
        'name'  => 'Prosciutto di Parma',
        'price' => 18.00,
        'image' => '/cleckbasket/assets/images/nutritions.png',
        'desc'  => 'Dry-cured Italian ham, thinly sliced.',
    ],
    [
        'name'  => 'Artisan Olives',
        'price' => 6.50,
        'image' => '/cleckbasket/assets/images/nutritions.png',
        'desc'  => 'Marinated Mediterranean olives with herbs.',
    ],
];
