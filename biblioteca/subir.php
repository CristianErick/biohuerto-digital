<?php
/**
 * Agregar Material a la Biblioteca — Semillitas del Futuro
 * Solo Administradores y Docentes pueden agregar recursos.
 */
session_start();

if (!isset($_SESSION['usuario_id']) || !in_array((int)$_SESSION['rol_id'], [1, 2])) {
    $_SESSION['login_error'] = 'Solo Administradores y Docentes pueden agregar materiales a la Biblioteca.';
    header('Location: ../auth/login.php');
    exit;
}

require_once __DIR__ . '/../includes/icons.php';

$pageTitle = 'Agregar a Biblioteca — Semillitas del Futuro';
$isSubfolder = true;
include __DIR__ . '/../includes/header.php';

$msg     = $_SESSION['biblio_msg'] ?? null;
$msgType = $_SESSION['biblio_msg_type'] ?? 'success';
unset($_SESSION['biblio_msg'], $_SESSION['biblio_msg_type']);
?>
<style>
  .biblio-form-card {
    background: var(--color-surface);
    border: 2px solid var(--color-border);
    border-radius: var(--radius-xl);
    padding: var(--space-xl);
    box-shadow: var(--shadow-sm);
    max-width: 700px;
    margin: 0 auto;
  }
</style>

<div class="sections" style="padding-top: 1.5rem;">
  <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:1.5rem;">
    <div>
      <h2 style="font-family:var(--font-heading); color:var(--color-primary-dark); font-size:1.8rem; margin:0; display:flex; align-items:center; gap:8px;">
        <?= icon('book', '', 24) ?>
        <span>Agregar a Biblioteca</span>
      </h2>
      <p style="color:var(--color-text-muted); font-size:0.9rem; margin:4px 0 0 0;">
        Comparte recursos pedagógicos con toda la comunidad educativa.
      </p>
    </div>
    <a href="index.php" class="btn btn--outline btn--sm">
      <?= icon('arrow-left', '', 15) ?> Volver a Biblioteca
    </a>
  </div>

  <?php if ($msg): ?>
    <div class="alert-banner alert-banner--<?= $msgType ?>" style="margin-bottom:1.5rem;">
      <span class="alert-banner__icon">
        <?= icon($msgType === 'success' ? 'check-circle' : 'alert-circle', '', 18) ?>
      </span>
      <div class="alert-banner__content"><?= htmlspecialchars($msg) ?></div>
    </div>
  <?php endif; ?>

  <div class="biblio-form-card">
    <form action="./guardar_biblio.php" method="POST" enctype="multipart/form-data" id="formBiblio">
      <!-- Título -->
      <div class="form-group">
        <label for="titulo">Título del Recurso <span style="color:var(--color-rose);">*</span></label>
        <input type="text" id="titulo" name="titulo" class="form-control" required maxlength="200"
               placeholder="Ej: Guía del Ciclo del Agua"
               value="<?= htmlspecialchars($_POST['titulo'] ?? '') ?>">
      </div>

      <!-- Área y Tipo -->
      <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(240px, 1fr)); gap:16px;">
        <div class="form-group">
          <label for="area">Área Temática <span style="color:var(--color-rose);">*</span></label>
          <select id="area" name="area" class="form-control" required>
            <option value="">-- Selecciona el área --</option>
            <option value="Botánica y Huertos" <?= (($_POST['area'] ?? '') === 'Botánica y Huertos') ? 'selected' : '' ?>>Botánica y Huertos</option>
            <option value="Ciencias de la Tierra" <?= (($_POST['area'] ?? '') === 'Ciencias de la Tierra') ? 'selected' : '' ?>>Ciencias de la Tierra</option>
            <option value="Biodiversidad y Ecología" <?= (($_POST['area'] ?? '') === 'Biodiversidad y Ecología') ? 'selected' : '' ?>>Biodiversidad y Ecología</option>
            <option value="Conservación y Reciclaje" <?= (($_POST['area'] ?? '') === 'Conservación y Reciclaje') ? 'selected' : '' ?>>Conservación y Reciclaje</option>
          </select>
        </div>

        <div class="form-group">
          <label for="tipo">Formato <span style="color:var(--color-rose);">*</span></label>
          <select id="tipo" name="tipo" class="form-control" required>
            <option value="">-- Selecciona el formato --</option>
            <option value="texto" <?= (($_POST['tipo'] ?? '') === 'texto') ? 'selected' : '' ?>>Guía / Texto</option>
            <option value="imagen" <?= (($_POST['tipo'] ?? '') === 'imagen') ? 'selected' : '' ?>>Infografía / Imagen</option>
            <option value="video" <?= (($_POST['tipo'] ?? '') === 'video') ? 'selected' : '' ?>>Video Educativo</option>
          </select>
        </div>
      </div>

      <!-- Descripción -->
      <div class="form-group">
        <label for="descripcion">Descripción</label>
        <textarea id="descripcion" name="descripcion" class="form-control" rows="4"
                  placeholder="¿De qué trata este recurso?"><?= htmlspecialchars($_POST['descripcion'] ?? '') ?></textarea>
      </div>

      <!-- Duración -->
      <div class="form-group">
        <label for="duracion">Duración estimada</label>
        <input type="text" id="duracion" name="duracion" class="form-control"
               placeholder="Ej: Lectura de 10 min, Video 5:30"
               value="<?= htmlspecialchars($_POST['duracion'] ?? '') ?>">
      </div>

      <!-- Enlace -->
      <div class="form-group">
        <label for="enlace">Enlace o URL (opcional)</label>
        <input type="url" id="enlace" name="enlace" class="form-control"
               placeholder="https://ejemplo.com/recurso"
               value="<?= htmlspecialchars($_POST['enlace'] ?? '') ?>">
      </div>

      <!-- Botones -->
      <div style="display:flex; gap:12px; margin-top:24px; flex-wrap:wrap;">
        <button type="submit" class="btn btn--primary" style="min-width:180px;">
          <?= icon('check', '', 16) ?> Publicar en Biblioteca
        </button>
        <a href="index.php" class="btn btn--outline">Cancelar</a>
      </div>
    </form>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const enlace = document.getElementById('enlace');
  const tipo = document.getElementById('tipo');

  if (enlace && tipo) {
    tipo.addEventListener('change', () => {
      if (tipo.value !== 'video') {
        enlace.placeholder = 'https://ejemplo.com/recurso';
      } else {
        enlace.placeholder = 'https://www.youtube.com/watch?v=...';
      }
    });
  }
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
