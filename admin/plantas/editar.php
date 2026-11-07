<?php
/**
 * Editar Planta — Panel Admin
 * Semillitas del Futuro · Sin Emojis · Iconos SVG
 */
$rolRequerido = 1;
require_once __DIR__ . '/../../admin/includes/auth_check.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../libs/storage.php';
require_once __DIR__ . '/../../libs/qr.php';

$pdo = getConnection();
$id  = intval($_GET['id'] ?? $_POST['id'] ?? 0);

if ($id <= 0) {
    $_SESSION['planta_msg'] = 'ID de planta no válido.';
    $_SESSION['planta_msg_type'] = 'error';
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre      = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $riego       = trim($_POST['riego'] ?? '');
    $luz         = trim($_POST['luz'] ?? '');
    $regenerarQr = isset($_POST['regenerar_qr']);

    $errores = [];
    if (empty($nombre)) {
        $errores[] = 'El nombre de la planta es obligatorio.';
    }

    $stmt = $pdo->prepare('SELECT * FROM plantas WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $plantaActual = $stmt->fetch();

    if (!$plantaActual) {
        $_SESSION['planta_msg'] = 'La planta no existe.';
        $_SESSION['planta_msg_type'] = 'error';
        header('Location: index.php');
        exit;
    }

    $rutaFotoDB = $plantaActual['ruta_foto'];
    $rutaQrDB   = $plantaActual['ruta_qr'];

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $foto = $_FILES['foto'];
        $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $mimePermitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

        $extension = strtolower(pathinfo($foto['name'], PATHINFO_EXTENSION));
        $mimeReal  = mime_content_type($foto['tmp_name']);

        if (!in_array($extension, $extensionesPermitidas) || !in_array($mimeReal, $mimePermitidos)) {
            $errores[] = 'Formato de imagen no permitido (solo JPG, PNG, WEBP).';
        } elseif ($foto['size'] > MAX_FILE_SIZE) {
            $errores[] = 'La nueva imagen supera el tamaño máximo permitido.';
        } else {
            $guardado = storage_guardar_upload($foto, 'plantas', $extensionesPermitidas, MAX_FILE_SIZE, $mimePermitidos);

            if ($guardado['ok']) {
                storage_eliminar($plantaActual['ruta_foto'] ?? null);
                $rutaFotoDB = $guardado['ruta'];
            } else {
                $errores[] = $guardado['error'] ?? 'No se pudo guardar la nueva foto en el servidor.';
            }
        }
    }

    if (empty($errores) && ($regenerarQr || empty($rutaQrDB) || !storage_existe($rutaQrDB))) {
        $urlPlanta = BASE_URL . '/biohuerto/planta.php?id=' . $id;
        $qr = qr_generar_png($urlPlanta);

        if ($qr['ok']) {
            $guardadoQR = storage_guardar_bytes($qr['datos'], 'qr_planta_' . $id . '.png', 'image/png', 'qrs');
            if ($guardadoQR['ok']) {
                $rutaQrDB = $guardadoQR['ruta'];
            }
        }
    }

    if (!empty($errores)) {
        $_SESSION['planta_msg'] = implode(' ', $errores);
        $_SESSION['planta_msg_type'] = 'error';
    } else {
        try {
            $stmt = $pdo->prepare('
                UPDATE plantas
                SET nombre = :nombre, descripcion = :descripcion, riego = :riego, luz = :luz,
                    ruta_foto = :ruta_foto, ruta_qr = :ruta_qr
                WHERE id = :id
            ');
            $stmt->execute([
                ':nombre'      => $nombre,
                ':descripcion' => $descripcion,
                ':riego'       => $riego ?: null,
                ':luz'         => $luz ?: null,
                ':ruta_foto'   => $rutaFotoDB,
                ':ruta_qr'     => $rutaQrDB,
                ':id'          => $id,
            ]);

            if (!empty($plantaActual['ruta_qr']) && $plantaActual['ruta_qr'] !== $rutaQrDB) {
                storage_eliminar($plantaActual['ruta_qr']);
            }

            $_SESSION['planta_msg'] = 'Planta "' . $nombre . '" actualizada correctamente.';
            $_SESSION['planta_msg_type'] = 'success';
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            $_SESSION['planta_msg'] = 'Error al actualizar en la base de datos.';
            $_SESSION['planta_msg_type'] = 'error';
        }
    }
}

$stmt = $pdo->prepare('SELECT * FROM plantas WHERE id = :id');
$stmt->execute([':id' => $id]);
$planta = $stmt->fetch();

if (!$planta) {
    $_SESSION['planta_msg'] = 'La planta no existe.';
    $_SESSION['planta_msg_type'] = 'error';
    header('Location: index.php');
    exit;
}

$msg     = $_SESSION['planta_msg'] ?? null;
$msgType = $_SESSION['planta_msg_type'] ?? 'error';
unset($_SESSION['planta_msg'], $_SESSION['planta_msg_type']);

$adminPageTitle = 'Editar Planta — Admin';
include __DIR__ . '/../../admin/includes/header.php';
?>

<div class="admin-page-header">
  <h2>Editar Planta: <?= htmlspecialchars($planta['nombre']) ?></h2>
  <a href="index.php" class="btn btn--outline btn--sm">
    <?= icon('arrow-left', '', 14) ?> Volver al listado
  </a>
</div>

<?php if ($msg): ?>
  <div class="alert-banner alert-banner--<?= $msgType === 'success' ? 'success' : 'danger' ?>">
    <span class="alert-banner__icon">
      <?= icon($msgType === 'success' ? 'check-circle' : 'alert-circle', '', 18) ?>
    </span>
    <div class="alert-banner__content"><?= htmlspecialchars($msg) ?></div>
  </div>
<?php endif; ?>

<div class="admin-card">
  <form action="editar.php" method="POST" enctype="multipart/form-data" class="admin-form">
    <input type="hidden" name="id" value="<?= $id ?>">

    <div class="form-group">
      <label for="nombre">Nombre de la especie <span style="color:var(--color-rose);">*</span></label>
      <input type="text" id="nombre" name="nombre" class="form-control" required maxlength="150"
             value="<?= htmlspecialchars($planta['nombre']) ?>">
    </div>

    <div class="form-group">
      <label for="descripcion">Descripción y recomendaciones</label>
      <textarea id="descripcion" name="descripcion" class="form-control" rows="5"><?= htmlspecialchars($planta['descripcion'] ?? '') ?></textarea>
    </div>

    <div class="form-group">
      <label for="riego">Régimen de riego</label>
      <select id="riego" name="riego" class="form-control">
        <option value="" <?= empty($planta['riego']) ? 'selected' : '' ?>>Seleccionar...</option>
        <option value="Frecuente" <?= ($planta['riego'] ?? '') === 'Frecuente' ? 'selected' : '' ?>>Frecuente</option>
        <option value="Moderado" <?= ($planta['riego'] ?? '') === 'Moderado' ? 'selected' : '' ?>>Moderado</option>
        <option value="Espaciado" <?= ($planta['riego'] ?? '') === 'Espaciado' ? 'selected' : '' ?>>Espaciado</option>
        <option value="Poco" <?= ($planta['riego'] ?? '') === 'Poco' ? 'selected' : '' ?>>Poco</option>
      </select>
    </div>

    <div class="form-group">
      <label for="luz">Luz requerida</label>
      <select id="luz" name="luz" class="form-control">
        <option value="" <?= empty($planta['luz']) ? 'selected' : '' ?>>Seleccionar...</option>
        <option value="Sol Directo" <?= ($planta['luz'] ?? '') === 'Sol Directo' ? 'selected' : '' ?>>Sol Directo</option>
        <option value="Sol Parcial" <?= ($planta['luz'] ?? '') === 'Sol Parcial' ? 'selected' : '' ?>>Sol Parcial</option>
        <option value="Sombra" <?= ($planta['luz'] ?? '') === 'Sombra' ? 'selected' : '' ?>>Sombra</option>
      </select>
    </div>

    <div class="form-group">
      <label>Fotografía de la planta</label>
      <?php if (!empty($planta['ruta_foto'])): ?>
        <div style="margin-bottom:12px; display:flex; align-items:center; gap:16px;">
          <img src="<?= storage_url($planta['ruta_foto'], '../../') ?>"
               alt="Foto actual" style="width:90px; height:90px; object-fit:cover; border-radius:8px; border:1px solid var(--color-border);">
          <span style="font-size:0.85rem; color:var(--color-text-muted);">Foto actual. Selecciona una nueva imagen únicamente si deseas reemplazarla.</span>
        </div>
      <?php endif; ?>
      <input type="file" id="foto" name="foto" accept="image/jpeg,image/png,image/gif,image/webp" class="form-control">
      <div id="preview" style="margin-top:10px;"></div>
    </div>

    <div class="form-group" style="background:var(--color-surface); border:1px solid var(--color-border); padding:16px; border-radius:8px;">
      <label style="display:flex; align-items:center; gap:6px;">
        <?= icon('qr-code', '', 18) ?>
        <span>Código QR</span>
      </label>
      <div style="display:flex; align-items:center; gap:16px; margin-top:8px;">
        <?php if (!empty($planta['ruta_qr'])): ?>
          <img src="<?= storage_url($planta['ruta_qr'], '../../') ?>" alt="QR" style="width:64px; height:64px; border-radius:6px; border:1px solid var(--color-border); padding:2px; background:#fff;">
        <?php endif; ?>
        <div>
          <label style="cursor:pointer; display:flex; align-items:center; gap:8px; font-weight:normal;">
            <input type="checkbox" name="regenerar_qr" value="1">
            <strong>Regenerar código QR automáticamente al guardar</strong>
          </label>
          <small style="color:var(--color-text-muted); display:block; margin-top:2px;">
            Enlace: <code><?= BASE_URL ?>/biohuerto/planta.php?id=<?= $id ?></code>
          </small>
        </div>
      </div>
    </div>

    <div style="display:flex; gap:12px; margin-top:24px; flex-wrap:wrap;">
      <button type="submit" class="btn btn--primary" style="min-width:180px;">
        <?= icon('check', '', 16) ?> Guardar Cambios
      </button>
      <a href="index.php" class="btn btn--outline">Cancelar</a>
    </div>
  </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const inputFoto = document.getElementById('foto');
  const preview   = document.getElementById('preview');
  if (inputFoto && preview) {
    inputFoto.addEventListener('change', () => {
      const file = inputFoto.files[0];
      if (!file) return;
      const reader = new FileReader();
      reader.onload = (e) => {
        preview.innerHTML = `<img src="${e.target.result}" style="max-height:160px; border-radius:8px; border:1px solid var(--color-border);" alt="Previa nueva">`;
      };
      reader.readAsDataURL(file);
    });
  }
});
</script>

<?php include __DIR__ . '/../../admin/includes/footer.php'; ?>
