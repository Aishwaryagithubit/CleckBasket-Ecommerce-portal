<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sign Up — CleckBasket</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@100;400;500;700;900&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="/cleckbasket/assets/css/signup.css" />
</head>
<body>
    <?php include __DIR__ . '/../header.php'; ?>

    <main class="auth-main">
    <!-- Background Geometric Decorations -->
    <div class="bg-decor-1"></div>
    <div class="bg-decor-2"></div>
    <div class="bg-decor-3"></div>

    <div class="auth-container">
        <!-- Left Content: Brand Identity -->
        <div class="auth-left">
            <div class="brand-text-container">
                <h1 class="brand-title">CleckBasket</h1>
                <p class="brand-subtitle">SHOP LOCAL &bull; EAT FRESH</p>
            </div>

            <div class="brand-message-container">
                <h2 class="brand-message">Connecting your kitchen to <span class="underline-text">local harvests</span>.
                </h2>
                <p class="brand-description">Access the season's finest produce, dairy, and artisanal goods directly
                    from farmers in your community.</p>
            </div>

            <div class="brand-image-container">
                <div class="image-backdrop"></div>
                <img src="/cleckbasket/assets/images/basketvegis.PNG" alt="Basket of Vegetables" class="brand-image">
            </div>

            <div class="brand-footnote">
                <div class="footnote-line">ESTABLISHED 2024</div>
                <div class="footnote-line black-text">SUPPORTING 450+<br>LOCAL FARMERS</div>
            </div>
        </div>

        <!-- Right Content: Sign-In Card -->
        <div class="auth-right">
            <div class="auth-card">
                <div class="card-header">
                    <h3 class="card-title">SIGN IN
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="login-icon">
                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                            <polyline points="10 17 15 12 10 7"></polyline>
                            <line x1="15" y1="12" x2="3" y2="12"></line>
                        </svg>
                    </h3>
                    <p class="card-subtitle">Please enter your details to sign in.</p>
                </div>

                <form class="auth-form" action="" method="post">
                    <div class="form-group">
                        <label for="email">EMAIL ADDRESS</label>
                        <input type="email" id="email" name="email" placeholder="farmer@clickbasket.com" required>
                    </div>

                    <div class="form-group">
                        <div class="password-labels">
                            <label for="password">PASSWORD</label>
                            <a href="#" class="forgot-password">FORGOT PASSWORD?</a>
                        </div>
                        <div class="password-input-wrap">
                            <input type="password" id="password" name="password"
                                placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;" required>
                            <button type="button" class="toggle-password" aria-label="Toggle password visibility">
                                <svg width="18" height="13" viewBox="0 0 18 13" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M9 0.5C5 0.5 1.73 3.11 0.5 6.5C1.73 9.89 5 12.5 9 12.5C13 12.5 16.27 9.89 17.5 6.5C16.27 3.11 13 0.5 9 0.5ZM9 10.5C6.79 10.5 5 8.71 5 6.5C5 4.29 6.79 2.5 9 2.5C11.21 2.5 13 4.29 13 6.5C13 8.71 11.21 10.5 9 10.5ZM9 4.5C7.9 4.5 7 5.4 7 6.5C7 7.6 7.9 8.5 9 8.5C10.1 8.5 11 7.6 11 6.5C11 5.4 10.1 4.5 9 4.5Z"
                                        fill="#83746C" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn-primary">Sign In</button>

                    <div class="divider">
                        <span>OR CONTINUE WITH</span>
                    </div>

                    <button type="button" class="btn-google">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M22.56 12.25C22.56 11.47 22.49 10.72 22.36 10H12V14.26H17.92C17.66 15.63 16.88 16.81 15.71 17.59V20.34H19.28C21.36 18.42 22.56 15.6 22.56 12.25Z"
                                fill="#4285F4" />
                            <path
                                d="M12 23C14.97 23 17.46 22.02 19.28 20.34L15.71 17.59C14.72 18.25 13.46 18.66 12 18.66C9.17 18.66 6.77 16.75 5.88 14.18H2.21V17.03C4.01 20.61 7.74 23 12 23Z"
                                fill="#34A853" />
                            <path
                                d="M5.88 14.18C5.65 13.52 5.52 12.78 5.52 12C5.52 11.22 5.65 10.48 5.88 9.82V6.97H2.21C1.46 8.46 1 10.18 1 12C1 13.82 1.46 15.54 2.21 17.03L5.88 14.18Z"
                                fill="#FBBC05" />
                            <path
                                d="M12 5.34C13.62 5.34 15.07 5.9 16.21 6.99L19.36 3.84C17.46 2.07 14.97 1 12 1C7.74 1 4.01 3.39 2.21 6.97L5.88 9.82C6.77 7.25 9.17 5.34 12 5.34Z"
                                fill="#EA4335" />
                        </svg>
                        Google
                    </button>
                </form>

                <div class="card-footer">
                    Don't have an account? <a href="/cleckbasket/includes/pages/signup.php" class="link-signup">Sign
                        Up</a>
                </div>
            </div>
        </div>
    </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleBtn = document.querySelector('.toggle-password');
            const passwordInput = document.getElementById('password');

            if (toggleBtn && passwordInput) {
                toggleBtn.addEventListener('click', function () {
                    if (passwordInput.type === 'password') {
                        passwordInput.type = 'text';
                    } else {
                        passwordInput.type = 'password';
                    }
                });
            }
        });
    </script>

    <?php include __DIR__ . '/../footer.php'; ?>
</body>
</html>