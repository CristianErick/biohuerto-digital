<?php
/**
 * Crear Planta — Formulario de registro (Solo Admin)
 * Semillitas del Futuro · Sin Emojis · Iconos SVG
 */
$rolRequerido = 1;
require_once __DIR__ . '/../../admin/includes/auth_check.php';

$adminPageTitle = 'Nueva Planta — Admin';
include __DIR__ . '/../../admin/includes/header.php';

$msg     = $_SESSION['planta_msg'] ?? null;
$msgType = $_SESSION['planta_msg_type'] ?? 'success';
unset($_SESSION['planta_msg'], $_SESSION['planta_msg_type']);
?>

<div class="admin-page-header">
  <h2>Registrar Nueva Planta en el Biohuerto</h2>
  <a href="index.php" class="btn btn--outline btn--sm">
    <?= icon('arrow-left', '', 14) ?> Volver al listado
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

<div class="admin-card">
  <form action="guardar.php" method="POST" enctype="multipart/form-data" class="admin-form" id="formPlanta">

    <!-- Nombre -->
    <div class="form-group">
      <label for="nombre">Nombre de la especie o planta <span style="color:var(--color-rose);">*</span></label>
      <input type="text" id="nombre" name="nombre" class="form-control"
             required maxlength="150"
             placeholder="Ej: Tomate Cherry, Lechuga Romana, Menta...">
    </div>

    <!-- Descripción -->
    <div class="form-group">
      <label for="descripcion">Descripción y recomendaciones pedagógicas</label>
      <textarea id="descripcion" name="descripcion" class="form-control" rows="5"
                placeholder="Describe la planta: germinación, tipo de riego, sol recomendado y datos curiosos para los niños..."></textarea>
    </div>

    <!-- Riego -->
    <div class="form-group">
      <label for="riego">Régimen de riego</label>
      <select id="riego" name="riego" class="form-control">
        <option value="">Seleccionar...</option>
        <option value="Frecuente">Frecuente</option>
        <option value="Moderado">Moderado</option>
        <option value="Espaciado">Espaciado</option>
        <option value="Poco">Poco</option>
      </select>
    </div>

    <!-- Luz -->
    <div class="form-group">
      <label for="luz">Luz requerida</label>
      <select id="luz" name="luz" class="form-control">
        <option value="">Seleccionar...</option>
        <option value="Sol Directo">Sol Directo</option>
        <option value="Sol Parcial">Sol Parcial</option>
        <option value="Sombra">Sombra</option>
      </select>
    </div>

    <!-- Foto de la planta -->
    <div class="form-group">
      <label>Fotografía de la planta <span style="color:var(--color-rose);">*</span></label>
      <div class="form-file-wrapper" id="dropZone">
        <div class="form-file-wrapper__icon" style="color:var(--color-primary);">
          <?= icon('camera', '', 36) ?>
        </div>
        <div class="form-file-wrapper__text">
          Selecciona o arrastra una imagen aquí<br>
          <small>Formatos: JPG, PNG, WEBP &bull; Máximo 5 MB</small>
        </div>
        <input type="file" id="foto" name="foto" accept="image/jpeg,image/png,image/gif,image/webp" required>
      </div>
      <div class="form-file-preview" id="preview"></div>
    </div>

    <!-- Botones -->
    <div style="display:flex; gap:12px; margin-top:24px; flex-wrap:wrap;">
      <button type="submit" class="btn btn--primary" style="min-width:200px;">
        <?= icon('check', '', 16) ?> Guardar y Generar Código QR
      </button>
      <a href="index.php" class="btn btn--outline">Cancelar</a>
    </div>

  </form>
</div>

<script>
'use strict';
document.addEventListener('DOMContentLoaded', () => {
  const inputFoto = document.getElementById('foto');
  const preview   = document.getElementById('preview');
  const dropZone  = document.getElementById('dropZone');

  if (inputFoto && preview) {
    inputFoto.addEventListener('change', () => {
      mostrarPreview(inputFoto.files[0]);
    });
  }

  function mostrarPreview(file) {
    if (!file) return;
    if (file.size > 5 * 1024 * 1024) {
      preview.innerHTML = '<div class="alert-banner alert-banner--danger" style="margin-top:8px;">La imagen supera los 5 MB permitidos.</div>';
      inputFoto.value = '';
      return;
    }
    const reader = new FileReader();
    reader.onload = (e) => {
      preview.innerHTML = `
        <div style="margin-top:12px; text-align:center;">
          <img src="${e.target.result}" alt="Vista previa" style="max-height:180px; border-radius:8px; border:1px solid var(--color-border); margin:0 auto;">
          <span style="display:block; font-size:0.8rem; color:var(--color-text-muted); margin-top:4px;">${file.name}</span>
        </div>
      `;
    };
    reader.readAsDataURL(file);
  }
});
</script>

<?php include __DIR__ . '/../../admin/includes/footer.php'; ?>
