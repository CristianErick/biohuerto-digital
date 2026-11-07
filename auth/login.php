<?php
/**
 * Iniciar Sesión — Semillitas del Futuro
 * Acceso para Docentes y Administradores · Sin Emojis · Iconos SVG
 */
session_start();

if (isset($_SESSION['usuario_id'])) {
    $redir = ($_SESSION['rol_id'] == 1) ? '../admin/index.php' : '../repositorio/mis_materiales.php';
    header('Location: ' . $redir);
    exit;
}

require_once __DIR__ . '/../includes/icons.php';

$error = $_SESSION['login_error'] ?? null;
unset($_SESSION['login_error']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
  <title>Acceso Institucional — Semillitas del Futuro</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@600;700;800&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/styles.css">
  <style>
    .login-page {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
      background: #f0fdf4;
    }
    .login-card {
      background: #ffffff;
      border: 2px solid var(--color-border);
      border-radius: 24px;
      box-shadow: 0 16px 40px -8px rgba(15, 23, 42, 0.1);
      padding: 40px 32px;
      width: 100%;
      max-width: 420px;
      text-align: center;
    }
    .login-badge {
      width: 64px;
      height: 64px;
      border-radius: 20px;
      background: var(--color-primary-soft);
      color: var(--color-primary);
      display: inline-flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 16px;
      border: 2px solid var(--color-primary);
    }
    .login-title {
      font-family: var(--font-heading);
      font-size: 1.8rem;
      color: var(--color-text);
      margin-bottom: 6px;
    }
    .login-sub {
      font-size: 0.95rem;
      color: var(--color-text-muted);
      margin-bottom: 32px;
    }
  </style>
</head>
<body>
  <div class="login-page">
    <div class="login-card">
      <div class="login-badge">
        <?= icon('lock', '', 28) ?>
      </div>
      <h1 class="login-title">Acceso Docente</h1>
      <p class="login-sub">Ingresa a tu cuenta para gestionar materiales y el huerto escolar</p>

      <?php if ($error): ?>
        <div class="alert-banner alert-banner--danger" style="text-align: left; margin-bottom: 24px;">
          <span class="alert-banner__icon"><?= icon('alert-circle', '', 18) ?></span>
          <div class="alert-banner__content"><?= htmlspecialchars($error) ?></div>
        </div>
      <?php endif; ?>

      <form action="procesar_login.php" method="POST" autocomplete="on">
        <div class="form-group">
          <label for="email">
            <?= icon('user', '', 18) ?>
            <span>Correo institucional</span>
          </label>
          <input type="email" id="email" name="email" class="form-control" required
                 placeholder="docente@semillitas.edu"
                 value="<?= htmlspecialchars($_GET['email'] ?? '') ?>"
                 autocomplete="email">
        </div>

        <div class="form-group">
          <label for="password">
            <?= icon('key', '', 18) ?>
            <span>Contraseña</span>
          </label>
          <input type="password" id="password" name="password" class="form-control" required
                 placeholder="Ingresa tu contraseña"
                 autocomplete="current-password">
        </div>

        <button type="submit" class="btn btn--primary btn--full">
          <?= icon('lock', '', 18) ?>
          <span>Iniciar Sesión</span>
        </button>
      </form>

      <a href="../index.php" class="btn btn--outline btn--full" style="margin-top: 12px;">
        <?= icon('arrow-left', '', 16) ?>
        <span>Volver a la página principal</span>
      </a>
    </div>
  </div>
</body>
</html>
