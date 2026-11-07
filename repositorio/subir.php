<?php
/**
 * Subir Material Educativo — Semillitas del Futuro
 * Sin Emojis · Iconos SVG · Mobile-First
 */
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

require_once __DIR__ . '/../includes/icons.php';

$pageTitle = 'Subir Material Educativo — Semillitas del Futuro';
$extraCSS  = ['css/repositorio.css'];
$isSubfolder = true;
include __DIR__ . '/../includes/header.php';

$msg     = $_SESSION['repo_msg'] ?? null;
$msgType = $_SESSION['repo_msg_type'] ?? 'error';
$old     = $_SESSION['repo_old'] ?? [];
unset($_SESSION['repo_msg'], $_SESSION['repo_msg_type'], $_SESSION['repo_old']);
?>

<div class="sections" style="max-width: 760px; margin: 0 auto; padding-top: 1.5rem;">

  <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:1.5rem;">
    <div>
      <h2 style="font-family:var(--font-heading); color:var(--color-primary-dark); font-size:1.8rem; margin:0; display:flex; align-items:center; gap:8px;">
        <?= icon('upload', '', 24) ?>
        <span>Subir Material Educativo</span>
      </h2>
      <p style="color:var(--color-text-muted); font-size:0.9rem; margin:4px 0 0 0;">
        Comparte recursos didácticos para estudiantes de primaria.
      </p>
    </div>
    <a href="mis_materiales.php" class="btn btn--outline btn--sm">
      <?= icon('arrow-left', '', 15) ?> Volver a Mis Materiales
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

  <div class="card" style="padding:var(--space-xl);">
    <form action="guardar.php" method="POST" enctype="multipart/form-data" class="admin-form" id="formMaterial">

      <!-- Título -->
      <div class="form-group">
        <label for="titulo">Título del Material <span style="color:var(--color-rose);">*</span></label>
        <input type="text" id="titulo" name="titulo" class="form-control" required maxlength="200"
               placeholder="Ej: Ficha de Fotosíntesis y Partes de la Planta"
               value="<?= htmlspecialchars($old['titulo'] ?? '') ?>">
      </div>

      <!-- Área y Grado -->
      <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(240px, 1fr)); gap:16px;">
        <div class="form-group">
          <label for="area">Área de Estudio <span style="color:var(--color-rose);">*</span></label>
          <select id="area" name="area" class="form-control" required>
            <option value="">-- Selecciona el área --</option>
            <?php
            $areas = [
                'Ciencias y Ambiente',
                'Matemáticas',
                'Comunicación',
                'Personal Social',
                'Arte y Cultura',
                'Inglés',
                'Educación Religiosa'
            ];
            foreach ($areas as $a):
            ?>
              <option value="<?= $a ?>" <?= (($old['area'] ?? '') === $a) ? 'selected' : '' ?>>
                <?= $a ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group">
          <label for="grado">Grado Escolar <span style="color:var(--color-rose);">*</span></label>
          <select id="grado" name="grado" class="form-control" required>
            <option value="">-- Selecciona el grado --</option>
            <?php for ($g = 1; $g <= 6; $g++): ?>
              <option value="<?= $g ?>° Primaria" <?= (($old['grado'] ?? '') === ($g . '° Primaria')) ? 'selected' : '' ?>>
                <?= $g ?>° de Primaria
              </option>
            <?php endfor; ?>
          </select>
        </div>
      </div>

      <!-- Descripción -->
      <div class="form-group">
        <label for="descripcion">Descripción o Instrucciones para el Estudiante</label>
        <textarea id="descripcion" name="descripcion" class="form-control" rows="4"
                  placeholder="Explica brevemente de qué trata este material, si es para imprimir o resolver en clase..."><?= htmlspecialchars($old['descripcion'] ?? '') ?></textarea>
      </div>

      <!-- Archivo -->
      <div class="form-group">
        <label>Archivo del Material <span style="color:var(--color-rose);">*</span></label>
        <div style="background:var(--color-surface); border:2px dashed var(--color-border); border-radius:var(--radius-md); padding:var(--space-lg); text-align:center;">
          <div style="color:var(--color-primary); margin-bottom:8px;">
            <?= icon('upload', '', 32) ?>
          </div>
          <span style="font-weight:700; color:var(--color-text); display:block;">Selecciona un archivo desde tu dispositivo</span>
          <small style="color:var(--color-text-muted); display:block; margin:4px 0 12px;">Formatos aceptados: PDF, Word (DOC/DOCX), PowerPoint, Excel o ZIP (Máx. 15 MB)</small>
          <input type="file" id="archivo" name="archivo" required
                 accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.zip,.txt"
                 class="form-control" style="max-width:360px; margin:0 auto;">
        </div>
        <div id="archivoPreview" style="margin-top:10px;"></div>
      </div>

      <!-- Botones -->
      <div style="display:flex; gap:12px; margin-top:24px; flex-wrap:wrap;">
        <button type="submit" class="btn btn--primary" style="min-width:180px;">
          <?= icon('check', '', 16) ?> Publicar Material
        </button>
        <a href="mis_materiales.php" class="btn btn--outline">Cancelar</a>
      </div>

    </form>
  </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const inputFile = document.getElementById('archivo');
  const preview   = document.getElementById('archivoPreview');

  if (inputFile && preview) {
    inputFile.addEventListener('change', () => {
      const file = inputFile.files[0];
      if (!file) { preview.innerHTML = ''; return; }

      const sizeKB = (file.size / 1024).toFixed(1);
      const sizeMB = (file.size / (1024 * 1024)).toFixed(2);
      const displaySize = file.size >= 1048576 ? `${sizeMB} MB` : `${sizeKB} KB`;

      if (file.size > 15 * 1024 * 1024) {
        preview.innerHTML = '<div class="alert-banner alert-banner--danger" style="margin-top:8px;">El archivo supera los 15 MB permitidos.</div>';
        inputFile.value = '';
        return;
      }

      preview.innerHTML = `
        <div class="alert-banner alert-banner--info" style="margin-top:8px;">
          <strong>Archivo seleccionado:</strong> ${file.name} (${displaySize})
        </div>
      `;
    });
  }
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
