<?php
/**
 * Catálogo de juegos educativos — Semillitas del Futuro
 *
 * Se usa para recomendar juegos en la ficha de cada planta
 * (la página que se abre al escanear el código QR).
 */

if (!function_exists('juegos_catalogo')) {
    function juegos_catalogo(): array
    {
        return [
            'partesPlanta' => [
                'titulo'      => 'Partes de la Planta',
                'descripcion' => 'Identifica la raíz, tallo, hojas, flor y fruto tocando cada parte botánica.',
                'icono'       => 'sprout',
                'cta'         => 'Identificar partes',
                'color'       => '#059669',
                'fondo'       => '#d1fae5',
            ],
            'memoria' => [
                'titulo'      => 'Memoria del Biohuerto',
                'descripcion' => 'Encuentra las parejas ocultas de hortalizas y frutas cultivadas en la escuela.',
                'icono'       => 'grid',
                'cta'         => 'Jugar memoria',
                'color'       => '#ea580c',
                'fondo'       => '#ffedd5',
            ],
            'adivina' => [
                'titulo'      => 'Adivina el Cultivo',
                'descripcion' => 'Sigue las 3 pistas misteriosas del huerto y deduce qué planta es.',
                'icono'       => 'search',
                'cta'         => 'Resolver misterio',
                'color'       => '#0284c7',
                'fondo'       => '#e0f2fe',
            ],
            'comestibles' => [
                'titulo'      => 'Partes Comestibles',
                'descripcion' => 'Clasifica qué parte comemos de cada hortaliza: hojas, raíces, frutos o bulbos.',
                'icono'       => 'tag',
                'cta'         => 'Clasificar alimentos',
                'color'       => '#7c3aed',
                'fondo'       => '#ede9fe',
            ],
            'gotita' => [
                'titulo'      => '¡Ayuda a Gotita!',
                'descripcion' => 'Misiones ecológicas de riego responsable y ahorro del agua escolar.',
                'icono'       => 'droplet',
                'cta'         => 'Salvar a Gotita',
                'color'       => '#0284c7',
                'fondo'       => '#e0f2fe',
            ],
            'sopa' => [
                'titulo'      => 'Sopa de Letras Verde',
                'descripcion' => 'Encuentra palabras de agronomía: clorofila, compost, semilla, riego y abono.',
                'icono'       => 'search',
                'cta'         => 'Buscar palabras',
                'color'       => '#047857',
                'fondo'       => '#d1fae5',
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
