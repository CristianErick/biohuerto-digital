<?php
/**
 * Generación de códigos QR — Semillitas del Futuro
 *
 * Intenta primero con phpqrcode (si está instalado) y, si no existe,
 * usa un servicio externo de generación de QR. El resultado se guarda como
 * bytes en la base de datos mediante libs/storage.php.
 */

if (!function_exists('qr_generar_png')) {
    /**
     * @return array{ok:bool, datos?:string, error?:string}
     */
    function qr_generar_png(string $texto): array
    {
        $texto = trim($texto);
        if ($texto === '') {
            return ['ok' => false, 'error' => 'El texto del QR está vacío.'];
        }

        // ── Opción 1: phpqrcode local ──────────────────────
        $base = @tempnam(sys_get_temp_dir(), 'sem_qr_');
        if ($base !== false) {
            $tmp = $base . '.png';
            @unlink($base);

            $lib = __DIR__ . '/phpqrcode/qrlib.php';
            if (is_file($lib)) {
                require_once $lib;
                if (class_exists('QRcode')) {
                    @QRcode::png($texto, $tmp, QR_ECLEVEL_M, 10, 2);
                    if (is_file($tmp) && filesize($tmp) > 100) {
                        $datos = file_get_contents($tmp);
                        @unlink($tmp);
                        if ($datos !== false) {
                            return ['ok' => true, 'datos' => $datos];
                        }
                    }
                }
            }
            @unlink($tmp);
        }

        // ── Opción 2: QuickChart (sin API key) ─────────────
        $datos = qr_descargar(
            'https://quickchart.io/qr?' . http_build_query([
                'text'    => $texto,
                'size'    => 320,
                'margin'  => 2,
                'ecLevel' => 'M',
                'format'  => 'png',
            ])
        );

        if ($datos !== null && str_starts_with($datos, "\x89PNG")) {
            return ['ok' => true, 'datos' => $datos];
        }

        // ── Opción 3: Google Charts (compatibilidad) ───────
        $datos = qr_descargar(
            'https://chart.googleapis.com/chart?' . http_build_query([
                'cht'  => 'qr',
                'chs'  => '320x320',
                'chl'  => $texto,
                'choe' => 'UTF-8',
            ])
        );

        if ($datos !== null && str_starts_with($datos, "\x89PNG")) {
            return ['ok' => true, 'datos' => $datos];
        }

        return ['ok' => false, 'error' => 'No se pudo generar el código QR. Verifica la conexión a internet.'];
    }
}

if (!function_exists('qr_descargar')) {
    function qr_descargar(string $url): ?string
    {
        $context = stream_context_create([
            'http' => [
                'timeout'    => 12,
                'user_agent' => 'Semillitas/1.0',
            ],
            'ssl' => [
                'verify_peer'      => true,
                'verify_peer_name' => true,
            ],
        ]);

        $datos = @file_get_contents($url, false, $context);

        return $datos === false ? null : $datos;
    }
}
