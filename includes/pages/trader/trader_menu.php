<?php
if (session_status() === PHP_SESSION_NONE) { 
    session_start(); 
}

$_SESSION['user_id'] = $_SESSION['user_id'] ?? 1;
$_SESSION['role'] = $_SESSION['role'] ?? 'trader';
$_SESSION['full_name'] = $_SESSION['full_name'] ?? 'Unus';

function h($v) { 
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); 
}

function trader_menu_item($href, $icon, $label, $activeKey, $currentActive) {
    $active = ($activeKey === $currentActive) ? ' active' : '';
    echo '<a class="side-link' . $active . '" href="' . h($href) . '">';
    echo '<i class="' . h($icon) . '"></i>';
    echo '<span>' . h($label) . '</span>';
    echo '</a>';
}

function render_trader_shell_start($pageTitle, $active, $breadcrumb, $heading, $rightHtml = '') {
    $name = $_SESSION['full_name'] ?? 'Unus';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($pageTitle) ?> - Cleck Basket</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <link rel="stylesheet" href="trader_frontend.css?v=12000">
</head>

<body>
    <header class="topbar">
        <button class="hamburger-btn" type="button" aria-label="Open trader menu" onclick="toggleTraderMenu()">
            <i class="fa-solid fa-bars"></i>
        </button>

        <div class="topbar-title">
            <strong><?= h($heading) ?></strong>
            <span>Trader Portal</span>
        </div>

        <div class="topbar-spacer"></div>

        <div class="cb-search">
            <span>What do you need today?</span>
            <i class="fa-solid fa-magnifying-glass"></i>
        </div>

        <div class="cb-user">
            <i class="fa-regular fa-user"></i>
            <span>Hi, <?= h($name) ?>!</span>
        </div>
    </header>

    <div class="menu-overlay" onclick="toggleTraderMenu()"></div>

    <aside class="trader-sidebar">
        <a class="sidebar-logo" href="traderdashboard.php">
            <img src="/assets/Images/cleck.png" alt="Cleck Basket Logo">
        </a>

        <nav class="sidebar-nav">
            <?php
                trader_menu_item('traderdashboard.php', 'fa-solid fa-gauge-high', 'Dashboard', 'dashboard', $active);
                trader_menu_item('add_product.php', 'fa-solid fa-circle-plus', 'Add Products', 'add', $active);
                trader_menu_item('My_products.php', 'fa-solid fa-list', 'Manage Products', 'products', $active);
                trader_menu_item('Daily_reports.php', 'fa-solid fa-chart-line', 'Daily Reports', 'daily', $active);
                trader_menu_item('Weekly_reports.php', 'fa-solid fa-chart-simple', 'Weekly Reports', 'weekly', $active);
                trader_menu_item('Monthly_reports.php', 'fa-solid fa-chart-pie', 'Monthly Reports', 'monthly', $active);
                trader_menu_item('trader_invoice.php', 'fa-solid fa-file-invoice', 'Invoice', 'invoice', $active);
                trader_menu_item('trader_profile.php', 'fa-solid fa-user', 'My Profile', 'profile', $active);
                trader_menu_item('logout_trader.php', 'fa-solid fa-right-from-bracket', 'Logout', 'logout', $active);
            ?>
        </nav>
    </aside>

    <main class="page">
        <section class="page-head">
            <div>
                <p><?= h($breadcrumb) ?></p>
                <h1><?= h($heading) ?></h1>
            </div>
            <?= $rightHtml ?>
        </section>
<?php
}

function render_trader_shell_end() {
?>
    </main>

    <script>
        function toggleTraderMenu() {
            document.body.classList.toggle('menu-open');
        }

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                document.body.classList.remove('menu-open');
            }
        });
    </script>
</body>
</html>
<?php
}
?>