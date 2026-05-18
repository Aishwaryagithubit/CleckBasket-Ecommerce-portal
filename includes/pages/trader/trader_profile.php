<?php
require_once 'trader_menu.php';

$userData = ['FULL_NAME' => $_SESSION['full_name'], 'EMAIL' => 'trader@example.com', 'PHONE' => '07123456789'];
$saved = $_SERVER['REQUEST_METHOD'] === 'POST';

render_trader_shell_start('Trader Profile', 'profile', 'TRADER PORTAL › SETTINGS', 'My Profile');
?>

<?php if($saved): ?><div class="card ok">Front-end demo: profile updated locally only.</div><?php endif; ?>

<div class="profile-card">
    <div class="profile-top">
        <i class="fa-regular fa-circle-user"></i>
        <h2><?= h($userData['FULL_NAME']) ?></h2>
        <p>Trader Account</p>
    </div>

    <form method="POST" class="profile-form">
        <label>Full Name<input name="full_name" value="<?= h($_POST['full_name'] ?? $userData['FULL_NAME']) ?>"></label>
        <label>Email<input type="email" name="email" value="<?= h($_POST['email'] ?? $userData['EMAIL']) ?>"></label>
        <label>Phone<input name="phone" value="<?= h($_POST['phone'] ?? $userData['PHONE']) ?>"></label>
        <h3>Change Password</h3>
        <label>Current Password<input type="password" name="current_password"></label>
        <label>New Password<input type="password" name="new_password"></label>
        <button class="btn save-btn" type="submit">Update Profile</button>
    </form>
</div>

<?php render_trader_shell_end(); ?>