<?php
/**
 * AMIGOS DEL BIOHUERTO — I.E.P. N.° 70090 Jayllihuaya
 * Selector interactivo con 11 personajes animados, historias multimedia y audios.
 */
require_once __DIR__ . '/../config/database.php';

$pageTitle = 'Amigos del Biohuerto — Semillitas del Futuro';
$pageDesc  = 'Conoce a los 11 simpáticos personajes del biohuerto escolar de Jayllihuaya: escucha sus historias y descubre sus superpoderes botánicos.';
$isSubfolder = true;
include __DIR__ . '/../includes/header.php';
?>

<style>
  .amigos-container {
    max-width: 768px;
    margin: 0 auto;
    padding: 0 var(--space-md) var(--space-2xl);
    box-sizing: border-box;
  }

  .nav-back-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: var(--space-sm);
  }

  .btn-back {
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
    text-decoration: none;
    transition: var(--transition-fast);
  }

  .btn-back:hover {
    background: var(--color-surface-hover);
    border-color: var(--color-primary);
  }

  /* Hero Banner */
  .amigos-hero {
    background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);
    color: #ffffff;
    border-radius: var(--radius-lg);
    padding: var(--space-lg) var(--space-md);
    margin-bottom: var(--space-lg);
    box-shadow: var(--shadow-sm);
    text-align: center;
  }

  .amigos-hero__badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(255, 255, 255, 0.22);
    padding: 4px 12px;
    border-radius: var(--radius-full);
    font-size: 0.78rem;
    font-weight: 800;
    margin-bottom: 8px;
  }

  .amigos-hero__title {
    font-family: var(--font-heading);
    font-size: 1.85rem;
    color: #ffffff;
    margin: 0 0 6px;
  }

  .amigos-hero__desc {
    font-size: 0.94rem;
    color: rgba(255, 255, 255, 0.95);
    margin: 0 auto;
    max-width: 540px;
    line-height: 1.5;
  }

  /* Grid de Personajes */
  .amigos-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
    gap: var(--space-md);
    margin-bottom: var(--space-xl);
  }

  @media (min-width: 600px) {
    .amigos-grid {
      grid-template-columns: repeat(3, 1fr);
    }
  }

  .personaje-card {
    background: #ffffff;
    border: 2px solid var(--color-border);
    border-radius: var(--radius-lg);
    padding: var(--space-md);
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    cursor: pointer;
    box-shadow: var(--shadow-xs);
    transition: var(--transition);
    position: relative;
    overflow: hidden;
  }

  .personaje-card:hover {
    transform: translateY(-5px) scale(1.02);
    box-shadow: var(--shadow-md);
    border-color: var(--color-accent);
  }

  .personaje-card__avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: var(--space-sm);
    box-shadow: inset 0 2px 8px rgba(0,0,0,0.06);
    transition: transform 0.3s ease;
  }

  .personaje-card:hover .personaje-card__avatar {
    transform: scale(1.1) rotate(5deg);
  }

  .personaje-card__name {
    font-family: var(--font-heading);
    font-size: 1.15rem;
    color: var(--color-text);
    margin: 0 0 2px;
  }

  .personaje-card__role {
    font-size: 0.76rem;
    color: var(--color-text-muted);
    font-weight: 700;
    margin-bottom: 8px;
  }

  .personaje-card__pill {
    background: #f1f5f9;
    color: var(--color-text);
    font-size: 0.72rem;
    font-weight: 800;
    padding: 3px 10px;
    border-radius: var(--radius-full);
  }

  /* Modal del Personaje */
  .modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.75);
    z-index: 1000;
    align-items: center;
    justify-content: center;
    padding: var(--space-sm);
    backdrop-filter: blur(5px);
  }
  .modal-overlay.active { display: flex; }

  .personaje-modal {
    background: #ffffff;
    width: 100%;
    max-width: 520px;
    max-height: 90vh;
    border-radius: var(--radius-lg);
    overflow-y: auto;
    box-shadow: var(--shadow-lg);
    display: flex;
    flex-direction: column;
    position: relative;
    box-sizing: border-box;
  }

  .personaje-modal__header {
    padding: var(--space-lg) var(--space-md);
    color: #ffffff;
    text-align: center;
    position: relative;
  }

  .personaje-modal__close {
    position: absolute;
    top: 14px;
    right: 14px;
    background: rgba(255, 255, 255, 0.25);
    border: none;
    color: #ffffff;
    width: 34px;
    height: 34px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
  }

  .personaje-modal__avatar-big {
    width: 90px;
    height: 90px;
    border-radius: 50%;
    background: #ffffff;
    margin: 0 auto var(--space-xs);
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: var(--shadow-md);
  }

  .personaje-modal__title {
    font-family: var(--font-heading);
    font-size: 1.7rem;
    margin: 0 0 2px;
    color: #ffffff;
  }

  .personaje-modal__lema {
    font-size: 0.88rem;
    font-style: italic;
    opacity: 0.95;
    margin: 0;
  }

  .personaje-modal__body {
    padding: var(--space-md);
    display: flex;
    flex-direction: column;
    gap: var(--space-md);
  }

  .quote-bubble {
    background: #f8fafc;
    border: 2px solid var(--color-border);
    border-radius: var(--radius-md);
    padding: var(--space-md);
    position: relative;
  }

  .quote-bubble p {
    font-size: 0.92rem;
    line-height: 1.55;
    color: var(--color-text);
    margin: 0;
  }

  /* Audio y Video Player Box */
  .media-box {
    background: #f0fdf4;
    border: 2px solid #a7f3d0;
    border-radius: var(--radius-md);
    padding: var(--space-md);
    text-align: center;
  }

  .media-box__title {
    font-family: var(--font-heading);
    font-size: 1.05rem;
    color: #065f46;
    margin: 0 0 8px;
  }

  .media-btn-row {
    display: flex;
    gap: 8px;
    justify-content: center;
    flex-wrap: wrap;
    margin-bottom: var(--space-sm);
  }

  .btn-play-media {
    padding: 8px 14px;
    border-radius: var(--radius-md);
    font-weight: 800;
    font-size: 0.85rem;
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: var(--transition-fast);
  }

  .btn-play-media--audio {
    background: #059669;
    color: #ffffff;
  }
  .btn-play-media--audio:hover { background: #047857; }

  .btn-play-media--video {
    background: #8b5cf6;
    color: #ffffff;
  }
  .btn-play-media--video:hover { background: #7c3aed; }

  .player-slot {
    margin-top: var(--space-sm);
    display: none;
  }

  .player-slot audio, .player-slot video {
    width: 100%;
    border-radius: var(--radius-sm);
  }
</style>

<div class="amigos-container">

  <!-- Volver -->
  <div class="nav-back-bar">
    <a href="index.php" class="btn-back">
      <?= icon('arrow-left', '', 16) ?>
      <span>Volver al Biohuerto</span>
    </a>
    <a href="cultivos.php" class="btn-back" style="color:#059669;">
      <?= icon('book', '', 16) ?>
      <span>Fichas de Cultivos</span>
    </a>
  </div>

  <!-- Hero -->
  <header class="amigos-hero animate-fade-in">
    <span class="amigos-hero__badge">
      <?= icon('heart', '', 14) ?> Amigos Guardianes
    </span>
    <h2 class="amigos-hero__title">Los 11 Amigos del Biohuerto</h2>
    <p class="amigos-hero__desc">
      ¡Toca a cualquiera de nuestros amigos para ver su video animado, conocer su mensaje y descubrir sus superpoderes botánicos!
    </p>
  </header>

  <!-- Grid de Personajes -->
  <div class="amigos-grid" id="amigosGrid">
    <!-- Generado con JS -->
  </div>

</div>

<!-- Modal del Personaje -->
<div class="modal-overlay" id="modalAmigo" onclick="cerrarModalAmigo(event)">
  <div class="personaje-modal" onclick="event.stopPropagation()">
    <header class="personaje-modal__header" id="modalHeader">
      <button type="button" class="personaje-modal__close" onclick="cerrarModalAmigo()" aria-label="Cerrar">
        <?= icon('x', '', 18) ?>
      </button>
      <div class="personaje-modal__avatar-big" id="modalAvatar"></div>
      <h3 class="personaje-modal__title" id="modalNombre"></h3>
      <p class="personaje-modal__lema" id="modalLema"></p>
    </header>

    <div class="personaje-modal__body">

      <!-- Diálogo del Personaje -->
      <div class="quote-bubble">
        <div style="font-weight:800; font-size:0.85rem; color:var(--color-primary-dark); margin-bottom:4px; display:flex; align-items:center; gap:6px;">
          <?= icon('volume-2', '', 16) ?> Mensaje del Personaje:
        </div>
        <p id="modalMensaje"></p>
      </div>

      <!-- Superpoder Nutricional -->
      <div class="quote-bubble" style="background:#fef3c7; border-color:#fde68a;">
        <div style="font-weight:800; font-size:0.85rem; color:#92400e; margin-bottom:4px; display:flex; align-items:center; gap:6px;">
          <?= icon('zap', '', 16) ?> Mi Superpoder:
        </div>
        <p id="modalSuperpoder" style="color:#78350f;"></p>
      </div>

      <!-- Video Animado del Personaje (Solo Video) -->
      <div class="media-box">
        <h4 class="media-box__title">
          <?= icon('video', '', 18) ?> Video de mi Historia
        </h4>
        <div id="videoBadgeEstado" style="margin-bottom:8px;"></div>

        <div class="player-slot" id="slotVideo" style="display:block;">
          <video controls id="amigoVideo" playsinline preload="metadata" style="width:100%; max-height:260px; border-radius:var(--radius-md); background:#000000; box-shadow:var(--shadow-sm);"></video>
          <div style="font-size:0.75rem; color:#64748b; margin-top:6px;">
            Archivo de video: <code id="amigoVideoPath"></code>
          </div>
        </div>
      </div>

      <!-- Botón de Acción -->
      <div style="display:flex; gap:8px;">
        <a id="btnFichaCultivo" href="cultivos.php" class="btn btn--primary btn--full" style="text-decoration:none;">
          <?= icon('book', '', 16) ?>
          <span>Ver Ficha del Cultivo</span>
        </a>
      </div>

    </div>
  </div>
</div>

<script>
// ═══ DATOS DE LOS 11 PERSONAJES AMIGOS DEL BIOHUERTO ═══
const AMIGOS = [
  {
    id: 'zanahoria',
    slug: 'zanahoria',
    nombre: 'Zani la Zanahoria',
    rol: 'El Guía Visionario',
    color: '#ea580c',
    bg: '#ffedd5',
    svgIcon: 'sprout',
    lema: '¡Ojos de águila y pasos veloces!',
    mensaje: '¡Hola, amiguito! Soy Zani. Me encanta crecer bien abrigadita bajo la tierra suave de Jayllihuaya. Mis hojas verdes saludan al sol mientras mi cuerpo anaranjado se llena de betacarotenos para que tengas una vista maravillosa y leas sin cansarte.',
    superpoder: 'Protege tu visión, fortalece tu piel contra el frío del altiplano y hace que tus dientes mastiquen crujientes.',
    videoFile: 'zanahoria_video.mp4',
    videoDisponible: true,
    esCultivo: true
  },
  {
    id: 'lechuga',
    slug: 'lechuga',
    nombre: 'Luchi la Lechuga',
    rol: 'La Doncella Fresca',
    color: '#10b981',
    bg: '#d1fae5',
    svgIcon: 'leaf',
    lema: '¡Frescura y paz en cada mordisco!',
    mensaje: '¡Hola! Soy Luchi. Mis hojas son verdes, tiernas y rizadas como vestidos de fiesta. En el biohuerto me gusta beber agüita fresca por las mañanas y darte mucha hidratación en el recreo.',
    superpoder: 'Ayuda a la digestión, te calma para que duermas tranquilo como un angelito y llena tu cuerpo de vitamina K.',
    videoFile: 'lechuga_video.mp4',
    videoDisponible: true,
    esCultivo: true
  },
  {
    id: 'acelga',
    slug: 'acelga',
    nombre: 'Celguita la Acelga',
    rol: 'La Guerrera de Hierro',
    color: '#059669',
    bg: '#dcfce7',
    svgIcon: 'sprout',
    lema: '¡Fuerza andina contra la anemia!',
    mensaje: '¡Soy Celguita! Mis hojas son gigantes y mis pencas son firmes como escudos. Soporto los vientos fríos del lago Titicaca con valentía y alimento a los estudiantes para que nunca tengan sueño en clase.',
    superpoder: 'Cargada con súper hierro y calcio para tener huesos invencibles y sangre oxigenada.',
    videoFile: 'acelga_video.mp4',
    videoDisponible: true,
    esCultivo: true
  },
  {
    id: 'tomate',
    slug: 'tomate',
    nombre: 'Tomatín el Tomate',
    rol: 'El Corazón Rojo',
    color: '#dc2626',
    bg: '#fee2e2',
    svgIcon: 'heart',
    lema: '¡Alegría, jugo y mucho licopeno!',
    mensaje: '¡Hola, campeones! Soy Tomatín. Al principio soy una bolita verde pero cuando el sol de Puno me acaricia, ¡pum!, me pongo rojo brillante y súper jugoso. ¡Soy la estrella de las ensaladas!',
    superpoder: 'Su licopeno cuida tu corazón para que bombee con fuerza cuando juegas fútbol o saltas la soga.',
    videoFile: 'tomate_video.mp4',
    videoDisponible: false,
    esCultivo: true
  },
  {
    id: 'cebolla',
    slug: 'cebolla',
    nombre: 'Cebollín la Cebolla',
    rol: 'El Protector Inmune',
    color: '#9333ea',
    bg: '#f3e8ff',
    svgIcon: 'layers',
    lema: '¡Capas y capas de pura defensa!',
    mensaje: '¡Atención, pequeños guardianes! Soy Cebollín. Aunque a veces hago soltar una lagrimita al picarme, tengo un corazón noble que defiende a los niños de los resfriados y la tos.',
    superpoder: 'Actúa como un antibiótico natural que despeja tus pulmones y fortalece tus defensas en invierno.',
    videoFile: 'cebolla_video.mp4',
    videoDisponible: false,
    esCultivo: true
  },
  {
    id: 'rabanito',
    slug: 'rabanito',
    nombre: 'Rabani el Rabanito',
    rol: 'El Veloz Escarlata',
    color: '#e11d48',
    bg: '#ffe4e6',
    svgIcon: 'sprout',
    lema: '¡Rápido como un cohete en el huerto!',
    mensaje: '¡Soy Rabani! Si quieres ver magia rápida en el biohuerto, ¡siémbrame a mí! En menos de un mes ya soy una esfera roja y blanca lista para regalarte sabor y minerales.',
    superpoder: 'Limpia tu pancita, ayuda al hígado y aporta vitamina C para que crezcas sin pesadez.',
    videoFile: 'rabanito_video.mp4',
    videoDisponible: false,
    esCultivo: true
  },
  {
    id: 'espinaca',
    slug: 'espinaca',
    nombre: 'Espi la Espinaca',
    rol: 'La Campeona de la Fuerza',
    color: '#15803d',
    bg: '#dcfce7',
    svgIcon: 'leaf',
    lema: '¡Energía verde para tu cerebro!',
    mensaje: '¡Hola! Soy Espi. Mis hojas suaves son de un verde oscuro brillante porque guardan una gran fábrica de clorofila y minerales. Si quieres resolver las matemáticas más difíciles, ¡comamos espinaca!',
    superpoder: 'Oxigena tu cerebro, te da agilidad mental y fortalece los músculos de todo tu cuerpo.',
    videoFile: 'espinaca_video.mp4',
    videoDisponible: false,
    esCultivo: true
  },
  {
    id: 'fresa',
    slug: 'fresa',
    nombre: 'Fresita la Fresa',
    rol: 'La Dulce Sonrisa',
    color: '#ef4444',
    bg: '#fee2e2',
    svgIcon: 'heart',
    lema: '¡Dientes limpios y dulzura natural!',
    mensaje: '¡Hola, amigos! Soy Fresita. Me gusta asomar entre paja limpia y lucir mis pequeñas semillas doradas como lunares elegantes. ¡Tengo más vitamina C que un limón grande!',
    superpoder: 'Mantiene tus encías rosadas y saludables, previene el sangrado y protege tus células.',
    videoFile: 'fresa_video.mp4',
    videoDisponible: false,
    esCultivo: true
  },
  {
    id: 'aguaymanto',
    slug: 'aguaymanto',
    nombre: 'Aguaymantito',
    rol: 'El Guardián Dorado Inca',
    color: '#f59e0b',
    bg: '#fef3c7',
    svgIcon: 'sun',
    lema: '¡El tesoro dorado de los Andes!',
    mensaje: '¡Kawsay! Soy Aguaymantito. Crecí en las tierras sagradas de los Incas. Mi farolito de papel me protege de los insectos hasta que mi fruta está dulce y lista para llenarte de poder andino.',
    superpoder: 'Súper vitamina A y C que blinda tu cuerpo contra la gripe y aclara tu vista para aprender más.',
    videoFile: 'aguaymanto_video.mp4',
    videoDisponible: false,
    esCultivo: true
  },
  {
    id: 'beterraga',
    slug: 'beterraga',
    nombre: 'Betito la Beterraga',
    rol: 'El Titán Púrpura',
    color: '#831843',
    bg: '#fce7f3',
    svgIcon: 'sprout',
    lema: '¡Poder terroso y corazón alegre!',
    mensaje: '¡Soy Betito! Mi color morado oscuro pinta las manos de quienes me cosechan con mucho cariño. Vengo con mucha fibra y azúcares saludables para recargarte la batería.',
    superpoder: 'Regenera los glóbulos rojos, llena tus pulmones de resistencia física y mejora la circulación.',
    videoFile: 'beterraga_video.mp4',
    videoDisponible: false,
    esCultivo: true
  },
  {
    id: 'gotita',
    slug: 'gotita',
    nombre: 'Gotita de Agua',
    rol: 'La Protectora de la Vida',
    color: '#0284c7',
    bg: '#e0f2fe',
    svgIcon: 'droplet',
    lema: '¡Cada gota cuenta en nuestro huerto!',
    mensaje: '¡Glub glub! ¡Hola a todos! Yo soy Gotita. Viajo desde las nubes y la lluvia del Titicaca hasta las raíces sedientas de cada plantita. Sin mí ninguna semilla puede nacer, por eso los niños deben regar sin derrochar.',
    superpoder: 'Da vida a todos los cultivos, disuelve los nutrientes del suelo y nos recuerda cuidar el agua del planeta.',
    videoFile: 'gotita_video.mp4',
    videoDisponible: false,
    esCultivo: false
  }
];

document.addEventListener('DOMContentLoaded', () => {
  const grid = document.getElementById('amigosGrid');

  AMIGOS.forEach(a => {
    const card = document.createElement('div');
    card.className = 'personaje-card animate-fade-in';
    card.onclick = () => abrirModalAmigo(a.id);

    card.innerHTML = `
      <div class="personaje-card__avatar" style="background:${a.bg}; color:${a.color};">
        ${crearIconoSVG(a.svgIcon, a.color, 44)}
      </div>
      <h3 class="personaje-card__name">${a.nombre}</h3>
      <span class="personaje-card__role">${a.rol}</span>
      <span class="personaje-card__pill" style="border: 1px solid ${a.color}; color:${a.color};">
        ${a.videoDisponible ? '🎬 Ver video animado' : 'Conocer historia'}
      </span>
    `;
    grid.appendChild(card);
  });

  // Abrir automáticamente si viene por URL
  const params = new URLSearchParams(window.location.search);
  const p = params.get('personaje');
  if (p) {
    abrirModalAmigo(p);
  }
});

function crearIconoSVG(tipo, color, size = 28) {
  if (tipo === 'droplet') {
    return `<svg width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="${color}" stroke-width="2.2"><path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"/></svg>`;
  }
  if (tipo === 'leaf') {
    return `<svg width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="${color}" stroke-width="2.2"><path d="M13 3c2 1 3 3 2 5"/><path d="M5 17c2-1 3-3 2-5"/><path d="M9 10c0-4 4-8 8-8s8 4 8 8-4 8-8 8-8-4-8-8z"/></svg>`;
  }
  if (tipo === 'heart') {
    return `<svg width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="${color}" stroke-width="2.2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>`;
  }
  if (tipo === 'sun') {
    return `<svg width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="${color}" stroke-width="2.2"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/></svg>`;
  }
  if (tipo === 'layers') {
    return `<svg width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="${color}" stroke-width="2.2"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>`;
  }
  return `<svg width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="${color}" stroke-width="2.2"><path d="M7 20h10"/><path d="M10 20c5.5-2.5.8-6.4 3-10"/><path d="M9.5 9.4c1.1.8 1.8 2.2 2.3 3.7-2 .4-3.5 .4-4.8-.3-1.2-.6-2.3-1.9-3-4.2 2.8-.5 4.4.1 5.5 .8z"/><path d="M14.1 6a7 7 0 0 0-1.1 4c1.9-.1 3.3-.6 4.3-1.4 1-1 1.6-2.3 1.7-4.6-2.7.1-4.3 1-4.9 2z"/></svg>`;
}

function abrirModalAmigo(id) {
  const a = AMIGOS.find(item => item.id === id || item.slug === id);
  if (!a) return;

  if (window.bioSound) window.bioSound.playClick();

  document.getElementById('modalHeader').style.background = `linear-gradient(135deg, ${a.color}, #0f172a)`;
  document.getElementById('modalAvatar').innerHTML = crearIconoSVG(a.svgIcon, a.color, 48);
  document.getElementById('modalNombre').textContent = a.nombre;
  document.getElementById('modalLema').textContent = `"${a.lema}"`;
  document.getElementById('modalMensaje').textContent = a.mensaje;
  document.getElementById('modalSuperpoder').textContent = a.superpoder;

  // Ruta del video estandarizada
  const videoSrc = `../assets/media/videos/${a.videoFile}`;
  document.getElementById('amigoVideoPath').textContent = videoSrc;

  const videoEl = document.getElementById('amigoVideo');
  videoEl.src = videoSrc;
  videoEl.load();

  // Estado del video
  const badgeEstado = document.getElementById('videoBadgeEstado');
  if (a.videoDisponible) {
    badgeEstado.innerHTML = `<span style="display:inline-flex; align-items:center; gap:6px; background:#dcfce7; color:#15803d; border:1px solid #86efac; border-radius:999px; padding:4px 12px; font-size:0.78rem; font-weight:800;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> ¡Video oficial de ${a.nombre} listo para ver!</span>`;
  } else {
    badgeEstado.innerHTML = `<span style="display:inline-flex; align-items:center; gap:6px; background:#f1f5f9; color:#64748b; border:1px solid #cbd5e1; border-radius:999px; padding:4px 12px; font-size:0.76rem; font-weight:700;">Video en producción (${a.videoFile})</span>`;
  }

  // Botón Ficha de cultivo o Juego
  const btnFicha = document.getElementById('btnFichaCultivo');
  if (a.esCultivo) {
    btnFicha.href = `cultivos.php?cultivo=${a.slug}`;
    btnFicha.innerHTML = `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg><span>Ver Ficha de Cultivo</span>`;
  } else {
    btnFicha.href = `../juegos/index.php?juego=gotita`;
    btnFicha.innerHTML = `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="6" y1="12" x2="10" y2="12"/><line x1="8" y1="10" x2="8" y2="14"/><rect x="2" y="6" width="20" height="12" rx="2"/></svg><span>¡Jugar con Gotita!</span>`;
  }

  document.getElementById('modalAmigo').classList.add('active');
  document.body.style.overflow = 'hidden';
}

function cerrarModalAmigo(event) {
  if (event && event.target !== event.currentTarget) return;
  const modal = document.getElementById('modalAmigo');
  modal.classList.remove('active');
  document.body.style.overflow = '';

  const videoEl = document.getElementById('amigoVideo');
  if (videoEl) {
    videoEl.pause();
    videoEl.currentTime = 0;
  }
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
