<?php
/**
 * Carga de variables de entorno — Semillitas del Futuro
 *
 * Orden de prioridad:
 *   1. Variables reales del proceso (Vercel, Railway, Render, Docker, etc.)
 *   2. Archivo .env en la raíz del proyecto (solo desarrollo local)
 *   3. Valor por defecto indicado en cada llamada
 */

if (!function_exists('env_load_file')) {
    /**
     * Lee un archivo .env sencillo (KEY=VALUE) sin dependencias externas.
     * No sobreescribe variables que ya existan en el entorno.
     */
    function env_load_file(string $path): void
    {
        static $loaded = [];

        $path = str_replace('\\', '/', $path);
        if (isset($loaded[$path]) || !is_file($path)) {
            return;
        }
        $loaded[$path] = true;

        $lines = @file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            return;
        }

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || $line[0] === '#' || $line[0] === ';') {
                continue;
            }
            if (stripos($line, 'export ') === 0) {
                $line = trim(substr($line, 7));
            }

            $pos = strpos($line, '=');
            if ($pos === false) {
                continue;
            }

            $key = trim(substr($line, 0, $pos));
            $value = trim(substr($line, $pos + 1));

            if ($key === '' || getenv($key) !== false) {
                continue;
            }

            // Quitar comillas envolventes
            $len = strlen($value);
            if ($len >= 2) {
                $first = $value[0];
                $last = $value[$len - 1];
                if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
                    $value = substr($value, 1, -1);
                }
            }

            putenv($key . '=' . $value);
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }
}

if (!function_exists('env')) {
    /**
     * Devuelve una variable de entorno o su valor por defecto.
     */
    function env(string $key, ?string $default = null): ?string
    {
        static $bootstrapped = false;

        if (!$bootstrapped) {
            $bootstrapped = true;
            env_load_file(dirname(__DIR__) . '/.env');
        }

        $value = getenv($key);
        if ($value === false || $value === '') {
            return $default;
        }

        return $value;
    }
}

if (!function_exists('env_bool')) {
    function env_bool(string $key, bool $default = false): bool
    {
        $value = env($key);
        if ($value === null) {
            return $default;
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}
