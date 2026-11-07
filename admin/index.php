<?php
/**
 * Dashboard Admin — Semillitas del Futuro
 * Sin Emojis · Iconos SVG
 */
$rolRequerido = 1; // Solo Admin
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/../config/database.php';

$pdo = getConnection();

// Estadísticas
$totalPlantas    = $pdo->query('SELECT COUNT(*) FROM plantas')->fetchColumn();
$totalUsuarios   = $pdo->query('SELECT COUNT(*) FROM usuarios')->fetchColumn();
$totalDocentes   = $pdo->query('SELECT COUNT(*) FROM usuarios WHERE rol_id = 2')->fetchColumn();
$totalMateriales = $pdo->query('SELECT COUNT(*) FROM materiales')->fetchColumn();

$adminPageTitle = 'Dashboard — Admin';
include __DIR__ . '/includes/header.php';
?>

<div class="admin-page-header">
  <div style="display: flex; align-items: center; gap: 8px;">
    <h2 style="margin: 0;">Panel de Administración</h2>
  </div>
  <span style="color:var(--color-text-muted); font-size:0.85rem;">
    Sesión: <strong><?= htmlspecialchars($usuarioNombre) ?></strong>
  </span>
</div>

<!-- Estadísticas -->
<div class="stats-grid">
  <div class="stat-card">
    <div class="stat-card__icon" style="color:var(--color-primary);">
      <?= icon('sprout', '', 32) ?>
    </div>
    <div class="stat-card__number"><?= (int)$totalPlantas ?></div>
    <div class="stat-card__label">Plantas Registradas</div>
  </div>

  <div class="stat-card">
    <div class="stat-card__icon" style="color:var(--color-sky);">
      <?= icon('users', '', 32) ?>
    </div>
    <div class="stat-card__number"><?= (int)$totalUsuarios ?></div>
    <div class="stat-card__label">Usuarios Totales</div>
  </div>

  <div class="stat-card">
    <div class="stat-card__icon" style="color:var(--color-indigo);">
      <?= icon('teacher', '', 32) ?>
    </div>
    <div class="stat-card__number"><?= (int)$totalDocentes ?></div>
    <div class="stat-card__label">Docentes Activos</div>
  </div>

  <div class="stat-card">
    <div class="stat-card__icon" style="color:var(--color-amber);">
      <?= icon('file-text', '', 32) ?>
    </div>
    <div class="stat-card__number"><?= (int)$totalMateriales ?></div>
    <div class="stat-card__label">Materiales Escolares</div>
  </div>
</div>

<!-- Accesos rápidos -->
<div class="admin-card">
  <h3 class="admin-card__title" style="display: flex; align-items: center; gap: 8px;">
    <?= icon('shield', '', 18) ?>
    <span>Accesos Rápidos del Administrador</span>
  </h3>
  <div style="display:flex; gap:12px; flex-wrap:wrap;">
    <a href="plantas/crear.php" class="btn btn--primary">
      <?= icon('plus', '', 16) ?> Nueva Planta con QR
    </a>
    <a href="plantas/index.php" class="btn btn--outline">
      <?= icon('sprout', '', 16) ?> Gestión de Plantas
    </a>
    <a href="usuarios/index.php" class="btn btn--secondary">
      <?= icon('users', '', 16) ?> Gestionar Usuarios
    </a>
    <a href="../repositorio/mis_materiales.php?ver=todos" class="btn btn--warning">
      <?= icon('folder', '', 16) ?> Auditar Materiales
    </a>
    <a href="../biblioteca/index.php" class="btn btn--outline">
      <?= icon('book', '', 16) ?> Biblioteca Pedagógica
    </a>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
