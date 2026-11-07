<?php
/**
 * Migrar archivos locales antiguos a MySQL — Semillitas del Futuro
 *
 * Uso (desde la raíz del proyecto):
 *   php tools/migrate_uploads.php
 *   php tools/migrate_uploads.php --delete
 *
 * Recorre las rutas guardadas en plantas.ruta_foto, plantas.ruta_qr y
 * materiales.ruta_archivo. Si el archivo existe en disco, lo guarda en la
 * tabla `archivos` y actualiza la referencia a "archivo:ID".
 *
 * Ejecútalo localmente apuntando a la base de datos de producción (variables
 * de entorno) para subir las fotos/QR/PDF antiguos antes de desplegar.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    die('Este script solo se puede ejecutar desde la línea de comandos.');
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../libs/storage.php';

$borrarLocales = in_array('--delete', $argv ?? [], true);
$pdo = getConnection();

/**
 * Migra una columna de una tabla.
 */
function migrar_columna(PDO $pdo, string $tabla, string $columna, string $idColumna, bool $borrarLocales): void
{
    $stmt = $pdo->query("SELECT {$idColumna} AS id, {$columna} AS ruta FROM {$tabla} WHERE {$columna} IS NOT NULL AND {$columna} <> ''");
    $filas = $stmt->fetchAll();

    echo "→ {$tabla}.{$columna}: " . count($filas) . " registro(s)" . PHP_EOL;

    foreach ($filas as $fila) {
        $ruta = trim((string) $fila['ruta']);

        if ($ruta === '' || str_starts_with($ruta, 'archivo:') || preg_match('#^(https?:)?//#i', $ruta)) {
            continue;
        }

        $abs = __DIR__ . '/../' . ltrim(str_replace('\\', '/', $ruta), '/');
        if (!is_file($abs)) {
            echo "   ! id={$fila['id']} no encontrado: {$ruta}" . PHP_EOL;
            continue;
        }

        $datos = @file_get_contents($abs);
        if ($datos === false) {
            echo "   ! id={$fila['id']} no se pudo leer: {$ruta}" . PHP_EOL;
            continue;
        }

        $guardado = storage_guardar_bytes($datos, basename($abs), storage_mime_de($abs), 'migrados');
        if (!$guardado['ok']) {
            echo "   ! id={$fila['id']} error: " . ($guardado['error'] ?? 'desconocido') . PHP_EOL;
            continue;
        }

        $upd = $pdo->prepare("UPDATE {$tabla} SET {$columna} = :ruta WHERE {$idColumna} = :id");
        $upd->execute([':ruta' => $guardado['ruta'], ':id' => $fila['id']]);

        echo "   ✓ id={$fila['id']} {$ruta} -> {$guardado['ruta']}" . PHP_EOL;

        if ($borrarLocales) {
            @unlink($abs);
        }
    }
}

echo 'Migrando archivos locales a MySQL (STORAGE_DRIVER=' . storage_driver() . ')' . PHP_EOL;

migrar_columna($pdo, 'plantas', 'ruta_foto', 'id', $borrarLocales);
migrar_columna($pdo, 'plantas', 'ruta_qr', 'id', $borrarLocales);
migrar_columna($pdo, 'materiales', 'ruta_archivo', 'id', $borrarLocales);

echo 'Listo.' . PHP_EOL;
