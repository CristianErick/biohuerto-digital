<?php
/**
 * Auto-prepend para producción (Docker / Vercel / Railway).
 *
 * Se registra con auto_prepend_file en php.ini. Permite configurar sesiones
 * compartidas antes de que cualquier página llame a session_start().
 */

if (PHP_SAPI !== 'cli') {
    require_once __DIR__ . '/env.php';
    require_once __DIR__ . '/database.php';
    require_once __DIR__ . '/session.php';
    require_once __DIR__ . '/schema.php';

    date_default_timezone_set((string) env('APP_TIMEZONE', 'America/Lima'));

    if (env_bool('DB_AUTO_MIGRATE', true)) {
        db_ensure_core_tables();
    }

    semillitas_configurar_sesion();
}
