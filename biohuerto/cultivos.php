<?php
/**
 * FICHAS EDUCATIVAS DE NUESTROS CULTIVOS — Semillitas del Futuro
 * I.E.P. N.° 70090 Jayllihuaya · Puno
 * Catálogo interactivo con buscador, filtros y ficha digital multimedia.
 */
require_once __DIR__ . '/../config/database.php';

$pageTitle = 'Nuestros Cultivos — Biohuerto Escolar';
$pageDesc  = 'Descubre las 10 hortalizas y frutas cultivadas en el biohuerto de la I.E.P. N.° 70090 Jayllihuaya con fichas digitales interactivas.';
$isSubfolder = true;
include __DIR__ . '/../includes/header.php';
?>

<style>
  .cultivos-container {
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

  /* Encabezado */
  .cultivos-hero {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: #ffffff;
    border-radius: var(--radius-lg);
    padding: var(--space-lg) var(--space-md);
    margin-bottom: var(--space-lg);
    box-shadow: var(--shadow-sm);
  }

  .cultivos-hero__title {
    font-family: var(--font-heading);
    font-size: 1.8rem;
    margin: 0 0 6px;
    color: #ffffff;
  }

  .cultivos-hero__desc {
    font-size: 0.92rem;
    color: rgba(255, 255, 255, 0.95);
    margin: 0;
    line-height: 1.5;
  }

  /* Buscador y Filtros */
  .search-filter-box {
    background: #ffffff;
    border: 1px solid var(--color-border);
    border-radius: var(--radius-md);
    padding: var(--space-md);
    box-shadow: var(--shadow-xs);
    margin-bottom: var(--space-lg);
  }

  .search-input-wrapper {
    position: relative;
    margin-bottom: var(--space-sm);
  }

  .search-input-wrapper input {
    width: 100%;
    padding: 12px 14px 12px 42px;
    border: 2px solid var(--color-border);
    border-radius: var(--radius-md);
    font-family: var(--font-main);
    font-size: 0.95rem;
    outline: none;
    box-sizing: border-box;
    transition: var(--transition-fast);
  }

  .search-input-wrapper input:focus {
    border-color: var(--color-primary);
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15);
  }

  .search-icon-pos {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--color-text-muted);
    pointer-events: none;
  }

  .filter-pills {
    display: flex;
    gap: 6px;
    overflow-x: auto;
    padding-bottom: 4px;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
  }
  .filter-pills::-webkit-scrollbar { display: none; }

  .filter-pill {
    background: #f1f5f9;
    border: 1px solid var(--color-border);
    color: var(--color-text-muted);
    padding: 6px 14px;
    border-radius: var(--radius-full);
    font-size: 0.82rem;
    font-weight: 700;
    cursor: pointer;
    white-space: nowrap;
    transition: var(--transition-fast);
  }

  .filter-pill.active, .filter-pill:hover {
    background: var(--color-primary);
    color: #ffffff;
    border-color: var(--color-primary);
  }

  /* Grid de Cultivos */
  .cultivos-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: var(--space-md);
  }

  @media (min-width: 580px) {
    .cultivos-grid {
      grid-template-columns: repeat(2, 1fr);
    }
  }

  .cultivo-card {
    background: #ffffff;
    border: 1px solid var(--color-border);
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-xs);
    display: flex;
    flex-direction: column;
    cursor: pointer;
    transition: var(--transition);
  }

  .cultivo-card:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-md);
    border-color: var(--color-primary);
  }

  .cultivo-card__top {
    height: 140px;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
  }

  .cultivo-card__tag {
    position: absolute;
    top: 10px;
    left: 10px;
    background: rgba(15, 23, 42, 0.7);
    color: #ffffff;
    font-size: 0.72rem;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: var(--radius-full);
    backdrop-filter: blur(4px);
  }

  .cultivo-card__time {
    position: absolute;
    bottom: 10px;
    right: 10px;
    background: #ffffff;
    color: var(--color-text);
    font-size: 0.72rem;
    font-weight: 800;
    padding: 3px 8px;
    border-radius: var(--radius-full);
    display: flex;
    align-items: center;
    gap: 4px;
    box-shadow: var(--shadow-xs);
  }

  .cultivo-card__body {
    padding: var(--space-md);
    display: flex;
    flex-direction: column;
    flex-grow: 1;
  }

  .cultivo-card__name {
    font-family: var(--font-heading);
    font-size: 1.25rem;
    color: var(--color-text);
    margin: 0 0 4px;
  }

  .cultivo-card__desc {
    font-size: 0.84rem;
    color: var(--color-text-muted);
    line-height: 1.45;
    margin: 0 0 var(--space-md);
    flex-grow: 1;
  }

  .cultivo-card__btn {
    width: 100%;
    min-height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    background: var(--color-primary-soft);
    color: var(--color-primary-dark);
    border-radius: var(--radius-md);
    font-weight: 800;
    font-size: 0.88rem;
    border: none;
    cursor: pointer;
    transition: var(--transition-fast);
  }

  .cultivo-card:hover .cultivo-card__btn {
    background: var(--color-primary);
    color: #ffffff;
  }

  /* ═══ Modal Ficha Digital ═══ */
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

  .ficha-modal {
    background: #ffffff;
    width: 100%;
    max-width: 580px;
    max-height: 90vh;
    border-radius: var(--radius-lg);
    overflow-y: auto;
    box-shadow: var(--shadow-lg);
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
    position: relative;
  }

  .ficha-modal__header {
    background: linear-gradient(135deg, #059669, #10b981);
    color: #ffffff;
    padding: var(--space-lg) var(--space-md);
    position: relative;
    border-radius: var(--radius-lg) var(--radius-lg) 0 0;
  }

  .ficha-modal__close {
    position: absolute;
    top: 14px;
    right: 14px;
    background: rgba(255, 255, 255, 0.2);
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

  .ficha-modal__title {
    font-family: var(--font-heading);
    font-size: 1.6rem;
    margin: 0;
    color: #ffffff;
  }

  .ficha-modal__scientific {
    font-style: italic;
    font-size: 0.85rem;
    opacity: 0.9;
  }

  .ficha-modal__body {
    padding: var(--space-md);
    display: flex;
    flex-direction: column;
    gap: var(--space-md);
  }

  .ficha-sec {
    background: #f8fafc;
    border: 1px solid var(--color-border);
    border-radius: var(--radius-md);
    padding: var(--space-md);
  }

  .ficha-sec__title {
    font-family: var(--font-heading);
    font-size: 1rem;
    color: var(--color-primary-dark);
    margin: 0 0 6px;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .ficha-sec__text {
    font-size: 0.88rem;
    color: var(--color-text);
    line-height: 1.5;
    margin: 0;
  }

  /* Cosecha Progress Bar */
  .harvest-bar-box {
    margin-top: 6px;
  }

  .harvest-track {
    height: 12px;
    background: #e2e8f0;
    border-radius: var(--radius-full);
    overflow: hidden;
    position: relative;
  }

  .harvest-fill {
    height: 100%;
    background: linear-gradient(90deg, #10b981, #f59e0b);
    border-radius: var(--radius-full);
  }

  .harvest-labels {
    display: flex;
    justify-content: space-between;
    font-size: 0.75rem;
    color: var(--color-text-muted);
    font-weight: 700;
    margin-top: 4px;
  }

  /* Reproductor Multimedia */
  .media-player-box {
    background: #eff6ff;
    border: 2px dashed #93c5fd;
    border-radius: var(--radius-md);
    padding: var(--space-md);
    text-align: center;
  }

  .media-player-box__title {
    font-family: var(--font-heading);
    font-size: 1rem;
    color: #1e40af;
    margin: 0 0 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
  }

  .media-buttons {
    display: flex;
    gap: 8px;
    justify-content: center;
    flex-wrap: wrap;
    margin-bottom: var(--space-sm);
  }

  .media-btn {
    padding: 8px 14px;
    border-radius: var(--radius-md);
    font-weight: 700;
    font-size: 0.85rem;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
    border: none;
    transition: var(--transition-fast);
  }

  .media-btn--audio {
    background: #3b82f6;
    color: #ffffff;
  }
  .media-btn--audio:hover { background: #2563eb; }

  .media-btn--video {
    background: #ec4899;
    color: #ffffff;
  }
  .media-btn--video:hover { background: #db2777; }

  .media-player-active {
    margin-top: var(--space-sm);
    display: none;
  }

  .media-player-active audio, .media-player-active video {
    width: 100%;
    border-radius: var(--radius-sm);
  }

  .qr-badge-box {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #f1f5f9;
    border-radius: var(--radius-md);
    padding: 10px 14px;
  }
</style>

<div class="cultivos-container">

  <!-- Barra Superior -->
  <div class="nav-back-bar">
    <a href="index.php" class="btn-back">
      <?= icon('arrow-left', '', 16) ?>
      <span>Volver al Biohuerto</span>
    </a>
    <a href="amigos.php" class="btn-back" style="color:#7c3aed;">
      <?= icon('users', '', 16) ?>
      <span>Amigos del Huerto</span>
    </a>
  </div>

  <!-- Hero -->
  <header class="cultivos-hero animate-fade-in">
    <div style="display:inline-flex; align-items:center; gap:6px; background:rgba(255,255,255,0.22); padding:3px 10px; border-radius:var(--radius-full); font-size:0.75rem; font-weight:800; margin-bottom:6px;">
      <?= icon('sprout', '', 14) ?> Catálogo Botánico Infantil
    </div>
    <h2 class="cultivos-hero__title">Nuestros 10 Cultivos del Biohuerto</h2>
    <p class="cultivos-hero__desc">
      Conoce cada planta que cultivamos en la I.E.P. N.° 70090 Jayllihuaya: descubre sus cuidados, tiempo de cosecha y escucha sus historias educativas.
    </p>
  </header>

  <!-- Buscador y Filtros -->
  <section class="search-filter-box" aria-label="Buscador y filtros de cultivos">
    <div class="search-input-wrapper">
      <span class="search-icon-pos"><?= icon('search', '', 18) ?></span>
      <input type="text" id="buscadorCultivos" placeholder="Buscar hortaliza (ej: zanahoria, tomate, hoja)..." autocomplete="off">
    </div>

    <div class="filter-pills" id="filtroBotones">
      <button class="filter-pill active" data-cat="todos">Todos (10)</button>
      <button class="filter-pill" data-cat="hoja">Hojas (3)</button>
      <button class="filter-pill" data-cat="raiz">Raíces (3)</button>
      <button class="filter-pill" data-cat="fruto">Frutos (3)</button>
      <button class="filter-pill" data-cat="bulbo">Bulbos (1)</button>
    </div>
  </section>

  <!-- Catálogo Grid -->
  <div class="cultivos-grid" id="cultivosGrid">
    <!-- Generado dinámicamente con JS -->
  </div>

</div>

<!-- Modal Ficha Digital -->
<div class="modal-overlay" id="modalFicha" onclick="cerrarFicha(event)">
  <div class="ficha-modal" onclick="event.stopPropagation()">
    <header class="ficha-modal__header" id="fichaHeader">
      <button type="button" class="ficha-modal__close" onclick="cerrarFicha()" aria-label="Cerrar ficha">
        <?= icon('x', '', 18) ?>
      </button>
      <div id="fichaTopBadges" style="margin-bottom:6px;"></div>
      <h3 class="ficha-modal__title" id="fichaNombre"></h3>
      <div class="ficha-modal__scientific" id="fichaCientifico"></div>
    </header>

    <div class="ficha-modal__body">

      <!-- Descripción -->
      <div class="ficha-sec">
        <h4 class="ficha-sec__title"><?= icon('book', '', 18) ?> Descripción</h4>
        <p class="ficha-sec__text" id="fichaDesc"></p>
      </div>

      <!-- Parte comestible y necesidades -->
      <div style="display:grid; grid-template-columns:1fr; gap:var(--space-sm);">
        <div class="ficha-sec">
          <h4 class="ficha-sec__title"><?= icon('sprout', '', 18) ?> ¿Qué parte consumimos?</h4>
          <p class="ficha-sec__text" id="fichaParte"></p>
        </div>
        <div class="ficha-sec">
          <h4 class="ficha-sec__title"><?= icon('sun', '', 18) ?> ¿Qué necesita?</h4>
          <p class="ficha-sec__text" id="fichaNecesita"></p>
        </div>
      </div>

      <!-- Cuidados básicos -->
      <div class="ficha-sec">
        <h4 class="ficha-sec__title"><?= icon('droplet', '', 18) ?> Cuidados Básicos</h4>
        <p class="ficha-sec__text" id="fichaCuidados"></p>
      </div>

      <!-- Tiempo de Cosecha -->
      <div class="ficha-sec">
        <h4 class="ficha-sec__title"><?= icon('calendar', '', 18) ?> Tiempo de Cosecha</h4>
        <div class="harvest-bar-box">
          <div class="harvest-track">
            <div class="harvest-fill" id="harvestProgress" style="width: 50%;"></div>
          </div>
          <div class="harvest-labels">
            <span>Siembra</span>
            <span id="harvestDaysLabel" style="font-weight:800; color:var(--color-primary-dark);"></span>
            <span>Cosecha</span>
          </div>
        </div>
      </div>

      <!-- Nutrientes y Beneficios -->
      <div class="ficha-sec" style="background:#ecfdf5; border-color:#a7f3d0;">
        <h4 class="ficha-sec__title" style="color:#065f46;"><?= icon('heart', '', 18) ?> Nutrientes y Salud</h4>
        <p class="ficha-sec__text" id="fichaNutrientes"></p>
      </div>

      <!-- Dato Curioso -->
      <div class="ficha-sec" style="background:#fef3c7; border-color:#fde68a;">
        <h4 class="ficha-sec__title" style="color:#92400e;"><?= icon('info', '', 18) ?> ¿Sabías que...?</h4>
        <p class="ficha-sec__text" id="fichaCurioso"></p>
      </div>

      <!-- Reproductor Multimedia HTML5 -->
      <div class="media-player-box">
        <h4 class="media-player-box__title">
          <?= icon('volume-2', '', 18) ?> Medios Educativos
        </h4>
        <p style="font-size:0.82rem; color:var(--color-text-muted); margin:0 0 10px;">
          Escucha la historia o mira el video del cultivo preparado por los docentes:
        </p>
        <div class="media-buttons">
          <button type="button" class="media-btn media-btn--audio" id="btnAudio">
            <?= icon('volume-2', '', 16) ?>
            <span>Escuchar historia (Audio)</span>
          </button>
          <button type="button" class="media-btn media-btn--video" id="btnVideo">
            <?= icon('video', '', 16) ?>
            <span>Ver video educativo</span>
          </button>
        </div>

        <!-- Reproductor de Audio -->
        <div class="media-player-active" id="playerAudioContainer">
          <audio controls id="audioElement" preload="none" style="width:100%;"></audio>
          <div id="audioFallbackNote" style="font-size:0.75rem; color:#6b7280; margin-top:4px;">
            Audio oficial: <code id="audioPathLabel"></code>
          </div>
        </div>

        <!-- Reproductor de Video -->
        <div class="media-player-active" id="playerVideoContainer">
          <video controls id="videoElement" preload="none" style="max-height:220px;"></video>
          <div id="videoFallbackNote" style="font-size:0.75rem; color:#6b7280; margin-top:4px;">
            Video oficial: <code id="videoPathLabel"></code>
          </div>
        </div>
      </div>

      <!-- Enlace al Amigo del Huerto -->
      <div class="qr-badge-box">
        <div>
          <strong style="font-size:0.88rem; color:var(--color-text); display:block;">¿Quieres conocer a su personaje?</strong>
          <span style="font-size:0.78rem; color:var(--color-text-muted);">Encuéntralo en "Amigos del Biohuerto"</span>
        </div>
        <a id="btnLinkAmigo" href="amigos.php" class="btn btn--primary btn--sm" style="text-decoration:none;">
          <?= icon('users', '', 14) ?>
          <span>Ver Personaje</span>
        </a>
      </div>

    </div>
  </div>
</div>

<script>
// ═══ DATOS DE LOS 10 CULTIVOS DEL BIOHUERTO ═══
const CULTIVOS = [
  {
    slug: 'lechuga',
    nombre: 'Lechuga',
    cientifico: 'Lactuca sativa',
    categoria: 'hoja',
    categoriaNombre: 'Hoja',
    color: '#10b981',
    bg: '#d1fae5',
    icono: 'leaf',
    diasMin: 45,
    diasMax: 60,
    descripcion: 'Hortaliza de hojas tiernas, frescas y muy crujientes. Es la reina de las ensaladas del colegio y crece muy rápido en el huerto.',
    parte: 'Consumimos las hojas verdes en ensaladas frescas, jugos o guarniciones.',
    necesita: 'Riego suave y frecuente, sol de mañana y sombra en horas de intenso calor; tierra con compost suave.',
    cuidados: 'Regar cada 2 días con lluvia fina sin encharcar. Deshierbar alrededor con cuidado y proteger de caracoles.',
    nutrientes: 'Rica en agua, vitaminas A, C y K. Favorece la buena digestión y ayuda a dormir relajado.',
    curioso: 'Se cultiva desde hace más de 2,500 años. Los antiguos egipcios la consideraban una planta sagrada de fertilidad.'
  },
  {
    slug: 'acelga',
    nombre: 'Acelga',
    cientifico: 'Beta vulgaris var. cicla',
    categoria: 'hoja',
    categoriaNombre: 'Hoja',
    color: '#059669',
    bg: '#dcfce7',
    icono: 'sprout',
    diasMin: 50,
    diasMax: 70,
    descripcion: 'Planta de hojas verdes gigantes y pencas carnosas. Es súper resistente al frío del altiplano y sigue produciendo hojas si cortas solo las de afuera.',
    parte: 'Consumimos sus grandes hojas verdes y sus tallos o pencas gruesas.',
    necesita: 'Tierra húmeda y profunda con abono orgánico. Tolera semisombra y vientos moderados.',
    cuidados: 'Cosechar arrancando suavemente las hojas exteriores sin dañar el corazón del centro para que siga brotando.',
    nutrientes: 'Bomba de hierro, calcio, magnesio y vitamina A. Ideal para combatir la anemia infantil y fortalecer los huesos.',
    curioso: '¡Existen variedades de acelga arcoíris con tallos rojos, amarillos, anaranjados y fucsias como un cuadro de arte!'
  },
  {
    slug: 'zanahoria',
    nombre: 'Zanahoria',
    cientifico: 'Daucus carota',
    categoria: 'raiz',
    categoriaNombre: 'Raíz',
    color: '#ea580c',
    bg: '#ffedd5',
    icono: 'sprout',
    diasMin: 70,
    diasMax: 80,
    descripcion: 'Hortaliza de raíz gruesa, alargada y de brillante color naranja. Es dulce, crujiente y crece completamente escondida bajo tierra.',
    parte: 'Consumimos la raíz principal carnosa que crece hacia abajo.',
    necesita: 'Tierra muy suelta y arenosa, sin piedras para que crezca recta; sol directo y riego moderado.',
    cuidados: 'Sembrar directo en el bancal (no le gusta que la trasplanten) y desahijar dejando 5 cm entre plantitas.',
    nutrientes: 'Altísima en betacaroteno (vitamina A). Protege la vista de los niños, nutre la piel y refuerza las defensas.',
    curioso: '¡Originalmente las zanahorias eran moradas y amarillas! Las anaranjadas fueron seleccionadas en Holanda en honor a su familia real.'
  },
  {
    slug: 'tomate',
    nombre: 'Tomate',
    cientifico: 'Solanum lycopersicum',
    categoria: 'fruto',
    categoriaNombre: 'Fruto',
    color: '#dc2626',
    bg: '#fee2e2',
    icono: 'heart',
    diasMin: 60,
    diasMax: 85,
    descripcion: 'Fruto jugoso, carnoso y rojo. Es originario de los Andes de Sudamérica y hoy es el ingrediente más popular del mundo.',
    parte: 'Consumimos el fruto maduro con sus ricas semillas gelatinosas.',
    necesita: 'Mucho sol directo (al menos 6 horas diarias), calor y riego en la base sin mojar sus hojas.',
    cuidados: 'Colocarle un tutor (palito guía) para sostener las ramas pesadas y podar los chupones laterales.',
    nutrientes: 'Rico en licopeno (potente antioxidante), vitamina C y potasio para el buen funcionamiento del corazón.',
    curioso: 'Aunque lo comemos en ensaladas y guisos como verdura, botánicamente el tomate es una fruta porque nace de una flor fecundada.'
  },
  {
    slug: 'cebolla',
    nombre: 'Cebolla',
    cientifico: 'Allium cepa',
    categoria: 'bulbo',
    categoriaNombre: 'Bulbo',
    color: '#9333ea',
    bg: '#f3e8ff',
    icono: 'layers',
    diasMin: 90,
    diasMax: 120,
    descripcion: 'Hortaliza de bulbo subterráneo formado por capas superpuestas. Es la base indispensable del aderezo en la gastronomía peruana.',
    parte: 'Consumimos el bulbo redondeado y también sus tallos verdes tiernos.',
    necesita: 'Suelo bien drenado que no se encharque, sol pleno y abono compostado bien maduro.',
    cuidados: 'Disminuir el riego cuando el bulbo ya esté gordito para evitar que se pudra antes de la cosecha.',
    nutrientes: 'Contiene quercetina y alicina, sustancias antibacterianas naturales que alivian los resfriados y bronquios.',
    curioso: 'Nos hace llorar al cortarla porque libera una sustancia de azufre que al mezclarse con la humedad del ojo crea una lágrima defensiva.'
  },
  {
    slug: 'rabanito',
    nombre: 'Rabanito',
    cientifico: 'Raphanus sativus',
    categoria: 'raiz',
    categoriaNombre: 'Raíz',
    color: '#e11d48',
    bg: '#ffe4e6',
    icono: 'sprout',
    diasMin: 25,
    diasMax: 35,
    descripcion: 'Pequeña raíz esférica de cáscara color rojo rubí y pulpa blanca crujiente con un toque picante y refrescante.',
    parte: 'Consumimos la raíz engrosada en ensaladas frescas.',
    necesita: 'Riego constante y ligero. Le gusta la tierra fresca y sol moderado.',
    cuidados: 'Cosecharlo joven; si lo dejas demasiado tiempo en la tierra se vuelve duro, poroso y amargo.',
    nutrientes: 'Vitamina C, azufre y fibra que limpian el hígado y ayudan a la digestión de las comidas escolares.',
    curioso: '¡Es el bólido de las hortalizas! En solo 25 a 30 días después de sembrar la semilla ya está listo para cosechar.'
  },
  {
    slug: 'espinaca',
    nombre: 'Espinaca',
    cientifico: 'Spinacia oleracea',
    categoria: 'hoja',
    categoriaNombre: 'Hoja',
    color: '#15803d',
    bg: '#dcfce7',
    icono: 'leaf',
    diasMin: 40,
    diasMax: 50,
    descripcion: 'Hortaliza de hojas suaves de color verde oscuro intenso. Famosa por dar fuerza y energía a los niños para estudiar y hacer deporte.',
    parte: 'Consumimos sus hojas tiernas crudas en ensalada o cocidas en ricas tortillas y sopas.',
    necesita: 'Clima fresco y suelo húmedo con humus de lombriz. No tolera el calor excesivo.',
    cuidados: 'Regar con frecuencia para que las hojas no se amarguen y cortar las hojas más grandes para permitir nuevos brotes.',
    nutrientes: 'Súper rica en hierro, ácido fólico, calcio y magnesio. Previene el cansancio y oxigena el cerebro.',
    curioso: 'El famoso personaje Popeye comía espinaca para tener superpoderes; se creó para incentivar a los niños a comer verduras verdes.'
  },
  {
    slug: 'fresa',
    nombre: 'Fresa',
    cientifico: 'Fragaria x ananassa',
    categoria: 'fruto',
    categoriaNombre: 'Fruto',
    color: '#ef4444',
    bg: '#fee2e2',
    icono: 'heart',
    diasMin: 60,
    diasMax: 90,
    descripcion: 'Fruta dulce, aromática y de color rojo encendido. Crece en matas rastreras con flores blancas y se reproduce mediante estolones.',
    parte: 'Consumimos el fruto rojo carnoso (receptáculo floral) con sus semillitas.',
    necesita: 'Sol directo por las mañanas, tierra rica y suelta, y acolchado de paja seca para que el fruto no toque el barro.',
    cuidados: 'Regar al pie de la planta sin mojar las flores ni los frutos maduros para evitar hongos.',
    nutrientes: 'Tiene más vitamina C que la naranja. Aporta antioxidantes y ácido fólico que mantienen las encías y dientes sanos.',
    curioso: '¡Es la única fruta del mundo que lleva todas sus semillas por fuera! Cada fresa tiene unas 200 diminutas semillas amarillas.'
  },
  {
    slug: 'aguaymanto',
    nombre: 'Aguaymanto',
    cientifico: 'Physalis peruviana',
    categoria: 'fruto',
    categoriaNombre: 'Fruto',
    color: '#f59e0b',
    bg: '#fef3c7',
    icono: 'sun',
    diasMin: 90,
    diasMax: 120,
    descripcion: 'Superfruta andina peruana, esférica y dorada, protegida por un delicado capuchón o farolito vegetal de papel.',
    parte: 'Consumimos la bolita dorada agridulce luego de abrir su capuchón protector.',
    necesita: 'Suelo arenoso bien drenado y sol radiante. Es un cultivo rústico muy adaptado a la altura de Puno.',
    cuidados: 'Tutorar sus ramas y podar las hojas secas. El capuchón protege naturalmente al fruto de los insectos plaga.',
    nutrientes: 'Monumento andino de vitaminas A, B y C. Refuerza el sistema inmunológico, combate resfriados y purifica la sangre.',
    curioso: 'Los Incas lo cultivaban en el Valle Sagrado y era la fruta favorita de la nobleza incaica, conocido como "Topotopo".'
  },
  {
    slug: 'beterraga',
    nombre: 'Beterraga (Remolacha)',
    cientifico: 'Beta vulgaris',
    categoria: 'raiz',
    categoriaNombre: 'Raíz',
    color: '#831843',
    bg: '#fce7f3',
    icono: 'sprout',
    diasMin: 55,
    diasMax: 70,
    descripcion: 'Hortaliza de raíz redonda y color morado purpúreo profundo. Su pulpa es dulce y terrosa, ideal para jugos y ensaladas cocidas.',
    parte: 'Consumimos la raíz tuberosa morada y también sus ricas hojas tiernas.',
    necesita: 'Tierra húmeda y suelta sin piedras, abono con compost y buena luz.',
    cuidados: 'Mantener humedad constante para que no se agriete el bulbo y desmalezar con frecuencia.',
    nutrientes: 'Cargada de hierro, ácido fólico y betacianina. Estimula la producción de glóbulos rojos y da mucha resistencia física.',
    curioso: 'Su pigmento natural es tan potente que se utiliza para teñir alimentos y textiles tradicionales de forma ecológica.'
  }
];

// ═══ RENDERIZADO Y LÓGICA DE INTERACCIÓN ═══
document.addEventListener('DOMContentLoaded', () => {
  const grid = document.getElementById('cultivosGrid');
  const buscador = document.getElementById('buscadorCultivos');
  const filtroBtns = document.querySelectorAll('.filter-pill');

  let filtroActual = 'todos';
  let busquedaActual = '';

  function renderCultivos() {
    grid.innerHTML = '';
    const filtrados = CULTIVOS.filter(c => {
      const coincideCat = (filtroActual === 'todos') || (c.categoria === filtroActual);
      const texto = `${c.nombre} ${c.cientifico} ${c.categoriaNombre} ${c.descripcion}`.toLowerCase();
      const coincideBusqueda = texto.includes(busquedaActual);
      return coincideCat && coincideBusqueda;
    });

    if (filtrados.length === 0) {
      grid.innerHTML = `
        <div style="grid-column: 1 / -1; text-align: center; padding: 40px 20px; background: #ffffff; border-radius: 16px;">
          <p style="color: #64748b; font-size: 1rem; margin: 0;">No se encontraron cultivos que coincidan con la búsqueda.</p>
        </div>
      `;
      return;
    }

    filtrados.forEach(c => {
      const card = document.createElement('article');
      card.className = 'cultivo-card animate-fade-in';
      card.onclick = () => abrirFicha(c.slug);

      card.innerHTML = `
        <div class="cultivo-card__top" style="background: ${c.bg}; color: ${c.color};">
          <span class="cultivo-card__tag">${c.categoriaNombre}</span>
          <span class="cultivo-card__time">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            ${c.diasMin}-${c.diasMax} días
          </span>
          <div style="transform: scale(2.2);">${obtenerIconoSVG(c.icono, c.color)}</div>
        </div>
        <div class="cultivo-card__body">
          <h3 class="cultivo-card__name">${c.nombre}</h3>
          <p class="cultivo-card__desc">${c.descripcion.substring(0, 95)}...</p>
          <button type="button" class="cultivo-card__btn">
            <span>Ver Ficha y Medios</span>
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
          </button>
        </div>
      `;
      grid.appendChild(card);
    });
  }

  // Buscador
  buscador.addEventListener('input', (e) => {
    busquedaActual = e.target.value.trim().toLowerCase();
    renderCultivos();
  });

  // Filtros
  filtroBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      filtroBtns.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      filtroActual = btn.getAttribute('data-cat');
      if (window.bioSound) window.bioSound.playClick();
      renderCultivos();
    });
  });

  renderCultivos();

  // Si viene con parámetro ?cultivo= en la URL
  const params = new URLSearchParams(window.location.search);
  const cultivoParam = params.get('cultivo');
  if (cultivoParam) {
    abrirFicha(cultivoParam);
  }
});

// Helper de iconos SVG inline
function obtenerIconoSVG(tipo, color) {
  if (tipo === 'leaf') {
    return `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="${color}" stroke-width="2"><path d="M13 3c2 1 3 3 2 5"/><path d="M5 17c2-1 3-3 2-5"/><path d="M9 10c0-4 4-8 8-8s8 4 8 8-4 8-8 8-8-4-8-8z"/></svg>`;
  }
  if (tipo === 'heart') {
    return `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="${color}" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>`;
  }
  if (tipo === 'sun') {
    return `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="${color}" stroke-width="2"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>`;
  }
  if (tipo === 'layers') {
    return `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="${color}" stroke-width="2"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>`;
  }
  return `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="${color}" stroke-width="2"><path d="M7 20h10"/><path d="M10 20c5.5-2.5.8-6.4 3-10"/><path d="M9.5 9.4c1.1.8 1.8 2.2 2.3 3.7-2 .4-3.5 .4-4.8-.3-1.2-.6-2.3-1.9-3-4.2 2.8-.5 4.4.1 5.5 .8z"/><path d="M14.1 6a7 7 0 0 0-1.1 4c1.9-.1 3.3-.6 4.3-1.4 1-1 1.6-2.3 1.7-4.6-2.7.1-4.3 1-4.9 2z"/></svg>`;
}

// ═══ ABRIR FICHA MODAL ═══
function abrirFicha(slug) {
  const c = CULTIVOS.find(item => item.slug === slug);
  if (!c) return;

  if (window.bioSound) window.bioSound.playClick();

  document.getElementById('fichaNombre').textContent = c.nombre;
  document.getElementById('fichaCientifico').textContent = `Nombre botánico: ${c.cientifico}`;
  document.getElementById('fichaDesc').textContent = c.descripcion;
  document.getElementById('fichaParte').textContent = c.parte;
  document.getElementById('fichaNecesita').textContent = c.necesita;
  document.getElementById('fichaCuidados').textContent = c.cuidados;
  document.getElementById('fichaNutrientes').textContent = c.nutrientes;
  document.getElementById('fichaCurioso').textContent = c.curioso;

  // Header badges
  document.getElementById('fichaTopBadges').innerHTML = `
    <span style="background:rgba(255,255,255,0.25); color:#ffffff; padding:3px 10px; border-radius:999px; font-size:0.75rem; font-weight:800;">
      Categoría: ${c.categoriaNombre}
    </span>
  `;

  // Barra de cosecha
  const pct = Math.min(100, Math.round((c.diasMax / 120) * 100));
  document.getElementById('harvestProgress').style.width = `${pct}%`;
  document.getElementById('harvestDaysLabel').textContent = `${c.diasMin} a ${c.diasMax} días aprox.`;

  // Multimedia paths estandarizados
  const audioSrc = `../assets/media/audios/${c.slug}_audio.mp3`;
  const videoSrc = `../assets/media/videos/${c.slug}_video.mp4`;

  document.getElementById('audioPathLabel').textContent = audioSrc;
  document.getElementById('videoPathLabel').textContent = videoSrc;

  const audioEl = document.getElementById('audioElement');
  const videoEl = document.getElementById('videoElement');
  audioEl.src = audioSrc;
  videoEl.src = videoSrc;

  // Botón enlace a amigos
  document.getElementById('btnLinkAmigo').href = `amigos.php?personaje=${c.slug}`;

  // Reset players
  document.getElementById('playerAudioContainer').style.display = 'none';
  document.getElementById('playerVideoContainer').style.display = 'none';

  // Listeners botones multimedia
  document.getElementById('btnAudio').onclick = () => {
    document.getElementById('playerAudioContainer').style.display = 'block';
    audioEl.play().catch(() => {});
  };

  document.getElementById('btnVideo').onclick = () => {
    document.getElementById('playerVideoContainer').style.display = 'block';
    videoEl.play().catch(() => {});
  };

  document.getElementById('modalFicha').classList.add('active');
  document.body.style.overflow = 'hidden';
}

function cerrarFicha(event) {
  if (event && event.target !== event.currentTarget) return;
  const modal = document.getElementById('modalFicha');
  modal.classList.remove('active');
  document.body.style.overflow = '';

  const audioEl = document.getElementById('audioElement');
  const videoEl = document.getElementById('videoElement');
  if (audioEl) { audioEl.pause(); audioEl.currentTime = 0; }
  if (videoEl) { videoEl.pause(); videoEl.currentTime = 0; }
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
