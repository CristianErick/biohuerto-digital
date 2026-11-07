<?php
/**
 * Listado de Plantas — Panel Admin
 * Sin Emojis · Iconos SVG
 */
$rolRequerido = 1;
require_once __DIR__ . '/../../admin/includes/auth_check.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../libs/storage.php';

$pdo = getConnection();

$msg     = $_SESSION['planta_msg'] ?? null;
$msgType = $_SESSION['planta_msg_type'] ?? 'success';
unset($_SESSION['planta_msg'], $_SESSION['planta_msg_type']);

$plantas = $pdo->query('SELECT * FROM plantas ORDER BY fecha_registro DESC')->fetchAll();

$adminPageTitle = 'Gestión de Plantas — Admin';
include __DIR__ . '/../../admin/includes/header.php';
?>

<div class="admin-page-header">
  <h2>Gestión de Plantas y Códigos QR</h2>
  <div style="display:flex; gap:10px; flex-wrap:wrap;">
    <a href="crear.php" class="btn btn--primary">
      <?= icon('plus', '', 16) ?> Nueva Planta
    </a>
    <?php $esDocenteOAdmin = isset($_SESSION['usuario_id']) && in_array((int)$_SESSION['rol_id'], [1, 2]); ?>
    <?php if ($esDocenteOAdmin): ?>
      <a href="generar_pdf.php" class="btn btn--secondary">
        <?= icon('file-pdf', '', 16) ?> Generar Fichas PDF A4
      </a>
    <?php endif; ?>
  </div>
</div>

<?php if ($msg): ?>
  <div class="alert-banner alert-banner--<?= $msgType === 'success' ? 'success' : 'danger' ?>">
    <span class="alert-banner__icon">
      <?= icon($msgType === 'success' ? 'check-circle' : 'alert-circle', '', 18) ?>
    </span>
    <div class="alert-banner__content"><?= htmlspecialchars($msg) ?></div>
  </div>
<?php endif; ?>

<?php if (empty($plantas)): ?>
  <div class="admin-card">
    <div class="empty-state">
      <div class="empty-state__icon" style="color:var(--color-primary);">
        <?= icon('sprout', '', 48) ?>
      </div>
      <p class="empty-state__text">Aún no hay plantas registradas en el biohuerto.</p>
      <a href="crear.php" class="btn btn--primary">
        <?= icon('plus', '', 16) ?> Registrar la primera planta
      </a>
    </div>
  </div>
<?php else: ?>
  <div class="admin-table-wrapper">
    <table class="admin-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Foto</th>
          <th>Nombre</th>
          <th>Descripción</th>
          <th>Código QR</th>
          <th>Fecha</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($plantas as $planta): ?>
          <tr>
            <td><strong>#<?= $planta['id'] ?></strong></td>
            <td>
              <?php if ($planta['ruta_foto']): ?>
                <img src="<?= storage_url($planta['ruta_foto'], '../../') ?>"
                     alt="<?= htmlspecialchars($planta['nombre']) ?>"
                     class="thumb">
              <?php else: ?>
                <span style="color:var(--color-text-muted);">Sin foto</span>
              <?php endif; ?>
            </td>
            <td><strong><?= htmlspecialchars($planta['nombre']) ?></strong></td>
            <td>
              <?php
              $desc = $planta['descripcion'] ?? '';
              echo htmlspecialchars(mb_strlen($desc) > 80 ? mb_substr($desc, 0, 80) . '...' : $desc);
              ?>
            </td>
            <td>
              <?php if ($planta['ruta_qr']): ?>
                <?php $esDocenteOAdmin = isset($_SESSION['usuario_id']) && in_array((int)$_SESSION['rol_id'], [1, 2]); ?>
                <?php if ($esDocenteOAdmin): ?>
                  <a href="<?= storage_url($planta['ruta_qr'], '../../') ?>" target="_blank" title="Ver QR ampliado y descargar">
                    <img src="<?= storage_url($planta['ruta_qr'], '../../') ?>"
                         alt="QR" style="width:50px; height:50px; border-radius:4px; border:1px solid var(--color-border); padding:2px; background:#fff;">
                  </a>
                <?php else: ?>
                  <span style="color:var(--color-amber); font-weight:700; font-size:0.75rem;">QR (Solo docentes)</span>
                <?php endif; ?>
              <?php else: ?>
                <span style="color:var(--color-amber); font-weight:700;">Sin QR</span>
              <?php endif; ?>
            </td>
            <td style="font-size:0.8rem; white-space:nowrap;">
              <?= date('d/m/Y', strtotime($planta['fecha_registro'])) ?>
            </td>
            <td>
              <div style="display:flex; gap:6px; flex-wrap:wrap;">
                <a href="../../biohuerto/planta.php?id=<?= $planta['id'] ?>"
                   class="btn btn--secondary btn--sm" target="_blank" title="Ver ficha pública del alumno">
                  <?= icon('eye', '', 14) ?> Ver
                </a>
                <a href="editar.php?id=<?= $planta['id'] ?>"
                   class="btn btn--warning btn--sm" title="Editar">
                  <?= icon('edit', '', 14) ?>
                </a>
                <a href="eliminar.php?id=<?= $planta['id'] ?>"
                   class="btn btn--danger btn--sm"
                   onclick="confirmDelete('¿Confirmas eliminar la planta <?= htmlspecialchars(addslashes($planta['nombre'])) ?>?', 'eliminar.php?id=<?= $planta['id'] ?>'); return false;"
                   title="Eliminar">
                  <?= icon('trash', '', 14) ?>
                </a>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <p style="text-align:center; margin-top:16px; color:var(--color-text-muted); font-size:0.85rem;">
    Total: <strong><?= count($plantas) ?></strong> planta(s) en el biohuerto.
  </p>
<?php endif; ?>

<?php include __DIR__ . '/../../admin/includes/footer.php'; ?>
