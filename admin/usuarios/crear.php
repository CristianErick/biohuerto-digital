<?php
/**
 * Crear Usuario — Panel Admin
 * Sin Emojis · Iconos SVG
 */
$rolRequerido = 1;
require_once __DIR__ . '/../../admin/includes/auth_check.php';
require_once __DIR__ . '/../../config/database.php';

$pdo = getConnection();
$roles = $pdo->query('SELECT * FROM roles ORDER BY id')->fetchAll();

$msg     = $_SESSION['usuario_msg'] ?? null;
$msgType = $_SESSION['usuario_msg_type'] ?? 'error';
$old     = $_SESSION['usuario_old'] ?? [];
unset($_SESSION['usuario_msg'], $_SESSION['usuario_msg_type'], $_SESSION['usuario_old']);

$adminPageTitle = 'Nuevo Usuario — Admin';
include __DIR__ . '/../../admin/includes/header.php';
?>

<div class="admin-page-header">
  <h2>Crear Nuevo Usuario</h2>
  <a href="index.php" class="btn btn--outline btn--sm">
    <?= icon('arrow-left', '', 14) ?> Volver al listado
  </a>
</div>

<?php if ($msg): ?>
  <div class="alert-banner alert-banner--<?= $msgType === 'success' ? 'success' : 'danger' ?>">
    <span class="alert-banner__icon"><?= icon('alert-circle', '', 18) ?></span>
    <div class="alert-banner__content"><?= htmlspecialchars($msg) ?></div>
  </div>
<?php endif; ?>

<div class="admin-card">
  <form action="guardar.php" method="POST" class="admin-form">

    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(240px, 1fr)); gap:16px;">
      <div class="form-group">
        <label for="nombre">Nombre <span style="color:var(--color-rose);">*</span></label>
        <input type="text" id="nombre" name="nombre" class="form-control" required
               maxlength="100" value="<?= htmlspecialchars($old['nombre'] ?? '') ?>"
               placeholder="Nombre del usuario">
      </div>
      <div class="form-group">
        <label for="apellido">Apellido <span style="color:var(--color-rose);">*</span></label>
        <input type="text" id="apellido" name="apellido" class="form-control" required
               maxlength="100" value="<?= htmlspecialchars($old['apellido'] ?? '') ?>"
               placeholder="Apellido del usuario">
      </div>
    </div>

    <div class="form-group">
      <label for="email">Correo Institucional <span style="color:var(--color-rose);">*</span></label>
      <input type="email" id="email" name="email" class="form-control" required
             maxlength="150" value="<?= htmlspecialchars($old['email'] ?? '') ?>"
             placeholder="correo@semillitas.edu">
    </div>

    <div class="form-group">
      <label for="password">Contraseña Inicial <span style="color:var(--color-rose);">*</span></label>
      <input type="password" id="password" name="password" class="form-control" required
             minlength="6" placeholder="Mínimo 6 caracteres">
    </div>

    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(240px, 1fr)); gap:16px;">
      <div class="form-group">
        <label for="rol_id">Rol en el Sistema <span style="color:var(--color-rose);">*</span></label>
        <select id="rol_id" name="rol_id" class="form-control" required>
          <?php foreach ($roles as $rol): ?>
            <option value="<?= $rol['id'] ?>"
              <?= (($old['rol_id'] ?? 2) == $rol['id']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($rol['nombre']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label for="especialidad">Especialidad o Área Curricular</label>
        <input type="text" id="especialidad" name="especialidad" class="form-control"
               maxlength="100" value="<?= htmlspecialchars($old['especialidad'] ?? '') ?>"
               placeholder="Ej: Ciencias Naturales, Primaria General">
      </div>
    </div>

    <div class="form-group" style="margin-top:10px;">
      <label style="cursor:pointer; display:flex; align-items:center; gap:8px;">
        <input type="checkbox" name="activo" value="1" checked>
        <span>Usuario activo en la plataforma</span>
      </label>
    </div>

    <div style="display:flex; gap:12px; margin-top:20px; flex-wrap:wrap;">
      <button type="submit" class="btn btn--primary" style="min-width:180px;">
        <?= icon('check', '', 16) ?> Crear Usuario
      </button>
      <a href="index.php" class="btn btn--outline">Cancelar</a>
    </div>
  </form>
</div>

<?php include __DIR__ . '/../../admin/includes/footer.php'; ?>
