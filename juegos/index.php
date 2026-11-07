<?php
/**
 * ZONA DE JUEGOS DIDÁCTICOS — Semillitas del Futuro
 * Sin Emojis · Iconos SVG · Mobile-First
 */
require_once __DIR__ . '/../config/database.php';

// Si se llega desde la ficha de una planta (QR), mostrar el nombre de la planta.
$plantaRecomendada = null;
$plantaParam = (int) ($_GET['planta'] ?? 0);
if ($plantaParam > 0) {
    try {
        $stmtPlanta = getConnection()->prepare('SELECT nombre FROM plantas WHERE id = :id LIMIT 1');
        $stmtPlanta->execute([':id' => $plantaParam]);
        $plantaRecomendada = $stmtPlanta->fetch() ?: null;
    } catch (Throwable $e) {
        $plantaRecomendada = null;
    }
}

$pageTitle = 'Zona de Juegos — Semillitas del Futuro';
$pageDesc  = 'Minijuegos interactivos de ciencias y medio ambiente: cuestionarios botánicos, sopas de letras y retos de reciclaje.';
$isSubfolder = true;
include __DIR__ . '/../includes/header.php';
?>

<link rel="stylesheet" href="css/juegos.css?v=20261107d">

<style>
  .juegos-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: var(--space-lg);
  }

  .juego-card {
    background: #ffffff;
    border: 1px solid var(--color-border);
    border-radius: var(--radius-lg);
    padding: var(--space-xl) var(--space-lg);
    text-align: center;
    box-shadow: var(--shadow-xs);
    display: flex;
    flex-direction: column;
    align-items: center;
    transition: var(--transition);
  }

  .juego-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-md);
    border-color: #fde68a;
  }

  .juego-card__icon {
    width: 60px;
    height: 60px;
    border-radius: var(--radius-md);
    background: #fef3c7;
    color: #b45309;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: var(--space-sm);
  }

  .juego-card__title {
    font-family: var(--font-heading);
    font-size: 1.3rem;
    color: var(--color-text);
    margin-bottom: var(--space-xs);
  }

  .juego-card__desc {
    font-size: 0.9rem;
    color: var(--color-text-muted);
    line-height: 1.5;
    margin-bottom: var(--space-md);
    flex-grow: 1;
  }

  .juego-reco-banner {
    display: flex;
    align-items: center;
    gap: 10px;
    background: #ecfdf5;
    border: 1px solid #a7f3d0;
    color: #065f46;
    border-radius: var(--radius-md);
    padding: 12px 16px;
    margin-bottom: var(--space-lg);
    font-size: 0.9rem;
    line-height: 1.4;
  }

  .juego-reco-banner__icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #d1fae5;
    flex-shrink: 0;
  }
</style>

<header class="juegos-header section-header animate-fade-in">
  <div style="display:inline-flex; align-items:center; gap:6px; background:rgba(255,255,255,0.2); padding:4px 12px; border-radius:var(--radius-full); font-size:0.8rem; font-weight:700; margin-bottom:8px;">
    <?= icon('gamepad', '', 16) ?>
    <span>Aprendizaje Lúdico e Interactivo</span>
  </div>
  <h2 style="font-family:var(--font-heading); font-size:1.9rem; margin-bottom:4px; color: #ffffff;">
    Zona de Juegos Educativos
  </h2>
  <p class="section-header__subtitle" style="color: #ffffff;">
    Aprende sobre botánica, reciclaje y cuidado del medio ambiente a través de desafíos dinámicos en JavaScript puro.
  </p>
</header>

<main class="sections" style="padding-top: 0;">

  <?php if ($plantaRecomendada): ?>
    <div class="juego-reco-banner">
      <span class="juego-reco-banner__icon"><?= icon('sprout', '', 20) ?></span>
      <span>
        Recomendado desde la ficha de
        <strong><?= htmlspecialchars($plantaRecomendada['nombre']) ?></strong>.
        ¡Elige un juego y sigue aprendiendo!
      </span>
    </div>
  <?php endif; ?>

  <div class="juegos-grid">

    <!-- Cuestionario Botánico -->
    <article class="juego-card">
      <div class="juego-card__icon">
        <?= icon('check-circle', '', 32) ?>
      </div>
      <h3 class="juego-card__title">Trivia del Biohuerto</h3>
      <p class="juego-card__desc">
        Preguntas interactivas con temporizador sobre partes de la planta, fotosíntesis, siembra y abonos orgánicos.
      </p>
      <button type="button" class="btn btn--warning btn--full" onclick="abrirModalJuego('trivia')">
        <?= icon('gamepad', '', 16) ?>
        <span>Jugar Trivia</span>
      </button>
    </article>

    <!-- Sopa de Letras -->
    <article class="juego-card">
      <div class="juego-card__icon">
        <?= icon('search', '', 32) ?>
      </div>
      <h3 class="juego-card__title">Sopa de Letras Verde</h3>
      <p class="juego-card__desc">
        Encuentra palabras clave sobre ecología, especies vegetales y herramientas agrícolas en una cuadrícula táctil.
      </p>
      <button type="button" class="btn btn--warning btn--full" onclick="abrirModalJuego('sopa')">
        <?= icon('search', '', 16) ?>
        <span>Buscar Palabras</span>
      </button>
    </article>

    <!-- Clasifica y Recicla -->
    <article class="juego-card">
      <div class="juego-card__icon">
        <?= icon('trash', '', 32) ?>
      </div>
      <h3 class="juego-card__title">Clasifica y Recicla</h3>
      <p class="juego-card__desc">
        Arrastra cada residuo al contenedor correcto (orgánicos, plástico, papel o vidrio) antes de que se agote el tiempo.
      </p>
      <button type="button" class="btn btn--warning btn--full" onclick="abrirModalJuego('recicla')">
        <?= icon('leaf', '', 16) ?>
        <span>Iniciar Reto</span>
      </button>
    </article>

  </div>

</main>

<!-- Modales de Juegos -->

<!-- Modal: Trivia -->
<div id="modal-juego-trivia" class="juego-modal-overlay" onclick="cerrarModalJuego('trivia')">
  <div class="juego-modal" onclick="event.stopPropagation()">
    <div class="juego-modal__header">
      <h2 class="juego-modal__title"><?= icon('check-circle', '', 24) ?> Trivia del Biohuerto</h2>
      <button class="juego-modal__close" onclick="cerrarModalJuego('trivia')"><?= icon('x', '', 20) ?></button>
    </div>
    <div class="juego-modal__body">
      <div class="juego-hud">
        <span class="juego-timer"><?= icon('clock', '', 16) ?> <span id="trivia-tiempo">00:00</span></span>
        <span class="juego-score">Trivia del Biohuerto</span>
      </div>
      <div id="trivia-juego">
        <div id="trivia-contenido"></div>
      </div>
      <div id="trivia-resultado" style="display:none;"></div>
    </div>
    <div class="juego-modal__actions">
      <button type="button" class="btn btn--primary" id="trivia-btn-reiniciar" style="display:none;" onclick="iniciarTrivia()">
        <?= icon('refresh-ccw', '', 16) ?>
        <span>Jugar de Nuevo</span>
      </button>
      <button type="button" class="btn btn--outline btn--full" onclick="cerrarModalJuego('trivia')">
        <?= icon('arrow-left', '', 16) ?>
        <span>Cerrar</span>
      </button>
    </div>
  </div>
</div>

<!-- Modal: Sopa de Letras -->
<div id="modal-juego-sopa" class="juego-modal-overlay" onclick="cerrarModalJuego('sopa')">
  <div class="juego-modal sopa-modal" onclick="event.stopPropagation()">
    <div class="juego-modal__header">
      <h2 class="juego-modal__title"><?= icon('search', '', 24) ?> Sopa de Letras Verde</h2>
      <button class="juego-modal__close" onclick="cerrarModalJuego('sopa')"><?= icon('x', '', 20) ?></button>
    </div>
    <div class="juego-modal__body" id="sopa-cuerpo">
      <p class="sopa-instructions">
        Toca la primera letra y luego la última de cada palabra. La palabra se marca sola si es correcta.
      </p>
      <div class="juego-hud">
        <span class="juego-timer"><?= icon('clock', '', 16) ?> <span id="sopa-tiempo">00:00</span></span>
        <span class="juego-score"><?= icon('check-circle', '', 16) ?> <span id="sopa-total">0</span>/<span id="sopa-total-max">10</span></span>
      </div>
      <div class="sopa-words" id="sopa-lista-palabras"></div>
      <div class="sopa-grid" id="sopa-grid"></div>
    </div>
    <div class="juego-resultado" id="sopa-resultado"></div>
    <div class="juego-modal__actions">
      <button type="button" class="btn btn--primary btn--full" onclick="iniciarSopa()">
        <?= icon('refresh-ccw', '', 16) ?>
        <span>Nuevo Juego</span>
      </button>
      <button type="button" class="btn btn--outline btn--full" onclick="cerrarModalJuego('sopa')">
        <?= icon('arrow-left', '', 16) ?>
        <span>Cerrar</span>
      </button>
    </div>
  </div>
</div>

<!-- Modal: Clasifica y Recicla -->
<div id="modal-juego-recicla" class="juego-modal-overlay" onclick="cerrarModalJuego('recicla')">
  <div class="juego-modal recicla-modal" onclick="event.stopPropagation()">
    <div class="juego-modal__header">
      <h2 class="juego-modal__title"><?= icon('leaf', '', 24) ?> Clasifica y Recicla</h2>
      <button class="juego-modal__close" onclick="cerrarModalJuego('recicla')"><?= icon('x', '', 20) ?></button>
    </div>
    <div class="juego-modal__body" id="recicla-cuerpo">
      <p class="recicla-instructions">
        Arrastra cada residuo a su contenedor o toca el residuo y luego el contenedor. ¡Clasifícalos todos!
      </p>
      <div class="juego-hud">
        <span class="juego-timer"><?= icon('clock', '', 16) ?> <span id="recicla-tiempo">00:00</span></span>
        <span class="juego-score"><?= icon('check-circle', '', 16) ?> <span id="recicla-total">0</span>/<span id="recicla-total-max">12</span></span>
      </div>
      <div class="recicla-containers" id="recicla-contenedores"></div>
      <div class="recicla-items" id="recicla-items"></div>
      <div class="recicla-feedback" id="recicla-feedback"></div>
    </div>
    <div class="juego-resultado" id="recicla-resultado"></div>
    <div class="juego-modal__actions">
      <button type="button" class="btn btn--primary btn--full" onclick="iniciarRecicla()">
        <?= icon('refresh-ccw', '', 16) ?>
        <span>Nuevo Juego</span>
      </button>
      <button type="button" class="btn btn--outline btn--full" onclick="cerrarModalJuego('recicla')">
        <?= icon('arrow-left', '', 16) ?>
        <span>Cerrar</span>
      </button>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

<script src="js/trivia.js?v=20261107d"></script>
<script src="js/sopa-letras.js?v=20261107d"></script>
<script src="js/recicla.js?v=20261107d"></script>
<script>
  function abrirModalJuego(tipo) {
    const modal = document.getElementById('modal-juego-' + tipo);
    if (!modal) return;
    modal.style.display = 'flex';

    if (tipo === 'trivia') {
      iniciarTrivia();
    } else if (tipo === 'sopa') {
      iniciarSopa();
    } else if (tipo === 'recicla') {
      iniciarRecicla();
    }
  }

  function cerrarModalJuego(tipo) {
    const modal = document.getElementById('modal-juego-' + tipo);
    if (modal) modal.style.display = 'none';

    if (tipo === 'trivia' && typeof detenerTrivia === 'function') detenerTrivia();
    if (tipo === 'sopa' && typeof detenerSopa === 'function') detenerSopa();
    if (tipo === 'recicla' && typeof detenerRecicla === 'function') detenerRecicla();
  }

  // Abrir automáticamente el juego recomendado desde la ficha de una planta.
  (function () {
    const tiposValidos = ['trivia', 'sopa', 'recicla'];
    const params = new URLSearchParams(window.location.search);
    const tipo = params.get('juego');

    if (!tiposValidos.includes(tipo)) return;

    const abrir = () => abrirModalJuego(tipo);
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', abrir);
    } else {
      abrir();
    }
  })();
</script>
