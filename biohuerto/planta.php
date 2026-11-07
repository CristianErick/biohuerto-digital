<?php
/**
 * FICHA DE PLANTA — Optimizado para Dispositivos Móviles (Escaneo QR)
 * Mobile-First · Sin Emojis · Iconos SVG · Alertas Didácticas
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../libs/storage.php';
require_once __DIR__ . '/../includes/juegos_catalogo.php';

$id = intval($_GET['id'] ?? 0);
$planta = null;

if ($id > 0) {
    try {
        $pdo = getConnection();
        $stmt = $pdo->prepare('SELECT * FROM plantas WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $planta = $stmt->fetch();
    } catch (PDOException $e) {
        $planta = null;
    }
}

$pageTitle = $planta
    ? htmlspecialchars($planta['nombre']) . ' — Biohuerto Semillitas'
    : 'Planta no encontrada — Biohuerto';
$isSubfolder = true;
include __DIR__ . '/../includes/header.php';
?>

<style>
  .planta-view-container {
    max-width: 760px;
    margin: 0 auto;
    padding: var(--space-md) var(--space-md) var(--space-2xl);
  }

  /* Barra de Retorno Móvil */
  .mobile-back-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: var(--space-sm);
  }

  .btn-back-touch {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: var(--color-primary-dark);
    font-weight: 700;
    font-size: 0.9rem;
    padding: 8px 12px;
    border-radius: var(--radius-sm);
    background: #ffffff;
    border: 1px solid var(--color-border);
    transition: var(--transition);
  }
  .btn-back-touch:hover { background: var(--color-surface-hover); }

  /* Hero Card de la Planta */
  .planta-main-card {
    background: #ffffff;
    border: 1px solid var(--color-border);
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    margin-bottom: var(--space-md);
  }

  .planta-hero-img-box {
    position: relative;
    width: 100%;
    height: 280px;
    background: var(--color-primary-soft);
  }
  @media (min-width: 600px) {
    .planta-hero-img-box { height: 360px; }
  }

  .planta-hero-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .planta-main-card__body {
    padding: var(--space-lg);
  }

  .planta-badge-tag {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: var(--color-primary-soft);
    color: var(--color-primary-dark);
    font-size: 0.78rem;
    font-weight: 800;
    padding: 4px 12px;
    border-radius: var(--radius-full);
    margin-bottom: var(--space-xs);
  }

  .planta-title {
    font-family: var(--font-heading);
    font-size: 1.85rem;
    color: var(--color-text);
    line-height: 1.2;
    margin-bottom: var(--space-xs);
  }

  .planta-date {
    font-size: 0.82rem;
    color: var(--color-text-muted);
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: var(--space-md);
  }

  .planta-desc-text {
    font-size: 1rem;
    line-height: 1.7;
    color: var(--color-text);
    white-space: pre-line;
  }

  /* Grid de Métricas Táctiles */
  .metrics-touch-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: var(--space-sm);
    margin-bottom: var(--space-md);
  }

  .metric-touch-card {
    background: #ffffff;
    border: 1px solid var(--color-border);
    border-radius: var(--radius-md);
    padding: var(--space-md);
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .metric-touch-card__icon {
    width: 42px;
    height: 42px;
    border-radius: var(--radius-sm);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .metric-touch-card--water .metric-touch-card__icon { background: #e0f2fe; color: #0284c7; }
  .metric-touch-card--sun .metric-touch-card__icon   { background: #fef3c7; color: #d97706; }

  .metric-touch-card__label { font-size: 0.75rem; color: var(--color-text-muted); font-weight: 600; }
  .metric-touch-card__val   { font-size: 0.95rem; font-weight: 800; color: var(--color-text); }

  /* Sección QR Táctil */
  .qr-card-box {
    background: #ffffff;
    border: 1px solid var(--color-border);
    border-radius: var(--radius-md);
    padding: var(--space-md);
    text-align: center;
    margin-top: var(--space-md);
  }

  .qr-card-box__img {
    width: 140px;
    height: 140px;
    margin: 0 auto var(--space-xs);
    border-radius: var(--radius-sm);
    border: 1px solid var(--color-border);
    padding: 6px;
    background: #ffffff;
  }

  /* Juegos recomendados al escanear el QR */
  .juegos-reco {
    margin-top: var(--space-lg);
    background: #ffffff;
    border: 1px solid var(--color-border);
    border-radius: var(--radius-md);
    padding: var(--space-md);
  }

  .juegos-reco__badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #ecfdf5;
    color: #047857;
    border-radius: var(--radius-full);
    padding: 4px 10px;
    font-size: 0.75rem;
    font-weight: 800;
    margin-bottom: 8px;
  }

  .juegos-reco h3 {
    font-family: var(--font-heading);
    font-size: 1.2rem;
    color: var(--color-text);
    margin: 0 0 4px;
  }

  .juegos-reco__intro {
    font-size: 0.88rem;
    color: var(--color-text-muted);
    margin: 0 0 var(--space-md);
  }

  .juegos-reco__grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
    gap: var(--space-sm);
  }

  .juego-reco-card {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    border: 1px solid var(--color-border);
    border-radius: var(--radius-md);
    padding: var(--space-md);
    text-decoration: none;
    color: inherit;
    transition: var(--transition);
    background: #f8fafc;
  }

  .juego-reco-card:hover {
    transform: translateY(-2px);
    border-color: var(--color-primary);
    box-shadow: var(--shadow-sm);
    background: #ffffff;
  }

  .juego-reco-card__icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .juego-reco-card__title {
    display: block;
    font-family: var(--font-heading);
    font-size: 1rem;
    font-weight: 700;
    color: var(--color-text);
    line-height: 1.25;
  }

  .juego-reco-card__desc {
    display: block;
    font-size: 0.8rem;
    color: var(--color-text-muted);
    line-height: 1.45;
    margin: 4px 0 8px;
  }

  .juego-reco-card__cta {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 0.8rem;
    font-weight: 800;
    color: var(--color-primary-dark);
  }

  .juegos-reco__all {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin-top: var(--space-md);
    font-size: 0.85rem;
    font-weight: 800;
    color: var(--color-primary-dark);
    text-decoration: none;
  }
</style>

<div class="planta-view-container">

  <!-- Barra Superior -->
  <div class="mobile-back-bar">
    <a href="index.php" class="btn-back-touch">
      <?= icon('arrow-left', '', 18) ?>
      <span>Volver al Biohuerto</span>
    </a>
    <span style="font-size: 0.8rem; color: var(--color-text-muted); font-weight: 600;">
      Ficha Botánica
    </span>
  </div>

  <?php if ($planta): ?>

    <!-- Alerta Didáctica para el Estudiante -->
    <div class="alert-banner alert-banner--success" role="alert">
      <span class="alert-banner__icon">
        <?= icon('check-circle', '', 20) ?>
      </span>
      <div class="alert-banner__content">
        <strong>Ficha identificada correctamente</strong>
        <span>Has accedido a la información oficial de esta especie cultivada en el biohuerto de la escuela.</span>
      </div>
      <button class="alert-banner__close" aria-label="Cerrar aviso">
        <?= icon('x', '', 16) ?>
      </button>
    </div>

    <!-- Tarjeta Principal de la Planta -->
    <article class="planta-main-card">
      <div class="planta-hero-img-box">
        <?php if (!empty($planta['ruta_foto'])): ?>
          <img src="<?= storage_url($planta['ruta_foto'], '../') ?>"
               alt="Fotografía de <?= htmlspecialchars($planta['nombre']) ?>"
               class="planta-hero-img">
        <?php else: ?>
          <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: var(--color-primary);">
            <?= icon('sprout', '', 64) ?>
          </div>
        <?php endif; ?>
      </div>

      <div class="planta-main-card__body">
        <span class="planta-badge-tag">
          <?= icon('sprout', '', 14) ?>
          <span>Especie Vegetal del Huerto</span>
        </span>

        <h2 class="planta-title">
          <?= htmlspecialchars($planta['nombre']) ?>
        </h2>

        <div class="planta-date">
          <?= icon('calendar', '', 15) ?>
          <span>Registrada el <?= date('d/m/Y', strtotime($planta['fecha_registro'])) ?></span>
        </div>

        <!-- Métricas Rápidas (solo se muestran si el admin las capturó) -->
        <?php $mostrarMetricas = !empty($planta['riego']) || !empty($planta['luz']); ?>
        <?php if ($mostrarMetricas): ?>
          <div class="metrics-touch-grid">
            <?php if (!empty($planta['riego'])): ?>
              <div class="metric-touch-card metric-touch-card--water">
                <div class="metric-touch-card__icon">
                  <?= icon('droplet', '', 20) ?>
                </div>
                <div>
                  <span class="metric-touch-card__label">Régimen de Riego</span>
                  <span class="metric-touch-card__val"><?= htmlspecialchars($planta['riego']) ?></span>
                </div>
              </div>
            <?php endif; ?>
            <?php if (!empty($planta['luz'])): ?>
              <div class="metric-touch-card metric-touch-card--sun">
                <div class="metric-touch-card__icon">
                  <?= icon('sun', '', 20) ?>
                </div>
                <div>
                  <span class="metric-touch-card__label">Luz Requerida</span>
                  <span class="metric-touch-card__val"><?= htmlspecialchars($planta['luz']) ?></span>
                </div>
              </div>
            <?php endif; ?>
          </div>
        <?php endif; ?>

        <div style="margin-top: var(--space-md);">
          <h3 style="font-family: var(--font-heading); font-size: 1.15rem; color: var(--color-text); margin-bottom: 6px;">
            Descripción y Cuidados
          </h3>
          <?php if (!empty($planta['descripcion'])): ?>
            <p class="planta-desc-text"><?= htmlspecialchars($planta['descripcion']) ?></p>
          <?php else: ?>
            <p style="color: var(--color-text-muted); font-style: italic;">
              Información de cuidados en proceso de documentación por el docente encargado.
            </p>
          <?php endif; ?>
        </div>
      </div>
    </article>

    <!-- Código QR de la Planta -->
    <?php if (!empty($planta['ruta_qr'])): ?>
      <div class="qr-card-box">
        <h4 style="font-family: var(--font-heading); font-size: 1.1rem; color: var(--color-text); margin-bottom: 4px;">
          Código QR de esta Planta
        </h4>
        <p style="font-size: 0.85rem; color: var(--color-text-muted); margin-bottom: var(--space-sm);">
          Este código permite a cualquier alumno abrir esta ficha con la cámara de su celular.
        </p>
        <img src="<?= storage_url($planta['ruta_qr'], '../') ?>"
             alt="Código QR de <?= htmlspecialchars($planta['nombre']) ?>"
             class="qr-card-box__img">
        <div>
          <?php
          $esDocenteOAdmin = isset($_SESSION['usuario_id']) && in_array((int)$_SESSION['rol_id'], [1, 2]);
          if ($esDocenteOAdmin): ?>
            <a href="<?= storage_url_descarga($planta['ruta_qr'], '../') ?>" download="qr_<?= urlencode($planta['nombre']) ?>.png" class="btn btn--outline btn--sm">
              <?= icon('download', '', 15) ?>
              <span>Descargar Código QR</span>
            </a>
          <?php endif; ?>
        </div>
      </div>
    <?php endif; ?>

    <!-- Juegos recomendados automáticamente para esta planta -->
    <?php $juegosRecomendados = juegos_recomendados(2); ?>
    <?php if (!empty($juegosRecomendados)): ?>
      <section class="juegos-reco" aria-labelledby="juegos-reco-title">
        <span class="juegos-reco__badge">
          <?= icon('gamepad', '', 16) ?>
          <span>Aprende jugando</span>
        </span>
        <h3 id="juegos-reco-title">Juegos recomendados para esta planta</h3>
        <p class="juegos-reco__intro">
          Refuerza lo que aprendiste sobre <strong><?= htmlspecialchars($planta['nombre']) ?></strong> con estos retos interactivos.
        </p>

        <div class="juegos-reco__grid">
          <?php foreach ($juegosRecomendados as $clave => $juego): ?>
            <a class="juego-reco-card"
               href="../juegos/index.php?juego=<?= urlencode($clave) ?>&amp;planta=<?= (int) $id ?>">
              <span class="juego-reco-card__icon" style="background:<?= htmlspecialchars($juego['fondo']) ?>; color:<?= htmlspecialchars($juego['color']) ?>;">
                <?= icon($juego['icono'], '', 22) ?>
              </span>
              <span>
                <span class="juego-reco-card__title"><?= htmlspecialchars($juego['titulo']) ?></span>
                <span class="juego-reco-card__desc"><?= htmlspecialchars($juego['descripcion']) ?></span>
                <span class="juego-reco-card__cta">
                  <?= htmlspecialchars($juego['cta']) ?>
                  <?= icon('arrow-right', '', 14) ?>
                </span>
              </span>
            </a>
          <?php endforeach; ?>
        </div>

        <a class="juegos-reco__all" href="../juegos/index.php">
          Ver todos los juegos <?= icon('arrow-right', '', 14) ?>
        </a>
      </section>
    <?php endif; ?>

  <?php else: ?>

    <div class="card" style="text-align: center; padding: var(--space-2xl);">
      <div style="color: var(--color-rose); margin-bottom: var(--space-xs);">
        <?= icon('alert-circle', '', 40) ?>
      </div>
      <h3 style="color: var(--color-text);">Planta no encontrada</h3>
      <p style="color: var(--color-text-muted); margin-top: 4px; margin-bottom: var(--space-md);">
        El registro solicitado no existe o ha sido reubicado en el biohuerto.
      </p>
      <a href="index.php" class="btn btn--primary">
        <?= icon('arrow-left', '', 16) ?>
        <span>Regresar al Huerto</span>
      </a>
    </div>

  <?php endif; ?>

</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
