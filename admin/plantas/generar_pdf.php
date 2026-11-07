<?php
/**
 * Generar Fichas PDF A4 — Códigos QR de Plantas
 * Solo Administradores y Docentes pueden generar este PDF.
 */
session_start();

if (!isset($_SESSION['usuario_id']) || !in_array((int)$_SESSION['rol_id'], [1, 2])) {
    $_SESSION['login_error'] = 'Solo Administradores y Docentes pueden generar fichas PDF.';
    header('Location: ../auth/login.php');
    exit;
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../libs/storage.php';

$pdo = getConnection();

// Obtener todas las plantas que tienen QR
$plantas = $pdo->query('SELECT * FROM plantas WHERE ruta_qr IS NOT NULL AND ruta_qr != "" ORDER BY nombre ASC')->fetchAll();

if (empty($plantas)) {
    $_SESSION['planta_msg'] = 'No hay plantas con códigos QR registrados.';
    $_SESSION['planta_msg_type'] = 'error';
    header('Location: index.php');
    exit;
}

$pageTitle = 'Generar Fichas PDF — Semillitas del Futuro';
$isSubfolder = true;
$basePath = '../../';
$porPagina = isset($_GET['por_pagina']) && in_array($_GET['por_pagina'], [4, 6]) ? (int)$_GET['por_pagina'] : 4;
include __DIR__ . '/../../includes/header.php';
?>

<style>
  /* ═══ Generador PDF — Diseño Limpio ═══ */
  .pdf-generator {
    max-width: 1000px;
    margin: 0 auto;
    padding: var(--space-md) var(--space-md) var(--space-2xl);
  }

  /* Header principal */
  .pdf-header {
    background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%);
    color: #ffffff;
    border-radius: var(--radius-xl);
    padding: var(--space-2xl) var(--space-xl);
    margin-bottom: var(--space-lg);
    box-shadow: var(--shadow-lg);
    text-align: center;
    position: relative;
    overflow: hidden;
  }

  .pdf-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 200px;
    height: 200px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
  }

  .pdf-header__content {
    position: relative;
    z-index: 2;
  }

  .pdf-header__icon {
    font-size: 3rem;
    margin-bottom: var(--space-sm);
    display: block;
  }

  .pdf-header__title {
    font-family: var(--font-heading);
    font-size: 2rem;
    margin: 0 0 var(--space-xs) 0;
    font-weight: 800;
  }

  .pdf-header__subtitle {
    font-size: 1rem;
    opacity: 0.95;
    margin: 0;
  }

  /* Info box */
  .pdf-info {
    background: var(--color-info-soft);
    border: 1px solid #22d3ee;
    border-radius: var(--radius-md);
    padding: var(--space-md);
    margin-bottom: var(--space-lg);
    color: var(--color-info);
    display: flex;
    align-items: flex-start;
    gap: var(--space-sm);
  }

  .pdf-info__icon {
    flex-shrink: 0;
    font-size: 1.5rem;
    margin-top: 2px;
  }

  .pdf-info__content {
    flex-grow: 1;
  }

  .pdf-info__strong {
    display: block;
    font-weight: 800;
    margin-bottom: var(--space-xs);
    color: var(--color-info-dark);
  }

  /* Estadísticas */
  .pdf-stats {
    display: flex;
    gap: var(--space-md);
    margin-bottom: var(--space-lg);
  }

  .pdf-stat-card {
    flex: 1;
    background: var(--color-surface);
    border: 1px solid var(--color-border);
    border-radius: var(--radius-lg);
    padding: var(--space-md);
    box-shadow: var(--shadow-sm);
    text-align: center;
  }

  .pdf-stat__number {
    font-family: var(--font-heading);
    font-size: 2rem;
    color: var(--color-primary-dark);
    font-weight: 800;
    display: block;
  }

  .pdf-stat__label {
    font-size: 0.85rem;
    color: var(--color-text-muted);
    font-weight: 600;
    margin-top: var(--space-xs);
  }

  /* Opciones de generación */
  .pdf-options-card {
    background: var(--color-surface);
    border: 2px solid var(--color-border);
    border-radius: var(--radius-xl);
    padding: var(--space-xl);
    margin-bottom: var(--space-lg);
    box-shadow: var(--shadow-sm);
  }

  .pdf-options-card__title {
    font-family: var(--font-heading);
    font-size: 1.4rem;
    color: var(--color-text);
    margin: 0 0 var(--space-md) 0;
    display: flex;
    align-items: center;
    gap: var(--space-sm);
  }

  .pdf-options-card__form {
    display: flex;
    align-items: center;
    gap: var(--space-md);
    flex-wrap: wrap;
  }

  .pdf-option-group {
    display: flex;
    align-items: center;
    gap: var(--space-sm);
    flex-wrap: wrap;
  }

  .pdf-option-label {
    font-weight: 700;
    color: var(--color-text);
    font-size: 0.95rem;
    min-width: 150px;
  }

  .pdf-option-select {
    flex: 1;
    min-width: 200px;
    max-width: 280px;
  }

  /* Vista previa de QRs */
  .pdf-preview-section {
    margin-bottom: var(--space-lg);
  }

  .pdf-preview__header {
    display: flex;
    align-items: center;
    gap: var(--space-sm);
    margin-bottom: var(--space-md);
  }

  .pdf-preview__title {
    font-family: var(--font-heading);
    font-size: 1.4rem;
    color: var(--color-text);
    margin: 0;
  }

  .qr-preview-grid {
    display: grid;
    gap: var(--space-md);
  }

  .qr-preview-card {
    background: var(--color-surface);
    border: 2px solid var(--color-border);
    border-radius: var(--radius-lg);
    padding: var(--space-md);
    text-align: center;
    box-shadow: var(--shadow-sm);
    transition: var(--transition);
    position: relative;
    overflow: hidden;
  }

  .qr-preview-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-md);
    border-color: var(--color-primary);
  }

  .qr-preview-card__qr {
    width: 110px;
    height: 110px;
    border: 2px solid var(--color-border);
    border-radius: var(--radius-sm);
    background: #ffffff;
    padding: 6px;
    margin: 0 auto var(--space-xs);
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .qr-preview-card__qr img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    border-radius: var(--radius-xs);
  }

  .qr-preview-card__qr-placeholder {
    width: 110px;
    height: 110px;
    border-radius: var(--radius-sm);
    background: var(--color-surface-hover);
    color: var(--color-text-muted);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    margin: 0 auto var(--space-xs);
  }

  .qr-preview-card__name {
    font-family: var(--font-heading);
    font-size: 0.95rem;
    color: var(--color-text);
    margin: var(--space-xs) 0 var(--space-xs) 0;
    font-weight: 700;
  }

  .qr-preview-card__id {
    font-size: 0.75rem;
    color: var(--color-text-muted);
    margin: 0;
  }

  /* Botones de acción */
  .pdf-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    justify-content: center;
    margin-top: var(--space-lg);
  }

  .btn-action {
    min-width: 160px;
    justify-content: center;
  }

  /* Empty state */
  .pdf-empty {
    text-align: center;
    padding: var(--space-3xl) var(--space-md);
    color: var(--color-text-muted);
  }

  .pdf-empty__icon {
    font-size: 4rem;
    color: var(--color-primary-light);
    margin-bottom: var(--space-md);
    display: block;
  }

  .pdf-empty h3 {
    font-family: var(--font-heading);
    color: var(--color-text);
    margin-bottom: var(--space-xs);
  }

  .pdf-empty p {
    font-size: 0.95rem;
    max-width: 400px;
    margin: 0 auto var(--space-md);
  }

  /* Responsive */
  @media (max-width: 768px) {
    .pdf-option-group {
      flex-direction: column;
      align-items: stretch;
    }
    .pdf-option-label {
      min-width: auto;
      text-align: left;
    }
    .pdf-option-select {
      min-width: auto;
      width: 100%;
    }
  }
</style>

<div class="pdf-generator">

  <!-- Header -->
  <header class="pdf-header">
    <div class="pdf-header__content">
      <span class="pdf-header__icon"><?= icon('file-pdf', '', 40) ?></span>
      <h1 class="pdf-header__title">Generar Fichas PDF A4</h1>
      <p class="pdf-header__subtitle">
        Códigos QR del Biohuerto Escolar — Listos para imprimir y encernar
      </p>
    </div>
  </header>

  <!-- Info -->
  <div class="pdf-info">
    <span class="pdf-info__icon"><?= icon('info', '', 24) ?></span>
    <div class="pdf-info__content">
      <span class="pdf-info__strong">¿Cómo funciona?</span>
      Se generará un PDF en tamaño A4 con los códigos QR de todas las plantas registradas. Cada ficha incluye el código QR, el nombre y la descripción de la planta, listos para imprimir y encernar en el huerto escolar.
    </div>
  </div>

  <!-- Estadísticas -->
  <div class="pdf-stats">
    <div class="pdf-stat-card">
      <span class="pdf-stat__number"><?= count($plantas) ?></span>
      <span class="pdf-stat__label">Plantas con QR</span>
    </div>
    <div class="pdf-stat-card">
      <span class="pdf-stat__number"><?= $porPagina ?> por página</span>
      <span class="pdf-stat__label">Distribución en A4</span>
    </div>
    <div class="pdf-stat-card">
      <span class="pdf-stat__number"><?= ceil(count($plantas) / $porPagina) ?></span>
      <span class="pdf-stat__label">Páginas estimadas</span>
    </div>
  </div>

  <!-- Opciones -->
  <div class="pdf-options-card">
    <h3 class="pdf-options-card__title"><?= icon('cog', '', 20) ?> Opciones de Generación</h3>
    <form id="form-generar-pdf" class="pdf-options-card__form" method="GET" action="generar_pdf.php">
      <div class="pdf-option-group">
        <label class="pdf-option-label" for="por_pagina">QRs por página:</label>
        <select name="por_pagina" id="por_pagina" class="pdf-option-select form-control">
          <option value="4" <?= $porPagina == 4 ? 'selected' : '' ?>>4 códigos (2×2)</option>
          <option value="6" <?= $porPagina == 6 ? 'selected' : '' ?>>6 códigos (2×3)</option>
        </select>
      </div>
      <div style="display:flex; justify-content:flex-end; margin-top: var(--space-sm);">
        <button type="submit" class="btn btn--primary btn-action">
          <?= icon('download', '', 18) ?>
          <span>Generar PDF</span>
        </button>
      </div>
    </form>
  </div>

  <!-- Vista previa de QRs -->
  <div class="pdf-preview-section">
    <div class="pdf-preview__header">
      <h2 class="pdf-preview__title"><?= icon('eye', '', 20) ?> Vista Previa de QRs</h2>
      <span style="font-size:0.85rem; color:var(--color-text-muted); background:var(--color-surface-hover); padding:4px 10px; border-radius:var(--radius-sm); border:1px solid var(--color-border);">
        <?= count($plantas) ?> código(s) QR
      </span>
    </div>
    <div class="qr-preview-grid" style="grid-template-columns: repeat(<?= $porPagina === 6 ? 3 : 2 ?>, 1fr);">
      <?php foreach ($plantas as $planta): ?>
        <div class="qr-preview-card">
          <?php if (!empty($planta['ruta_qr'])): ?>
            <div class="qr-preview-card__qr">
              <img src="<?= storage_url($planta['ruta_qr'], '../../') ?>"
                   alt="QR de <?= htmlspecialchars($planta['nombre']) ?>"
                   loading="lazy">
            </div>
          <?php else: ?>
            <div class="qr-preview-card__qr-placeholder">
              <?= icon('alert-circle', '', 24) ?>
            </div>
          <?php endif; ?>
          <h3 class="qr-preview-card__name"><?= htmlspecialchars($planta['nombre']) ?></h3>
          <p class="qr-preview-card__id">ID: #<?= $planta['id'] ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Botón volver -->
  <div class="pdf-actions">
    <a href="index.php" class="btn btn--outline btn-action">
      <?= icon('arrow-left', '', 16) ?>
      <span>Volver a Gestión de Plantas</span>
    </a>
  </div>

</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('form-generar-pdf');
    if (!form) return;

    form.addEventListener('submit', (e) => {
      e.preventDefault();

      const porPagina = parseInt(document.getElementById('por_pagina').value) || 4;

      // Recopilar datos de las plantas desde la vista previa
      const plantas = [];
      document.querySelectorAll('.qr-preview-card').forEach(item => {
        const nombre = item.querySelector('.qr-preview-card__name').textContent;
        const id = item.querySelector('.qr-preview-card__id').textContent.replace('#', '');
        const img = item.querySelector('.qr-preview-card__qr img');
        const qrUrl = img ? img.src : null;

        plantas.push({ nombre, id, qrUrl });
      });

      if (plantas.length === 0) {
        showToast('No hay plantas con códigos QR para generar el PDF.', 'warning');
        return;
      }

      mostrarCarga('Generando PDF, por favor espera...');

      setTimeout(() => {
        generarPDF(plantas, porPagina);
      }, 100);
    });
  });

  function mostrarCarga(mensaje) {
    const existentes = document.querySelectorAll('.toast');
    existentes.forEach(t => t.remove());
    const contenedor = document.getElementById('toast-container') || (() => {
      const c = document.createElement('div');
      c.id = 'toast-container';
      c.className = 'toast-container';
      document.body.appendChild(c);
      return c;
    })();
    const toast = document.createElement('div');
    toast.className = 'toast toast--info';
    toast.innerHTML = `
      <span class="toast__icon">⏳</span>
      <span class="toast__message">${mensaje}</span>
    `;
    contenedor.appendChild(toast);
  }

  function generarPDF(plantas, porPagina) {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF({
      orientation: 'portrait',
      unit: 'mm',
      format: 'a4'
    });

    const pageWidth = 210;
    const pageHeight = 297;
    const margen = 15;

    // Configuración según cantidad por página
    let cols, rows, anchoQR, altoQR, espacioX, espacioY;
    if (porPagina === 4) {
      cols = 2;
      rows = 2;
      anchoQR = 45;
      altoQR = 45;
    } else {
      cols = 2;
      rows = 3;
      anchoQR = 38;
      altoQR = 38;
    }

    espacioX = (pageWidth - margen * 2 - anchoQR * cols) / (cols + 1);
    espacioY = (pageHeight - 60 - anchoQR * rows) / (rows + 1);

    let totalPaginas = Math.ceil(plantas.length / porPagina);
    let plantaIndex = 0;

    for (let p = 0; p < totalPaginas; p++) {
      if (p > 0) doc.addPage();

      // Título de la página
      doc.setFontSize(16);
      doc.setFont('helvetica', 'bold');
      doc.setTextColor(16, 150, 105);
      doc.text('Semillitas del Futuro — Biohuerto Escolar', margen, 22);

      doc.setFontSize(9);
      doc.setFont('helvetica', 'normal');
      doc.setTextColor(100, 100, 100);
      doc.text('Fichas de Códigos QR para el Huerto Escolar', margen, 32);

      doc.setDrawColor(16, 150, 105);
      doc.setLineWidth(0.8);
      doc.line(margen, 38, pageWidth - margen, 38);

      for (let r = 0; r < rows && plantaIndex < plantas.length; r++) {
        for (let c = 0; c < cols && plantaIndex < plantas.length; c++) {
          const planta = plantas[plantaIndex];
          const x = margen + espacioX + c * (espacioX + anchoQR);
          const y = 50 + espacioY + r * (espacioY + altoQR);

          // Marco decorativo simple
          doc.setDrawColor(16, 150, 105);
          doc.setLineWidth(1.5);
          doc.roundedRect(x - 6, y - 6, anchoQR + 12, altoQR + 45, 4, 4);

          // Código QR
          if (planta.qrUrl) {
            try {
              doc.addImage(planta.qrUrl, 'PNG', x, y, anchoQR, altoQR);
            } catch (err) {
              doc.setFillColor(240, 240, 240);
              doc.roundedRect(x, y, anchoQR, altoQR, 2, 2);
              doc.setTextColor(150, 150, 150);
              doc.setFontSize(7);
              doc.text('QR no disponible', x + 5, y + altoQR / 2);
            }
          }

          // Nombre de la planta
          doc.setTextColor(30, 30, 30);
          doc.setFont('helvetica', 'bold');
          doc.setFontSize(10);
          const nombrePlanta = planta.nombre.length > 20
            ? planta.nombre.substring(0, 20) + '...'
            : planta.nombre;
          doc.text(nombrePlanta, x + anchoQR / 2, y + altoQR + 12, { align: 'center' });

          // Descripción de la planta (si existe, sino mostrar ID)
          const descripcion = planta.descripcion || 'Sin descripción';
          const descCorta = descripcion.length > 25
            ? descripcion.substring(0, 25) + '...'
            : descripcion;
          doc.setFont('helvetica', 'normal');
          doc.setFontSize(7.5);
          doc.setTextColor(100, 100, 100);
          doc.text(descCorta, x + anchoQR / 2, y + altoQR + 20, { align: 'center' });

          plantaIndex++;
        }
      }

      // Pie de página simple
      doc.setFontSize(7.5);
      doc.setTextColor(150, 150, 150);
      doc.text('Semillitas del Futuro — Plataforma Educativa Primaria', margen, pageHeight - 8);
      doc.text('Página ' + (p + 1) + ' de ' + totalPaginas, pageWidth - margen - 25, pageHeight - 8, { align: 'right' });
    }

    doc.save('fichas_qr_biohuerto.pdf');

    document.querySelectorAll('.toast').forEach(t => t.remove());
    showToast('PDF generado correctamente: fichas_qr_biohuerto.pdf', 'success');
  }
</script>
