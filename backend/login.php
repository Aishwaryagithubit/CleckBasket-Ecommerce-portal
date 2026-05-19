<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /cleckbasket/includes/pages/login.php');
    exit;
}

$email    = strtolower(trim($_POST['email'] ?? ''));
$password = trim($_POST['password'] ?? '');

if ($email === '' || $password === '') {
    header('Location: /cleckbasket/includes/pages/login.php?error=' . urlencode('Email and password are required.'));
    exit;
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/cleckbasket/backend/connect.php';
$conn = getDBConnection();
if (!$conn) {
    header('Location: /cleckbasket/includes/pages/login.php?error=' . urlencode('Database connection failed.'));
    exit;
}
$sql  = "SELECT USER_ID, FIRSTNAME, LASTNAME, EMAIL, PASSWORD_HASH, ROLE, STATUS FROM USERS
         WHERE LOWER(EMAIL) = :email";
$stmt = oci_parse($conn, $sql);
oci_bind_by_name($stmt, ':email', $email);
oci_execute($stmt);

$user = oci_fetch_assoc($stmt);
oci_free_statement($stmt);

if (!$user) {
    oci_close($conn);
    header('Location: /cleckbasket/includes/pages/login.php?error=' . urlencode('Incorrect email or password.'));
    exit;
}

$status = strtoupper(trim($user['STATUS'] ?? ''));
if ($status === 'PENDING') {
    oci_close($conn);
    header('Location: /cleckbasket/includes/pages/login.php?error=' . urlencode('Your account is awaiting admin approval. Please check back later.'));
    exit;
}
if ($status === 'REJECTED') {
    oci_close($conn);
    header('Location: /cleckbasket/includes/pages/login.php?error=' . urlencode('Your account application has been rejected. Please contact support.'));
    exit;
}
if ($status !== 'ACTIVE') {
    oci_close($conn);
    header('Location: /cleckbasket/includes/pages/login.php?error=' . urlencode('Your account is not active. Please contact support.'));
    exit;
}

$storedHash = $user['PASSWORD_HASH'];
$passwordValid = password_verify($password, $storedHash) || $password === $storedHash;

if (!$passwordValid) {
    oci_close($conn);
    header('Location: /cleckbasket/includes/pages/login.php?error=' . urlencode('Incorrect email or password.'));
    exit;
}
oci_close($conn);

// Read values using UPPERCASE keys — Oracle always returns uppercase
$userId    = $user['USER_ID'];
$email     = $user['EMAIL'];
$firstname = $user['FIRSTNAME'];
$lastname  = $user['LASTNAME'];
$role      = strtolower(trim($user['ROLE']));
$fullName  = $firstname . ' ' . $lastname;

// Set PHP session
$_SESSION['user_id']   = $userId;
$_SESSION['email']     = $email;
$_SESSION['full_name'] = $fullName;
$_SESSION['user_name'] = $firstname;
$_SESSION['role']      = $role;

if ($role === 'admin') {
    $_SESSION['admin_logged_in'] = true;
} else {
    unset($_SESSION['admin_logged_in']);
}

// Determine where to redirect based on role
if ($role === 'admin') {
    $redirectUrl = '/cleckbasket/includes/admin/index.php';
} elseif ($role === 'trader') {
    $redirectUrl = '/cleckbasket/includes/pages/trader/traderdashboard.php';
} else {
    $redirectUrl = '/cleckbasket/includes/pages/homepage.php';
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Redirecting...</title>
</head>
<body>
<script>
    // Sync localStorage so header.php JS knows user is logged in
    localStorage.setItem('is_login', 'true');
    localStorage.setItem('user_name', <?= json_encode($firstname) ?>);
    // Redirect to correct page based on role
    window.location.href = <?= json_encode($redirectUrl) ?>;
</script>
</body>
</html>