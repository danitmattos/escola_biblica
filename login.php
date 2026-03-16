<?php
session_start();

// Se já estiver autenticado, redireciona direto
if (isset($_SESSION['usuario'])) {
    header('Location: index.php');
    exit();
}

// Erro vindo de autenticacao.php via query string
$erro = isset($_GET['erro']) ? htmlspecialchars($_GET['erro'], ENT_QUOTES, 'UTF-8') : '';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login — Escola Bíblica</title>
  <link rel="stylesheet" href="style.css" />
  <style>
    /* Ícones simples via SVG inline (sem dependência externa) */
    .icon { width: 20px; height: 20px; fill: currentColor; vertical-align: middle; }

    /* Marca/logo */
    .brand-icon {
      width: 64px;
      height: 64px;
      background-color: var(--color-primary);
      border-radius: var(--radius-lg);
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto var(--space-4);
    }

    .brand-icon svg {
      width: 36px;
      height: 36px;
      fill: white;
    }

    .brand-name {
      font-size: var(--text-2xl);
      font-weight: 700;
      color: var(--color-gray-900);
      text-align: center;
    }

    .brand-tagline {
      font-size: var(--text-sm);
      color: var(--color-text-muted);
      text-align: center;
      margin-top: var(--space-1);
      margin-bottom: 0;
    }

    /* Campo com ícone */
    .input-wrapper {
      position: relative;
    }

    .input-wrapper .input-icon {
      position: absolute;
      left: var(--space-3);
      top: 50%;
      transform: translateY(-50%);
      color: var(--color-gray-400);
      display: flex;
      pointer-events: none;
    }

    .input-wrapper .form-control {
      padding-left: calc(var(--space-3) * 2 + 18px);
    }

    /* Botão mostrar/ocultar senha */
    .input-wrapper .toggle-password {
      position: absolute;
      right: var(--space-3);
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      color: var(--color-gray-400);
      cursor: pointer;
      display: flex;
      padding: 2px;
      border-radius: var(--radius-sm);
      transition: color var(--transition-fast);
    }

    .input-wrapper .toggle-password:hover {
      color: var(--color-gray-600);
    }

    /* Linha "Lembrar / Esqueceu" */
    .form-row-inline {
      display: flex;
      align-items: center;
      justify-content: space-between;
      font-size: var(--text-sm);
    }

    .checkbox-label {
      display: flex;
      align-items: center;
      gap: var(--space-2);
      cursor: pointer;
      color: var(--color-gray-600);
    }

    .checkbox-label input[type="checkbox"] {
      width: 16px;
      height: 16px;
      accent-color: var(--color-primary);
      cursor: pointer;
    }

    .link-forgot {
      color: var(--color-primary);
      font-size: var(--text-sm);
      font-weight: 500;
    }

    .link-forgot:hover {
      color: var(--color-primary-dark);
      text-decoration: underline;
    }

    /* Divisor com texto */
    .divider-text {
      display: flex;
      align-items: center;
      gap: var(--space-3);
      color: var(--color-text-muted);
      font-size: var(--text-xs);
      margin: var(--space-6) 0;
    }

    .divider-text::before,
    .divider-text::after {
      content: '';
      flex: 1;
      border-top: 1px solid var(--color-border);
    }

    /* Rodapé da auth-card */
    .auth-footer {
      text-align: center;
      font-size: var(--text-sm);
      color: var(--color-text-muted);
      margin-top: var(--space-6);
    }

    /* Feedback de erro geral */
    .login-error {
      display: none; /* JS mostra quando necessário */
    }

    .login-error.visible {
      display: flex;
    }
  </style>
</head>
<body>

  <main class="auth-page">
    <div class="auth-card">

      <!-- Logo / Marca -->
      <div class="auth-card__logo">
        <div class="brand-icon">
          <!-- Ícone de livro aberto -->
          <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 2C9.5 2 7.5 3 6 4.5 4.5 3 2.5 2 0 2v18c2.5 0 4.5 1 6 2.5C7.5 21 9.5 20 12 20c2.5 0 4.5 1 6 2.5C19.5 21 21.5 20 24 20V2c-2.5 0-4.5 1-6 2.5C16.5 3 14.5 2 12 2zm-1 15.5c-1.2-.8-2.7-1.3-5-1.5V5c2.3.2 3.8.7 5 1.5v11zm8 0c-2.3.2-3.8.7-5 1.5V6.5c1.2-.8 2.7-1.3 5-1.5v12.5z"/>
          </svg>
        </div>
        <div class="brand-name">Escola Bíblica</div>
        <p class="brand-tagline">Sistema de Gestão de Alunos</p>
      </div>

      <!-- Alerta de erro (PHP ou JS) -->
      <div class="alert alert-danger login-error<?php echo $erro ? ' visible' : ''; ?>" id="alertError" role="alert">
        <svg class="icon" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
        <span id="alertErrorMsg"><?php echo $erro ?: 'Usuário ou senha inválidos.'; ?></span>
      </div>

      <!-- Formulário -->
      <form id="loginForm" method="POST" action="autenticacao.php" novalidate>

        <!-- E-mail -->
        <div class="form-group">
          <label class="form-label" for="email">E-mail</label>
          <div class="input-wrapper">
            <span class="input-icon">
              <svg class="icon" viewBox="0 0 20 20"><path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/></svg>
            </span>
            <input
              class="form-control"
              type="email"
              id="email"
              name="usuario_email"
              placeholder="seu@email.com"
              autocomplete="username"
              required
            />
          </div>
          <span class="form-error" id="emailError"></span>
        </div>

        <!-- Senha -->
        <div class="form-group">
          <label class="form-label" for="password">Senha</label>
          <div class="input-wrapper">
            <span class="input-icon">
              <svg class="icon" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/></svg>
            </span>
            <input
              class="form-control"
              type="password"
              id="password"
              name="usuario_senha"
              placeholder="••••••••"
              autocomplete="current-password"
              required
            />
            <button type="button" class="toggle-password" id="togglePassword" aria-label="Mostrar senha">
              <!-- Ícone olho (aberto) -->
              <svg id="iconEyeOpen" class="icon" viewBox="0 0 20 20">
                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
              </svg>
              <!-- Ícone olho (fechado) -->
              <svg id="iconEyeClosed" class="icon" style="display:none" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd"/>
                <path d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.064 7 9.542 7 .847 0 1.669-.105 2.454-.303z"/>
              </svg>
            </button>
          </div>
          <span class="form-error" id="passwordError"></span>
        </div>

        <!-- Lembrar-me / Esqueceu a senha -->
        <div class="form-row-inline" style="margin-bottom: var(--space-6);">
          <label class="checkbox-label">
            <input type="checkbox" id="rememberMe" name="rememberMe" />
            Lembrar-me
          </label>
          <a href="#" class="link-forgot">Esqueceu a senha?</a>
        </div>

        <!-- Botão entrar -->
        <button type="submit" class="btn btn-primary w-full btn-lg" id="btnLogin">
          <span id="btnText">Entrar</span>
          <span id="btnSpinner" class="spinner" style="display:none; width:18px; height:18px; border-width:2px; border-color:rgba(255,255,255,.3); border-top-color:white;"></span>
        </button>

      </form>

      <div class="auth-footer">
        Não tem acesso? Fale com o
        <a href="#">administrador</a>.
      </div>

    </div>
  </main>

  <script>
    // --- Toggle mostrar/ocultar senha ---
    const toggleBtn     = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const iconOpen      = document.getElementById('iconEyeOpen');
    const iconClosed    = document.getElementById('iconEyeClosed');

    toggleBtn.addEventListener('click', () => {
      const isHidden = passwordInput.type === 'password';
      passwordInput.type       = isHidden ? 'text'  : 'password';
      iconOpen.style.display   = isHidden ? 'none'  : '';
      iconClosed.style.display = isHidden ? ''      : 'none';
    });

    // --- Validação client-side antes do POST ---
    const form          = document.getElementById('loginForm');
    const emailInput    = document.getElementById('email');
    const emailError    = document.getElementById('emailError');
    const passwordError = document.getElementById('passwordError');
    const alertError    = document.getElementById('alertError');
    const btnText       = document.getElementById('btnText');
    const btnSpinner    = document.getElementById('btnSpinner');
    const btnLogin      = document.getElementById('btnLogin');

    function setFieldError(input, errorEl, msg) {
      input.classList.toggle('is-invalid', !!msg);
      errorEl.textContent = msg;
    }

    function clearErrors() {
      setFieldError(emailInput, emailError, '');
      setFieldError(passwordInput, passwordError, '');
      alertError.classList.remove('visible');
    }

    function setLoading(loading) {
      btnLogin.disabled        = loading;
      btnText.style.display    = loading ? 'none' : '';
      btnSpinner.style.display = loading ? ''     : 'none';
    }

    form.addEventListener('submit', (e) => {
      clearErrors();

      const email    = emailInput.value.trim();
      const password = passwordInput.value;
      let valid = true;

      if (!email) {
        setFieldError(emailInput, emailError, 'Informe o e-mail.');
        valid = false;
      } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        setFieldError(emailInput, emailError, 'E-mail inválido.');
        valid = false;
      }

      if (!password) {
        setFieldError(passwordInput, passwordError, 'Informe a senha.');
        valid = false;
      } else if (password.length < 6) {
        setFieldError(passwordInput, passwordError, 'A senha deve ter pelo menos 6 caracteres.');
        valid = false;
      }

      if (!valid) {
        e.preventDefault();
        return;
      }

      // Mostra spinner enquanto o servidor processa
      setLoading(true);
    });
  </script>

</body>
</html>
