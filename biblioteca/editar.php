<?php
/**
 * Editar Recurso de la Biblioteca — Semillitas del Futuro
 * Solo Administradores y Docentes pueden editar recursos de la biblioteca.
 * Docentes solo pueden editar los recursos que ellos agregaron.
 * Admin puede editar cualquier recurso, incluidos los ejemplos predeterminados.
 */
session_start();

// ═══ PROCESAMIENTO PHP ANTES DE ENVIAR HTML ═══

if (!isset($_SESSION['usuario_id']) || !in_array((int)$_SESSION['rol_id'], [1, 2])) {
    $_SESSION['login_error'] = 'Solo Administradores y Docentes pueden editar recursos de la Biblioteca.';
    header('Location: ../auth/login.php');
    exit;
}

require_once __DIR__ . '/../includes/icons.php';
require_once __DIR__ . '/../libs/biblioteca_store.php';

$pageTitle = 'Editar Recurso — Biblioteca';
$isSubfolder = true;

$id = intval($_GET['id'] ?? $_POST['id'] ?? 0);
$usuarioId = $_SESSION['usuario_id'];
$usuarioNombre = $_SESSION['usuario_nombre'] ?? 'Docente';
$isAdmin = ($_SESSION['rol_id'] == 1);

// Recursos agregados guardados en la base de datos
$recursos = biblioteca_recursos_agregados();

// Recursos estáticos predeterminados (no están en el JSON, vienen del código)
$recursosEstaticos = [
    [
        'id'          => 1001,
        'tipo'        => 'texto',
        'area'        => 'Botánica y Huertos',
        'titulo'      => 'Guía Didáctica: El Ciclo de Vida de las Plantas Angiospermas',
        'descripcion' => 'Documento pedagógico con actividades para explicar la germinación, polinización y dispersión de semillas en alumnos de 3° a 5° de primaria.',
        'duracion'    => 'Lectura de 8 min',
        'enlace'      => '#',
        'icono'       => 'file-text',
        'creado_por'  => 'Sistema',
        'fecha'       => 'Sistema',
        'esEstatico'  => true
    ],
    [
        'id'          => 1002,
        'tipo'        => 'video',
        'area'        => 'Ciencias de la Tierra',
        'titulo'      => 'Video Explicativo: ¿Cómo se forma el suelo fértil y el compost?',
        'descripcion' => 'Material audiovisual animado que muestra la descomposición de materia orgánica y la actividad de lombrices californianas para enriquecer la tierra.',
        'duracion'    => 'Video: 5:45 min',
        'enlace'      => 'https://www.youtube.com/watch?v=kY_ZfLp_990',
        'icono'       => 'video',
        'creado_por'  => 'Sistema',
        'fecha'       => 'Sistema',
        'esEstatico'  => true
    ],
    [
        'id'          => 1003,
        'tipo'        => 'imagen',
        'area'        => 'Biodiversidad y Ecología',
        'titulo'      => 'Infografía Ilustrada: Insectos Beneficiosos en el Biohuerto Escolar',
        'descripcion' => 'Lámina a todo color lista para proyectar o imprimir: mariquitas, abejas solitarias, crisopas y mantis religiosas como control biológico.',
        'duracion'    => 'Infografía HD',
        'enlace'      => '#',
        'icono'       => 'image',
        'creado_por'  => 'Sistema',
        'fecha'       => 'Sistema',
        'esEstatico'  => true
    ],
    [
        'id'          => 1004,
        'tipo'        => 'video',
        'area'        => 'Botánica y Huertos',
        'titulo'      => 'Taller Práctico: Técnica del Riego por Goteo Casero con Botellas',
        'descripcion' => 'Tutorial paso a paso para que los docentes elaboren sistemas de riego por goteo solar con materiales reciclados junto a los estudiantes.',
        'duracion'    => 'Video: 7:20 min',
        'enlace'      => 'https://www.youtube.com/watch?v=S38pE2sQG1o',
        'icono'       => 'video',
        'creado_por'  => 'Sistema',
        'fecha'       => 'Sistema',
        'esEstatico'  => true
    ],
    [
        'id'          => 1005,
        'tipo'        => 'texto',
        'area'        => 'Conservación y Reciclaje',
        'titulo'      => 'Unidad de Aprendizaje: Las 3R (Reducir, Reutilizar, Reciclar) en el Aula',
        'descripcion' => 'Plan de sesión de aprendizaje con rúbricas de evaluación diagnóstica y formativa para educación primaria.',
        'duracion'    => 'Lectura de 12 min',
        'enlace'      => '#',
        'icono'       => 'file-text',
        'creado_por'  => 'Sistema',
        'fecha'       => 'Sistema',
        'esEstatico'  => true
    ],
    [
        'id'          => 1006,
        'tipo'        => 'imagen',
        'area'        => 'Ciencias de la Tierra',
        'titulo'      => 'Lámina Didáctica: El Ciclo Hidrológico del Agua en la Naturaleza',
        'descripcion' => 'Diagrama esquemático con flechas y etiquetas de evaporación, condensación, precipitación e infiltración adaptado para niños.',
        'duracion'    => 'Lámina Imprimible',
        'enlace'      => '#',
        'icono'       => 'image',
        'creado_por'  => 'Sistema',
        'fecha'       => 'Sistema',
        'esEstatico'  => true
    ]
];

// Buscar el recurso en el JSON (recursos agregados) o en los estáticos
$recursoIndexJson = -1;
$recursoIndexEstatico = -1;
$recurso = null;
$origen = ''; // 'json' o 'estatico'

foreach ($recursos as $i => $r) {
    if (isset($r['id']) && $r['id'] == $id) {
        $recursoIndexJson = $i;
        $recurso = $r;
        $origen = 'json';
        break;
    }
}

if (!$recurso) {
    foreach ($recursosEstaticos as $i => $r) {
        if (isset($r['id']) && $r['id'] == $id) {
            $recursoIndexEstatico = $i;
            $recurso = $r;
            $origen = 'estatico';
            break;
        }
    }
}

if (!$recurso) {
    $_SESSION['biblio_msg'] = 'El recurso no existe.';
    $_SESSION['biblio_msg_type'] = 'error';
    header('Location: ./index.php');
    exit;
}

// Verificar permisos
if ($isAdmin) {
    // Admin puede editar cualquier recurso
    $puedeEditar = true;
} elseif (isset($recurso['creado_por']) && $recurso['creado_por'] === $usuarioNombre && $origen === 'json') {
    // Docente solo puede editar sus propios recursos del JSON
    $puedeEditar = true;
} else {
    $puedeEditar = false;
}

if (!$puedeEditar) {
    $_SESSION['biblio_msg'] = 'No tienes permiso para editar este recurso.';
    $_SESSION['biblio_msg_type'] = 'error';
    header('Location: ./index.php');
    exit;
}

// Procesar POST (guardar cambios)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo      = trim($_POST['titulo'] ?? '');
    $area        = trim($_POST['area'] ?? '');
    $tipo        = trim($_POST['tipo'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $duracion    = trim($_POST['duracion'] ?? '');
    $enlace      = trim($_POST['enlace'] ?? '');

    $errores = [];
    if (empty($titulo)) $errores[] = 'El título es obligatorio.';
    if (empty($area))   $errores[] = 'El área es obligatoria.';
    if (empty($tipo))   $errores[] = 'El formato es obligatorio.';

    if (!empty($errores)) {
        $_SESSION['biblio_msg'] = implode(' ', $errores);
        $_SESSION['biblio_msg_type'] = 'error';
        header('Location: ./editar.php?id=' . $id);
        exit;
    } else {
        $recursoActualizado = [
            'id'          => $id,
            'tipo'        => $tipo,
            'area'        => $area,
            'titulo'      => $titulo,
            'descripcion' => $descripcion,
            'duracion'    => $duracion ?: 'Recurso educativo',
            'enlace'      => $enlace ?: '#',
            'icono'       => $tipo === 'video' ? 'video' : ($tipo === 'imagen' ? 'image' : 'file-text'),
            'creado_por'  => $recurso['creado_por'] ?? $usuarioNombre,
            'fecha'       => date('d/m/Y'),
            'esEstatico'  => $origen === 'estatico' ? true : ($recurso['esEstatico'] ?? false)
        ];

        if ($origen === 'estatico') {
            // Si es un recurso estático y quien edita es admin, lo agregamos al JSON
            // para que los cambios persistan
            if ($isAdmin) {
                // Remover 'esEstatico' antes de guardar en JSON (es solo para control interno)
                $paraGuardar = $recursoActualizado;
                unset($paraGuardar['esEstatico']);
                $recursos[] = $paraGuardar;

                if (biblioteca_guardar_agregados($recursos)) {
                    $_SESSION['biblio_msg'] = 'Recurso actualizado correctamente (guardado en la biblioteca personalizada).';
                    $_SESSION['biblio_msg_type'] = 'success';
                    header('Location: ./index.php');
                    exit;
                } else {
                    $_SESSION['biblio_msg'] = 'Error al guardar el recurso en la biblioteca.';
                    $_SESSION['biblio_msg_type'] = 'error';
                    header('Location: ./editar.php?id=' . $id);
                    exit;
                }
            } else {
                $_SESSION['biblio_msg'] = 'No tienes permiso para modificar recursos del sistema.';
                $_SESSION['biblio_msg_type'] = 'error';
                header('Location: ./index.php');
                exit;
            }
        } else {
            // Recurso del JSON: actualizar en su posición
            $recursos[$recursoIndexJson] = $recursoActualizado;

            if (biblioteca_guardar_agregados($recursos)) {
                $_SESSION['biblio_msg'] = 'Recurso actualizado correctamente.';
                $_SESSION['biblio_msg_type'] = 'success';
                header('Location: ./index.php');
                exit;
            } else {
                $_SESSION['biblio_msg'] = 'Error al guardar los cambios.';
                $_SESSION['biblio_msg_type'] = 'error';
                header('Location: ./editar.php?id=' . $id);
                exit;
            }
        }
    }
}

// Obtener mensaje flash
$msg     = $_SESSION['biblio_msg'] ?? null;
$msgType = $_SESSION['biblio_msg_type'] ?? 'error';
unset($_SESSION['biblio_msg'], $_SESSION['biblio_msg_type']);

// Opciones de formulario
$areasDisponibles = [
    'Botánica y Huertos',
    'Ciencias de la Tierra',
    'Biodiversidad y Ecología',
    'Conservación y Reciclaje'
];

$tiposDisponibles = [
    'texto' => 'Guía / Texto',
    'imagen' => 'Infografía / Imagen',
    'video' => 'Video Educativo'
];

// ═══ INCLUIR HEADER DESPUÉS DE TODO EL PHP ═══
include __DIR__ . '/../includes/header.php';
?>

<div class="sections" style="padding-top: 1.5rem;">
  <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:1.5rem;">
    <div>
      <h2 style="font-family:var(--font-heading); color:var(--color-primary-dark); font-size:1.8rem; margin:0; display:flex; align-items:center; gap:8px;">
        <?= icon('edit', '', 24) ?>
        <span>Editar Recurso de Biblioteca</span>
      </h2>
      <p style="color:var(--color-text-muted); font-size:0.9rem; margin:4px 0 0 0;">
        Recurso: <strong><?= htmlspecialchars($recurso['titulo']) ?></strong>
        <?php if ($origen === 'estatico'): ?>
          <span style="font-size:0.75rem; color:var(--color-text-muted); margin-left:6px;">(Recurso del sistema)</span>
        <?php endif; ?>
      </p>
    </div>
    <a href="./index.php" class="btn btn--outline btn--sm">
      <?= icon('arrow-left', '', 15) ?> Volver a Biblioteca
    </a>
  </div>

  <?php if ($msg): ?>
    <div class="alert-banner alert-banner--<?= $msgType ?>" style="margin-bottom:1.5rem;">
      <span class="alert-banner__icon"><?= icon($msgType === 'success' ? 'check-circle' : 'alert-circle', '', 18) ?></span>
      <div class="alert-banner__content"><?= htmlspecialchars($msg) ?></div>
    </div>
  <?php endif; ?>

  <div class="card" style="padding:var(--space-xl); max-width: 700px; margin: 0 auto;">
    <form action="./editar.php?id=<?= $id ?>" method="POST" id="formBiblioEditar">
      <input type="hidden" name="id" value="<?= $id ?>">

      <div class="form-group">
        <label for="titulo">Título del Recurso <span style="color:var(--color-rose);">*</span></label>
        <input type="text" id="titulo" name="titulo" class="form-control" required maxlength="200"
               value="<?= htmlspecialchars($recurso['titulo']) ?>">
      </div>

      <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(240px, 1fr)); gap:16px;">
        <div class="form-group">
          <label for="area">Área Temática <span style="color:var(--color-rose);">*</span></label>
          <select id="area" name="area" class="form-control" required>
            <?php foreach ($areasDisponibles as $a): ?>
              <option value="<?= $a ?>" <?= ($recurso['area'] === $a) ? 'selected' : '' ?>>
                <?= $a ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group">
          <label for="tipo">Formato <span style="color:var(--color-rose);">*</span></label>
          <select id="tipo" name="tipo" class="form-control" required>
            <?php foreach ($tiposDisponibles as $valor => $etiqueta): ?>
              <option value="<?= $valor ?>" <?= ($recurso['tipo'] === $valor) ? 'selected' : '' ?>>
                <?= $etiqueta ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label for="descripcion">Descripción</label>
        <textarea id="descripcion" name="descripcion" class="form-control" rows="4"><?= htmlspecialchars($recurso['descripcion']) ?></textarea>
      </div>

      <div class="form-group">
        <label for="duracion">Duración estimada</label>
        <input type="text" id="duracion" name="duracion" class="form-control"
               placeholder="Ej: Lectura de 10 min, Video 5:30"
               value="<?= htmlspecialchars($recurso['duracion']) ?>">
      </div>

      <div class="form-group">
        <label for="enlace">Enlace o URL (opcional)</label>
        <input type="url" id="enlace" name="enlace" class="form-control"
               placeholder="https://ejemplo.com/recurso"
               value="<?= htmlspecialchars($recurso['enlace'] ?? '#') ?>">
      </div>

      <div style="display:flex; gap:12px; margin-top:24px; flex-wrap:wrap;">
        <button type="submit" class="btn btn--primary" style="min-width:180px;">
          <?= icon('check', '', 16) ?> Guardar Cambios
        </button>
        <a href="./index.php" class="btn btn--outline">Cancelar</a>
      </div>
    </form>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
