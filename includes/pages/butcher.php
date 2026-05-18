<?php
/**
 * Butcher — Data Only
 * This file returns product data for the Butcher category.
 * It is included by shop.php, which handles the layout/CSS/JS.
 */

$category_key   = 'butcher';
$category_name  = 'Butcher';
$hero_title     = 'Meat Products — From Your Local Butcher, To Your Doorstep.';
$hero_subtitle  = 'Fresh. Fine Cut. Premium Quality.';
$hero_images    = [
    ['src' => '/cleckbasket/assets/images/wholechicken.png', 'alt' => 'Whole Chicken'],
    ['src' => '/cleckbasket/assets/images/wholechicken.png', 'alt' => 'Whole Chicken'],
    ['src' => '/cleckbasket/assets/images/wholechicken.png', 'alt' => 'Whole Chicken'],
    ['src' => '/cleckbasket/assets/images/buffalomeat.png',  'alt' => 'Buffalo Meat'],
    ['src' => '/cleckbasket/assets/images/porkribs.png',     'alt' => 'Pork Ribs'],
];

$products = [
    [
        'name'  => 'Whole Chicken',
        'price' => 7.20,
        'image' => '/cleckbasket/assets/images/wholechicken.png',
        'desc'  => 'Free-range whole chicken, perfect for roasting.',
    ],
    [
        'name'  => 'Pork Ribs',
        'price' => 9.00,
        'image' => '/cleckbasket/assets/images/porkribs.png',
        'desc'  => 'Tender pork ribs, great for grilling or slow cooking.',
    ],
    [
        'name'  => 'Buffalo Meat',
        'price' => 4.75,
        'image' => '/cleckbasket/assets/images/buffalomeat.png',
        'desc'  => 'Lean buffalo mince, locally sourced.',
    ],
    [
        'name'  => 'Lamb Chops',
        'price' => 12.40,
        'image' => '/cleckbasket/assets/images/lambchops.png',
        'desc'  => 'Premium lamb chops from local farms.',
    ],
];
