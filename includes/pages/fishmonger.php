<?php
/**
 * Fishmonger — Data Only
 * This file returns product data for the Fishmonger category.
 * It is included by shop.php, which handles the layout/CSS/JS.
 */

$category_key   = 'fishmonger';
$category_name  = 'Fishmonger';
$hero_title     = 'Seafood — Straight From the Harbour, Onto Your Plate.';
$hero_subtitle  = 'Wild Caught. Sustainably Sourced. Ocean Fresh.';
$hero_images    = [
    ['src' => '/cleckbasket/assets/images/salmonfillet.png', 'alt' => 'Salmon Fillet'],
    ['src' => '/cleckbasket/assets/images/fishmongers.png',  'alt' => 'Fresh Fish'],
    ['src' => '/cleckbasket/assets/images/salmonfillet.png', 'alt' => 'Prawns'],
];

$products = [
    [
        'name'  => 'Salmon Fillet',
        'price' => 11.50,
        'image' => '/cleckbasket/assets/images/salmonfillet.png',
        'desc'  => 'Fresh Atlantic salmon fillet, skin-on.',
    ],
    [
        'name'  => 'Cod Loin',
        'price' => 8.90,
        'image' => '/cleckbasket/assets/images/fishmongers.png',
        'desc'  => 'Thick-cut cod loin, perfect for fish and chips.',
    ],
    [
        'name'  => 'King Prawns',
        'price' => 14.00,
        'image' => '/cleckbasket/assets/images/fishmongers.png',
        'desc'  => 'Jumbo king prawns, peeled and deveined.',
    ],
    [
        'name'  => 'Smoked Mackerel',
        'price' => 6.50,
        'image' => '/cleckbasket/assets/images/fishmongers.png',
        'desc'  => 'Oak-smoked mackerel fillets, ready to eat.',
    ],
];
