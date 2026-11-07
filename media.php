<?php
/**
 * Servir archivos almacenados en la base de datos.
 *
 * URL: /media.php?id=123
 *      /media.php?id=123&descargar=1
 */

require_once __DIR__ . '/libs/storage.php';

$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) {
    http_response_code(400);
    die('Archivo no válido.');
}

$archivo = storage_contenido('archivo:' . $id);
if ($archivo === null) {
    http_response_code(404);
    die('El archivo no existe.');
}

$datos = (string) $archivo['datos'];
$mime  = (string) ($archivo['mime'] ?: 'application/octet-stream');
$nombre = (string) ($archivo['nombre'] ?: ('archivo_' . $id));

header('Content-Type: ' . $mime);
header('Content-Length: ' . strlen($datos));
header('X-Content-Type-Options: nosniff');
header('Cache-Control: public, max-age=31536000, immutable');

if (isset($_GET['descargar'])) {
    header('Content-Disposition: attachment; filename="' . rawurlencode($nombre) . '"; filename*=UTF-8\'\'' . rawurlencode($nombre));
} else {
    header('Content-Disposition: inline; filename="' . rawurlencode($nombre) . '"');
}

echo $datos;
