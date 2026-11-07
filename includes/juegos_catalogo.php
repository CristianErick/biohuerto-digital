<?php
/**
 * Catálogo de juegos educativos — Semillitas del Futuro
 *
 * Se usa para recomendar juegos aleatorios en la ficha de cada planta
 * (la página que se abre al escanear el código QR).
 */

if (!function_exists('juegos_catalogo')) {
    function juegos_catalogo(): array
    {
        return [
            'trivia' => [
                'titulo'      => 'Trivia del Biohuerto',
                'descripcion' => 'Responde preguntas sobre plantas, fotosíntesis, siembra y abonos mientras corre el tiempo.',
                'icono'       => 'check-circle',
                'cta'         => 'Jugar trivia',
                'color'       => '#b45309',
                'fondo'       => '#fef3c7',
            ],
            'sopa' => [
                'titulo'      => 'Sopa de Letras Verde',
                'descripcion' => 'Encuentra palabras de ecología y agricultura en una cuadrícula táctil.',
                'icono'       => 'search',
                'cta'         => 'Buscar palabras',
                'color'       => '#047857',
                'fondo'       => '#d1fae5',
            ],
            'recicla' => [
                'titulo'      => 'Clasifica y Recicla',
                'descripcion' => 'Arrastra o toca cada residuo y llévalo al contenedor correcto antes de que termine el tiempo.',
                'icono'       => 'leaf',
                'cta'         => 'Iniciar reto',
                'color'       => '#0369a1',
                'fondo'       => '#e0f2fe',
            ],
        ];
    }
}

if (!function_exists('juegos_recomendados')) {
    /**
     * Devuelve entre 1 y 3 juegos en orden aleatorio.
     *
     * @return array<string, array>
     */
    function juegos_recomendados(int $cantidad = 2): array
    {
        $catalogo = juegos_catalogo();
        $claves = array_keys($catalogo);
        shuffle($claves);

        $cantidad = max(1, min($cantidad, count($claves)));
        $claves = array_slice($claves, 0, $cantidad);

        $recomendados = [];
        foreach ($claves as $clave) {
            $recomendados[$clave] = $catalogo[$clave];
        }

        return $recomendados;
    }
}
