  <?php
// trader_register.php
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
                <form action="/cleckbasket/includes/pages/trader_register.php" method="POST" class="register-form">
                    
                                  
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
        document.querySelector('.register-form').addEventListener('submit', function (e) {
            e.preventDefault(); // Prevent standard form submission

            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const email = document.getElementById('email').value;

            if (password !== confirmPassword) {
                alert('Passwords do not match!');
                return;
            }

            // Save user email to localStorage (used later in profile or OTP)
            localStorage.setItem('user_email', email);

            // Simulate successful registration process and redirect to OTP Verification page
            window.location.href = '/cleckbasket/includes/pages/otp_verification.php';
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
                <a href="#">SHIPPING INFO</a>
                <a href="#">WHOLESALE</a>
            </nav>

            <div class="footer-right">
                <p>&copy; 2024 CLECKBASKET ORGANIC CURATORS.</p>
            </div>
        </div>
    </div>

</body>

</html>