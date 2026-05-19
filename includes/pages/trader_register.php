<?php
ob_start();
error_reporting(0);
require_once '../../backend/connect.php';
require_once '../../backend/send_otp_email.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    $shop_name = trim($_POST['shop_name'] ?? '');
    $seller_category = trim($_POST['seller_category'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validate input
    if (!$shop_name || !$seller_category || !$email || !$password || !$confirm_password) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit;
    }

    if ($password !== $confirm_password) {
        echo json_encode(['success' => false, 'message' => 'Passwords do not match']);
        exit;
    }

    if (strlen($password) < 6) {
        echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters']);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email address']);
        exit;
    }

    $conn = getDBConnection();
    if (!$conn) {
        echo json_encode(['success' => false, 'message' => 'Database connection failed']);
        exit;
    }

    // Check if email already exists
    $check_sql = "SELECT COUNT(*) as cnt FROM users WHERE email = :email";
    $stmt = oci_parse($conn, $check_sql);
    oci_bind_by_name($stmt, ':email', $email);
    oci_execute($stmt);
    $row = oci_fetch_assoc($stmt);

    if ($row['CNT'] > 0) {
        oci_free_statement($stmt);
        oci_close($conn);
        echo json_encode(['success' => false, 'message' => 'Email already registered']);
        exit;
    }
    oci_free_statement($stmt);

    // Hash password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Generate verification code
    $verification_code = strtoupper(bin2hex(random_bytes(3)));

    // Insert new trader user — status PENDING until admin approves
    $insert_user_sql = "INSERT INTO users (firstname, lastname, email, contact_no, password_hash, verification_code, role, status)
                        VALUES (:firstname, :lastname, :email, '', :password_hash, :verification_code, 'TRADER', 'PENDING')";

    $stmt = oci_parse($conn, $insert_user_sql);
    oci_bind_by_name($stmt, ':firstname', $seller_category);
    oci_bind_by_name($stmt, ':lastname', $shop_name);
    oci_bind_by_name($stmt, ':email', $email);
    oci_bind_by_name($stmt, ':password_hash', $password_hash);
    oci_bind_by_name($stmt, ':verification_code', $verification_code);

    if (!oci_execute($stmt)) {
        $error = oci_error($stmt);
        oci_free_statement($stmt);
        oci_close($conn);
        echo json_encode(['success' => false, 'message' => 'User registration failed: ' . $error['message']]);
        exit;
    }

    // Get the newly inserted user_id
    $get_user_id = "SELECT user_id FROM users WHERE email = :email";
    $stmt2 = oci_parse($conn, $get_user_id);
    oci_bind_by_name($stmt2, ':email', $email);
    oci_execute($stmt2);
    $user_row = oci_fetch_assoc($stmt2);
    $user_id = $user_row['USER_ID'];
    oci_free_statement($stmt2);

    // Insert shop record
    $insert_shop_sql = "INSERT INTO shop (shop_name, user_id, description)
                        VALUES (:shop_name, :user_id, :description)";

    $stmt = oci_parse($conn, $insert_shop_sql);
    oci_bind_by_name($stmt, ':shop_name', $shop_name);
    oci_bind_by_name($stmt, ':user_id', $user_id);
    $description = "Shop in $seller_category category";
    oci_bind_by_name($stmt, ':description', $description);

    if (oci_execute($stmt)) {
        oci_commit($conn);
        oci_free_statement($stmt);
        oci_close($conn);

        // Send OTP email — for traders firstname holds the category, use shop_name as display name
        sendOtpEmail($email, $shop_name, $verification_code);

        echo json_encode(['success' => true, 'message' => 'Registration successful', 'email' => $email]);
        exit;
    } else {
        $error = oci_error($stmt);
        oci_free_statement($stmt);
        oci_close($conn);
        echo json_encode(['success' => false, 'message' => 'Shop creation failed: ' . $error['message']]);
        exit;
    }
}
?>
  <!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trader Registration — CleckBasket</title>

    <!-- Link to external fonts and stylesheets -->
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Using footer styles for the bottom bar -->
    <link rel="stylesheet" href="/cleckbasket/assets/css/footer.css">

    <!-- Specific styles for this registration page -->
    <link rel="stylesheet" href="/cleckbasket/assets/css/register.css">
</head>

<body>
        <?php include '../header.php'; ?>


    <div class="registration-page">
        <div class="logo-header">
         
            <h1>Trader Registration</h1>
            <p class="subtitle">Are you a Customer? Register as a Customer <a
                    href="/cleckbasket/includes/pages/customer_register.php" class="link-green">here</a>.</p>
        </div>


        <div class="register-hero">
            <div class="registration-container">
                <form class="register-form" id="registerForm">
                    
                                  
                    <div class="form-group">
                        <label for="seller_category">Seller Category</label>
                        <select id="seller_category" name="seller_category" required>
                            <option value="" disabled selected>Select your category</option>
                            <option value="butcher">Butcher</option>
                            <option value="bakery">Bakery</option>
                            <option value="fishmonger">Fishmonger</option>
                            <option value="greengrocer">Greengrocer</option>
                            <option value="delicatessen">Delicatessen</option>
                        </select>
                    </div>
                    <div class="form-group">
                                            <label for="shop_name">Shop Name</label>
                                            <input type="text" id="shop_name" name="shop_name" placeholder="Enter your shop name" required>
                                        </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="Enter your email" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Enter your password" required>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Confirm Password</label>
                        <input type="password" id="confirm_password" name="confirm_password"
                            placeholder="Re-enter your password" required>
                    </div>

                    <div class="form-checkbox">
                        <input type="checkbox" id="terms" name="terms" required>
                        <div class="checkbox-text">
                            <label for="terms">I agree to terms and conditions.</label>
                            <a href="#" class="read-terms">Read Terms</a>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit" >Register</button>
                </form>
            </div>

            <div class="register-visual" aria-hidden="true">
                <img src="/cleckbasket/assets/images/sidelayout.png" alt="Fresh groceries" loading="lazy">
            </div>
        </div>
    </div>

    <script>
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;

            if (password !== confirmPassword) {
                alert('Passwords do not match!');
                return;
            }

            const formData = new FormData(this);

            fetch('/cleckbasket/includes/pages/trader_register.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    localStorage.setItem('user_email', data.email);
                    alert('Registration successful! Please verify your account.');
                    window.location.href = '/cleckbasket/includes/pages/otp_verification.php';
                } else {
                    alert('Registration failed: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred during registration');
            });
        });
    </script>

    <div class="footer-bar custom-footer">
        <div class="footer-bar-container">
            <div class="footer-left">
                <img src="/cleckbasket/assets/images/logo.png" alt="Cleck Basket" />
            </div>

            <nav class="footer-center">
                <a href="#">PRIVACY POLICY</a>
                <a href="#">TERMS OF SERVICE</a>
                <a href="/cleckbasket/includes/pages/shippinginformation.html">PICK UP INFO</a>
                <a href="#">WHOLESALE</a>
            </nav>

            <div class="footer-right">
                <p>&copy; 2024 CLECKBASKET ORGANIC CURATORS.</p>
            </div>
        </div>
    </div>

</body>

</html>