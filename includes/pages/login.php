<?php /* Login Page - Frontend Only */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sign In — FreshCart</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,wght@0,400;0,600;0,700;1,400&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="/cleckbasket/assets/css/login.css" />
</head>
<body>
  <?php include __DIR__ . '/../header.php'; ?>

  <!-- Login Page Wrapper -->
  <!-- Login Page Wrapper -->
  <main class="login-page">

    <!-- Left Panel — Login Form -->
    <section class="form-panel">
      <div class="form-card">

        <!-- Mobile Logo -->
        <div class="mobile-logo" aria-hidden="true">
          <span>🛒</span> <span>FreshCart</span>
        </div>

        <!-- Welcome -->
        <header class="form-header">
          <h1 class="form-header__title">
            Log in 
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:bottom; margin-left: 4px; margin-bottom: 2px;">
              <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4M10 17l5-5-5-5M15 12H3"/>
            </svg>
          </h1>
          <p class="form-header__subtitle">Welcome back to your curated organic pantry.</p>
        </header>

        <!-- Login Form -->
        <form id="loginForm" class="login-form" novalidate>

          <!-- Email -->
          <div class="field" id="emailField">
            <label class="field__label" for="email">EMAIL ADDRESS</label>
            <div class="field__wrap">
              <input
                type="email"
                id="email"
                name="email"
                class="field__input"
                placeholder="e.g. curator@organic.com"
                autocomplete="email"
                required
              />
            </div>
            <span class="field__error" id="emailError" role="alert"></span>
          </div>

          <!-- Password -->
          <div class="field" id="passwordField">
            <label class="field__label" for="password">PASSWORD</label>
            <div class="field__wrap">
              <input
                type="password"
                id="password"
                name="password"
                class="field__input field__input--password"
                placeholder="••••••••"
                autocomplete="current-password"
                required
              />
              <button type="button" class="field__toggle" id="togglePassword" aria-label="Show password">
                <svg class="eye-show" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                <svg class="eye-hide" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" y1="2" x2="22" y2="22"/></svg>
              </button>
            </div>
            <span class="field__error" id="passwordError" role="alert"></span>
          </div>

          <!-- Remember + Forgot -->
          <div class="form-meta">
            <label class="checkbox-label">
              <input type="checkbox" id="rememberMe" name="rememberMe" class="checkbox-input" />
              <span class="checkbox-custom"></span>
              <span class="checkbox-text">Remember me</span>
            </label>
            <a href="forgot-password.php" class="forgot-link">Forgot password?</a>
          </div>

          <!-- Submit -->
          <button type="submit" class="btn-login" id="loginBtn">
            <span class="btn-login__text">LOG IN</span>
            <span class="btn-login__loader" aria-hidden="true"></span>
          </button>

        </form>

        <!-- Signup Redirect -->
        <p class="signup-prompt">
          Don't have an account? 
          <a href="signup.php" class="signup-link">Signup</a>
        </p>

      </div>
    </section>

    <!-- Right Panel — Brand Visual -->
    <aside class="brand-panel" aria-hidden="true">
      <div class="brand-panel__bg"></div>
      <div class="brand-panel__content">
        
        <!-- Top Right Badge -->
        <div class="badge-seasonal">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg> 
          SEASONAL PICKS
        </div>

        <!-- Bottom Left Info Card -->
        <div class="sourcing-card">
          <div class="sourcing-card__icon">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 2s8 4 8 10V22L12 19l-8 3v-10c0-6 8-10 8-10z"></path>
                <path d="M12 2v17"></path>
            </svg>
          </div>
          <div class="sourcing-card__text">
            <span class="sourcing-card__label">SOURCING</span>
            <h3 class="sourcing-card__title">100% Certified<br/>Local</h3>
            <p class="sourcing-card__desc">Every item is hand-picked from<br/>seasonal harvests within 50 miles.</p>
          </div>
        </div>

      </div>
    </aside>

  </main>

  <script>
    (function () {
      const toggle = document.getElementById('togglePassword');
      const password = document.getElementById('password');
      const eyeShow = document.querySelector('.eye-show');
      const eyeHide = document.querySelector('.eye-hide');
      const loginForm = document.getElementById('loginForm');
      const loginBtn = document.getElementById('loginBtn');
      const loginText = loginBtn ? loginBtn.querySelector('.btn-login__text') : null;
      const loginLoader = loginBtn ? loginBtn.querySelector('.btn-login__loader') : null;

      if (toggle && password && eyeShow && eyeHide) {
        toggle.addEventListener('click', function () {
          const isPassword = password.type === 'password';
          password.type = isPassword ? 'text' : 'password';
          toggle.setAttribute('aria-label', isPassword ? 'Hide password' : 'Show password');
          eyeShow.style.display = isPassword ? 'none' : 'block';
          eyeHide.style.display = isPassword ? 'block' : 'none';
        });
      }

      if (loginForm && loginBtn && loginText && loginLoader) {
        loginForm.addEventListener('submit', function () {
          loginBtn.disabled = true;
          loginText.textContent = 'Signing In';
          loginLoader.style.display = 'inline-block';
        });
      }

      if (loginForm) {
        loginForm.addEventListener('submit', function (e) {
          e.preventDefault();
          const emailInput = document.getElementById('email');
          const emailValue = emailInput ? emailInput.value.trim() : '';
          const displayName = emailValue ? emailValue.split('@')[0] : 'User';
          localStorage.setItem('is_login', 'true');
          localStorage.setItem('user_name', displayName);
          window.location.href = '/cleckbasket/includes/pages/homepage.php';
        });
      }
    })();
  </script>

  <?php include __DIR__ . '/../footer.php'; ?>
</body>
</html>
