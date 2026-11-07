<?php
/**
 * Almacén clave/valor en MySQL — Semillitas del Futuro
 *
 * Evita depender de archivos JSON escribibles en el servidor, algo que no
 * funciona en contenedores efímeros como Vercel.
 */

require_once __DIR__ . '/../config/schema.php';

if (!function_exists('appdata_get')) {
    function appdata_get(string $clave, array $default = []): array
    {
        db_ensure_core_tables();

        try {
            $stmt = getConnection()->prepare('SELECT valor FROM app_data WHERE clave = :clave LIMIT 1');
            $stmt->execute([':clave' => $clave]);
            $row = $stmt->fetch();

            if ($row) {
                $datos = json_decode((string) $row['valor'], true);
                if (is_array($datos)) {
                    return $datos;
                }
            }
        } catch (Throwable $e) {
            error_log('Semillitas appdata get: ' . $e->getMessage());
        }

        return $default;
    }
}

if (!function_exists('appdata_set')) {
    function appdata_set(string $clave, array $valor): bool
    {
        db_ensure_core_tables();

        try {
            $json = json_encode($valor, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            if ($json === false) {
                return false;
            }

            $stmt = getConnection()->prepare(
                'INSERT INTO app_data (clave, valor, actualizado_en)
                 VALUES (:clave, :valor, NOW())
                 ON DUPLICATE KEY UPDATE valor = VALUES(valor), actualizado_en = NOW()'
            );

            return $stmt->execute([
                ':clave' => $clave,
                ':valor' => $json,
            ]);
        } catch (Throwable $e) {
            error_log('Semillitas appdata set: ' . $e->getMessage());
            return false;
        }
    }
}
