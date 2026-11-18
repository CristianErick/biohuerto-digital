<?php
/**
 * PLATAFORMA DE JUEGOS EDUCATIVOS POR CICLOS — Semillitas del Futuro
 * I.E.P. N.° 70090 Jayllihuaya · Puno
 * Mobile-First · Sin dependencias externas · Web Audio API · Estrellas de Victoria
 */
require_once __DIR__ . '/../config/database.php';

$plantaRecomendada = null;
$plantaParam = (int) ($_GET['planta'] ?? 0);
if ($plantaParam > 0) {
    try {
        $pdo = getSafeConnection();
        if ($pdo) {
            $stmtPlanta = $pdo->prepare('SELECT nombre FROM plantas WHERE id = :id LIMIT 1');
            $stmtPlanta->execute([':id' => $plantaParam]);
            $plantaRecomendada = $stmtPlanta->fetch() ?: null;
        }
    } catch (Throwable $e) {
        $plantaRecomendada = null;
    }
}

$pageTitle = 'Juegos Educativos por Ciclos — Semillitas del Futuro';
$pageDesc  = 'Minijuegos educativos interactivos por ciclos para primaria: partes de la planta, memoria, germinación, trivias y crucigramas ecológicos.';
$isSubfolder = true;
include __DIR__ . '/../includes/header.php';
?>

<link rel="stylesheet" href="css/juegos.css?v=20261117b">

<style>
  .juegos-page-container {
    max-width: 768px;
    margin: 0 auto;
    padding: 0 var(--space-md) var(--space-2xl);
    box-sizing: border-box;
  }

  .juegos-hero {
    background: linear-gradient(135deg, #d97706 0%, #f59e0b 60%, #fbbf24 100%);
    color: #ffffff;
    border-radius: var(--radius-lg);
    padding: var(--space-lg) var(--space-md);
    margin-bottom: var(--space-lg);
    box-shadow: var(--shadow-sm);
    text-align: center;
  }

  .juegos-hero__badge {
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

  .juegos-hero__title {
    font-family: var(--font-heading);
    font-size: 1.85rem;
    color: #ffffff;
    margin: 0 0 6px;
  }

  .juegos-hero__desc {
    font-size: 0.94rem;
    color: rgba(255, 255, 255, 0.95);
    margin: 0 auto;
    max-width: 520px;
    line-height: 1.5;
  }
</style>

<div class="juegos-page-container">

  <!-- Hero Banner -->
  <header class="juegos-hero animate-fade-in">
    <span class="juegos-hero__badge">
      <?= icon('gamepad', '', 14) ?> Plataforma Educativa Lúdica
    </span>
    <h2 class="juegos-hero__title">Zona de Juegos por Ciclos</h2>
    <p class="juegos-hero__desc">
      Aprende sobre botánica, reciclaje, agua y cultivos a través de desafíos interactivos organizados para cada grado escolar.
    </p>
  </header>

  <?php if ($plantaRecomendada): ?>
    <div style="background:#ecfdf5; border:1px solid #a7f3d0; color:#065f46; border-radius:var(--radius-md); padding:10px 14px; margin-bottom:var(--space-md); display:flex; align-items:center; gap:8px; font-size:0.9rem;">
      <?= icon('sprout', '', 20) ?>
      <span>Recomendado desde la ficha de <strong><?= htmlspecialchars($plantaRecomendada['nombre']) ?></strong>. ¡Elige un juego y diviértete aprendiendo!</span>
    </div>
  <?php endif; ?>

  <!-- Pestañas de Selección de Ciclos Educativos -->
  <nav class="ciclos-tabs" aria-label="Selector de Ciclos Educativos">
    <button class="ciclo-tab-btn ciclo-tab-btn--c3 active" data-ciclo="c3" type="button">
      <span class="ciclo-tab-btn__badge">Ciclo III</span>
      <span class="ciclo-tab-btn__title">Exploradores</span>
      <span class="ciclo-tab-btn__grades">1.° y 2.° Grado</span>
    </button>

    <button class="ciclo-tab-btn ciclo-tab-btn--c4" data-ciclo="c4" type="button">
      <span class="ciclo-tab-btn__badge">Ciclo IV</span>
      <span class="ciclo-tab-btn__title">Detectives</span>
      <span class="ciclo-tab-btn__grades">3.° y 4.° Grado</span>
    </button>

    <button class="ciclo-tab-btn ciclo-tab-btn--c5" data-ciclo="c5" type="button">
      <span class="ciclo-tab-btn__badge">Ciclo V</span>
      <span class="ciclo-tab-btn__title">Maestros</span>
      <span class="ciclo-tab-btn__grades">5.° y 6.° Grado</span>
    </button>
  </nav>

  <!-- ══════════════════════════════════════════════════════════════
       PANEL CICLO III: EXPLORADORES (1.° y 2.° Grado)
       ══════════════════════════════════════════════════════════════ -->
  <section class="ciclo-panel active" id="panel-c3" aria-label="Juegos Ciclo III">
    <div class="juegos-grid">

      <!-- Juego 1: Drag-and-drop Partes de la Planta -->
      <article class="juego-card">
        <div class="juego-card__top">
          <div class="juego-card__icon" style="background:#d1fae5; color:#059669;">
            <?= icon('sprout', '', 28) ?>
          </div>
          <div>
            <span class="juego-card__tag" style="background:#ecfdf5; color:#059669;">Interactivo</span>
            <h3 class="juego-card__title">Partes de la Planta</h3>
          </div>
        </div>
        <p class="juego-card__desc">
          Identifica la raíz, tallo, hojas, flor y fruto tocando cada parte y ubicándola en su lugar.
        </p>
        <div class="juego-card__footer">
          <button type="button" class="btn btn--primary btn--full" onclick="window.gameManager.abrir('partesPlanta')">
            <?= icon('play', '', 16) ?>
            <span>Jugar Partes</span>
          </button>
        </div>
      </article>

      <!-- Juego 2: Memoria del Biohuerto -->
      <article class="juego-card">
        <div class="juego-card__top">
          <div class="juego-card__icon" style="background:#ffedd5; color:#ea580c;">
            <?= icon('grid', '', 28) ?>
          </div>
          <div>
            <span class="juego-card__tag" style="background:#fff7ed; color:#c2410c;">Memoria</span>
            <h3 class="juego-card__title">Memoria de Hortalizas</h3>
          </div>
        </div>
        <p class="juego-card__desc">
          Voltea las cartas y encuentra las parejas de zanahorias, lechugas, tomates y fresas.
        </p>
        <div class="juego-card__footer">
          <button type="button" class="btn btn--primary btn--full" onclick="window.gameManager.abrir('memoria')">
            <?= icon('play', '', 16) ?>
            <span>Jugar Memoria</span>
          </button>
        </div>
      </article>

      <!-- Juego 3: Qué necesita una planta para vivir -->
      <article class="juego-card">
        <div class="juego-card__top">
          <div class="juego-card__icon" style="background:#fef3c7; color:#d97706;">
            <?= icon('sun', '', 28) ?>
          </div>
          <div>
            <span class="juego-card__tag" style="background:#fffbeb; color:#b45309;">Selección</span>
            <h3 class="juego-card__title">¿Qué necesita para vivir?</h3>
          </div>
        </div>
        <p class="juego-card__desc">
          Elige entre Agua, Sol, Tierra y Aire evitando los distractores que no nutren a la planta.
        </p>
        <div class="juego-card__footer">
          <button type="button" class="btn btn--primary btn--full" onclick="window.gameManager.abrir('necesidades')">
            <?= icon('play', '', 16) ?>
            <span>Elegir Elementos</span>
          </button>
        </div>
      </article>

      <!-- Juego 4: Secuencia de Germinación -->
      <article class="juego-card">
        <div class="juego-card__top">
          <div class="juego-card__icon" style="background:#ede9fe; color:#7c3aed;">
            <?= icon('layers', '', 28) ?>
          </div>
          <div>
            <span class="juego-card__tag" style="background:#f5f3ff; color:#6d28d9;">Secuencia</span>
            <h3 class="juego-card__title">Secuencia de Germinación</h3>
          </div>
        </div>
        <p class="juego-card__desc">
          Ordena del 1 al 4 los pasos del nacimiento de la plántula desde que bebe agua hasta sus hojas.
        </p>
        <div class="juego-card__footer">
          <button type="button" class="btn btn--primary btn--full" onclick="window.gameManager.abrir('secuencia')">
            <?= icon('play', '', 16) ?>
            <span>Ordenar Pasos</span>
          </button>
        </div>
      </article>

      <!-- Juego 5: Clasificación Acciones del Agua -->
      <article class="juego-card">
        <div class="juego-card__top">
          <div class="juego-card__icon" style="background:#e0f2fe; color:#0284c7;">
            <?= icon('droplet', '', 28) ?>
          </div>
          <div>
            <span class="juego-card__tag" style="background:#f0f9ff; color:#0369a1;">Ecología</span>
            <h3 class="juego-card__title">Cuidamos el Agua</h3>
          </div>
        </div>
        <p class="juego-card__desc">
          Clasifica las acciones que cuidan el agua vs las que la desperdician en la escuela.
        </p>
        <div class="juego-card__footer">
          <button type="button" class="btn btn--primary btn--full" onclick="window.gameManager.abrir('accionesAgua')">
            <?= icon('play', '', 16) ?>
            <span>Clasificar Acciones</span>
          </button>
        </div>
      </article>

    </div>
  </section>

  <!-- ══════════════════════════════════════════════════════════════
       PANEL CICLO IV: DETECTIVES DEL BIOHUERTO (3.° y 4.° Grado)
       ══════════════════════════════════════════════════════════════ -->
  <section class="ciclo-panel" id="panel-c4" aria-label="Juegos Ciclo IV">
    <div class="juegos-grid">

      <!-- Juego 1: Adivina el Cultivo -->
      <article class="juego-card">
        <div class="juego-card__top">
          <div class="juego-card__icon" style="background:#e0f2fe; color:#0284c7;">
            <?= icon('search', '', 28) ?>
          </div>
          <div>
            <span class="juego-card__tag" style="background:#eff6ff; color:#1d4ed8;">Trivia con Pistas</span>
            <h3 class="juego-card__title">Adivina el Cultivo</h3>
          </div>
        </div>
        <p class="juego-card__desc">
          Lee las 3 pistas misteriosas de cada caso y deduce qué hortaliza o fruta del huerto es.
        </p>
        <div class="juego-card__footer">
          <button type="button" class="btn btn--primary btn--full" onclick="window.gameManager.abrir('adivina')">
            <?= icon('play', '', 16) ?>
            <span>Resolver Misterio</span>
          </button>
        </div>
      </article>

      <!-- Juego 2: Une cada Parte con su Función -->
      <article class="juego-card">
        <div class="juego-card__top">
          <div class="juego-card__icon" style="background:#dcfce7; color:#15803d;">
            <?= icon('check-circle', '', 28) ?>
          </div>
          <div>
            <span class="juego-card__tag" style="background:#f0fdf4; color:#166534;">Relación Lineal</span>
            <h3 class="juego-card__title">Parte y Función</h3>
          </div>
        </div>
        <p class="juego-card__desc">
          Conecta cada órgano botánico (raíz, tallo, hojas, flor, fruto) con su tarea biológica.
        </p>
        <div class="juego-card__footer">
          <button type="button" class="btn btn--primary btn--full" onclick="window.gameManager.abrir('uneFunciones')">
            <?= icon('play', '', 16) ?>
            <span>Emparejar Funciones</span>
          </button>
        </div>
      </article>

      <!-- Juego 3: Verdadero o Falso -->
      <article class="juego-card">
        <div class="juego-card__top">
          <div class="juego-card__icon" style="background:#fee2e2; color:#dc2626;">
            <?= icon('award', '', 28) ?>
          </div>
          <div>
            <span class="juego-card__tag" style="background:#fef2f2; color:#b91c1c;">Reto Rápido</span>
            <h3 class="juego-card__title">Verdadero o Falso</h3>
          </div>
        </div>
        <p class="juego-card__desc">
          Demuestra tu conocimiento ecológico respondiendo afirmaciones sobre compost, sol y riego.
        </p>
        <div class="juego-card__footer">
          <button type="button" class="btn btn--primary btn--full" onclick="window.gameManager.abrir('verdaderoFalso')">
            <?= icon('play', '', 16) ?>
            <span>Responder Reto</span>
          </button>
        </div>
      </article>

      <!-- Juego 4: Clasifica según parte comestible -->
      <article class="juego-card">
        <div class="juego-card__top">
          <div class="juego-card__icon" style="background:#ffedd5; color:#ea580c;">
            <?= icon('tag', '', 28) ?>
          </div>
          <div>
            <span class="juego-card__tag" style="background:#fff7ed; color:#c2410c;">Clasificación</span>
            <h3 class="juego-card__title">Partes Comestibles</h3>
          </div>
        </div>
        <p class="juego-card__desc">
          Ubica los 10 cultivos en sus canastas correctas: Raíces, Hojas, Frutos o Bulbos.
        </p>
        <div class="juego-card__footer">
          <button type="button" class="btn btn--primary btn--full" onclick="window.gameManager.abrir('comestibles')">
            <?= icon('play', '', 16) ?>
            <span>Llenar Canastas</span>
          </button>
        </div>
      </article>

      <!-- Juego 5: ¡Ayuda a Gotita! -->
      <article class="juego-card">
        <div class="juego-card__top">
          <div class="juego-card__icon" style="background:#e0f2fe; color:#0284c7;">
            <?= icon('droplet', '', 28) ?>
          </div>
          <div>
            <span class="juego-card__tag" style="background:#f0f9ff; color:#0369a1;">Misión Gotita</span>
            <h3 class="juego-card__title">¡Ayuda a Gotita!</h3>
          </div>
        </div>
        <p class="juego-card__desc">
          Resuelve situaciones reales para ahorrar y reutilizar el agua en el huerto escolar.
        </p>
        <div class="juego-card__footer">
          <button type="button" class="btn btn--primary btn--full" onclick="window.gameManager.abrir('gotita')">
            <?= icon('play', '', 16) ?>
            <span>Salvar el Agua</span>
          </button>
        </div>
      </article>

    </div>
  </section>

  <!-- ══════════════════════════════════════════════════════════════
       PANEL CICLO V: MAESTROS DEL BIOHUERTO (5.° y 6.° Grado)
       ══════════════════════════════════════════════════════════════ -->
  <section class="ciclo-panel" id="panel-c5" aria-label="Juegos Ciclo V">
    <div class="juegos-grid">

      <!-- Juego 1: Sopa de Letras Botánica -->
      <article class="juego-card">
        <div class="juego-card__top">
          <div class="juego-card__icon" style="background:#d1fae5; color:#059669;">
            <?= icon('search', '', 28) ?>
          </div>
          <div>
            <span class="juego-card__tag" style="background:#ecfdf5; color:#047857;">Agilidad Mental</span>
            <h3 class="juego-card__title">Sopa de Letras Verde</h3>
          </div>
        </div>
        <p class="juego-card__desc">
          Encuentra palabras técnicas de agronomía: Clorofila, Compost, Semilla, Riego y Abono.
        </p>
        <div class="juego-card__footer">
          <button type="button" class="btn btn--primary btn--full" onclick="window.gameManager.abrir('sopa')">
            <?= icon('play', '', 16) ?>
            <span>Buscar Palabras</span>
          </button>
        </div>
      </article>

      <!-- Juego 2: Crucigrama Didáctico -->
      <article class="juego-card">
        <div class="juego-card__top">
          <div class="juego-card__icon" style="background:#ede9fe; color:#7c3aed;">
            <?= icon('grid', '', 28) ?>
          </div>
          <div>
            <span class="juego-card__tag" style="background:#f5f3ff; color:#6d28d9;">Crucigrama</span>
            <h3 class="juego-card__title">Crucigrama Agrícola</h3>
          </div>
        </div>
        <p class="juego-card__desc">
          Escribe las letras en las casillas cruzadas respondiendo a pistas botánicas de Jayllihuaya.
        </p>
        <div class="juego-card__footer">
          <button type="button" class="btn btn--primary btn--full" onclick="window.gameManager.abrir('crucigrama')">
            <?= icon('play', '', 16) ?>
            <span>Resolver Crucigrama</span>
          </button>
        </div>
      </article>

      <!-- Juego 3: Procesos y Nutrición Avanzada -->
      <article class="juego-card">
        <div class="juego-card__top">
          <div class="juego-card__icon" style="background:#ffedd5; color:#ea580c;">
            <?= icon('zap', '', 28) ?>
          </div>
          <div>
            <span class="juego-card__tag" style="background:#fff7ed; color:#c2410c;">Ciencia Avanzada</span>
            <h3 class="juego-card__title">Procesos y Nutrición</h3>
          </div>
        </div>
        <p class="juego-card__desc">
          Relaciona fotosíntesis, compostaje, polinización y clorofila con su base científica.
        </p>
        <div class="juego-card__footer">
          <button type="button" class="btn btn--primary btn--full" onclick="window.gameManager.abrir('procesos')">
            <?= icon('play', '', 16) ?>
            <span>Iniciar Desafío</span>
          </button>
        </div>
      </article>

    </div>
  </section>

</div>

<!-- ══════════════════════════════════════════════════════════════
     MODAL GLOBAL UNIFICADO PARA LOS JUEGOS
     ══════════════════════════════════════════════════════════════ -->
<div class="juego-modal-overlay" id="modalJuegoGlobal" onclick="cerrarJuegoModal(event)">
  <div class="juego-modal" onclick="event.stopPropagation()">

    <!-- Header Modal -->
    <header class="juego-modal__header">
      <h3 class="juego-modal__title" id="juegoTituloModal">Juego Educativo</h3>
      <button type="button" class="juego-modal__close" onclick="window.gameManager.cerrar()" aria-label="Cerrar juego">
        <?= icon('x', '', 18) ?>
      </button>
    </header>

    <!-- HUD (Temporizador y Puntos) -->
    <div class="juego-hud">
      <div class="juego-hud__timer">
        <?= icon('clock', '', 16) ?>
        <span id="juegoTimerTexto">00:00</span>
      </div>
      <div class="juego-hud__score">
        <?= icon('award', '', 16) ?>
        <span id="juegoPuntosTexto">0 pts</span>
      </div>
    </div>

    <!-- Cuerpo Dinámico del Juego -->
    <div class="juego-modal__body" id="juegoContenidoDinamico">
      <!-- El script inyecta el contenido interactivo aquí -->
    </div>

    <!-- Overlay de Victoria con Estrellas Animadas -->
    <div class="victoria-overlay" id="victoriaOverlay">
      <div style="font-size:2.8rem; margin-bottom:4px;">🏆</div>
      <h3 class="victoria-title">¡Misión Cumplida!</h3>
      <div class="stars-row">
        <svg class="star-icon filled" viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
        <svg class="star-icon filled" viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
        <svg class="star-icon filled" viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
      </div>
      <p class="victoria-msg" id="victoriaMsg"></p>
      <div id="victoriaTime" style="font-size:0.85rem; font-weight:700; color:#059669; margin-bottom:14px;"></div>
      <div style="display:flex; gap:8px;">
        <button type="button" class="btn btn--primary" onclick="reiniciarJuegoActual()">
          <?= icon('refresh-ccw', '', 16) ?>
          <span>Jugar Otra Vez</span>
        </button>
        <button type="button" class="btn btn--outline" onclick="window.gameManager.cerrar()">
          <?= icon('arrow-left', '', 16) ?>
          <span>Elegir Otro Juego</span>
        </button>
      </div>
    </div>

    <!-- Acciones Inferiores -->
    <footer class="juego-modal__actions">
      <button type="button" class="btn btn--outline btn--full" onclick="window.gameManager.cerrar()">
        <?= icon('arrow-left', '', 16) ?>
        <span>Salir al Menú de Juegos</span>
      </button>
    </footer>

  </div>
</div>

<!-- Scripts de Sonidos y Motor de Juegos -->
<script src="../assets/js/sounds.js?v=20261117"></script>
<script src="js/juegos-engine.js?v=20261117b"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
  // Manejo de Pestañas de Ciclos
  const tabBtns = document.querySelectorAll('.ciclo-tab-btn');
  const panels = document.querySelectorAll('.ciclo-panel');

  tabBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      tabBtns.forEach(b => b.classList.remove('active'));
      panels.forEach(p => p.classList.remove('active'));

      btn.classList.add('active');
      const ciclo = btn.getAttribute('data-ciclo');
      const panel = document.getElementById(`panel-${ciclo}`);
      if (panel) panel.classList.add('active');

      if (window.bioSound) window.bioSound.playClick();
    });
  });

  // Si viene con parámetro ?juego= en la URL
  const params = new URLSearchParams(window.location.search);
  const juegoParam = params.get('juego');
  if (juegoParam) {
    if (juegoParam === 'gotita') {
      const tabC4 = document.querySelector('.ciclo-tab-btn--c4');
      if (tabC4) tabC4.click();
    }
    setTimeout(() => {
      window.gameManager.abrir(juegoParam);
    }, 250);
  }
});

function cerrarJuegoModal(e) {
  if (e && e.target !== e.currentTarget) return;
  window.gameManager.cerrar();
}

function reiniciarJuegoActual() {
  if (window.gameManager && window.gameManager.juegoActual) {
    window.gameManager.abrir(window.gameManager.juegoActual);
  }
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
