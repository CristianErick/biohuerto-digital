<?php
/**
 * Configuración de conexión a la base de datos MySQL.
 *
 * Todos los valores se pueden definir por variables de entorno, lo que permite
 * usar el mismo código en Laragon, Docker, Vercel, Railway, Render, etc.
 *
 * Variables principales:
 *   DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASS
 *   DB_SSL=true, DB_SSL_CA=/ruta/ca.pem, DB_SSL_VERIFY=true
 *   APP_URL=https://midominio.com
 *   STORAGE_DRIVER=db|local
 *   SESSION_DRIVER=files|database
 */

require_once __DIR__ . '/env.php';

define('DB_HOST', (string) env('DB_HOST', 'localhost'));
define('DB_PORT', (string) env('DB_PORT', '3306'));
define('DB_NAME', (string) env('DB_NAME', 'semillitas_db'));
define('DB_USER', (string) env('DB_USER', 'root'));
define('DB_PASS', (string) env('DB_PASS', ''));
define('DB_CHARSET', (string) env('DB_CHARSET', 'utf8mb4'));
define('DB_SSL', env_bool('DB_SSL', false));
define('DB_SSL_CA', (string) env('DB_SSL_CA', ''));
define('DB_SSL_VERIFY', env_bool('DB_SSL_VERIFY', true));

/**
 * Construye el DSN de MySQL.
 */
function db_dsn(): string
{
    return 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
}

/**
 * Opciones PDO comunes.
 */
function db_options(): array
{
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::ATTR_TIMEOUT            => 10,
    ];

    if (DB_SSL) {
        $ca = DB_SSL_CA;
        $caPem = (string) env('DB_SSL_CA_PEM', '');
        if ($ca === '' && $caPem !== '') {
            $tmpCa = rtrim(sys_get_temp_dir(), '/\\') . DIRECTORY_SEPARATOR . 'semillitas_db_ca.pem';
            if (!is_file($tmpCa) || @file_get_contents($tmpCa) !== $caPem) {
                @file_put_contents($tmpCa, $caPem);
            }
            $ca = $tmpCa;
        }
        if ($ca !== '') {
            $options[PDO::MYSQL_ATTR_SSL_CA] = $ca;
        }
        $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = DB_SSL_VERIFY;
    }

    return $options;
}

/**
 * Detecta la URL base del sitio cuando APP_URL no está definida.
 * Funciona tanto en http://localhost/semillitas como en la raíz de un dominio.
 */
function db_detect_base_url(): string
{
    $scheme = 'http';
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        $scheme = 'https';
    } elseif (strtolower((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '')) === 'https') {
        $scheme = 'https';
    }

    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

    $root    = str_replace('\\', '/', realpath(__DIR__ . '/..') ?: dirname(__DIR__));
    $docroot = str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT'] ?? '') ?: '');

    $path = '';
    if ($docroot !== '' && str_starts_with(strtolower($root), strtolower($docroot))) {
        $path = substr($root, strlen($docroot));
    }

    return $scheme . '://' . $host . rtrim($path, '/');
}

if (!defined('BASE_URL')) {
    define('BASE_URL', rtrim((string) env('APP_URL', db_detect_base_url()), '/'));
}

// Rutas locales de uploads (solo se usan con STORAGE_DRIVER=local o archivos antiguos)
define('UPLOAD_PLANTAS', __DIR__ . '/../uploads/plantas/');
define('UPLOAD_QRS',     __DIR__ . '/../uploads/qrs/');
define('UPLOAD_MATERIALES', __DIR__ . '/../repositorio/uploads/');

// Tamaño máximo de archivos (5 MB por defecto)
define('MAX_FILE_SIZE', (int) env('MAX_FILE_SIZE', (string) (5 * 1024 * 1024)));

/**
 * Obtener conexión PDO reutilizable.
 *
 * @return PDO
 */
function getConnection(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        try {
            $pdo = new PDO(db_dsn(), DB_USER, DB_PASS, db_options());
        } catch (PDOException $e) {
            error_log('Semillitas DB: ' . $e->getMessage());

            http_response_code(500);
            if (env_bool('APP_DEBUG', false)) {
                die('Error de conexión a la base de datos: ' . htmlspecialchars($e->getMessage()));
            }
            die('No se pudo conectar con la base de datos. Revisa la configuración del servidor.');
        }
    }

    return $pdo;
}
