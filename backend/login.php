<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /cleckbasket/includes/pages/login.php');
    exit;
}

$email = strtolower(trim($_POST['email'] ?? ''));
$password = trim($_POST['password'] ?? '');

if ($email === '' || $password === '') {
    header('Location: /cleckbasket/includes/pages/login.php?error=' . urlencode('Email and password are required.'));
    exit;
}

// Connect to Oracle
require_once $_SERVER['DOCUMENT_ROOT'] . '/cleckbasket/backend/connect.php';
$conn = getDBConnection();

// Query the USERS table
$sql = "SELECT USER_ID, FIRSTNAME, LASTNAME, EMAIL, ROLE FROM USERS 
        WHERE LOWER(EMAIL) = :email AND PASSWORD = :password AND STATUS = 'ACTIVE'";

$stmt = oci_parse($conn, $sql);
oci_bind_by_name($stmt, ':email', $email);
oci_bind_by_name($stmt, ':password', $password);
oci_execute($stmt);

$user = oci_fetch_assoc($stmt);

if (!$user) {
    header('Location: /cleckbasket/includes/pages/login.php?error=' . urlencode('Incorrect email or password.'));
    exit;
}

// Set session
$_SESSION['user_id']   = $user['USER_ID'];
$_SESSION['email']     = $user['EMAIL'];
$_SESSION['full_name'] = $user['FIRSTNAME'] . ' ' . $user['LASTNAME'];
$_SESSION['user_name'] = $user['FIRSTNAME'];
$_SESSION['role']      = strtolower($user['ROLE']);

if (strtolower($user['ROLE']) === 'admin') {
    $_SESSION['admin_logged_in'] = true;
} else {
    unset($_SESSION['admin_logged_in']);
}

oci_free_statement($stmt);
oci_close($conn);

switch (strtolower($user['ROLE'])) {
    case 'admin':
        header('Location: /cleckbasket/includes/admin/index.php');
        break;
    case 'trader':
        header('Location: /cleckbasket/includes/pages/trader/traderdashboard.php');
        break;
    default:
        header('Location: /cleckbasket/includes/pages/homepage.php');
        break;
}
exit;
?>