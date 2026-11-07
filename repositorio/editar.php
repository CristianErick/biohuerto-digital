<?php
/**
 * Editar Material Educativo — Semillitas del Futuro
 * Sin Emojis · Iconos SVG · Mobile-First
 */
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/icons.php';
require_once __DIR__ . '/../libs/storage.php';

$pdo = getConnection();

$id = intval($_GET['id'] ?? $_POST['id'] ?? 0);
$usuarioId = $_SESSION['usuario_id'];
$isAdmin = ($_SESSION['rol_id'] == 1);

if ($id <= 0) {
    $_SESSION['repo_msg'] = 'ID de material inválido.';
    $_SESSION['repo_msg_type'] = 'error';
    header('Location: mis_materiales.php');
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM materiales WHERE id = :id');
$stmt->execute([':id' => $id]);
$material = $stmt->fetch();

if (!$material) {
    $_SESSION['repo_msg'] = 'El material no existe.';
    $_SESSION['repo_msg_type'] = 'error';
    header('Location: mis_materiales.php');
    exit;
}

if (!$isAdmin && $material['usuario_id'] != $usuarioId) {
    http_response_code(403);
    die('No tienes permiso para editar este material.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo      = trim($_POST['titulo'] ?? '');
    $area        = trim($_POST['area'] ?? '');
    $grado       = trim($_POST['grado'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');

    $errores = [];
    if (empty($titulo)) $errores[] = 'El título es obligatorio.';
    if (empty($area))   $errores[] = 'El área es obligatoria.';
    if (empty($grado))  $errores[] = 'El grado es obligatorio.';

    $nombreOriginal = $material['nombre_archivo'];
    $rutaArchivoDB  = $material['ruta_archivo'];
    $tipoArchivo    = $material['tipo_archivo'];
    $tamanioBytes   = $material['tamanio'];

    if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
        $archivo = $_FILES['archivo'];
        $extensionesPermitidas = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'zip', 'txt', 'rar'];
        $ext = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $extensionesPermitidas)) {
            $errores[] = 'Extensión de archivo no permitida.';
        } elseif ($archivo['size'] > 15 * 1024 * 1024) {
            $errores[] = 'El nuevo archivo supera los 15 MB.';
        } else {
            $guardado = storage_guardar_upload($archivo, 'materiales', $extensionesPermitidas, 15 * 1024 * 1024);

            if ($guardado['ok']) {
                storage_eliminar($material['ruta_archivo'] ?? null);

                $nombreOriginal = basename($archivo['name']);
                $rutaArchivoDB  = $guardado['ruta'];
                $tipoArchivo    = $ext;
                $tamanioBytes   = $archivo['size'];
            } else {
                $errores[] = $guardado['error'] ?? 'Error al guardar el nuevo archivo en el servidor.';
            }
        }
    }

    if (!empty($errores)) {
        $_SESSION['repo_msg'] = implode(' ', $errores);
        $_SESSION['repo_msg_type'] = 'error';
    } else {
        try {
            $stmt = $pdo->prepare('
                UPDATE materiales
                SET titulo = :titulo, descripcion = :descripcion, area = :area, grado = :grado,
                    nombre_archivo = :nombre_archivo, ruta_archivo = :ruta_archivo,
                    tipo_archivo = :tipo_archivo, tamanio = :tamanio, updated_at = NOW()
                WHERE id = :id
            ');
            $stmt->execute([
                ':titulo'         => $titulo,
                ':descripcion'    => $descripcion,
                ':area'           => $area,
                ':grado'          => $grado,
                ':nombre_archivo' => $nombreOriginal,
                ':ruta_archivo'   => $rutaArchivoDB,
                ':tipo_archivo'   => $tipoArchivo,
                ':tamanio'        => $tamanioBytes,
                ':id'             => $id,
            ]);

            $_SESSION['repo_msg'] = 'Material actualizado correctamente.';
            $_SESSION['repo_msg_type'] = 'success';
            header('Location: mis_materiales.php');
            exit;

        } catch (PDOException $e) {
            $_SESSION['repo_msg'] = 'Error al actualizar en la base de datos.';
            $_SESSION['repo_msg_type'] = 'error';
        }
    }
}

$pageTitle = 'Editar Material — Semillitas del Futuro';
$extraCSS  = ['css/repositorio.css'];
$isSubfolder = true;
include __DIR__ . '/../includes/header.php';

$msg     = $_SESSION['repo_msg'] ?? null;
$msgType = $_SESSION['repo_msg_type'] ?? 'error';
unset($_SESSION['repo_msg'], $_SESSION['repo_msg_type']);
?>

<div class="sections" style="max-width: 760px; margin: 0 auto; padding-top: 1.5rem;">

  <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:1.5rem;">
    <div>
      <h2 style="font-family:var(--font-heading); color:var(--color-primary-dark); font-size:1.8rem; margin:0; display:flex; align-items:center; gap:8px;">
        <?= icon('edit', '', 24) ?>
        <span>Editar Material Educativo</span>
      </h2>
      <p style="color:var(--color-text-muted); font-size:0.9rem; margin:4px 0 0 0;">
        Recurso: <strong><?= htmlspecialchars($material['titulo']) ?></strong>
      </p>
    </div>
    <a href="mis_materiales.php" class="btn btn--outline btn--sm">
      <?= icon('arrow-left', '', 15) ?> Volver a Mis Materiales
    </a>
  </div>

  <?php if ($msg): ?>
    <div class="alert-banner alert-banner--<?= $msgType ?>" style="margin-bottom:1.5rem;">
      <span class="alert-banner__icon"><?= icon('alert-circle', '', 18) ?></span>
      <div class="alert-banner__content"><?= htmlspecialchars($msg) ?></div>
    </div>
  <?php endif; ?>

  <div class="card" style="padding:var(--space-xl);">
    <form action="editar.php" method="POST" enctype="multipart/form-data" class="admin-form">
      <input type="hidden" name="id" value="<?= $id ?>">

      <div class="form-group">
        <label for="titulo">Título del Material <span style="color:var(--color-rose);">*</span></label>
        <input type="text" id="titulo" name="titulo" class="form-control" required maxlength="200"
               value="<?= htmlspecialchars($material['titulo']) ?>">
      </div>

      <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(240px, 1fr)); gap:16px;">
        <div class="form-group">
          <label for="area">Área de Estudio <span style="color:var(--color-rose);">*</span></label>
          <select id="area" name="area" class="form-control" required>
            <?php
            $areas = [
                'Ciencias y Ambiente',
                'Matemáticas',
                'Comunicación',
                'Personal Social',
                'Arte y Cultura',
                'Inglés',
                'Educación Religiosa'
            ];
            foreach ($areas as $a):
            ?>
              <option value="<?= $a ?>" <?= ($material['area'] === $a) ? 'selected' : '' ?>>
                <?= $a ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group">
          <label for="grado">Grado Escolar <span style="color:var(--color-rose);">*</span></label>
          <select id="grado" name="grado" class="form-control" required>
            <?php for ($g = 1; $g <= 6; $g++): ?>
              <option value="<?= $g ?>° Primaria" <?= ($material['grado'] === ($g . '° Primaria')) ? 'selected' : '' ?>>
                <?= $g ?>° de Primaria
              </option>
            <?php endfor; ?>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label for="descripcion">Descripción o Instrucciones</label>
        <textarea id="descripcion" name="descripcion" class="form-control" rows="4"><?= htmlspecialchars($material['descripcion'] ?? '') ?></textarea>
      </div>

      <div class="form-group">
        <label>Archivo Adjunto Actual</label>
        <div class="alert-banner alert-banner--info" style="margin-bottom:12px;">
          <span class="alert-banner__icon"><?= icon('file-text', '', 18) ?></span>
          <div class="alert-banner__content">
            <strong><?= htmlspecialchars($material['nombre_archivo']) ?></strong>
            <span style="display:block; font-size:0.8rem; margin-top:2px;">
              Tipo: <?= strtoupper($material['tipo_archivo']) ?> &bull; <?= number_format($material['tamanio']/1024, 0) ?> KB &bull;
              <a href="descargar.php?id=<?= $material['id'] ?>" style="text-decoration:underline; font-weight:bold;">Descargar archivo actual</a>
            </span>
          </div>
        </div>

        <label style="font-weight:600; font-size:0.85rem; color:var(--color-text-muted);">
          Reemplazar archivo (opcional):
        </label>
        <input type="file" name="archivo" class="form-control"
               accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.zip,.txt">
      </div>

      <div style="display:flex; gap:12px; margin-top:24px; flex-wrap:wrap;">
        <button type="submit" class="btn btn--primary" style="min-width:180px;">
          <?= icon('check', '', 16) ?> Guardar Cambios
        </button>
        <a href="mis_materiales.php" class="btn btn--outline">Cancelar</a>
      </div>

    </form>
  </div>

</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
