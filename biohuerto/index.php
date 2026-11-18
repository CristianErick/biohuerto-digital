<?php
/**
 * BIOHUERTO ESCOLAR — I.E.P. N.° 70090 Jayllihuaya
 * "Semillitas del Futuro: Cultivando una Escuela Sostenible"
 * Mobile-First · Didáctico e Infantil · Vanilla JS · Iconos SVG
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../libs/storage.php';

$plantas = [];
try {
    $pdo = getSafeConnection();
    if ($pdo) {
        $stmt = $pdo->query('SELECT * FROM plantas ORDER BY fecha_registro DESC');
        $plantas = $stmt->fetchAll();
    }
} catch (Throwable $e) {
    $plantas = [];
}

$pageTitle = 'Biohuerto Escolar — Semillitas del Futuro';
$pageDesc  = 'Aprende sobre el biohuerto escolar, las partes de las plantas, la germinación y nuestros cultivos en la I.E.P. N.° 70090 Jayllihuaya.';
$isSubfolder = true;
include __DIR__ . '/../includes/header.php';
?>

<style>
  /* ═══ Estilos Globales del Biohuerto ═══ */
  .bio-container {
    width: 100%;
    max-width: 768px;
    margin: 0 auto;
    padding: 0 var(--space-md) var(--space-2xl);
    box-sizing: border-box;
  }

  /* Hero Banner */
  .bio-hero {
    background: linear-gradient(135deg, #059669 0%, #10b981 60%, #34d399 100%);
    color: #ffffff;
    border-radius: var(--radius-lg);
    padding: var(--space-lg) var(--space-md);
    margin-bottom: var(--space-lg);
    box-shadow: var(--shadow-md);
    position: relative;
    overflow: hidden;
  }

  .bio-hero::after {
    content: '';
    position: absolute;
    bottom: -20px;
    right: -20px;
    width: 130px;
    height: 130px;
    background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, transparent 70%);
    border-radius: 50%;
    pointer-events: none;
  }

  .bio-hero__top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: var(--space-xs);
    flex-wrap: wrap;
    gap: 8px;
  }

  .bio-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(255, 255, 255, 0.22);
    backdrop-filter: blur(6px);
    color: #ffffff;
    padding: 4px 12px;
    border-radius: var(--radius-full);
    font-size: 0.8rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .bio-hero__title {
    font-family: var(--font-heading);
    font-size: 1.85rem;
    color: #ffffff;
    margin: 6px 0;
    line-height: 1.2;
  }

  .bio-hero__desc {
    font-size: 0.95rem;
    color: rgba(255, 255, 255, 0.95);
    line-height: 1.5;
    margin: 0;
  }

  /* ═══ Módulos de Acceso Rápido ═══ */
  .quick-nav-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: var(--space-sm);
    margin-bottom: var(--space-xl);
  }

  .quick-nav-card {
    background: #ffffff;
    border: 2px solid var(--color-border);
    border-radius: var(--radius-md);
    padding: var(--space-md);
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 8px;
    text-decoration: none;
    color: inherit;
    box-shadow: var(--shadow-xs);
    transition: var(--transition);
  }

  .quick-nav-card:hover, .quick-nav-card:active {
    transform: translateY(-3px);
    box-shadow: var(--shadow-md);
    border-color: var(--color-primary);
  }

  .quick-nav-card--orange:hover { border-color: #f97316; }
  .quick-nav-card--violet:hover { border-color: #8b5cf6; }

  .quick-nav-icon {
    width: 44px;
    height: 44px;
    border-radius: var(--radius-sm);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .quick-nav-card__title {
    font-family: var(--font-heading);
    font-size: 1.1rem;
    color: var(--color-text);
    margin: 0;
  }

  .quick-nav-card__sub {
    font-size: 0.78rem;
    color: var(--color-text-muted);
    line-height: 1.35;
  }

  /* ═══ Encabezados de Sección Temáticos ═══ */
  .section-intro {
    margin-bottom: var(--space-md);
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .section-intro__icon {
    width: 38px;
    height: 38px;
    border-radius: var(--radius-sm);
    background: var(--color-primary-soft);
    color: var(--color-primary-dark);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .section-intro__title {
    font-family: var(--font-heading);
    font-size: 1.35rem;
    color: var(--color-text);
    margin: 0;
  }

  .section-intro__sub {
    font-size: 0.84rem;
    color: var(--color-text-muted);
    margin: 2px 0 0;
  }

  /* ═══ Tarjetas Informativas "¿Qué es un Biohuerto?" ═══ */
  .info-card {
    background: #ffffff;
    border: 1px solid var(--color-border);
    border-radius: var(--radius-md);
    padding: var(--space-md) var(--space-lg);
    box-shadow: var(--shadow-xs);
    margin-bottom: var(--space-md);
    border-left: 5px solid var(--color-primary);
  }

  .info-card h4 {
    font-family: var(--font-heading);
    font-size: 1.15rem;
    color: var(--color-primary-dark);
    margin: 0 0 6px;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .info-card p {
    font-size: 0.92rem;
    color: var(--color-text);
    line-height: 1.55;
    margin: 0;
  }

  .benefits-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: var(--space-sm);
    margin-bottom: var(--space-xl);
  }

  @media (min-width: 580px) {
    .benefits-grid {
      grid-template-columns: repeat(2, 1fr);
    }
  }

  .benefit-card {
    background: #ffffff;
    border: 1px solid var(--color-border);
    border-radius: var(--radius-md);
    padding: var(--space-md);
    box-shadow: var(--shadow-xs);
  }

  .benefit-card__head {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 800;
    font-size: 0.95rem;
    margin-bottom: 6px;
  }

  .benefit-card--food .benefit-card__head { color: #d97706; }
  .benefit-card--eco .benefit-card__head { color: #059669; }

  .benefit-card p {
    font-size: 0.86rem;
    color: var(--color-text-muted);
    line-height: 1.45;
    margin: 0;
  }

  /* ═══ Desplegables de Partes de la Planta (Acordeón) ═══ */
  .accordion-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-bottom: var(--space-xl);
  }

  .accordion-item {
    background: #ffffff;
    border: 1px solid var(--color-border);
    border-radius: var(--radius-md);
    overflow: hidden;
    transition: var(--transition-fast);
  }

  .accordion-item.active {
    border-color: var(--color-primary);
    box-shadow: var(--shadow-sm);
  }

  .accordion-header {
    width: 100%;
    background: none;
    border: none;
    padding: var(--space-md);
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-family: var(--font-heading);
    font-size: 1.05rem;
    color: var(--color-text);
    cursor: pointer;
    text-align: left;
    transition: background 0.2s ease;
  }

  .accordion-header:hover {
    background: var(--color-surface-hover);
  }

  .accordion-header__left {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .accordion-badge {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
    font-weight: 800;
  }

  .accordion-content {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.35s ease, padding 0.35s ease;
    background: #f8fafc;
    padding: 0 var(--space-md);
    border-top: 1px solid transparent;
  }

  .accordion-item.active .accordion-content {
    max-height: 300px;
    padding: var(--space-md);
    border-top: 1px solid var(--color-border);
  }

  .accordion-content p {
    margin: 0;
    font-size: 0.9rem;
    color: var(--color-text);
    line-height: 1.55;
  }

  .accordion-content__funfact {
    display: flex;
    align-items: flex-start;
    gap: 8px;
    background: #fef3c7;
    color: #92400e;
    border-radius: var(--radius-sm);
    padding: 8px 12px;
    margin-top: 10px;
    font-size: 0.82rem;
    line-height: 1.4;
  }

  /* ═══ Necesidades de las Plantas (Scroll Horizontal Táctil) ═══ */
  .needs-scroll {
    display: flex;
    gap: var(--space-sm);
    overflow-x: auto;
    padding-bottom: var(--space-sm);
    margin-bottom: var(--space-xl);
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
  }
  .needs-scroll::-webkit-scrollbar { display: none; }

  .need-card {
    flex: 0 0 210px;
    background: #ffffff;
    border: 1px solid var(--color-border);
    border-radius: var(--radius-md);
    padding: var(--space-md);
    box-shadow: var(--shadow-xs);
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
    gap: 6px;
  }

  .need-card__icon {
    width: 44px;
    height: 44px;
    border-radius: var(--radius-sm);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 4px;
  }

  .need-card__title {
    font-family: var(--font-heading);
    font-size: 1.05rem;
    color: var(--color-text);
    margin: 0;
  }

  .need-card__desc {
    font-size: 0.82rem;
    color: var(--color-text-muted);
    line-height: 1.4;
    margin: 0;
  }

  /* ═══ Proceso de Germinación (Paso a Paso) ═══ */
  .germination-timeline {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-bottom: var(--space-xl);
  }

  .germination-step {
    background: #ffffff;
    border: 1px solid var(--color-border);
    border-radius: var(--radius-md);
    padding: var(--space-md);
    display: flex;
    align-items: flex-start;
    gap: 14px;
    box-shadow: var(--shadow-xs);
  }

  .germination-num {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: var(--color-primary);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 1.1rem;
    flex-shrink: 0;
  }

  .germination-step__title {
    font-family: var(--font-heading);
    font-size: 1.05rem;
    color: var(--color-text);
    margin: 0 0 4px;
  }

  .germination-step__text {
    font-size: 0.86rem;
    color: var(--color-text-muted);
    line-height: 1.45;
    margin: 0;
  }

  /* ═══ Sección Plantas Físicas con QR ═══ */
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
    cursor: pointer;
    transition: var(--transition);
    box-sizing: border-box;
    text-decoration: none;
    color: inherit;
  }

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
    height: 180px;
    background: var(--color-primary-soft);
    overflow: hidden;
  }

  .planta-card-mobile__image {
    width: 100%;
    height: 100%;
    object-fit: cover;
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
    margin: 0 0 6px;
  }

  .planta-card-mobile__desc {
    font-size: 0.86rem;
    color: var(--color-text-muted);
    line-height: 1.5;
    margin-bottom: var(--space-md);
    flex-grow: 1;
  }

  .planta-card-mobile__btn {
    min-height: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: var(--space-xs);
    background: var(--color-primary);
    color: #ffffff;
    border-radius: var(--radius-md);
    font-weight: 700;
    font-size: 0.92rem;
    text-decoration: none;
    transition: var(--transition);
  }

  .planta-card-mobile__btn:hover {
    background: var(--color-primary-dark);
  }

  /* Modal Escaneo */
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
    box-sizing: border-box;
  }
</style>

<main class="bio-container">

  <!-- ═══ BANNER PRINCIPAL ═══ -->
  <header class="bio-hero animate-fade-in">
    <div class="bio-hero__top">
      <span class="bio-badge">
        <?= icon('sprout', '', 14) ?> Biohuerto Escolar
      </span>
      <span style="font-size: 0.78rem; opacity: 0.95; font-weight: 700;">
        I.E.P. N.° 70090 Jayllihuaya
      </span>
    </div>
    <h2 class="bio-hero__title">Pequeños Guardianes de la Naturaleza</h2>
    <p class="bio-hero__desc">
      Aprende cómo crecen los alimentos, descubre los secretos de la fotosíntesis y conoce a nuestras hortalizas cultivadas con amor en Jayllihuaya – Puno.
    </p>
  </header>

  <!-- ═══ BOTONES DE ACCESO RÁPIDO A NUEVOS MÓDULOS ═══ -->
  <section class="quick-nav-grid" aria-label="Accesos rápidos del biohuerto">
    <a href="cultivos.php" class="quick-nav-card quick-nav-card--orange">
      <div class="quick-nav-icon" style="background:#ffedd5; color:#ea580c;">
        <?= icon('book', '', 24) ?>
      </div>
      <div>
        <h3 class="quick-nav-card__title">Fichas de Cultivos</h3>
        <span class="quick-nav-card__sub">10 hortalizas con buscador, recetas y datos curiosos.</span>
      </div>
    </a>

    <a href="amigos.php" class="quick-nav-card quick-nav-card--violet">
      <div class="quick-nav-icon" style="background:#ede9fe; color:#7c3aed;">
        <?= icon('users', '', 24) ?>
      </div>
      <div>
        <h3 class="quick-nav-card__title">Amigos del Huerto</h3>
        <span class="quick-nav-card__sub">11 personajes con historias interactivas en audio y video.</span>
      </div>
    </a>
  </section>

  <!-- ═══ SECCIÓN 1: ¿QUÉ ES UN BIOHUERTO ESCOLAR? ═══ -->
  <section style="margin-bottom: var(--space-2xl);">
    <div class="section-intro">
      <div class="section-intro__icon">
        <?= icon('leaf', '', 20) ?>
      </div>
      <div>
        <h3 class="section-intro__title">Conocemos nuestro Biohuerto</h3>
        <p class="section-intro__sub">Aprender haciendo, cultivando y cosechando salud</p>
      </div>
    </div>

    <div class="info-card">
      <h4><?= icon('sprout', '', 18) ?> ¿Qué es un Biohuerto Escolar?</h4>
      <p>
        Un biohuerto escolar es un espacio dentro de nuestra escuela donde estudiantes y maestros sembramos, cuidamos y cosechamos hortalizas, hierbas aromáticas y frutas de forma 100% natural, sin químicos ni pesticidas tóxicos. Es un laboratorio vivo donde aprendemos ciencia y trabajo en equipo.
      </p>
    </div>

    <!-- Beneficios: Alimentación y Medioambiente -->
    <div class="benefits-grid">
      <div class="benefit-card benefit-card--food">
        <div class="benefit-card__head">
          <?= icon('heart', '', 18) ?>
          <span>Beneficios en la Alimentación</span>
        </div>
        <p>
          Las hortalizas cosechadas están llenas de vitaminas, minerales y fibra. Al comer lechuga, acelga o zanahoria del biohuerto, los niños crecen fuertes, saludables y previenen enfermedades como la anemia.
        </p>
      </div>

      <div class="benefit-card benefit-card--eco">
        <div class="benefit-card__head">
          <?= icon('sun', '', 18) ?>
          <span>Cuidado del Medioambiente</span>
        </div>
        <p>
          Transformamos cáscaras de frutas en abono orgánico (compost), ahorramos agua con riego por goteo y purificamos el aire de Jayllihuaya. ¡Cultivar un huerto enfría el planeta!
        </p>
      </div>
    </div>
  </section>

  <!-- ═══ SECCIÓN 2: EL MARAVILLOSO MUNDO DE LAS PLANTAS ═══ -->
  <section style="margin-bottom: var(--space-2xl);">
    <div class="section-intro">
      <div class="section-intro__icon" style="background:#fef3c7; color:#b45309;">
        <?= icon('sprout', '', 20) ?>
      </div>
      <div>
        <h3 class="section-intro__title">El Maravilloso Mundo de las Plantas</h3>
        <p class="section-intro__sub">Toca cada parte para descubrir su misterio botánico</p>
      </div>
    </div>

    <!-- Acordeones Interactivos: Partes de la Planta -->
    <div class="accordion-group" id="partesPlanta">

      <!-- Raíz -->
      <div class="accordion-item">
        <button class="accordion-header" type="button" aria-expanded="false">
          <span class="accordion-header__left">
            <span class="accordion-badge" style="background:#fef3c7; color:#b45309;">1</span>
            <strong>La Raíz (Bajo tierra)</strong>
          </span>
          <span class="accordion-arrow"><?= icon('chevron-down', '', 18) ?></span>
        </button>
        <div class="accordion-content">
          <p>
            Crece bajo el suelo. Cumple dos funciones vitales: <strong>fijar la planta</strong> con fuerza para que no se caiga con el viento, y <strong>absorber agua y sales minerales</strong> de la tierra.
          </p>
          <div class="accordion-content__funfact">
            <?= icon('info', '', 16) ?>
            <span>¡En la zanahoria y el rabanito, nos comemos la raíz entera porque allí almacenan energía!</span>
          </div>
        </div>
      </div>

      <!-- Tallo -->
      <div class="accordion-item">
        <button class="accordion-header" type="button" aria-expanded="false">
          <span class="accordion-header__left">
            <span class="accordion-badge" style="background:#e0f2fe; color:#0369a1;">2</span>
            <strong>El Tallo (La columna vertebral)</strong>
          </span>
          <span class="accordion-arrow"><?= icon('chevron-down', '', 18) ?></span>
        </button>
        <div class="accordion-content">
          <p>
            Sostiene a las hojas, flores y frutos elevados hacia el sol. Por dentro tiene pequeños tubitos (como cañitas) que transportan agua desde la raíz hasta la punta más alta.
          </p>
          <div class="accordion-content__funfact">
            <?= icon('info', '', 16) ?>
            <span>En la acelga y el apio consumimos sus tallos carnosos y crujientes (pencas).</span>
          </div>
        </div>
      </div>

      <!-- Hojas -->
      <div class="accordion-item">
        <button class="accordion-header" type="button" aria-expanded="false">
          <span class="accordion-header__left">
            <span class="accordion-badge" style="background:#dcfce7; color:#15803d;">3</span>
            <strong>Las Hojas (La fábrica de alimento)</strong>
          </span>
          <span class="accordion-arrow"><?= icon('chevron-down', '', 18) ?></span>
        </button>
        <div class="accordion-content">
          <p>
            Son verdes por la <strong>clorofila</strong>. Aquí ocurre la <strong>fotosíntesis</strong>: usando la luz del sol, agua y aire (CO2), la planta fabrica su propio alimento (azúcares) y nos regala oxígeno puro.
          </p>
          <div class="accordion-content__funfact">
            <?= icon('info', '', 16) ?>
            <span>¡La lechuga y la espinaca son ejemplos de deliciosas hojas que nutren nuestro cuerpo!</span>
          </div>
        </div>
      </div>

      <!-- Flores -->
      <div class="accordion-item">
        <button class="accordion-header" type="button" aria-expanded="false">
          <span class="accordion-header__left">
            <span class="accordion-badge" style="background:#fce7f3; color:#be185d;">4</span>
            <strong>Las Flores (La reproducción)</strong>
          </span>
          <span class="accordion-arrow"><?= icon('chevron-down', '', 18) ?></span>
        </button>
        <div class="accordion-content">
          <p>
            Tienen colores vistosos y perfumes dulces para atraer a las abejas y mariposas. Los polinizadores transportan el polen y permiten que la flor se transforme en un fruto fértil.
          </p>
          <div class="accordion-content__funfact">
            <?= icon('info', '', 16) ?>
            <span>El brócoli y la coliflor que comemos en casa son en realidad ramilletes de flores tiernas.</span>
          </div>
        </div>
      </div>

      <!-- Frutos -->
      <div class="accordion-item">
        <button class="accordion-header" type="button" aria-expanded="false">
          <span class="accordion-header__left">
            <span class="accordion-badge" style="background:#fee2e2; color:#b91c1c;">5</span>
            <strong>El Fruto (El escudo protector)</strong>
          </span>
          <span class="accordion-arrow"><?= icon('chevron-down', '', 18) ?></span>
        </button>
        <div class="accordion-content">
          <p>
            Es el ovario maduro de la flor. Protege a las semillas en su interior y sirve como tentador alimento para animales y personas, ayudando a dispersar las semillas por el campo.
          </p>
          <div class="accordion-content__funfact">
            <?= icon('info', '', 16) ?>
            <span>El tomate, el aguaymanto y la fresa son frutos repletos de agua y vitaminas.</span>
          </div>
        </div>
      </div>

      <!-- Semillas -->
      <div class="accordion-item">
        <button class="accordion-header" type="button" aria-expanded="false">
          <span class="accordion-header__left">
            <span class="accordion-badge" style="background:#ffedd5; color:#c2410c;">6</span>
            <strong>La Semilla (El origen de la vida)</strong>
          </span>
          <span class="accordion-arrow"><?= icon('chevron-down', '', 18) ?></span>
        </button>
        <div class="accordion-content">
          <p>
            En su interior guarda un bebé de planta (embrión) en estado dormido y un paquete de alimento. Cuando toca tierra húmeda y cálida, despierta y comienza a crecer.
          </p>
          <div class="accordion-content__funfact">
            <?= icon('info', '', 16) ?>
            <span>Las habas, arvejas y el maíz son semillas energéticas fundamentales en Puno.</span>
          </div>
        </div>
      </div>

    </div>

    <!-- ═══ Necesidades de las Plantas ═══ -->
    <div style="margin-bottom: var(--space-xs); display: flex; align-items: center; justify-content: space-between;">
      <h4 style="font-family: var(--font-heading); font-size: 1.15rem; color: var(--color-text); margin: 0;">
        ¿Qué necesita una planta para vivir?
      </h4>
      <span style="font-size: 0.75rem; color: var(--color-text-muted);">Desliza &rarr;</span>
    </div>

    <div class="needs-scroll">
      <div class="need-card">
        <div class="need-card__icon" style="background:#fef3c7; color:#d97706;">
          <?= icon('sun', '', 24) ?>
        </div>
        <h5 class="need-card__title">1. Luz Solar</h5>
        <p class="need-card__desc">
          El sol es la fuente de energía. Sin sol, las hojas no pueden encender su motor de fotosíntesis y se vuelven amarillas.
        </p>
      </div>

      <div class="need-card">
        <div class="need-card__icon" style="background:#e0f2fe; color:#0284c7;">
          <?= icon('droplet', '', 24) ?>
        </div>
        <h5 class="need-card__title">2. Agua Limpia</h5>
        <p class="need-card__desc">
          Disuelve los minerales del suelo para que las raíces los beban. Mantiene a la planta erguida y fresca.
        </p>
      </div>

      <div class="need-card">
        <div class="need-card__icon" style="background:#ede9fe; color:#7c3aed;">
          <?= icon('wind', '', 24) ?>
        </div>
        <h5 class="need-card__title">3. Aire Puro (CO₂)</h5>
        <p class="need-card__desc">
          Respira dióxido de carbono del aire para tejer sus tallos y devuelve oxígeno limpio para nosotros.
        </p>
      </div>

      <div class="need-card">
        <div class="need-card__icon" style="background:#dcfce7; color:#15803d;">
          <?= icon('layers', '', 24) ?>
        </div>
        <h5 class="need-card__title">4. Suelo Fértil</h5>
        <p class="need-card__desc">
          Tierra esponjosa con abono compostado que retiene la humedad y ofrece minerales como nitrógeno y potasio.
        </p>
      </div>
    </div>

    <!-- ═══ Proceso de Germinación ═══ -->
    <h4 style="font-family: var(--font-heading); font-size: 1.15rem; color: var(--color-text); margin: 0 0 var(--space-sm);">
      El Milagro de la Germinación (Paso a Paso)
    </h4>

    <div class="germination-timeline">
      <div class="germination-step">
        <div class="germination-num">1</div>
        <div>
          <h5 class="germination-step__title">La Semilla Despierta (Absorción de agua)</h5>
          <p class="germination-step__text">
            Al sembrarla en tierra húmeda, la semilla bebe agua, se hincha y su cáscara exterior se ablanda.
          </p>
        </div>
      </div>

      <div class="germination-step">
        <div class="germination-num">2</div>
        <div>
          <h5 class="germination-step__title">Nace la Radícula (Hacia abajo)</h5>
          <p class="germination-step__text">
            Rompe la cáscara y asoma la primera raíz pequeña, que busca la gravedad hacia abajo para fijarse al suelo.
          </p>
        </div>
      </div>

      <div class="germination-step">
        <div class="germination-num">3</div>
        <div>
          <h5 class="germination-step__title">Brote del Tallito (Hacia arriba)</h5>
          <p class="germination-step__text">
            Un tallo fino se estira hacia la superficie empujando hacia arriba en busca de la luz del sol.
          </p>
        </div>
      </div>

      <div class="germination-step">
        <div class="germination-num">4</div>
        <div>
          <h5 class="germination-step__title">Nacimiento de las Primeras Hojas</h5>
          <p class="germination-step__text">
            Se abren los cotiledones y brotan las hojas verdaderas. ¡Comienza la fotosíntesis y la plántula ya es autónoma!
          </p>
        </div>
      </div>
    </div>
  </section>

  <!-- ═══ SECCIÓN 3: PLANTAS DE NUESTRO HUERTO (CON QR) ═══ -->
  <section id="plantasRegistradas">
    <div class="section-intro">
      <div class="section-intro__icon" style="background:#d1fae5; color:#059669;">
        <?= icon('camera', '', 20) ?>
      </div>
      <div>
        <h3 class="section-intro__title">Plantas Registradas con QR</h3>
        <p class="section-intro__sub">Identifica los cultivos sembrados en los bancales del colegio</p>
      </div>
    </div>

    <!-- Botón Escanear QR -->
    <div class="qr-scanner-trigger" id="btnEscanearQR">
      <div class="qr-scanner-trigger__icon">
        <?= icon('qr-code', '', 26) ?>
      </div>
      <div style="flex-grow: 1;">
        <span style="font-weight: 800; font-size: 1rem; color: var(--color-primary-dark); display: block;">
          Escanear QR en el Biohuerto
        </span>
        <span style="font-size: 0.82rem; color: var(--color-text-muted); display: block; margin-top: 2px;">
          ¿Estás frente a un bancal? Apunta la cámara del celular al cartel de la planta.
        </span>
      </div>
      <span style="color: var(--color-primary);">
        <?= icon('camera', '', 22) ?>
      </span>
    </div>

    <!-- Grid de Plantas de la Base de Datos -->
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: var(--space-md); flex-wrap: wrap; gap: 8px;">
      <h4 style="font-family: var(--font-heading); font-size: 1.15rem; color: var(--color-text); margin: 0;">
        Especies en el Huerto (<?= count($plantas) ?>)
      </h4>
      <?php if (isset($_SESSION['rol_id']) && $_SESSION['rol_id'] == 1): ?>
        <a href="../admin/plantas/crear.php" class="btn btn--primary btn--sm">
          <?= icon('plus', '', 16) ?>
          <span>Registrar Planta</span>
        </a>
      <?php endif; ?>
    </div>

    <?php if (empty($plantas)): ?>
      <div class="card" style="text-align: center; padding: var(--space-xl); background: #ffffff; border-radius: var(--radius-md);">
        <div style="color: var(--color-primary); margin-bottom: 8px;">
          <?= icon('sprout', '', 40) ?>
        </div>
        <h4 style="font-family: var(--font-heading); font-size: 1.15rem; color: var(--color-text); margin: 0 0 4px;">
          Explora también nuestras Fichas de Cultivos
        </h4>
        <p style="font-size: 0.88rem; color: var(--color-text-muted); margin: 0 0 var(--space-md);">
          Conoce en detalle los 10 cultivos principales con fotos, recetas, días de cosecha y audios interactivos.
        </p>
        <a href="cultivos.php" class="btn btn--primary">
          <?= icon('book', '', 16) ?>
          <span>Ver Catálogo de Cultivos</span>
        </a>
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

              <a href="planta.php?id=<?= $planta['id'] ?>" class="planta-card-mobile__btn">
                <span>Ver Ficha Técnica</span>
                <?= icon('arrow-right', '', 16) ?>
              </a>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

  </section>

</main>

<!-- Modal Explicativo de Escaneo QR -->
<div class="modal-backdrop" id="modalQR">
  <div class="modal-sheet">
    <div style="width: 56px; height: 56px; border-radius: 50%; background: var(--color-primary-soft); color: var(--color-primary); display: flex; align-items: center; justify-content: center; margin: 0 auto var(--space-sm);">
      <?= icon('camera', '', 28) ?>
    </div>
    <h3 style="font-family: var(--font-heading); font-size: 1.35rem; color: var(--color-text); margin-bottom: var(--space-xs);">
      ¿Cómo escanear en el Huerto?
    </h3>
    <p style="font-size: 0.9rem; color: var(--color-text-muted); line-height: 1.5; margin-bottom: var(--space-md);">
      Cada bancal de la <strong>I.E.P. N.° 70090 Jayllihuaya</strong> cuenta con un cartel identificador con su código QR. Abre la cámara de tu celular o tablet, enfoca el cartel y se abrirá al instante la ficha botánica.
    </p>
    <button type="button" class="btn btn--primary btn--full" id="cerrarModalQR">
      <?= icon('check', '', 16) ?>
      <span>¡Entendido!</span>
    </button>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  // Acordeón interactivo de partes de la planta
  const items = document.querySelectorAll('.accordion-item');
  items.forEach(item => {
    const btn = item.querySelector('.accordion-header');
    btn.addEventListener('click', () => {
      const wasActive = item.classList.contains('active');
      items.forEach(i => {
        i.classList.remove('active');
        i.querySelector('.accordion-header').setAttribute('aria-expanded', 'false');
      });
      if (!wasActive) {
        item.classList.add('active');
        btn.setAttribute('aria-expanded', 'true');
        if (window.bioSound) window.bioSound.playClick();
      }
    });
  });

  // Modal QR
  const btnEscanear = document.getElementById('btnEscanearQR');
  const modal = document.getElementById('modalQR');
  const btnCerrar = document.getElementById('cerrarModalQR');

  if (btnEscanear && modal) {
    btnEscanear.addEventListener('click', () => {
      modal.classList.add('active');
      if (window.bioSound) window.bioSound.playClick();
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
