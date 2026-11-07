<?php
/**
 * Header del Panel de Administración — Semillitas del Futuro
 * Sin Emojis · Iconos SVG
 */

$adminPageTitle = $adminPageTitle ?? 'Admin — Semillitas del Futuro';
$adminExtraCSS  = $adminExtraCSS  ?? [];

$adminBase = '';
$scriptPath = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
if (preg_match('#/admin/.+#', $scriptPath)) {
    $adminBase = '../';
}
$siteBase = $adminBase ? '../../' : '../';

require_once __DIR__ . '/../../includes/icons.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
  <title><?= htmlspecialchars($adminPageTitle) ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@600;700;800&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= $siteBase ?>assets/css/styles.css">
  <link rel="stylesheet" href="<?= $adminBase ?>css/admin.css">
  <?php foreach ($adminExtraCSS as $css): ?>
  <link rel="stylesheet" href="<?= htmlspecialchars($css) ?>">
  <?php endforeach; ?>
</head>
<body class="admin-body">

  <!-- ═══ TOPBAR ADMIN ═══ -->
  <header class="admin-topbar">
    <div class="admin-topbar__left">
      <a href="<?= $adminBase ?>index.php" class="admin-topbar__brand">
        <span class="admin-topbar__icon">
          <?= icon('shield', '', 20) ?>
        </span>
        <span class="admin-topbar__title">Semillitas <small>Admin</small></span>
      </a>
    </div>
    <div class="admin-topbar__right">
      <span class="admin-topbar__user">
        <?= icon('user', '', 16) ?>
        <span><?= htmlspecialchars($usuarioNombre ?? 'Admin') ?></span>
        <span class="admin-topbar__badge"><?= htmlspecialchars($rolNombre ?? 'Administrador') ?></span>
      </span>
      <a href="<?= $siteBase ?>index.php" class="admin-topbar__link" title="Ver sitio público">
        <?= icon('home', '', 16) ?>
      </a>
      <a href="<?= $siteBase ?>auth/logout.php" class="admin-topbar__link admin-topbar__logout" title="Cerrar sesión">
        <?= icon('log-out', '', 16) ?> Salir
      </a>
    </div>
  </header>

  <!-- ═══ NAV ADMIN ═══ -->
  <nav class="admin-nav">
    <button class="admin-nav__toggle" aria-expanded="false" id="adminNavToggle">
      <?= icon('menu', '', 18) ?> Menú de Gestión
    </button>
    <ul class="admin-nav__menu">
      <li>
        <a href="<?= $adminBase ?>index.php" class="admin-nav__link">
          <?= icon('home', '', 16) ?> Dashboard
        </a>
      </li>
      <li>
        <a href="<?= $adminBase ?>plantas/index.php" class="admin-nav__link">
          <?= icon('sprout', '', 16) ?> Plantas
        </a>
      </li>
      <li>
        <a href="<?= $adminBase ?>usuarios/index.php" class="admin-nav__link">
          <?= icon('users', '', 16) ?> Usuarios
        </a>
      </li>
      <li>
        <a href="<?= $siteBase ?>repositorio/index.php" class="admin-nav__link">
          <?= icon('folder', '', 16) ?> Repositorio
        </a>
      </li>
      <li>
        <a href="<?= $siteBase ?>biblioteca/index.php" class="admin-nav__link">
          <?= icon('book', '', 16) ?> Biblioteca
        </a>
      </li>
      <li>
        <a href="<?= $siteBase ?>index.php" class="admin-nav__link">
          <?= icon('home', '', 16) ?> Ver Sitio
        </a>
      </li>
    </ul>
  </nav>

  <!-- ═══ CONTENIDO PRINCIPAL ═══ -->
  <main class="admin-content">
