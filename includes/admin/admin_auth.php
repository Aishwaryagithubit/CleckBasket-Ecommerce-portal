<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (empty($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

if (!isset($_SESSION['admin_username'])) {
    $_SESSION['admin_username'] = 'Admin';
}

require_once __DIR__ . '/../../backend/connect.php';

function h($v) {
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

function admin_sidebar($active = '') {
    $items = [
        ['href' => 'index.php',      'icon' => 'fa-chart-line',    'label' => 'Dashboard',          'key' => 'dashboard'],
        ['href' => 'customers.php',  'icon' => 'fa-users',         'label' => 'Customer Directory', 'key' => 'customers'],
        ['href' => 'products.php',   'icon' => 'fa-box',           'label' => 'Products',           'key' => 'products'],
        ['href' => 'orders.php',     'icon' => 'fa-shopping-cart', 'label' => 'Orders',             'key' => 'orders'],
        ['href' => 'add-trader.php', 'icon' => 'fa-user-plus',     'label' => 'Trader Management',  'key' => 'traders'],
        ['href' => 'categories.php', 'icon' => 'fa-tags',          'label' => 'Categories',         'key' => 'categories'],
        ['href' => 'reports.php',    'icon' => 'fa-chart-bar',     'label' => 'Reports',            'key' => 'reports'],
        ['href' => 'messages.php',   'icon' => 'fa-envelope',      'label' => 'Messages',           'key' => 'messages'],
        ['href' => 'settings.php',   'icon' => 'fa-cog',           'label' => 'Settings',           'key' => 'settings'],
        ['href' => 'logout.php',     'icon' => 'fa-sign-out-alt',  'label' => 'Logout',             'key' => 'logout'],
    ];
    echo '<aside class="sidebar">';
    echo '<div class="logo"><a href="index.php" style="text-decoration:none;color:inherit;"><img src="../../assets/images/logo.png" alt="CleckBasket" style="height:40px;width:auto;"></a></div>';
    echo '<nav class="nav-menu">';
    foreach ($items as $item) {
        $cls = ($item['key'] === $active) ? ' active' : '';
        if ($item['key'] === 'logout') $cls .= ' logout';
        echo '<a href="' . h($item['href']) . '" class="nav-item' . $cls . '">';
        echo '<i class="fas ' . h($item['icon']) . '"></i><span>' . h($item['label']) . '</span>';
        echo '</a>';
    }
    echo '</nav></aside>';
}
