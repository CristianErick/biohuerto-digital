<?php
/**
 * Repositorio Docente — Vista Principal para Estudiantes y Docentes
 * Semillitas del Futuro · Sin Emojis · Iconos SVG · Mobile-First
 */
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/icons.php';

$pdo = getConnection();

// Filtros GET
$areaFiltro  = trim($_GET['area'] ?? '');
$gradoFiltro = trim($_GET['grado'] ?? '');
$busqueda    = trim($_GET['q'] ?? '');

// Consulta de materiales activos con datos del docente
$sql = '
    SELECT m.*, u.nombre AS docente_nombre, u.apellido AS docente_apellido
    FROM materiales m
    INNER JOIN usuarios u ON m.usuario_id = u.id
    WHERE m.activo = 1
';
$params = [];

if (!empty($areaFiltro)) {
    $sql .= ' AND m.area = :area';
    $params[':area'] = $areaFiltro;
}

if (!empty($gradoFiltro)) {
    $sql .= ' AND m.grado = :grado';
    $params[':grado'] = $gradoFiltro;
}

if (!empty($busqueda)) {
    $sql .= ' AND (m.titulo LIKE :q OR m.descripcion LIKE :q)';
    $params[':q'] = '%' . $busqueda . '%';
}

$sql .= ' ORDER BY m.created_at DESC';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$materiales = $stmt->fetchAll();

// Estado de sesión
$isDocente = isset($_SESSION['usuario_id']) && ($_SESSION['rol_id'] == 2 || $_SESSION['rol_id'] == 1);
$usuarioNombre = $_SESSION['usuario_nombre'] ?? '';
$isAdmin = isset($_SESSION['rol_id']) && $_SESSION['rol_id'] == 1;

$pageTitle = 'Repositorio Educativo — Semillitas del Futuro';
$pageDesc  = 'Descarga fichas de estudio, lecturas y guías educativas preparadas por tus profesores.';
$extraCSS  = ['css/repositorio.css'];
$isSubfolder = true;
include __DIR__ . '/../includes/header.php';
?>

<!-- Header del Repositorio -->
<header class="repo-header section-header animate-fade-in">
  <div class="section-badge">
    <?= icon('folder', '', 16) ?>
    <span>Recursos Escolares para Primaria</span>
  </div>
  <h2 class="repo-header__title">Repositorio de Materiales Educativos</h2>
  <p class="repo-header__subtitle">
    Descarga fichas prácticas, lecturas, guías y recursos de aprendizaje organizados por área y grado.
  </p>
</header>

<main class="sections" style="padding-top: 0;">

  <!-- Barra contextual si el docente está logueado -->
  <?php if ($isDocente): ?>
    <div class="docente-bar">
      <div class="docente-bar__user">
        <?= icon($isAdmin ? 'shield' : 'teacher', '', 20) ?>
        <span>Bienvenido(a), <strong><?= htmlspecialchars($usuarioNombre) ?></strong> (<?= htmlspecialchars($_SESSION['rol_nombre']) ?>)</span>
      </div>
      <div class="docente-bar__actions">
        <a href="subir.php" class="btn btn--primary btn--sm">
          <?= icon('plus', '', 15) ?> Subir Material
        </a>
        <a href="mis_materiales.php" class="btn btn--secondary btn--sm">
          <?= icon('folder', '', 15) ?> Mis Materiales
        </a>
        <?php if ($isAdmin): ?>
          <a href="../admin/index.php" class="btn btn--warning btn--sm">
            <?= icon('shield', '', 15) ?> Panel Admin
          </a>
        <?php endif; ?>
        <a href="../auth/logout.php" class="btn btn--outline btn--sm">
          <?= icon('log-out', '', 15) ?> Salir
        </a>
      </div>
    </div>
  <?php else: ?>
    <div class="docente-invite">
      <div style="display:flex; align-items:center; gap:12px;">
        <div style="width:40px; height:40px; border-radius:50%; background:#fef3c7; color:#b45309; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
          <?= icon('teacher', '', 20) ?>
        </div>
        <div>
          <strong style="color:var(--color-text);">¿Eres profesor(a) de la institución?</strong>
          <p style="margin:2px 0 0 0; font-size:0.85rem; color:var(--color-text-muted);">
            Inicia sesión para subir, editar y compartir fichas didácticas con tus alumnos.
          </p>
        </div>
      </div>
      <a href="../auth/login.php" class="btn btn--primary btn--sm">
        <?= icon('lock', '', 15) ?> Acceso Docente
      </a>
    </div>
  <?php endif; ?>

  <!-- Filtros y Buscador -->
  <form method="GET" action="index.php" class="repo-filters">
    <div class="form-group">
      <label for="q">Buscar por título o tema</label>
      <input type="text" id="q" name="q" class="form-control"
             placeholder="Ej: Fotosíntesis, Fracciones..."
             value="<?= htmlspecialchars($busqueda) ?>">
    </div>

    <div class="form-group">
      <label for="area">Área Curricular</label>
      <select id="area" name="area" class="form-control">
        <option value="">-- Todas las áreas --</option>
        <?php
        $areasDisponibles = [
            'Ciencias y Ambiente',
            'Matemáticas',
            'Comunicación',
            'Personal Social',
            'Arte y Cultura',
            'Inglés',
            'Educación Religiosa'
        ];
        foreach ($areasDisponibles as $a):
        ?>
          <option value="<?= $a ?>" <?= $areaFiltro === $a ? 'selected' : '' ?>><?= $a ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="form-group">
      <label for="grado">Grado Escolar</label>
      <select id="grado" name="grado" class="form-control">
        <option value="">-- Todos los grados --</option>
        <?php for ($g = 1; $g <= 6; $g++): ?>
          <option value="<?= $g ?>° Primaria" <?= $gradoFiltro === ($g . '° Primaria') ? 'selected' : '' ?>>
            <?= $g ?>° de Primaria
          </option>
        <?php endfor; ?>
      </select>
    </div>

    <div class="filtros-actions">
      <button type="submit" class="btn btn--primary">
        <?= icon('search', '', 16) ?> Filtrar
      </button>
      <?php if (!empty($areaFiltro) || !empty($gradoFiltro) || !empty($busqueda)): ?>
        <a href="index.php" class="btn btn--outline" title="Limpiar filtros">
          <?= icon('x', '', 16) ?>
        </a>
      <?php endif; ?>
    </div>
  </form>

  <!-- Lista de Materiales -->
  <?php if (empty($materiales)): ?>
    <div class="card" style="text-align:center; padding: 3rem 1rem;">
      <div style="color:var(--color-text-muted); margin-bottom:1rem;">
        <?= icon('folder', '', 48) ?>
      </div>
      <h3 style="color:var(--color-text); margin-bottom:0.5rem;">No se encontraron recursos</h3>
      <p style="color:var(--color-text-muted); font-size:0.95rem;">
        <?= (!empty($areaFiltro) || !empty($gradoFiltro) || !empty($busqueda))
            ? 'Prueba cambiando o limpiando los filtros de búsqueda.'
            : 'Aún no hay materiales disponibles para descargar.' ?>
      </p>
      <?php if ($isDocente): ?>
        <div style="margin-top:1rem;">
          <a href="subir.php" class="btn btn--primary">
            <?= icon('plus', '', 16) ?> Subir el primer material
          </a>
        </div>
      <?php endif; ?>
    </div>
  <?php else: ?>
    <div class="materiales-grid">
      <?php foreach ($materiales as $mat): ?>
        <?php
          $tipo = strtolower($mat['tipo_archivo']);
          $iconName = 'file-text';
          if (in_array($tipo, ['pdf'])) $iconName = 'file-text';
          elseif (in_array($tipo, ['doc', 'docx'])) $iconName = 'file-text';
          elseif (in_array($tipo, ['ppt', 'pptx'])) $iconName = 'image';
          elseif (in_array($tipo, ['xls', 'xlsx'])) $iconName = 'folder';

          $tamBytes = $mat['tamanio'];
          $tamStr = ($tamBytes >= 1048576)
              ? number_format($tamBytes / 1048576, 1) . ' MB'
              : number_format($tamBytes / 1024, 0) . ' KB';
        ?>
        <article class="material-card">
          <div class="material-card__badges">
            <span class="badge badge--area"><?= htmlspecialchars($mat['area']) ?></span>
            <span class="badge badge--grado"><?= htmlspecialchars($mat['grado']) ?></span>
            <span class="badge badge--tipo"><?= strtoupper($mat['tipo_archivo']) ?></span>
          </div>

          <div style="color:var(--color-indigo); margin-bottom:0.5rem;">
            <?= icon($iconName, '', 28) ?>
          </div>

          <h3 class="material-card__title"><?= htmlspecialchars($mat['titulo']) ?></h3>

          <p class="material-card__desc">
            <?= nl2br(htmlspecialchars($mat['descripcion'] ?: 'Material pedagógico interactivo para reforzar conocimientos.')) ?>
          </p>

          <div class="material-card__meta">
            <span>Docente: <?= htmlspecialchars($mat['docente_nombre'] . ' ' . $mat['docente_apellido']) ?></span>
            <span><?= $tamStr ?> &bull; <?= (int)$mat['descargas'] ?> descargas</span>
          </div>

          <a href="descargar.php?id=<?= $mat['id'] ?>" class="btn-descarga">
            <?= icon('download', '', 16) ?>
            <span>Descargar Recurso</span>
          </a>
        </article>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
