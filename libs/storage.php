<?php
/**
 * Almacenamiento de archivos — Semillitas del Futuro
 *
 * Por defecto los archivos se guardan en la tabla `archivos` de MySQL para que
 * sobrevivan a reinicios de contenedores (Vercel, Railway, Render, etc.).
 *
 * Con STORAGE_DRIVER=local se guardan en disco, como en el desarrollo clásico.
 *
 * Las rutas guardadas en la base de datos pueden ser:
 *   - archivo:123                 -> fila de la tabla archivos
 *   - uploads/plantas/foto.jpg    -> archivo local (compatibilidad)
 *   - https://...                 -> URL externa
 */

require_once __DIR__ . '/../config/env.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/schema.php';

if (!function_exists('storage_driver')) {
    function storage_driver(): string
    {
        $driver = strtolower((string) env('STORAGE_DRIVER', 'db'));

        return in_array($driver, ['db', 'local'], true) ? $driver : 'db';
    }
}

if (!function_exists('storage_mime_de')) {
    function storage_mime_de(string $nombre): string
    {
        $ext = strtolower(pathinfo($nombre, PATHINFO_EXTENSION));

        $mimes = [
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png'  => 'image/png',
            'gif'  => 'image/gif',
            'webp' => 'image/webp',
            'svg'  => 'image/svg+xml',
            'pdf'  => 'application/pdf',
            'doc'  => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'ppt'  => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'xls'  => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'zip'  => 'application/zip',
            'rar'  => 'application/vnd.rar',
            'txt'  => 'text/plain',
        ];

        return $mimes[$ext] ?? 'application/octet-stream';
    }
}

if (!function_exists('storage_guardar_bytes')) {
    /**
     * Guarda contenido binario.
     *
     * @return array{ok:bool, ruta?:string, nombre?:string, error?:string}
     */
    function storage_guardar_bytes(string $datos, string $nombre, ?string $mime = null, string $carpeta = 'archivos'): array
    {
        db_ensure_core_tables();

        $nombre = basename($nombre) !== '' ? basename($nombre) : 'archivo';
        $mime   = $mime ?: storage_mime_de($nombre);

        if (storage_driver() === 'local') {
            $carpeta = trim(str_replace(['..', '\\'], ['', '/'], $carpeta), '/');
            $dir = __DIR__ . '/../uploads/' . $carpeta . '/';
            if (!is_dir($dir) && !@mkdir($dir, 0755, true)) {
                return ['ok' => false, 'error' => 'No se pudo crear la carpeta de archivos.'];
            }

            $seguro = preg_replace('/[^A-Za-z0-9._-]/', '_', $nombre) ?: 'archivo';
            $final  = date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '_' . $seguro;

            if (@file_put_contents($dir . $final, $datos) === false) {
                return ['ok' => false, 'error' => 'No se pudo guardar el archivo en el servidor.'];
            }

            return [
                'ok'     => true,
                'ruta'   => 'uploads/' . $carpeta . '/' . $final,
                'nombre' => $nombre,
            ];
        }

        try {
            $pdo = getConnection();
            $stmt = $pdo->prepare(
                'INSERT INTO archivos (nombre, mime, tamanio, datos, creado_en)
                 VALUES (:nombre, :mime, :tamanio, :datos, NOW())'
            );
            $stmt->bindValue(':nombre', $nombre);
            $stmt->bindValue(':mime', $mime);
            $stmt->bindValue(':tamanio', strlen($datos), PDO::PARAM_INT);
            $stmt->bindValue(':datos', $datos, PDO::PARAM_LOB);
            $stmt->execute();

            return [
                'ok'     => true,
                'ruta'   => 'archivo:' . (int) $pdo->lastInsertId(),
                'nombre' => $nombre,
            ];
        } catch (Throwable $e) {
            error_log('Semillitas storage: ' . $e->getMessage());
            return ['ok' => false, 'error' => 'No se pudo guardar el archivo en la base de datos.'];
        }
    }
}

if (!function_exists('storage_guardar_upload')) {
    /**
     * Valida y guarda un archivo subido con $_FILES.
     *
     * @param array $file    Entrada de $_FILES
     * @param string $carpeta Carpeta lógica (solo se usa con STORAGE_DRIVER=local)
     * @param array $exts    Extensiones permitidas (vacío = cualquiera)
     * @param int $maxBytes  Tamaño máximo
     * @param array $mimes   MIME permitidos (vacío = no validar)
     * @return array{ok:bool, ruta?:string, nombre?:string, error?:string}
     */
    function storage_guardar_upload(array $file, string $carpeta, array $exts, int $maxBytes, array $mimes = []): array
    {
        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            return ['ok' => false, 'error' => 'No se recibió un archivo válido.'];
        }

        if (($file['size'] ?? 0) <= 0) {
            return ['ok' => false, 'error' => 'El archivo está vacío.'];
        }

        if ($file['size'] > $maxBytes) {
            $mb = round($maxBytes / 1024 / 1024, 1);
            return ['ok' => false, 'error' => 'El archivo supera el tamaño máximo de ' . $mb . ' MB.'];
        }

        $nombre = basename((string) ($file['name'] ?? 'archivo'));
        $ext    = strtolower(pathinfo($nombre, PATHINFO_EXTENSION));

        if ($exts !== [] && !in_array($ext, $exts, true)) {
            return ['ok' => false, 'error' => 'Formato de archivo no permitido.'];
        }

        $mime = '';
        if (function_exists('mime_content_type')) {
            $mime = (string) @mime_content_type((string) $file['tmp_name']);
        }

        if ($mimes !== [] && $mime !== '' && !in_array($mime, $mimes, true)) {
            return ['ok' => false, 'error' => 'El contenido del archivo no es válido.'];
        }

        $datos = @file_get_contents((string) $file['tmp_name']);
        if ($datos === false) {
            return ['ok' => false, 'error' => 'No se pudo leer el archivo temporal.'];
        }

        return storage_guardar_bytes($datos, $nombre, $mime ?: storage_mime_de($nombre), $carpeta);
    }
}

if (!function_exists('storage_url')) {
    /**
     * Convierte una ruta guardada en BD a una URL utilizable en HTML.
     *
     * @param string $prefijo Ruta relativa al script actual: '', '../' o '../../'
     */
    function storage_url(?string $ruta, string $prefijo = '../'): string
    {
        $ruta = trim((string) $ruta);
        if ($ruta === '') {
            return '';
        }

        if (preg_match('#^(https?:)?//#i', $ruta)) {
            return $ruta;
        }

        if (preg_match('/^archivo:(\d+)$/', $ruta, $m)) {
            return $prefijo . 'media.php?id=' . (int) $m[1];
        }

        return $prefijo . ltrim($ruta, '/');
    }
}

if (!function_exists('storage_url_descarga')) {
    function storage_url_descarga(?string $ruta, string $prefijo = '../'): string
    {
        $url = storage_url($ruta, $prefijo);
        if ($url === '') {
            return '';
        }

        return $url . (str_contains($url, '?') ? '&' : '?') . 'descargar=1';
    }
}

if (!function_exists('storage_existe')) {
    function storage_existe(?string $ruta): bool
    {
        db_ensure_core_tables();

        $ruta = trim((string) $ruta);
        if ($ruta === '') {
            return false;
        }

        if (preg_match('#^(https?:)?//#i', $ruta)) {
            return true;
        }

        if (preg_match('/^archivo:(\d+)$/', $ruta, $m)) {
            try {
                $stmt = getConnection()->prepare('SELECT 1 FROM archivos WHERE id = :id');
                $stmt->execute([':id' => (int) $m[1]]);
                return (bool) $stmt->fetchColumn();
            } catch (Throwable $e) {
                return false;
            }
        }

        return is_file(__DIR__ . '/../' . ltrim($ruta, '/'));
    }
}

if (!function_exists('storage_contenido')) {
    /**
     * Devuelve nombre, mime, tamaño y datos de un archivo de la BD.
     *
     * @return array{nombre:string, mime:string, tamanio:int, datos:string}|null
     */
    function storage_contenido(string $ruta): ?array
    {
        db_ensure_core_tables();

        if (!preg_match('/^archivo:(\d+)$/', $ruta, $m)) {
            return null;
        }

        try {
            $stmt = getConnection()->prepare(
                'SELECT nombre, mime, tamanio, datos FROM archivos WHERE id = :id LIMIT 1'
            );
            $stmt->execute([':id' => (int) $m[1]]);
            $row = $stmt->fetch();

            return $row ?: null;
        } catch (Throwable $e) {
            error_log('Semillitas storage read: ' . $e->getMessage());
            return null;
        }
    }
}

if (!function_exists('storage_eliminar')) {
    function storage_eliminar(?string $ruta): void
    {
        db_ensure_core_tables();

        $ruta = trim((string) $ruta);
        if ($ruta === '') {
            return;
        }

        if (preg_match('#^(https?:)?//#i', $ruta)) {
            return;
        }

        if (preg_match('/^archivo:(\d+)$/', $ruta, $m)) {
            try {
                $stmt = getConnection()->prepare('DELETE FROM archivos WHERE id = :id');
                $stmt->execute([':id' => (int) $m[1]]);
            } catch (Throwable $e) {
                error_log('Semillitas storage delete: ' . $e->getMessage());
            }
            return;
        }

        $ruta = ltrim(str_replace('\\', '/', $ruta), '/');
        if (str_starts_with($ruta, 'uploads/') || str_starts_with($ruta, 'repositorio/uploads/')) {
            $abs = __DIR__ . '/../' . $ruta;
            if (is_file($abs)) {
                @unlink($abs);
            }
        }
    }
}
