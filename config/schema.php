<?php
/**
 * Esquema mínimo requerido por la aplicación.
 *
 * Se ejecuta con CREATE TABLE IF NOT EXISTS para que el proyecto funcione
 * incluso si el administrador olvida importar database_v3.sql.
 */

require_once __DIR__ . '/database.php';

if (!function_exists('db_ensure_core_tables')) {
    function db_ensure_core_tables(): void
    {
        static $done = false;
        if ($done) {
            return;
        }
        $done = true;

        try {
            $pdo = getConnection();

            $pdo->exec(
                "CREATE TABLE IF NOT EXISTS archivos (
                    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                    nombre VARCHAR(255) NOT NULL,
                    mime VARCHAR(150) NOT NULL DEFAULT 'application/octet-stream',
                    tamanio INT UNSIGNED NOT NULL DEFAULT 0,
                    datos LONGBLOB NOT NULL,
                    creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (id)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
            );

            $pdo->exec(
                "CREATE TABLE IF NOT EXISTS app_data (
                    clave VARCHAR(100) NOT NULL,
                    valor LONGTEXT NOT NULL,
                    actualizado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY (clave)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
            );

            $pdo->exec(
                "CREATE TABLE IF NOT EXISTS sessions (
                    id VARCHAR(128) NOT NULL,
                    data MEDIUMTEXT NOT NULL,
                    last_activity INT UNSIGNED NOT NULL,
                    PRIMARY KEY (id),
                    KEY idx_sessions_last_activity (last_activity)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
            );
        } catch (Throwable $e) {
            error_log('Semillitas schema: ' . $e->getMessage());
        }
    }
}
