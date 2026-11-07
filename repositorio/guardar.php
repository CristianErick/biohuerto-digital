<?php
/**
 * Guardar Material Educativo — Backend PHP
 * Semillitas del Futuro
 */
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../libs/storage.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: subir.php');
    exit;
}

$usuarioId   = $_SESSION['usuario_id'];
$titulo      = trim($_POST['titulo'] ?? '');
$area        = trim($_POST['area'] ?? '');
$grado       = trim($_POST['grado'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');

$_SESSION['repo_old'] = compact('titulo', 'area', 'grado', 'descripcion');

// Validaciones
$errores = [];

if (empty($titulo)) {
    $errores[] = 'El título del material es obligatorio.';
}
if (empty($area)) {
    $errores[] = 'Debes seleccionar el área de estudio.';
}
if (empty($grado)) {
    $errores[] = 'Debes seleccionar el grado escolar.';
}

// Validar Archivo
if (!isset($_FILES['archivo']) || $_FILES['archivo']['error'] !== UPLOAD_ERR_OK) {
    $errores[] = 'Debes adjuntar un archivo válido para el material.';
} else {
    $archivo = $_FILES['archivo'];
    $maxBytes = 15 * 1024 * 1024; // 15 MB

    $extensionesPermitidas = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'zip', 'txt', 'rar'];
    $nombreOriginal = basename($archivo['name']);
    $extension = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));

    if (!in_array($extension, $extensionesPermitidas)) {
        $errores[] = 'Extensión no permitida. Formatos aceptados: PDF, Word, PowerPoint, Excel, ZIP o TXT.';
    }

    if ($archivo['size'] > $maxBytes) {
        $errores[] = 'El archivo supera el tamaño máximo permitido de 15 MB.';
    }
}

if (!empty($errores)) {
    $_SESSION['repo_msg'] = implode(' ', $errores);
    $_SESSION['repo_msg_type'] = 'error';
    header('Location: subir.php');
    exit;
}

// Guardar archivo (en MySQL por defecto; en disco con STORAGE_DRIVER=local)
$guardado = storage_guardar_upload($archivo, 'materiales', $extensionesPermitidas, $maxBytes);

if (!$guardado['ok']) {
    $_SESSION['repo_msg'] = $guardado['error'] ?? 'Hubo un error al guardar el archivo en el servidor.';
    $_SESSION['repo_msg_type'] = 'error';
    header('Location: subir.php');
    exit;
}

$rutaRelativaDB = $guardado['ruta'];

// Guardar en la base de datos
try {
    $pdo = getConnection();
    $stmt = $pdo->prepare('
        INSERT INTO materiales
        (usuario_id, titulo, descripcion, area, grado, nombre_archivo, ruta_archivo, tipo_archivo, tamanio, descargas, activo, created_at)
        VALUES
        (:usuario_id, :titulo, :descripcion, :area, :grado, :nombre_archivo, :ruta_archivo, :tipo_archivo, :tamanio, 0, 1, NOW())
    ');

    $stmt->execute([
        ':usuario_id'     => $usuarioId,
        ':titulo'         => $titulo,
        ':descripcion'    => $descripcion,
        ':area'           => $area,
        ':grado'          => $grado,
        ':nombre_archivo' => $nombreOriginal,
        ':ruta_archivo'   => $rutaRelativaDB,
        ':tipo_archivo'   => $extension,
        ':tamanio'        => $archivo['size'],
    ]);

    unset($_SESSION['repo_old']);
    $_SESSION['repo_msg'] = '¡Material "' . $titulo . '" subido y publicado con éxito!';
    $_SESSION['repo_msg_type'] = 'success';
    header('Location: mis_materiales.php');
    exit;

} catch (PDOException $e) {
    // Si falla el INSERT, borrar el archivo huérfano
    storage_eliminar($rutaRelativaDB);
    $_SESSION['repo_msg'] = 'Error al registrar el material en la base de datos.';
    $_SESSION['repo_msg_type'] = 'error';
    header('Location: subir.php');
    exit;
}
