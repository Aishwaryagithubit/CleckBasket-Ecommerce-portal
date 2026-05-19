<?php
require_once 'trader_menu.php';

$userId = $_SESSION['user_id'];
$shop   = ['shop_name' => '', 'description' => ''];
$notice = '';
$error  = '';

$conn = getDBConnection();
if ($conn) {
    $dbShop = getTraderShop($conn, $userId);
    if ($dbShop) {
        $shop['shop_name']   = $dbShop['SHOP_NAME'];
        $shop['description'] = $dbShop['DESCRIPTION'];
        $shopId = $dbShop['SHOP_ID'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $newName = trim($_POST['shop_name'] ?? '');
            $newDesc = trim($_POST['description'] ?? '');

            if (!$newName) {
                $error = 'Shop name is required.';
            } else {
                $sql  = "UPDATE shop SET shop_name = :sname, description = :sdesc WHERE shop_id = :sid";
                $stmt = oci_parse($conn, $sql);
                oci_bind_by_name($stmt, ':sname', $newName);
                oci_bind_by_name($stmt, ':sdesc', $newDesc);
                oci_bind_by_name($stmt, ':sid',  $shopId);
                if (oci_execute($stmt)) {
                    oci_commit($conn);
                    $notice             = 'Shop details updated successfully.';
                    $shop['shop_name']  = $newName;
                    $shop['description'] = $newDesc;
                } else {
                    $err   = oci_error($stmt);
                    $error = 'Update failed: ' . $err['message'];
                }
                oci_free_statement($stmt);
            }
        }
    } else {
        $error = 'No shop found for your account.';
    }
    oci_close($conn);
}

$right = '<a class="btn" href="traderdashboard.php">Back to Dashboard</a>';
render_trader_shell_start('Edit Shop', 'profile', 'TRADER PORTAL › SHOP SETTINGS', 'Edit Shop', $right);
?>

<?php if ($notice): ?>
    <div class="notice success"><i class="fa-solid fa-check"></i> <?= h($notice) ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="notice danger"><i class="fa-solid fa-circle-exclamation"></i> <?= h($error) ?></div>
<?php endif; ?>

<div class="edit-card">
    <form method="POST" class="edit-form">
        <label>Shop Name
            <input name="shop_name" value="<?= h($_POST['shop_name'] ?? $shop['shop_name']) ?>" required>
        </label>
        <label class="full-row">Description
            <textarea name="description"><?= h($_POST['description'] ?? $shop['description']) ?></textarea>
        </label>
        <button class="btn save-btn" type="submit">Save Shop Details</button>
    </form>
</div>

<?php render_trader_shell_end(); ?>
