<?php
/**
 * Greengrocer — Data Only
 * This file returns product data for the Greengrocer category.
 * It is included by shop.php, which handles the layout/CSS/JS.
 */

$category_key   = 'greengrocer';
$category_name  = 'Greengrocer';
$hero_title     = 'Fresh Produce — Picked This Morning, Delivered Today.';
$hero_subtitle  = 'Seasonal. Organic. Farm-to-Table.';
$hero_images    = [
    ['src' => '/cleckbasket/assets/images/greengrocers.png', 'alt' => 'Fresh Vegetables'],
    ['src' => '/cleckbasket/assets/images/BitterGourd.png',  'alt' => 'Bitter Gourd'],
    ['src' => '/cleckbasket/assets/images/passionfruit.png', 'alt' => 'Passion Fruit'],
];

$products = [
    [
        'name'  => 'Bitter Gourd',
        'price' => 2.50,
        'image' => '/cleckbasket/assets/images/BitterGourd.png',
        'desc'  => 'Fresh bitter gourd, locally grown.',
    ],
    [
        'name'  => 'Passion Fruit',
        'price' => 3.80,
        'image' => '/cleckbasket/assets/images/passionfruit.png',
        'desc'  => 'Sweet and tangy passion fruits, ripe and ready.',
    ],
    [
        'name'  => 'Fresh Blueberries',
        'price' => 5.60,
        'image' => '/cleckbasket/assets/images/passionfruit.png',
        'desc'  => 'Plump blueberries from Cleckheaton farms.',
    ],
    [
        'name'  => 'Aged Gouda',
        'price' => 9.00,
        'image' => '/cleckbasket/assets/images/agedgouda.png',
        'desc'  => 'Rich, nutty aged gouda cheese.',
    ],
];
