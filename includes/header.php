<?php
/**
 * Header reutilizable — Semillitas del Futuro
 * Incluir al inicio de cada página: <?php include __DIR__ . '/../includes/header.php'; ?>
 */

$pageTitle = $pageTitle ?? 'Semillitas del Futuro';
$pageDesc  = $pageDesc  ?? 'Plataforma educativa para niños de primaria y docentes. Biohuerto, ciencias y recursos pedagógicos.';
$extraCSS  = $extraCSS  ?? [];

// Ruta base
$basePath = $basePath ?? '';
if (empty($basePath) && isset($isSubfolder) && $isSubfolder) {
    $basePath = '../';
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/icons.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
  <meta name="description" content="<?= htmlspecialchars($pageDesc) ?>">
  <meta name="theme-color" content="#059669">
  <title><?= htmlspecialchars($pageTitle) ?></title>
  <link rel="icon" type="image/svg+xml" href="<?= $basePath ?>assets/img/favicon.svg">
  <link rel="shortcut icon" href="<?= $basePath ?>assets/img/favicon.svg">

  <!-- Google Fonts (Nunito + Baloo 2) -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@600;700;800&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">

  <!-- CSS Principal -->
  <link rel="stylesheet" href="<?= $basePath ?>assets/css/styles.css">

  <?php foreach ($extraCSS as $css): ?>
  <link rel="stylesheet" href="<?= htmlspecialchars($css) ?>">
  <?php endforeach; ?>
</head>
<body>

  <!-- ═══ HEADER / BARRA DE NAVEGACIÓN ═══ -->
  <header class="header">
    <div class="header__inner">

      <!-- Logo a la izquierda (visible siempre) -->
      <a href="<?= $basePath ?>index.php" class="header__brand desktop-logo">
        <span class="header__logo-badge" style="background:transparent; padding:0; overflow:hidden;">
          <img src="<?= $basePath ?>assets/img/logo-mascota.svg" alt="Semillitas Logo" style="width:38px; height:38px;">
        </span>
        <div>
          <h1 class="header__title">Semillitas</h1>
          <span class="header__subtitle">Educación Primaria</span>
        </div>
      </a>

      <!-- Botón móvil para menú (izquierda en móvil) -->
      <button class="nav__toggle" id="navToggle" aria-expanded="false" aria-controls="navMenu" aria-label="Abrir menú de navegación">
        <?= icon('menu', 'nav-icon-menu', 22) ?>
      </button>

      <!-- Enlaces de navegación -->
      <nav class="nav" aria-label="Menú principal">
        <ul class="nav__menu" id="navMenu" role="menubar">
          <li>
            <a class="nav__link" href="<?= $basePath ?>index.php" role="menuitem">
              <?= icon('home', '', 18) ?> Inicio
            </a>
          </li>
          <li>
            <a class="nav__link" href="<?= $basePath ?>biohuerto/index.php" role="menuitem">
              <?= icon('sprout', '', 18) ?> Biohuerto
            </a>
          </li>
          <li>
            <a class="nav__link" href="<?= $basePath ?>biblioteca/index.php" role="menuitem">
              <?= icon('book', '', 18) ?> Biblioteca
            </a>
          </li>
          <li>
            <a class="nav__link" href="<?= $basePath ?>juegos/index.php" role="menuitem">
              <?= icon('gamepad', '', 18) ?> Juegos
            </a>
          </li>
          <li>
            <a class="nav__link" href="<?= $basePath ?>repositorio/index.php" role="menuitem">
              <?= icon('folder', '', 18) ?> Repositorio
            </a>
          </li>

          <?php if (!empty($_SESSION['usuario_id'])): ?>
            <?php if ($_SESSION['rol_id'] == 1): ?>
              <li>
                <a class="nav__link nav__link--auth" href="<?= $basePath ?>admin/index.php" role="menuitem">
                  <?= icon('shield', '', 18) ?> Panel Admin
                </a>
              </li>
            <?php else: ?>
              <li>
                <a class="nav__link nav__link--auth" href="<?= $basePath ?>repositorio/mis_materiales.php" role="menuitem">
                  <?= icon('teacher', '', 18) ?> Mis Materiales
                </a>
              </li>
            <?php endif; ?>
            <li>
              <a class="nav__link" href="<?= $basePath ?>auth/logout.php" role="menuitem" title="Cerrar sesión">
                <?= icon('log-out', '', 18) ?> Salir
              </a>
            </li>
          <?php else: ?>
            <li>
              <a class="nav__link nav__link--ghost" href="<?= $basePath ?>auth/login.php" role="menuitem">
                <?= icon('lock', '', 16) ?> Iniciar Sesión
              </a>
            </li>
          <?php endif; ?>
        </ul>
      </nav>

      <!-- Logo a la derecha solo en móvil -->
      <a href="<?= $basePath ?>index.php" class="header__brand mobile-logo-right">
        <span class="header__logo-badge" style="background:transparent; padding:0; overflow:hidden;">
          <img src="<?= $basePath ?>assets/img/logo-mascota.svg" alt="Semillitas Logo" style="width:38px; height:38px;">
        </span>
        <div>
          <h1 class="header__title">Semillitas</h1>
          <span class="header__subtitle">Educación Primaria</span>
        </div>
      </a>
    </div>
  </header>
