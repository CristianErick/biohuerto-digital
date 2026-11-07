<?php
/**
 * SEMILLITAS DEL FUTURO — Página Principal (index.php)
 * Ilustraciones Gráficas Infantiles · Mobile-First
 */

$pageTitle = 'Semillitas del Futuro — Plataforma Educativa';
$basePath  = '';
include __DIR__ . '/includes/header.php';
?>

  <!-- ═══ HERO PRINCIPAL ═══ -->
  <section class="hero" aria-label="Bienvenida">
    <div class="hero__badge">
      <img src="assets/img/logo-mascota.svg" alt="Semillita" style="width:20px; height:20px; display:inline-block;">
      <span>Educación Primaria y Ambiental</span>
    </div>
    <h2 class="hero__heading">Aprende, cultiva y explora la naturaleza</h2>
    <p class="hero__text">
      Un espacio digital interactivo diseñado para niños de primaria y docentes. Descubre el mundo de las plantas, las ciencias y el cuidado de nuestro planeta.
    </p>
  </section>

  <!-- ═══ SECCIONES PRINCIPALES ═══ -->
  <main class="sections" id="secciones">

    <!-- Alerta Informativa / Tip del Día -->
    <div class="alert-banner alert-banner--success animate-fade-in" role="alert">
      <span class="alert-banner__icon">
        <?= icon('check-circle', '', 20) ?>
      </span>
      <div class="alert-banner__content">
        <strong>Consejo ecológico escolar</strong>
        <span>Cuidar nuestro biohuerto ayuda a preservar abejas y mariposas polinizadoras. Visita la sección de Biohuerto para conocer los cultivos del colegio.</span>
      </div>
      <button class="alert-banner__close" aria-label="Cerrar aviso">
        <?= icon('x', '', 16) ?>
      </button>
    </div>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 8px;">
      <h2 class="sections__title" style="margin-bottom: 0;">
        <span>Módulos de Aprendizaje</span>
      </h2>
      <span style="font-size: 0.85rem; color: var(--color-text-muted); font-weight: 600;">
        Toca una tarjeta para ingresar
      </span>
    </div>

    <div class="card__grid sections__grid">

      <!-- 1 ▸ Biohuerto -->
      <article class="card card--biohuerto animate-slide-up" tabindex="0" aria-label="Módulo Biohuerto">
        <div class="card__illustration">
          <img src="assets/img/card-biohuerto.svg" alt="Biohuerto Escolar y Cultivos">
        </div>
        <div class="card__tags">
          <span class="tag">Ciencias</span>
          <span class="tag">Naturaleza</span>
        </div>
        <h3 class="card__title">Biohuerto</h3>
        <p class="card__desc">
          Aprende sobre hortalizas, siembra y riego. Conoce las plantas del colegio con códigos QR físicos.
        </p>
        <a href="biohuerto/index.php" class="card__btn" aria-label="Ir al Biohuerto">
          <span>Explorar Huerto</span>
          <?= icon('arrow-right-circle', '', 16) ?>
        </a>
      </article>

      <!-- 2 ▸ Biblioteca de Contenidos -->
      <article class="card card--biblioteca animate-slide-up" style="animation-delay: 0.1s;" tabindex="0" aria-label="Módulo Biblioteca de Contenidos">
        <div class="card__illustration">
          <img src="assets/img/card-biblioteca.svg" alt="Biblioteca Pedagógica de Ciencias">
        </div>
        <div class="card__tags">
          <span class="tag">Lectura</span>
          <span class="tag">Infografías</span>
        </div>
        <h3 class="card__title">Biblioteca</h3>
        <p class="card__desc">
          Textos pedagógicos, infografías ilustradas y videos educativos sobre botánica, ecología y ciencias.
        </p>
        <a href="biblioteca/index.php" class="card__btn" aria-label="Ir a la Biblioteca">
          <span>Ver Recursos</span>
          <?= icon('arrow-right-circle', '', 16) ?>
        </a>
      </article>

      <!-- 3 ▸ Zona de Juegos -->
      <article class="card card--juegos animate-slide-up" style="animation-delay: 0.2s;" tabindex="0" aria-label="Módulo Zona de Juegos">
        <div class="card__illustration">
          <img src="assets/img/card-juegos.svg" alt="Juegos Didácticos y Cuestionarios">
        </div>
        <div class="card__tags">
          <span class="tag">Diversión</span>
          <span class="tag">Aprendizaje</span>
        </div>
        <h3 class="card__title">Zona de Juegos</h3>
        <p class="card__desc">
          Minijuegos interactivos en JavaScript: trivias botánicas, sopas de letras y retos de reciclaje.
        </p>
        <a href="juegos/index.php" class="card__btn" aria-label="Ir a la Zona de Juegos">
          <span>Jugar Ahora</span>
          <?= icon('arrow-right-circle', '', 16) ?>
        </a>
      </article>

      <!-- 4 ▸ Repositorio Docente -->
      <article class="card card--repositorio animate-slide-up" style="animation-delay: 0.3s;" tabindex="0" aria-label="Módulo Repositorio Docente">
        <div class="card__illustration">
          <img src="assets/img/card-repositorio.svg" alt="Repositorio de Fichas y Materiales">
        </div>
        <div class="card__tags">
          <span class="tag">Docentes</span>
          <span class="tag">Descargas</span>
        </div>
        <h3 class="card__title">Repositorio</h3>
        <p class="card__desc">
          Fichas de trabajo, guías y recursos organizados por grado y área para alumnos de primaria.
        </p>
        <a href="repositorio/index.php" class="card__btn" aria-label="Ir al Repositorio">
          <span>Abrir Repositorio</span>
          <?= icon('arrow-right-circle', '', 16) ?>
        </a>
      </article>

    </div>
  </main>

<?php include __DIR__ . '/includes/footer.php'; ?>
