<?php
session_start();

if (isset($_SESSION['user_id'])) {
    $role = $_SESSION['role'] ?? 'customer';
    if ($role === 'admin') {
        header('Location: /cleckbasket/includes/admin/index.php');
    } elseif ($role === 'trader') {
        header('Location: /cleckbasket/includes/pages/trader/traderdashboard.php');
    } else {
        header('Location: /cleckbasket/includes/pages/homepage.php');
    }
    exit;
}

$error   = isset($_GET['error'])   ? urldecode($_GET['error'])   : '';
$success = isset($_GET['success']) ? urldecode($_GET['success']) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sign In — CleckBasket</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="/cleckbasket/assets/css/login.css" />
</head>
<body>
  <?php include __DIR__ . '/../header.php'; ?>

  <main class="login-page">

    <section class="form-panel">
      <div class="form-card">

        <header class="form-header">
          <h1 class="form-header__title">Log In</h1>
          <p class="form-header__subtitle">Welcome back to CleckBasket.</p>
        </header>

        <?php if (!empty($error)): ?>
          <div style="background:#ffebee;color:#c62828;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:14px;">
            <?php echo htmlspecialchars($error); ?>
          </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
          <div style="background:#e8f5e9;color:#2e7d32;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:14px;">
            <?php echo htmlspecialchars($success); ?>
          </div>
        <?php endif; ?>

        <form id="loginForm" class="login-form" action="/cleckbasket/backend/login.php" method="POST" novalidate>

          <div class="field">
            <label class="field__label" for="email">EMAIL ADDRESS</label>
            <div class="field__wrap">
              <input type="email" id="email" name="email" class="field__input"
                placeholder="e.g. david@gmail.com" autocomplete="email" required />
            </div>
          </div>

          <div class="field">
            <label class="field__label" for="password">PASSWORD</label>
            <div class="field__wrap">
              <input type="password" id="password" name="password" class="field__input"
                placeholder="••••••••" autocomplete="current-password" required />
              <button type="button" class="field__toggle" id="togglePassword" aria-label="Show password">
                <svg class="eye-show" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                  <circle cx="12" cy="12" r="3"/>
                </svg>
                <svg class="eye-hide" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none">
                  <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/>
                  <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/>
                  <path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/>
                  <line x1="2" y1="2" x2="22" y2="22"/>
                </svg>
              </button>
            </div>
          </div>

          <div class="form-meta">
            <label class="checkbox-label">
              <input type="checkbox" id="rememberMe" name="rememberMe" class="checkbox-input" />
              <span class="checkbox-custom"></span>
              <span class="checkbox-text">Remember me</span>
            </label>
            <a href="/cleckbasket/includes/pages/forgot_password.php" class="forgot-link">Forgot password?</a>
          </div>

          <button type="submit" class="btn-login" id="loginBtn">
            <span class="btn-login__text">LOG IN</span>
            <span class="btn-login__loader" aria-hidden="true"></span>
          </button>

        </form>

        <p class="signup-prompt">
          Don't have an account?
          <a href="/cleckbasket/includes/pages/customer_register.php" class="signup-link">Register as Customer</a>
          &nbsp;|&nbsp;
          <a href="/cleckbasket/includes/pages/trader_register.php" class="signup-link">Register as Trader</a>
        </p>

      </div>
    </section>

    <aside class="brand-panel" aria-hidden="true">
      <div class="brand-panel__bg"></div>
      <div class="brand-panel__content">
        <div class="badge-seasonal">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
          </svg>
          SEASONAL PICKS
        </div>
        <div class="sourcing-card">
          <div class="sourcing-card__icon">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M12 2s8 4 8 10V22L12 19l-8 3v-10c0-6 8-10 8-10z"/>
              <path d="M12 2v17"/>
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
      const toggle   = document.getElementById('togglePassword');
      const password = document.getElementById('password');
      const eyeShow  = document.querySelector('.eye-show');
      const eyeHide  = document.querySelector('.eye-hide');

      if (toggle && password) {
        toggle.addEventListener('click', function () {
          const isPass = password.type === 'password';
          password.type = isPass ? 'text' : 'password';
          eyeShow.style.display = isPass ? 'none' : 'block';
          eyeHide.style.display = isPass ? 'block' : 'none';
        });
      }

      // IMPORTANT: Do NOT add e.preventDefault() here
      // Let the form submit normally to backend/login.php
      const loginForm = document.getElementById('loginForm');
      const loginBtn  = document.getElementById('loginBtn');
      if (loginForm && loginBtn) {
        loginForm.addEventListener('submit', function () {
          loginBtn.disabled = true;
          const text   = loginBtn.querySelector('.btn-login__text');
          const loader = loginBtn.querySelector('.btn-login__loader');
          if (text)   text.textContent = 'Signing In...';
          if (loader) loader.style.display = 'inline-block';
        });
      }
    })();
  </script>

  <?php include __DIR__ . '/../footer.php'; ?>
</body>
</html>