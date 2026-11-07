<?php
/**
 * Persistencia de la Biblioteca Pedagógica — Semillitas del Futuro
 *
 * Antes se guardaba en biblioteca/biblioteca_recursos.json. Ahora se usa la
 * tabla app_data, con importación automática del JSON antiguo la primera vez.
 */

require_once __DIR__ . '/appdata.php';

if (!function_exists('biblioteca_recursos_agregados')) {
    function biblioteca_recursos_agregados(): array
    {
        $recursos = appdata_get('biblioteca_recursos', []);

        if ($recursos === []) {
            $legacy = dirname(__DIR__) . '/biblioteca/biblioteca_recursos.json';
            if (is_file($legacy)) {
                $contenido = @file_get_contents($legacy);
                $datos = $contenido !== false ? json_decode($contenido, true) : null;
                if (is_array($datos) && $datos !== []) {
                    $datos = array_values(array_filter($datos, 'is_array'));
                    appdata_set('biblioteca_recursos', $datos);
                    return $datos;
                }
            }
        }

        return array_values(array_filter($recursos, 'is_array'));
    }
}

if (!function_exists('biblioteca_guardar_agregados')) {
    function biblioteca_guardar_agregados(array $recursos): bool
    {
        return appdata_set('biblioteca_recursos', array_values(array_filter($recursos, 'is_array')));
    }
}
