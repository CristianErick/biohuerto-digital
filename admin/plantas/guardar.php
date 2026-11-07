<?php
/**
 * Guardar Planta + Generar QR — Semillitas del Futuro
 *
 * Procesa el formulario de crear.php:
 *  1. Valida datos y archivo de imagen.
 *  2. Guarda la foto (en MySQL por defecto; en disco con STORAGE_DRIVER=local).
 *  3. Inserta el registro en la BD.
 *  4. Genera el código QR y lo guarda.
 *  5. Actualiza el registro con la ruta del QR.
 */
$rolRequerido = 1;
require_once __DIR__ . '/../../admin/includes/auth_check.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../libs/storage.php';
require_once __DIR__ . '/../../libs/qr.php';

// ── Solo aceptar POST ────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: crear.php');
    exit;
}

// ── Recoger datos del formulario ─────────────────────────
$nombre      = trim($_POST['nombre'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');
$riego       = trim($_POST['riego'] ?? '');
$luz         = trim($_POST['luz'] ?? '');

// ── Validaciones ─────────────────────────────────────────
$errores = [];

if (empty($nombre)) {
    $errores[] = 'El nombre de la planta es obligatorio.';
}

if (strlen($nombre) > 150) {
    $errores[] = 'El nombre no puede exceder 150 caracteres.';
}

// Validar archivo de foto
$extensionesPermitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
$mimePermitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
    $errores[] = 'Debes subir una foto de la planta.';
} else {
    $foto = $_FILES['foto'];
    $extension = strtolower(pathinfo($foto['name'], PATHINFO_EXTENSION));
    $mimeReal  = mime_content_type($foto['tmp_name']);

    if (!in_array($extension, $extensionesPermitidas)) {
        $errores[] = 'Formato de imagen no permitido. Usa: JPG, PNG, GIF o WEBP.';
    }

    if (!in_array($mimeReal, $mimePermitidos)) {
        $errores[] = 'El archivo no es una imagen válida.';
    }

    if ($foto['size'] > MAX_FILE_SIZE) {
        $errores[] = 'La imagen supera el tamaño máximo permitido.';
    }
}

// Si hay errores, regresar al formulario
if (!empty($errores)) {
    $_SESSION['planta_msg'] = implode(' ', $errores);
    $_SESSION['planta_msg_type'] = 'error';
    header('Location: crear.php');
    exit;
}

// ── Guardar la foto de la planta ─────────────────────────
$guardado = storage_guardar_upload($foto, 'plantas', $extensionesPermitidas, MAX_FILE_SIZE, $mimePermitidos);

if (!$guardado['ok']) {
    $_SESSION['planta_msg'] = $guardado['error'] ?? 'Error al guardar la imagen.';
    $_SESSION['planta_msg_type'] = 'error';
    header('Location: crear.php');
    exit;
}

$rutaFotoDB = $guardado['ruta'];

// ── Insertar en la base de datos (sin QR todavía) ────────
try {
    $pdo = getConnection();

    $stmt = $pdo->prepare('
        INSERT INTO plantas (nombre, descripcion, riego, luz, ruta_foto, fecha_registro)
        VALUES (:nombre, :descripcion, :riego, :luz, :ruta_foto, NOW())
    ');

    $stmt->execute([
        ':nombre'      => $nombre,
        ':descripcion' => $descripcion,
        ':riego'       => $riego ?: null,
        ':luz'         => $luz ?: null,
        ':ruta_foto'   => $rutaFotoDB,
    ]);

    $plantaId = (int) $pdo->lastInsertId();

} catch (PDOException $e) {
    // Limpiar la foto subida si falla el INSERT
    storage_eliminar($rutaFotoDB);

    $_SESSION['planta_msg'] = 'Error al guardar en la base de datos.';
    $_SESSION['planta_msg_type'] = 'error';
    header('Location: crear.php');
    exit;
}

// ── Generar el código QR ─────────────────────────────────
$urlPlanta = BASE_URL . '/biohuerto/planta.php?id=' . $plantaId;
$nombreQR  = 'qr_planta_' . $plantaId . '.png';

$qr = qr_generar_png($urlPlanta);
$qrGenerado = false;

if ($qr['ok']) {
    $guardadoQR = storage_guardar_bytes($qr['datos'], $nombreQR, 'image/png', 'qrs');

    if ($guardadoQR['ok']) {
        try {
            $stmt = $pdo->prepare('UPDATE plantas SET ruta_qr = :ruta_qr WHERE id = :id');
            $stmt->execute([':ruta_qr' => $guardadoQR['ruta'], ':id' => $plantaId]);
            $qrGenerado = true;
        } catch (PDOException $e) {
            // El QR se generó pero no se pudo actualizar la BD (no crítico)
            storage_eliminar($guardadoQR['ruta']);
        }
    }
}

// ── Mensaje de éxito y redirección ───────────────────────
$msg = '¡Planta "' . $nombre . '" registrada exitosamente!';
if ($qrGenerado) {
    $msg .= ' El código QR fue generado automáticamente.';
} else {
    $msg .= ' No se pudo generar el QR automáticamente. Puedes regenerarlo desde Editar.';
}

$_SESSION['planta_msg'] = $msg;
$_SESSION['planta_msg_type'] = 'success';
header('Location: index.php');
exit;
