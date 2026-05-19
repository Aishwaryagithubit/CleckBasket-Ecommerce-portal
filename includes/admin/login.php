<?php
session_start();
require_once __DIR__ . '/../../backend/connect.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $authenticated = false;

    // Check DB for admin user (role = 'ADMIN')
    $conn = getDBConnection();
    if ($conn) {
        $stmt = oci_parse($conn,
            "SELECT user_id, firstname, password_hash FROM users
             WHERE (email = :u OR LOWER(firstname) = LOWER(:u))
             AND UPPER(role) = 'ADMIN'");
        oci_bind_by_name($stmt, ':u', $username);
        oci_execute($stmt);
        $row = oci_fetch_assoc($stmt);
        oci_free_statement($stmt);
        oci_close($conn);

        if ($row && (password_verify($password, $row['PASSWORD_HASH']) || $password === $row['PASSWORD_HASH'])) {
            $authenticated = true;
            $_SESSION['admin_username'] = $row['FIRSTNAME'];
        }
    }

    // Hardcoded fallback (admin/admin123) in case no DB admin exists yet
    if (!$authenticated && $username === 'admin' && $password === 'admin123') {
        $authenticated = true;
        $_SESSION['admin_username'] = 'Admin';
    }

    if ($authenticated) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: index.php');
        exit;
    } else {
        $error = 'Invalid username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - CleckBasket</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/admin-login.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h1>Admin Panel</h1>
            <p>CleckBasket Administration</p>

            <?php if ($error): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">Username / Email</label>
                    <input type="text" id="username" name="username" required autocomplete="username">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required autocomplete="current-password">
                </div>
                <button type="submit" class="btn-login">Login</button>
            </form>

            <p class="demo-credentials">Fallback: admin / admin123</p>
        </div>
    </div>
</body>
</html>
