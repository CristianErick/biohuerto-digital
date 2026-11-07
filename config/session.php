<?php
/**
 * Sesiones compartidas en base de datos — Semillitas del Futuro
 *
 * En Vercel (contenedores serverless) el sistema de archivos no es duradero ni
 * compartido entre instancias. Guardar las sesiones en MySQL evita que los
 * usuarios pierdan la sesión al cambiar de instancia.
 *
 * Activar con: SESSION_DRIVER=database
 */

require_once __DIR__ . '/env.php';
require_once __DIR__ . '/database.php';

if (!class_exists('SemillitasDbSessionHandler')) {
    class SemillitasDbSessionHandler implements SessionHandlerInterface
    {
        private ?PDO $pdo = null;
        private int $ttl;

        public function __construct(int $ttl = 1440)
        {
            $this->ttl = max(60, $ttl);
        }

        private function pdo(): PDO
        {
            if ($this->pdo === null) {
                $this->pdo = new PDO(db_dsn(), DB_USER, DB_PASS, db_options());
            }

            return $this->pdo;
        }

        public function open(string $path, string $name): bool
        {
            return true;
        }

        public function close(): bool
        {
            return true;
        }

        public function read(string $id): string|false
        {
            try {
                $stmt = $this->pdo()->prepare(
                    'SELECT data FROM sessions WHERE id = :id AND last_activity >= :minimo LIMIT 1'
                );
                $stmt->execute([
                    ':id'     => $id,
                    ':minimo' => time() - $this->ttl,
                ]);
                $row = $stmt->fetch();

                return $row ? (string) $row['data'] : '';
            } catch (Throwable $e) {
                error_log('Semillitas session read: ' . $e->getMessage());
                return '';
            }
        }

        public function write(string $id, string $data): bool
        {
            try {
                $stmt = $this->pdo()->prepare(
                    'INSERT INTO sessions (id, data, last_activity)
                     VALUES (:id, :data, :actividad)
                     ON DUPLICATE KEY UPDATE data = VALUES(data), last_activity = VALUES(last_activity)'
                );

                return $stmt->execute([
                    ':id'        => $id,
                    ':data'      => $data,
                    ':actividad' => time(),
                ]);
            } catch (Throwable $e) {
                error_log('Semillitas session write: ' . $e->getMessage());
                return false;
            }
        }

        public function destroy(string $id): bool
        {
            try {
                $stmt = $this->pdo()->prepare('DELETE FROM sessions WHERE id = :id');
                return $stmt->execute([':id' => $id]);
            } catch (Throwable $e) {
                error_log('Semillitas session destroy: ' . $e->getMessage());
                return false;
            }
        }

        public function gc(int $max_lifetime): int|false
        {
            try {
                $stmt = $this->pdo()->prepare('DELETE FROM sessions WHERE last_activity < :minimo');
                $stmt->execute([':minimo' => time() - max($max_lifetime, $this->ttl)]);
                return $stmt->rowCount();
            } catch (Throwable $e) {
                error_log('Semillitas session gc: ' . $e->getMessage());
                return false;
            }
        }
    }
}

if (!function_exists('semillitas_configurar_sesion')) {
    /**
     * Configura cookies y, opcionalmente, el manejador de sesiones en base de datos.
     * Debe llamarse antes del primer session_start().
     */
    function semillitas_configurar_sesion(): void
    {
        if (PHP_SAPI === 'cli' || session_status() !== PHP_SESSION_NONE) {
            return;
        }

        $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (strtolower((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '')) === 'https');

        $secure = env_bool('SESSION_SECURE_COOKIE', $https);

        if (PHP_VERSION_ID >= 70300) {
            session_set_cookie_params([
                'lifetime' => 0,
                'path'     => '/',
                'domain'   => '',
                'secure'   => $secure,
                'httponly' => true,
                'samesite' => (string) env('SESSION_SAMESITE', 'Lax'),
            ]);
        } else {
            session_set_cookie_params(0, '/; samesite=' . env('SESSION_SAMESITE', 'Lax'), '', $secure, true);
        }

        if (strtolower((string) env('SESSION_DRIVER', 'files')) === 'database') {
            $ttl = (int) env('SESSION_LIFETIME', '1440');
            session_set_save_handler(new SemillitasDbSessionHandler($ttl), true);
        }
    }
}
