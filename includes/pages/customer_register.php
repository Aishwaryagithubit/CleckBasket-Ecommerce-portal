<?php
ob_start();
error_reporting(0);
require_once '../../backend/connect.php';
require_once '../../backend/send_otp_email.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    $name = trim($_POST['text'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validate input
    if (!$name || !$phone || !$email || !$password || !$confirm_password) {
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

    // Split name into firstname and lastname
    $name_parts = explode(' ', $name, 2);
    $firstname = $name_parts[0];
    $lastname = isset($name_parts[1]) ? $name_parts[1] : '';

    // Hash password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Generate verification code
    $verification_code = strtoupper(bin2hex(random_bytes(3)));

    // Insert new user
    $insert_sql = "INSERT INTO users (firstname, lastname, email, contact_no, password_hash, verification_code, role, status)
                   VALUES (:firstname, :lastname, :email, :contact_no, :password_hash, :verification_code, 'CUSTOMER', 'ACTIVE')";

    $stmt = oci_parse($conn, $insert_sql);
    oci_bind_by_name($stmt, ':firstname', $firstname);
    oci_bind_by_name($stmt, ':lastname', $lastname);
    oci_bind_by_name($stmt, ':email', $email);
    oci_bind_by_name($stmt, ':contact_no', $phone);
    oci_bind_by_name($stmt, ':password_hash', $password_hash);
    oci_bind_by_name($stmt, ':verification_code', $verification_code);

    if (oci_execute($stmt)) {
        oci_commit($conn);
        oci_free_statement($stmt);
        oci_close($conn);

        // Send OTP email
        sendOtpEmail($email, $firstname, $verification_code);

        echo json_encode(['success' => true, 'message' => 'Registration successful', 'email' => $email]);
        exit;
    } else {
        $error = oci_error($stmt);
        oci_free_statement($stmt);
        oci_close($conn);
        echo json_encode(['success' => false, 'message' => 'Registration failed: ' . $error['message']]);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Registration — CleckBasket</title>
    
    <!-- Link to external fonts and stylesheets -->
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
          
            <h1>Customer Registration</h1>
                <p class="subtitle">Are you a Trader? Register as a Trader <a href="/cleckbasket/includes/pages/trader_register.php" class="link-green">here</a>.</p>
        </div>


        <div class="register-hero">
            <div class="registration-container">
                

                <form class="register-form" id="registerForm">
                    <div class="form-group">
                        <label for="text">Name</label>
                        <input type="text" id="text" name="text" placeholder="Enter your name" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" placeholder="Enter your phone number" required>
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
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="Re-enter your password" required>
                    </div>

                    <div class="form-checkbox">
                        <input type="checkbox" id="terms" name="terms" required>
                        <div class="checkbox-text">
                            <label for="terms">I agree to terms and conditions.</label>
                            <a href="#" class="read-terms">Read Terms</a>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit">Register</button>
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
            const email = document.getElementById('email').value;

            if (password !== confirmPassword) {
                alert('Passwords do not match!');
                return;
            }

            const formData = new FormData(this);

            fetch('/cleckbasket/includes/pages/customer_register.php', {
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
