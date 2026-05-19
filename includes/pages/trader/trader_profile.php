<?php
require_once 'trader_menu.php';

$userId   = $_SESSION['user_id'];
$userData = ['FIRSTNAME' => '', 'LASTNAME' => '', 'EMAIL' => '', 'CONTACT_NO' => ''];
$notice   = '';
$error    = '';

$conn = getDBConnection();
if ($conn) {
    // Load profile
    $stmt = oci_parse($conn, "SELECT firstname, lastname, email, contact_no FROM users WHERE user_id = :owner_id");
    oci_bind_by_name($stmt, ':owner_id', $userId);
    oci_execute($stmt);
    $row = oci_fetch_assoc($stmt);
    if ($row) $userData = $row;
    oci_free_statement($stmt);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $firstname = trim($_POST['firstname'] ?? '');
        $lastname  = trim($_POST['lastname']  ?? '');
        $phone     = trim($_POST['phone']     ?? '');
        $curPw     = $_POST['current_password'] ?? '';
        $newPw     = $_POST['new_password']     ?? '';

        if (!$firstname) {
            $error = 'First name is required.';
        } else {
            if ($newPw) {
                // Verify current password first
                $chk = oci_parse($conn, "SELECT password_hash FROM users WHERE user_id = :owner_id");
                oci_bind_by_name($chk, ':owner_id', $userId);
                oci_execute($chk);
                $pwRow = oci_fetch_assoc($chk);
                oci_free_statement($chk);

                $stored = $pwRow['PASSWORD_HASH'] ?? '';
                if (!password_verify($curPw, $stored) && $curPw !== $stored) {
                    $error = 'Current password is incorrect.';
                } elseif (strlen($newPw) < 6) {
                    $error = 'New password must be at least 6 characters.';
                } else {
                    $newHash = password_hash($newPw, PASSWORD_DEFAULT);
                    $sql = "UPDATE users SET firstname = :fn, lastname = :ln, contact_no = :ph,
                                            password_hash = :pw WHERE user_id = :owner_id";
                    $stmt2 = oci_parse($conn, $sql);
                    oci_bind_by_name($stmt2, ':fn',       $firstname);
                    oci_bind_by_name($stmt2, ':ln',       $lastname);
                    oci_bind_by_name($stmt2, ':ph',       $phone);
                    oci_bind_by_name($stmt2, ':pw',       $newHash);
                    oci_bind_by_name($stmt2, ':owner_id', $userId);
                    if (oci_execute($stmt2)) {
                        oci_commit($conn);
                        $notice = 'Profile and password updated.';
                        $_SESSION['user_name']  = $firstname;
                        $_SESSION['full_name']  = $firstname . ' ' . $lastname;
                    } else {
                        $err = oci_error($stmt2); $error = $err['message'];
                    }
                    oci_free_statement($stmt2);
                }
            } else {
                $sql   = "UPDATE users SET firstname = :fn, lastname = :ln, contact_no = :ph WHERE user_id = :owner_id";
                $stmt2 = oci_parse($conn, $sql);
                oci_bind_by_name($stmt2, ':fn',       $firstname);
                oci_bind_by_name($stmt2, ':ln',       $lastname);
                oci_bind_by_name($stmt2, ':ph',       $phone);
                oci_bind_by_name($stmt2, ':owner_id', $userId);
                if (oci_execute($stmt2)) {
                    oci_commit($conn);
                    $notice = 'Profile updated successfully.';
                    $_SESSION['user_name'] = $firstname;
                    $_SESSION['full_name'] = $firstname . ' ' . $lastname;
                } else {
                    $err = oci_error($stmt2); $error = $err['message'];
                }
                oci_free_statement($stmt2);
            }

            if (!$error) {
                $userData['FIRSTNAME']  = $firstname;
                $userData['LASTNAME']   = $lastname;
                $userData['CONTACT_NO'] = $phone;
            }
        }
    }
    oci_close($conn);
}

render_trader_shell_start('Trader Profile', 'profile', 'TRADER PORTAL › SETTINGS', 'My Profile');
?>

<?php if ($notice): ?>
    <div class="notice success"><i class="fa-solid fa-check"></i> <?= h($notice) ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="notice danger"><i class="fa-solid fa-circle-exclamation"></i> <?= h($error) ?></div>
<?php endif; ?>

<div class="profile-card">
    <div class="profile-top">
        <i class="fa-regular fa-circle-user"></i>
        <h2><?= h($userData['FIRSTNAME'] . ' ' . $userData['LASTNAME']) ?></h2>
        <p>Trader Account</p>
    </div>

    <form method="POST" class="profile-form">
        <label>First Name
            <input name="firstname" value="<?= h($_POST['firstname'] ?? $userData['FIRSTNAME']) ?>" required>
        </label>
        <label>Last Name
            <input name="lastname" value="<?= h($_POST['lastname'] ?? $userData['LASTNAME']) ?>">
        </label>
        <label>Email
            <input type="email" value="<?= h($userData['EMAIL']) ?>" disabled title="Email cannot be changed">
        </label>
        <label>Phone
            <input name="phone" value="<?= h($_POST['phone'] ?? $userData['CONTACT_NO']) ?>">
        </label>
        <h3>Change Password <small>(leave blank to keep current)</small></h3>
        <label>Current Password<input type="password" name="current_password"></label>
        <label>New Password<input type="password" name="new_password"></label>
        <button class="btn save-btn" type="submit">Update Profile</button>
    </form>
</div>

<?php render_trader_shell_end(); ?>
