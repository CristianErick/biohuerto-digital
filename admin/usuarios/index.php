<?php
/**
 * Listado de Usuarios — Panel Admin (CRUD completo)
 * Sin Emojis · Iconos SVG
 */
$rolRequerido = 1;
require_once __DIR__ . '/../../admin/includes/auth_check.php';
require_once __DIR__ . '/../../config/database.php';

$pdo = getConnection();

$msg     = $_SESSION['usuario_msg'] ?? null;
$msgType = $_SESSION['usuario_msg_type'] ?? 'success';
unset($_SESSION['usuario_msg'], $_SESSION['usuario_msg_type']);

$usuarios = $pdo->query('
    SELECT u.*, r.nombre AS rol_nombre
    FROM usuarios u
    INNER JOIN roles r ON u.rol_id = r.id
    ORDER BY u.rol_id ASC, u.created_at DESC
')->fetchAll();

$adminPageTitle = 'Gestión de Usuarios — Admin';
include __DIR__ . '/../../admin/includes/header.php';
?>

<div class="admin-page-header">
  <h2>Gestión de Usuarios y Docentes</h2>
  <a href="crear.php" class="btn btn--primary">
    <?= icon('plus', '', 16) ?> Nuevo Usuario
  </a>
</div>

<?php if ($msg): ?>
  <div class="alert-banner alert-banner--<?= $msgType === 'success' ? 'success' : 'danger' ?>">
    <span class="alert-banner__icon">
      <?= icon($msgType === 'success' ? 'check-circle' : 'alert-circle', '', 18) ?>
    </span>
    <div class="alert-banner__content"><?= htmlspecialchars($msg) ?></div>
  </div>
<?php endif; ?>

<?php if (empty($usuarios)): ?>
  <div class="admin-card">
    <div class="empty-state">
      <div class="empty-state__icon" style="color:var(--color-sky);">
        <?= icon('users', '', 48) ?>
      </div>
      <p class="empty-state__text">No hay usuarios registrados en el sistema.</p>
      <a href="crear.php" class="btn btn--primary">
        <?= icon('plus', '', 16) ?> Crear el primer usuario
      </a>
    </div>
  </div>
<?php else: ?>
  <div class="admin-table-wrapper">
    <table class="admin-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nombre Completo</th>
          <th>Correo Electrónico</th>
          <th>Especialidad</th>
          <th>Rol Asignado</th>
          <th>Estado</th>
          <th>Registro</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($usuarios as $u): ?>
          <tr>
            <td>#<?= $u['id'] ?></td>
            <td><strong><?= htmlspecialchars($u['nombre'] . ' ' . $u['apellido']) ?></strong></td>
            <td style="font-size:0.85rem;"><?= htmlspecialchars($u['email']) ?></td>
            <td><?= htmlspecialchars($u['especialidad'] ?? '—') ?></td>
            <td>
              <span style="display:inline-flex; align-items:center; gap:4px; background:<?= $u['rol_id'] == 1 ? '#ecfdf5;color:#047857' : '#e0f2fe;color:#0369a1' ?>;
                           padding:3px 10px; border-radius:12px; font-size:0.8rem; font-weight:700;">
                <?= icon($u['rol_id'] == 1 ? 'shield' : 'teacher', '', 13) ?>
                <span><?= htmlspecialchars($u['rol_nombre']) ?></span>
              </span>
            </td>
            <td>
              <span style="font-weight:700; font-size:0.85rem; color:<?= $u['activo'] ? 'var(--color-primary)' : 'var(--color-rose)' ?>;">
                <?= $u['activo'] ? 'Activo' : 'Inactivo' ?>
              </span>
            </td>
            <td style="font-size:0.8rem; white-space:nowrap;">
              <?= date('d/m/Y', strtotime($u['created_at'])) ?>
            </td>
            <td>
              <div style="display:flex; gap:6px; flex-wrap:wrap;">
                <a href="editar.php?id=<?= $u['id'] ?>" class="btn btn--warning btn--sm" title="Editar">
                  <?= icon('edit', '', 14) ?> Editar
                </a>
                <?php if ($u['id'] != $usuarioId): ?>
                  <a href="eliminar.php?id=<?= $u['id'] ?>"
                     class="btn btn--danger btn--sm"
                     onclick="confirmDelete('¿Confirmas eliminar al usuario <?= htmlspecialchars(addslashes($u['nombre'])) ?>?', 'eliminar.php?id=<?= $u['id'] ?>'); return false;"
                     title="Eliminar">
                    <?= icon('trash', '', 14) ?>
                  </a>
                <?php endif; ?>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <p style="text-align:center; margin-top:16px; color:var(--color-text-muted); font-size:0.85rem;">
    Total: <strong><?= count($usuarios) ?></strong> usuario(s) registrados.
  </p>
<?php endif; ?>

<?php include __DIR__ . '/../../admin/includes/footer.php'; ?>
