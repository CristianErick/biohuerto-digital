<?php
/**
 * Descarga Segura de Materiales — Semillitas del Futuro
 *
 * Incrementa el contador de descargas y envía el archivo con encabezados HTTP limpios.
 * Soporta archivos guardados en MySQL (archivo:ID) y archivos locales antiguos.
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../libs/storage.php';

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    http_response_code(400);
    die('ID de material inválido.');
}

try {
    $pdo = getConnection();

    // Obtener información del material
    $stmt = $pdo->prepare('SELECT * FROM materiales WHERE id = :id AND activo = 1 LIMIT 1');
    $stmt->execute([':id' => $id]);
    $material = $stmt->fetch();

    if (!$material) {
        http_response_code(404);
        die('El material solicitado no existe o ha sido retirado.');
    }

    $ruta = (string) ($material['ruta_archivo'] ?? '');
    $datos = null;
    $rutaArchivo = null;

    if (preg_match('/^archivo:(\d+)$/', $ruta)) {
        $archivo = storage_contenido($ruta);

        if ($archivo === null) {
            http_response_code(404);
            die('El archivo físico no fue encontrado en el servidor.');
        }

        $datos = (string) $archivo['datos'];
        $contentType = (string) ($archivo['mime'] ?: 'application/octet-stream');
        $tamanio = strlen($datos);
    } else {
        // Compatibilidad con archivos locales antiguos
        $rutaArchivo = realpath(__DIR__ . '/../' . ltrim($ruta, '/'));
        $directorioPermitido = realpath(__DIR__ . '/uploads');

        if (
            !$rutaArchivo ||
            !is_file($rutaArchivo) ||
            !$directorioPermitido ||
            strpos($rutaArchivo, $directorioPermitido) !== 0
        ) {
            http_response_code(404);
            die('El archivo físico no fue encontrado en el servidor.');
        }

        // Mapeo básico de extensiones a MIME Types
        $ext = strtolower(pathinfo($material['nombre_archivo'], PATHINFO_EXTENSION));
        $mimes = [
            'pdf'  => 'application/pdf',
            'doc'  => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'ppt'  => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'xls'  => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'zip'  => 'application/zip',
            'txt'  => 'text/plain',
            'jpg'  => 'image/jpeg',
            'png'  => 'image/png'
        ];
        $contentType = $mimes[$ext] ?? 'application/octet-stream';
        $tamanio = filesize($rutaArchivo);
    }

    // Incrementar contador de descargas
    $updateStmt = $pdo->prepare('UPDATE materiales SET descargas = descargas + 1 WHERE id = :id');
    $updateStmt->execute([':id' => $id]);

    // Limpiar cualquier buffer previo
    if (ob_get_level()) {
        ob_end_clean();
    }

    // Encabezados para forzar la descarga con el nombre original del archivo
    header('Content-Description: File Transfer');
    header('Content-Type: ' . $contentType);
    header('Content-Disposition: attachment; filename="' . rawurlencode($material['nombre_archivo']) . '"; filename*=UTF-8\'\'' . rawurlencode($material['nombre_archivo']));
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: ' . $tamanio);
    header('X-Content-Type-Options: nosniff');

    if ($datos !== null) {
        echo $datos;
    } else {
        readfile($rutaArchivo);
    }
    exit;

} catch (PDOException $e) {
    error_log('Semillitas descarga: ' . $e->getMessage());
    http_response_code(500);
    die('Error al procesar la descarga.');
}
