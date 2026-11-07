<?php
/**
 * BIBLIOTECA DE CONTENIDOS PEDAGÓGICOS
 * Acceso público para todos los usuarios.
 * Solo Administradores y Docentes pueden subir materiales.
 */
session_start();

$pageTitle = 'Biblioteca de Contenidos — Semillitas del Futuro';
$pageDesc  = 'Recursos pedagógicos, textos, infografías y videos educativos sobre ciencias y medio ambiente.';
$isSubfolder = true;
require_once __DIR__ . '/../libs/biblioteca_store.php';
include __DIR__ . '/../includes/header.php';

$puedeGestionar = isset($_SESSION['usuario_id']) && in_array((int)$_SESSION['rol_id'], [1, 2]);
$isAdmin = isset($_SESSION['rol_id']) && $_SESSION['rol_id'] == 1;
$usuarioNombre = $_SESSION['usuario_nombre'] ?? '';

// Colección de recursos pedagógicos
$recursos = [
    [
        'id'          => 1,
        'tipo'        => 'texto',
        'area'        => 'Botánica y Huertos',
        'titulo'      => 'Guía Didáctica: El Ciclo de Vida de las Plantas Angiospermas',
        'descripcion' => 'Documento pedagógico con actividades para explicar la germinación, polinización y dispersión de semillas en alumnos de 3° a 5° de primaria.',
        'duracion'    => 'Lectura de 8 min',
        'enlace'      => '#',
        'icono'       => 'file-text'
    ],
    [
        'id'          => 2,
        'tipo'        => 'video',
        'area'        => 'Ciencias de la Tierra',
        'titulo'      => 'Video Explicativo: ¿Cómo se forma el suelo fértil y el compost?',
        'descripcion' => 'Material audiovisual animado que muestra la descomposición de materia orgánica y la actividad de lombrices californianas para enriquecer la tierra.',
        'duracion'    => 'Video: 5:45 min',
        'enlace'      => 'https://www.youtube.com/watch?v=kY_ZfLp_990',
        'icono'       => 'video'
    ],
    [
        'id'          => 3,
        'tipo'        => 'imagen',
        'area'        => 'Biodiversidad y Ecología',
        'titulo'      => 'Infografía Ilustrada: Insectos Beneficiosos en el Biohuerto Escolar',
        'descripcion' => 'Lámina a todo color lista para proyectar o imprimir: mariquitas, abejas solitarias, crisopas y mantis religiosas como control biológico.',
        'duracion'    => 'Infografía HD',
        'enlace'      => '#',
        'icono'       => 'image'
    ],
    [
        'id'          => 4,
        'tipo'        => 'video',
        'area'        => 'Botánica y Huertos',
        'titulo'      => 'Taller Práctico: Técnica del Riego por Goteo Casero con Botellas',
        'descripcion' => 'Tutorial paso a paso para que los docentes elaboren sistemas de riego por goteo solar con materiales reciclados junto a los estudiantes.',
        'duracion'    => 'Video: 7:20 min',
        'enlace'      => 'https://www.youtube.com/watch?v=S38pE2sQG1o',
        'icono'       => 'video'
    ],
    [
        'id'          => 5,
        'tipo'        => 'texto',
        'area'        => 'Conservación y Reciclaje',
        'titulo'      => 'Unidad de Aprendizaje: Las 3R (Reducir, Reutilizar, Reciclar) en el Aula',
        'descripcion' => 'Plan de sesión de aprendizaje con rúbricas de evaluación diagnóstica y formativa para educación primaria.',
        'duracion'    => 'Lectura de 12 min',
        'enlace'      => '#',
        'icono'       => 'file-text'
    ],
    [
        'id'          => 6,
        'tipo'        => 'imagen',
        'area'        => 'Ciencias de la Tierra',
        'titulo'      => 'Lámina Didáctica: El Ciclo Hidrológico del Agua en la Naturaleza',
        'descripcion' => 'Diagrama esquemático con flechas y etiquetas de evaporación, condensación, precipitación e infiltración adaptado para niños.',
        'duracion'    => 'Lámina Imprimible',
        'enlace'      => '#',
        'icono'       => 'image'
    ]
];

$filtroArea = trim($_GET['area'] ?? '');
$filtroTipo = trim($_GET['tipo'] ?? '');

// Recursos estáticos predeterminados
$recursosEstaticos = [
    [
        'id'          => 1001,
        'tipo'        => 'texto',
        'area'        => 'Botánica y Huertos',
        'titulo'      => 'Guía Didáctica: El Ciclo de Vida de las Plantas Angiospermas',
        'descripcion' => 'Documento pedagógico con actividades para explicar la germinación, polinización y dispersión de semillas en alumnos de 3° a 5° de primaria.',
        'duracion'    => 'Lectura de 8 min',
        'enlace'      => '#',
        'icono'       => 'file-text'
    ],
    [
        'id'          => 1002,
        'tipo'        => 'video',
        'area'        => 'Ciencias de la Tierra',
        'titulo'      => 'Video Explicativo: ¿Cómo se forma el suelo fértil y el compost?',
        'descripcion' => 'Material audiovisual animado que muestra la descomposición de materia orgánica y la actividad de lombrices californianas para enriquecer la tierra.',
        'duracion'    => 'Video: 5:45 min',
        'enlace'      => 'https://www.youtube.com/watch?v=kY_ZfLp_990',
        'icono'       => 'video'
    ],
    [
        'id'          => 1003,
        'tipo'        => 'imagen',
        'area'        => 'Biodiversidad y Ecología',
        'titulo'      => 'Infografía Ilustrada: Insectos Beneficiosos en el Biohuerto Escolar',
        'descripcion' => 'Lámina a todo color lista para proyectar o imprimir: mariquitas, abejas solitarias, crisopas y mantis religiosas como control biológico.',
        'duracion'    => 'Infografía HD',
        'enlace'      => '#',
        'icono'       => 'image'
    ],
    [
        'id'          => 1004,
        'tipo'        => 'video',
        'area'        => 'Botánica y Huertos',
        'titulo'      => 'Taller Práctico: Técnica del Riego por Goteo Casero con Botellas',
        'descripcion' => 'Tutorial paso a paso para que los docentes elaboren sistemas de riego por goteo solar con materiales reciclados junto a los estudiantes.',
        'duracion'    => 'Video: 7:20 min',
        'enlace'      => 'https://www.youtube.com/watch?v=S38pE2sQG1o',
        'icono'       => 'video'
    ],
    [
        'id'          => 1005,
        'tipo'        => 'texto',
        'area'        => 'Conservación y Reciclaje',
        'titulo'      => 'Unidad de Aprendizaje: Las 3R (Reducir, Reutilizar, Reciclar) en el Aula',
        'descripcion' => 'Plan de sesión de aprendizaje con rúbricas de evaluación diagnóstica y formativa para educación primaria.',
        'duracion'    => 'Lectura de 12 min',
        'enlace'      => '#',
        'icono'       => 'file-text'
    ],
    [
        'id'          => 1006,
        'tipo'        => 'imagen',
        'area'        => 'Ciencias de la Tierra',
        'titulo'      => 'Lámina Didáctica: El Ciclo Hidrológico del Agua en la Naturaleza',
        'descripcion' => 'Diagrama esquemático con flechas y etiquetas de evaporación, condensación, precipitación e infiltración adaptado para niños.',
        'duracion'    => 'Lámina Imprimible',
        'enlace'      => '#',
        'icono'       => 'image'
    ]
];

// Cargar recursos agregados por docentes desde la base de datos
$recursosAgregados = biblioteca_recursos_agregados();

// Combinar: recursos agregados primero, luego estáticos
$recursos = array_merge($recursosAgregados, $recursosEstaticos);

$recursosFiltrados = array_filter($recursos, function ($r) use ($filtroArea, $filtroTipo) {
    if (!empty($filtroArea) && $r['area'] !== $filtroArea) return false;
    if (!empty($filtroTipo) && $r['tipo'] !== $filtroTipo) return false;
    return true;
});
?>

<!-- Banner Superior -->
<header class="biblioteca-header section-header animate-fade-in">
  <div class="biblioteca-header__badge section-badge">
    <?= icon('book', '', 14) ?>
    <span>Biblioteca Pública - Acceso Libre</span>
  </div>
  <h2 class="biblioteca-header__title">Biblioteca Pedagógica de Ciencias</h2>
  <p class="section-header__subtitle" style="color: #ffffff;">
    Materiales científicos, láminas didácticas y guías metodológicas para clases de ciencias naturales y educación ambiental.
  </p>
</header>

<main class="sections" style="padding-top: 0;">

  <!-- Barra de Usuario -->
  <div class="biblioteca-user-bar" style="background:#ffffff; border:1px solid var(--color-border); border-radius:var(--radius-md); padding:var(--space-md); margin-bottom:var(--space-lg); box-shadow:var(--shadow-xs); display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
    <div style="display: flex; align-items: center; gap: 10px;">
      <div style="width: 36px; height: 36px; border-radius: 50%; background: #e0f2fe; color: #0284c7; display: flex; align-items: center; justify-content: center;">
        <?= icon('book', '', 20) ?>
      </div>
      <div>
        <strong style="color: var(--color-text); font-size: 0.95rem;">Visitante</strong>
      </div>
    </div>
    <?php if ($puedeGestionar): ?>
    <div style="display: flex; gap: 8px;">
      <a href="subir.php" class="btn btn--primary btn--sm">
        <?= icon('upload', '', 16) ?>
        <span>Agregar a Biblioteca</span>
      </a>
      <a href="../repositorio/mis_materiales.php" class="btn btn--outline btn--sm">
        <?= icon('folder', '', 16) ?>
        <span>Mis Materiales</span>
      </a>
    </div>
    <?php else: ?>
    <div style="display: flex; gap: 8px;">
      <a href="../auth/login.php" class="btn btn--outline btn--sm" style="white-space: nowrap;">
        <?= icon('lock', '', 16) ?>
        <span>Inicia sesión para subir material</span>
      </a>
    </div>
    <?php endif; ?>
  </div>

  <!-- Filtros -->
  <form method="GET" action="index.php" class="biblioteca-filters repo-filters">
    <div class="form-group">
      <label for="area">Área Temática</label>
      <select id="area" name="area" class="form-control">
        <option value="">-- Todas las Áreas --</option>
        <option value="Botánica y Huertos" <?= $filtroArea === 'Botánica y Huertos' ? 'selected' : '' ?>>Botánica y Huertos</option>
        <option value="Ciencias de la Tierra" <?= $filtroArea === 'Ciencias de la Tierra' ? 'selected' : '' ?>>Ciencias de la Tierra</option>
        <option value="Biodiversidad y Ecología" <?= $filtroArea === 'Biodiversidad y Ecología' ? 'selected' : '' ?>>Biodiversidad y Ecología</option>
        <option value="Conservación y Reciclaje" <?= $filtroArea === 'Conservación y Reciclaje' ? 'selected' : '' ?>>Conservación y Reciclaje</option>
      </select>
    </div>

    <div class="form-group">
      <label for="tipo">Formato del Contenido</label>
      <select id="tipo" name="tipo" class="form-control">
        <option value="">-- Todos los Formatos --</option>
        <option value="texto" <?= $filtroTipo === 'texto' ? 'selected' : '' ?>>Guías y Textos</option>
        <option value="imagen" <?= $filtroTipo === 'imagen' ? 'selected' : '' ?>>Infografías Ilustradas</option>
        <option value="video" <?= $filtroTipo === 'video' ? 'selected' : '' ?>>Videos Educativos</option>
      </select>
    </div>

    <div class="filtros-actions">
      <button type="submit" class="btn btn--primary">
        <?= icon('search', '', 16) ?>
        <span>Filtrar</span>
      </button>
      <?php if (!empty($filtroArea) || !empty($filtroTipo)): ?>
        <a href="index.php" class="btn btn--outline" title="Quitar filtros">
          <?= icon('x', '', 16) ?>
        </a>
      <?php endif; ?>
    </div>
  </form>

  <!-- Grilla de Contenidos -->
  <?php if (empty($recursosFiltrados)): ?>
    <div class="card" style="text-align: center; padding: var(--space-2xl);">
      <div style="color: var(--color-text-muted); margin-bottom: 8px;">
        <?= icon('search', '', 36) ?>
      </div>
      <h3 style="color: var(--color-text);">No se encontraron recursos</h3>
      <p style="color: var(--color-text-muted);">Prueba cambiando los filtros seleccionados.</p>
    </div>
  <?php else: ?>
    <div class="biblioteca-grid">
      <?php foreach ($recursosFiltrados as $rec): ?>
        <article class="recurso-card">
          <div class="recurso-card__top">
            <span class="recurso-badge recurso-badge--<?= $rec['tipo'] ?>">
              <?= htmlspecialchars($rec['tipo']) ?>
            </span>
            <span style="font-size: 0.8rem; font-weight: 700; color: var(--color-text-muted);">
              <?= htmlspecialchars($rec['area']) ?>
            </span>
          </div>

          <h3 class="recurso-card__title">
            <?= htmlspecialchars($rec['titulo']) ?>
          </h3>

          <p class="recurso-card__desc">
            <?= htmlspecialchars($rec['descripcion']) ?>
          </p>

          <div class="recurso-card__footer">
            <span style="display: flex; align-items: center; gap: 4px;">
              <?= icon($rec['icono'], '', 15) ?>
              <span><?= htmlspecialchars($rec['duracion']) ?></span>
            </span>
            <?php if ($rec['tipo'] === 'video' && $rec['enlace'] !== '#'): ?>
              <a href="<?= htmlspecialchars($rec['enlace']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn--secondary btn--sm">
                <?= icon('video', '', 15) ?> Ver Video
              </a>
            <?php else: ?>
              <button type="button" class="btn btn--outline btn--sm btn-consultar" data-enlace="<?= htmlspecialchars($rec['enlace'] ?? '#') ?>">
                <?= icon('eye', '', 15) ?> Consultar
              </button>
            <?php endif; ?>

            <!-- Botones de gestión: Admin edita todo, Docente solo sus recursos agregados -->
            <?php
            $esRecursoDelJson = isset($rec['creado_por']) && !empty($rec['creado_por']);
            $puedeEditarEste = false;
            $puedeEliminarEste = false;

            if ($isAdmin) {
                // Admin puede editar y eliminar cualquier recurso
                $puedeEditarEste = true;
                $puedeEliminarEste = true;
            } elseif ($puedeGestionar && !$isAdmin && $esRecursoDelJson && $rec['creado_por'] === $usuarioNombre) {
                // Docente solo puede editar/eliminar los recursos que él mismo agregó al JSON
                $puedeEditarEste = true;
                $puedeEliminarEste = true;
            }
            ?>
            <?php if ($puedeEditarEste || $puedeEliminarEste): ?>
              <?php if ($puedeEditarEste): ?>
                <a href="editar.php?id=<?= $rec['id'] ?>" class="btn btn--warning btn--sm" title="Editar">
                  <?= icon('edit', '', 14) ?>
                </a>
              <?php endif; ?>
              <?php if ($puedeEliminarEste): ?>
                <a href="eliminar.php?id=<?= $rec['id'] ?>" class="btn btn--danger btn--sm"
                   onclick="confirmDelete('¿Confirmas eliminar el recurso: <?= addslashes($rec['titulo']) ?>?', 'eliminar.php?id=<?= $rec['id'] ?>'); return false;"
                   title="Eliminar">
                  <?= icon('trash', '', 14) ?>
                </a>
              <?php endif; ?>
            <?php endif; ?>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
