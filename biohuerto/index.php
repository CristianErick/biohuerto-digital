<?php
/**
 * BIOHUERTO ESCOLAR — Vista Enfocada en Dispositivos Móviles para Estudiantes
 * Mobile-First · Sin Emojis · Iconografía SVG · Alertas Interactivas
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../libs/storage.php';

$pdo = getConnection();

// Obtener plantas ordenadas
$stmt = $pdo->query('SELECT * FROM plantas ORDER BY fecha_registro DESC');
$plantas = $stmt->fetchAll();

$pageTitle = 'Biohuerto Escolar — Semillitas del Futuro';
$pageDesc  = 'Explora las plantas del biohuerto escolar, aprende técnicas de siembra, riego y escanea los códigos QR.';
$isSubfolder = true;
include __DIR__ . '/../includes/header.php';
?>

<style>
  /* ---------- Estilos Móviles Optimizados para Biohuerto ---------- */
  .bio-mobile-container {
    max-width: 900px;
    margin: 0 auto;
    padding: 0 var(--space-md) var(--space-2xl);
  }

  /* Botón Destacado Escanear QR en Huerto */
  .qr-scanner-trigger {
    background: #ffffff;
    border: 2px solid #a7f3d0;
    border-radius: var(--radius-md);
    padding: var(--space-md);
    display: flex;
    align-items: center;
    gap: var(--space-md);
    box-shadow: var(--shadow-sm);
    margin-bottom: var(--space-md);
    text-align: left;
    cursor: pointer;
    transition: var(--transition);
  }

  .qr-scanner-trigger:active,
  .qr-scanner-trigger:hover {
    transform: translateY(-2px);
    border-color: var(--color-primary);
    box-shadow: var(--shadow-md);
  }

  .qr-scanner-trigger__icon {
    width: 48px;
    height: 48px;
    border-radius: var(--radius-md);
    background: var(--color-primary-soft);
    color: var(--color-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .qr-scanner-trigger__title {
    font-weight: 800;
    font-size: 1rem;
    color: var(--color-primary-dark);
    display: block;
  }

  .qr-scanner-trigger__sub {
    font-size: 0.82rem;
    color: var(--color-text-muted);
    display: block;
    margin-top: 2px;
  }

  /* Cuidados Rápidos Horizontales (Touch Slider) */
  .cuidados-scroll {
    display: flex;
    gap: var(--space-sm);
    overflow-x: auto;
    padding-bottom: var(--space-sm);
    margin-bottom: var(--space-lg);
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
  }
  .cuidados-scroll::-webkit-scrollbar { display: none; }

  .cuidado-item {
    flex: 0 0 240px;
    background: #ffffff;
    border: 1px solid var(--color-border);
    border-radius: var(--radius-md);
    padding: var(--space-md);
    box-shadow: var(--shadow-xs);
  }

  .cuidado-item__header {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 6px;
    color: var(--color-primary);
    font-weight: 700;
    font-size: 0.92rem;
  }

  .cuidado-item__text {
    font-size: 0.82rem;
    color: var(--color-text-muted);
    line-height: 1.4;
  }

  /* Tarjetas de Plantas para Celular */
  .plantas-mobile-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: var(--space-md);
  }

  @media (min-width: 600px) {
    .plantas-mobile-grid {
      grid-template-columns: repeat(2, 1fr);
    }
  }

  @media (min-width: 900px) {
    .plantas-mobile-grid {
      grid-template-columns: repeat(3, 1fr);
    }
  }

  .planta-card-mobile {
    background: #ffffff;
    border: 1px solid var(--color-border);
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-xs);
    display: flex;
    flex-direction: column;
    transition: var(--transition);
  }

  .planta-card-mobile:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-md);
    border-color: #a7f3d0;
  }

  .planta-card-mobile__image-box {
    position: relative;
    width: 100%;
    height: 190px;
    background: var(--color-primary-soft);
    overflow: hidden;
  }

  .planta-card-mobile__image {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .planta-card-mobile__qr-badge {
    position: absolute;
    bottom: 10px;
    right: 10px;
    background: rgba(15, 23, 42, 0.75);
    color: #ffffff;
    font-size: 0.72rem;
    font-weight: 700;
    padding: 4px 8px;
    border-radius: var(--radius-sm);
    display: flex;
    align-items: center;
    gap: 4px;
    backdrop-filter: blur(4px);
  }

  .planta-card-mobile__body {
    padding: var(--space-md);
    display: flex;
    flex-direction: column;
    flex-grow: 1;
  }

  .planta-card-mobile__title {
    font-family: var(--font-heading);
    font-size: 1.25rem;
    color: var(--color-text);
    margin-bottom: 4px;
  }

  .planta-card-mobile__desc {
    font-size: 0.88rem;
    color: var(--color-text-muted);
    line-height: 1.5;
    margin-bottom: var(--space-md);
    flex-grow: 1;
  }

  .planta-card-mobile__tags {
    display: flex;
    gap: 8px;
    margin-bottom: var(--space-md);
    font-size: 0.75rem;
    color: var(--color-text-muted);
    font-weight: 600;
  }

  .planta-card-mobile__btn {
    width: 100%;
    min-height: 46px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: var(--space-xs);
    background: var(--color-primary);
    color: #ffffff;
    border-radius: var(--radius-md);
    font-weight: 700;
    font-size: 0.95rem;
    transition: var(--transition);
  }

  .planta-card-mobile__btn:hover {
    background: var(--color-primary-dark);
  }

  /* Modal interactivo de Escaneo */
  .modal-backdrop {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.65);
    z-index: 500;
    align-items: center;
    justify-content: center;
    padding: var(--space-md);
    backdrop-filter: blur(4px);
  }
  .modal-backdrop.active { display: flex; }

  .modal-sheet {
    background: #ffffff;
    width: 100%;
    max-width: 440px;
    border-radius: var(--radius-lg);
    padding: var(--space-lg);
    box-shadow: var(--shadow-lg);
    text-align: center;
    animation: modalSlideUp 0.3s ease;
  }

  @keyframes modalSlideUp {
    from { transform: translateY(20px); opacity: 0; }
    to   { transform: translateY(0);    opacity: 1; }
  }
</style>

<!-- Banner Superior Móvil -->
<header class="bio-hero-mobile">
  <div class="bio-hero-mobile__top">
    <span class="bio-hero-mobile__badge section-badge">
      <?= icon('sprout', '', 14) ?>
      <span>Huerto Primaria</span>
    </span>
    <span style="font-size: 0.75rem; opacity: 0.9;">
      <?= count($plantas) ?> especies registradas
    </span>
  </div>
  <h2 class="bio-hero-mobile__title">El Biohuerto Escolar</h2>
  <p class="bio-hero-mobile__desc">
    Explora la naturaleza, aprende sobre el riego y descubre cada planta cultivada en nuestro colegio.
  </p>
</header>

<main class="bio-mobile-container">

  <!-- ═══ ALERTAS INTELIGENTES PARA ESTUDIANTES ═══ -->
  <div class="alert-banner alert-banner--warning" role="alert" id="alertaRiego">
    <span class="alert-banner__icon">
      <?= icon('droplet', '', 20) ?>
    </span>
    <div class="alert-banner__content">
      <strong>Alerta matutina de riego</strong>
      <span>Si la tierra superficial se siente seca al tacto, riega suavemente cerca del tallo sin mojar las hojas al mediodía para evitar quemaduras por el sol.</span>
    </div>
    <button class="alert-banner__close" aria-label="Cerrar aviso">
      <?= icon('x', '', 16) ?>
    </button>
  </div>

  <div class="alert-banner alert-banner--info" role="alert" id="alertaSemillitas">
    <span class="alert-banner__icon">
      <?= icon('sun', '', 20) ?>
    </span>
    <div class="alert-banner__content">
      <strong>¿Sabías que...?</strong>
      <span>Las plantas absorben dióxido de carbono y liberan el oxígeno que respiramos durante el día mediante la fotosíntesis.</span>
    </div>
    <button class="alert-banner__close" aria-label="Cerrar aviso">
      <?= icon('x', '', 16) ?>
    </button>
  </div>

  <!-- ═══ BOTÓN DESTACADO PARA CELULAR: ESCANEAR QR ═══ -->
  <div class="qr-scanner-trigger" id="btnEscanearQR">
    <div class="qr-scanner-trigger__icon">
      <?= icon('qr-code', '', 26) ?>
    </div>
    <div style="flex-grow: 1;">
      <span class="qr-scanner-trigger__title">Escanear QR en el Biohuerto</span>
      <span class="qr-scanner-trigger__sub">¿Estás frente a un cultivo? Pulsa aquí para consultar su ficha técnica física</span>
    </div>
    <span style="color: var(--color-primary);">
      <?= icon('camera', '', 22) ?>
    </span>
  </div>

  <!-- ═══ GUÍA RÁPIDA DE CUIDADOS TÁCTIL (Touch Cards) ═══ -->
  <div style="margin-bottom: var(--space-xs); display: flex; align-items: center; justify-content: space-between;">
    <h3 style="font-family: var(--font-heading); font-size: 1.15rem; color: var(--color-text);">
      Consejos de Siembra
    </h3>
    <span style="font-size: 0.75rem; color: var(--color-text-muted);">Desliza horizontalmente &rarr;</span>
  </div>

  <div class="cuidados-scroll">
    <div class="cuidado-item">
      <div class="cuidado-item__header">
        <?= icon('leaf', '', 18) ?>
        <span>Profundidad</span>
      </div>
      <p class="cuidado-item__text">
        Siembra las semillas al doble de su tamaño. Las semillas pequeñas como la lechuga apenas se cubren con una fina capa de abono.
      </p>
    </div>

    <div class="cuidado-item">
      <div class="cuidado-item__header">
        <?= icon('droplet', '', 18) ?>
        <span>Humedad Constante</span>
      </div>
      <p class="cuidado-item__text">
        Usa regadera de lluvia fina para no desenterrar las semillas recién puestas. Mantén la tierra fresca pero sin lodo.
      </p>
    </div>

    <div class="cuidado-item">
      <div class="cuidado-item__header">
        <?= icon('sun', '', 18) ?>
        <span>Luz Diaria</span>
      </div>
      <p class="cuidado-item__text">
        Ubica los almácigos donde reciban luz solar indirecta durante sus primeros brotes antes del trasplante definitivo.
      </p>
    </div>

    <div class="cuidado-item">
      <div class="cuidado-item__header">
        <?= icon('leaf', '', 18) ?>
        <span>Abono Orgánico</span>
      </div>
      <p class="cuidado-item__text">
        Usa compost casero o humus de lombriz para nutrir la tierra de forma natural, evitando químicos que dañen el suelo y las plantas.
      </p>
    </div>
  </div>

  <!-- ═══ LISTADO DE PLANTAS REGISTRADAS ═══ -->
  <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: var(--space-md); flex-wrap: wrap; gap: 8px;">
    <h3 style="font-family: var(--font-heading); font-size: 1.25rem; color: var(--color-text); margin: 0;">
      Plantas de Nuestro Huerto
    </h3>
    <?php if (isset($_SESSION['rol_id']) && $_SESSION['rol_id'] == 1): ?>
      <a href="../admin/plantas/crear.php" class="btn btn--primary btn--sm">
        <?= icon('plus', '', 16) ?>
        <span>Agregar Planta</span>
      </a>
    <?php endif; ?>
  </div>

  <?php if (empty($plantas)): ?>
    <div class="card" style="text-align: center; padding: var(--space-xl);">
      <div style="color: var(--color-primary); margin-bottom: 8px;">
        <?= icon('sprout', '', 40) ?>
      </div>
      <h4 style="font-family: var(--font-heading); font-size: 1.2rem; color: var(--color-text);">Aún no hay plantas registradas</h4>
      <p style="font-size: 0.9rem; color: var(--color-text-muted); margin-top: 4px;">
        Próximamente los docentes agregarán las fotos y códigos QR de las especies sembradas.
      </p>
    </div>
  <?php else: ?>
    <div class="plantas-mobile-grid">
      <?php foreach ($plantas as $planta): ?>
        <article class="planta-card-mobile">
          <div class="planta-card-mobile__image-box">
            <?php if (!empty($planta['ruta_foto'])): ?>
              <img src="<?= storage_url($planta['ruta_foto'], '../') ?>"
                   alt="Foto de <?= htmlspecialchars($planta['nombre']) ?>"
                   class="planta-card-mobile__image"
                   loading="lazy">
            <?php else: ?>
              <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; color: var(--color-primary);">
                <?= icon('sprout', '', 48) ?>
              </div>
            <?php endif; ?>

            <?php if (!empty($planta['ruta_qr'])): ?>
              <span class="planta-card-mobile__qr-badge">
                <?= icon('qr-code', '', 14) ?>
                <span>Con QR</span>
              </span>
            <?php endif; ?>
          </div>

          <div class="planta-card-mobile__body">
            <h4 class="planta-card-mobile__title">
              <?= htmlspecialchars($planta['nombre']) ?>
            </h4>

            <p class="planta-card-mobile__desc">
              <?php
                $desc = $planta['descripcion'] ?? '';
                echo htmlspecialchars(mb_strlen($desc) > 85 ? mb_substr($desc, 0, 85) . '...' : $desc);
              ?>
            </p>

            <div class="planta-card-mobile__tags">
              <span style="display: flex; align-items: center; gap: 4px;">
                <?= icon('calendar', '', 14) ?>
                <?= date('d/m/Y', strtotime($planta['fecha_registro'])) ?>
              </span>
              <span>&bull;</span>
              <span>Cultivo Escolar</span>
            </div>

            <a href="planta.php?id=<?= $planta['id'] ?>" class="planta-card-mobile__btn">
              <span>Ver Ficha Técnica</span>
              <?= icon('arrow-right', '', 16) ?>
            </a>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

</main>

<!-- Modal Explicativo de Escaneo QR para Celulares -->
<div class="modal-backdrop" id="modalQR">
  <div class="modal-sheet">
    <div style="width: 56px; height: 56px; border-radius: 50%; background: var(--color-primary-soft); color: var(--color-primary); display: flex; align-items: center; justify-content: center; margin: 0 auto var(--space-sm);">
      <?= icon('camera', '', 28) ?>
    </div>
    <h3 style="font-family: var(--font-heading); font-size: 1.35rem; color: var(--color-text); margin-bottom: var(--space-xs);">
      ¿Cómo escanear en el Huerto?
    </h3>
    <p style="font-size: 0.9rem; color: var(--color-text-muted); line-height: 1.5; margin-bottom: var(--space-md);">
      Cada maceta o bancal de nuestro colegio tiene un cartel con su <strong>código QR</strong>.
      Abre la cámara de tu celular, apúntala al código y se abrirá la ficha técnica con todos sus secretos botánicos.
    </p>
    <div style="display: flex; gap: 8px;">
      <button type="button" class="btn btn--primary" style="flex: 1;" id="cerrarModalQR">
        <?= icon('check', '', 16) ?>
        <span>¡Entendido!</span>
      </button>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const btnEscanear = document.getElementById('btnEscanearQR');
  const modal = document.getElementById('modalQR');
  const btnCerrar = document.getElementById('cerrarModalQR');

  if (btnEscanear && modal) {
    btnEscanear.addEventListener('click', () => {
      modal.classList.add('active');
    });
  }

  if (btnCerrar && modal) {
    btnCerrar.addEventListener('click', () => {
      modal.classList.remove('active');
    });
    modal.addEventListener('click', (e) => {
      if (e.target === modal) modal.classList.remove('active');
    });
  }
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
