<?php
/**
 * ============================================================
 * Instalador de phpqrcode — Semillitas del Futuro
 * ============================================================
 *
 * Diseñado para entornos sin la extensión ZipArchive (como Laragon por defecto).
 * Descarga directamente la versión monolítica pura de phpqrcode o
 * descomprime utilizando herramientas del sistema como fallback.
 * ============================================================
 */

$destDir = __DIR__;
$isCLI = (php_sapi_name() === 'cli');

function output($msg, $isCLI) {
    if ($isCLI) {
        echo strip_tags($msg) . PHP_EOL;
    } else {
        echo "<p style='margin:8px 0;'>{$msg}</p>";
    }
}

if (!$isCLI) {
    echo '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8">';
    echo '<title>Instalar phpqrcode</title>';
    echo '<style>body{font-family:sans-serif;max-width:720px;margin:40px auto;padding:24px;line-height:1.6;background:#f9f9f9;} ';
    echo '.box{background:#fff;padding:24px;border-radius:12px;box-shadow:0 2px 10px rgba(0,0,0,0.08);} ';
    echo '.ok{color:#2E7D32;} .err{color:#C62828;} .warn{color:#E65100;} ';
    echo 'code{background:#f0f0f0;padding:3px 6px;border-radius:4px;font-size:0.9em;}</style>';
    echo '</head><body><div class="box">';
    echo '<h1>Instalador de phpqrcode</h1>';
}

// 1. Verificar si ya está instalada
if (file_exists($destDir . '/qrlib.php') && (file_exists($destDir . '/phpqrcode.php') || file_exists($destDir . '/qrconst.php'))) {
    output('<span class="ok"><strong>¡phpqrcode ya está instalada correctamente!</strong></span>', $isCLI);
    output('Ubicación: <code>' . $destDir . '/qrlib.php</code>', $isCLI);
    output('<br><a href="../../admin/plantas/crear.php" style="display:inline-block;padding:10px 18px;background:#059669;color:#fff;text-decoration:none;border-radius:8px;font-weight:bold;">Ir a Registrar Planta &rarr;</a>', $isCLI);
    if (!$isCLI) echo '</div></body></html>';
    exit;
}

$context = stream_context_create([
    'http' => [
        'timeout'    => 20,
        'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Semillitas-Installer',
    ],
    'ssl' => [
        'verify_peer'      => false,
        'verify_peer_name' => false,
    ],
]);

// 2. Método Principal: Descargar directamente phpqrcode.php (Versión integrada, NO necesita ZIP)
output('Intentando descarga directa del script integrado (sin requerir ZipArchive)...', $isCLI);

$directUrls = [
    'https://raw.githubusercontent.com/t0k4rt/phpqrcode/master/phpqrcode.php',
    'https://raw.githubusercontent.com/lasalesi/phpqrcode/master/phpqrcode.php'
];

$instalado = false;

foreach ($directUrls as $url) {
    output('Probando fuente: <code>' . htmlspecialchars($url) . '</code>', $isCLI);
    $code = @file_get_contents($url, false, $context);
    
    if ($code !== false && strpos($code, 'class QRcode') !== false) {
        file_put_contents($destDir . '/phpqrcode.php', $code);
        
        $wrapper = "<?php\n// Wrapper de compatibilidad para phpqrcode\nrequire_once __DIR__ . '/phpqrcode.php';\n";
        file_put_contents($destDir . '/qrlib.php', $wrapper);
        
        $instalado = true;
        output('<span class="ok"><strong>¡Descarga e instalación directa exitosa!</strong> (No requirió ZIP)</span>', $isCLI);
        break;
    }
}

// 3. Fallback: Si ya existe un ZIP descargado o se usa ZIP sin clase ZipArchive
if (!$instalado) {
    $zipPath = $destDir . '/phpqrcode_temp.zip';
    
    if (!file_exists($zipPath)) {
        output('Descargando paquete ZIP como alternativa...', $isCLI);
        $zipUrl = 'https://github.com/t0k4rt/phpqrcode/archive/refs/heads/master.zip';
        $zipData = @file_get_contents($zipUrl, false, $context);
        if ($zipData !== false) {
            file_put_contents($zipPath, $zipData);
        }
    }
    
    if (file_exists($zipPath)) {
        output('Descomprimiendo archivo ZIP...', $isCLI);
        
        // Intento A: Si la clase ZipArchive existe en PHP
        if (class_exists('ZipArchive')) {
            $zip = new ZipArchive();
            if ($zip->open($zipPath) === true) {
                $tempDir = $destDir . '/temp_extract';
                $zip->extractTo($tempDir);
                $zip->close();
                
                $extracted = glob($tempDir . '/phpqrcode-*')[0] ?? $tempDir;
                foreach (glob($extracted . '/*.php') as $f) {
                    copy($f, $destDir . '/' . basename($f));
                }
                
                array_map('unlink', glob($extracted . '/*'));
                @rmdir($extracted);
                @rmdir($tempDir);
                $instalado = file_exists($destDir . '/qrlib.php');
            }
        } 
        
        // Intento B: Si ZipArchive no está activo, usar PowerShell de Windows
        if (!$instalado && strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            output('<code>ZipArchive</code> no está activo en php.ini. Utilizando descompresor nativo de Windows (PowerShell)...', $isCLI);
            $tempDir = $destDir . '\temp_ps';
            $psCmd = 'powershell -NoProfile -ExecutionPolicy Bypass -Command "Expand-Archive -Path \'' . $zipPath . '\' -DestinationPath \'' . $tempDir . '\' -Force"';
            @shell_exec($psCmd);
            
            $folders = glob($tempDir . '/phpqrcode-*');
            $extracted = !empty($folders) ? $folders[0] : $tempDir;
            
            foreach (glob($extracted . '/*.php') as $f) {
                copy($f, $destDir . '/' . basename($f));
            }
            
            array_map('unlink', glob($extracted . '/*'));
            @rmdir($extracted);
            @rmdir($tempDir);
            
            $instalado = file_exists($destDir . '/qrlib.php');
        }
        
        @unlink($zipPath);
    }
}

// 4. Resultado final
if ($instalado && file_exists($destDir . '/qrlib.php')) {
    output('<br><span class="ok" style="font-size:1.2em;"><strong>¡phpqrcode quedó 100% instalada y lista!</strong></span>', $isCLI);
    output('Archivo principal verificado: <code>' . $destDir . '/qrlib.php</code>', $isCLI);
    output('Ahora el módulo del biohuerto generará los códigos QR automáticamente en formato PNG.', $isCLI);
    output('<br><a href="../../admin/plantas/crear.php" style="display:inline-block;padding:12px 22px;background:#059669;color:#fff;text-decoration:none;border-radius:8px;font-weight:bold;">Ir a Registrar Planta &rarr;</a>', $isCLI);
} else {
    output('<br><span class="err">No se pudo completar la instalación automática.</span>', $isCLI);
    output('<strong>Para habilitar la extensión en Laragon en 10 segundos:</strong>', $isCLI);
    output('1. Clic derecho en el icono de Laragon (o botón Menú).', $isCLI);
    output('2. Selecciona <strong>PHP</strong> &rarr; <strong>Extensiones</strong>.', $isCLI);
    output('3. Haz clic en <strong>zip</strong> para marcarla con un check.', $isCLI);
    output('4. Recarga esta página.', $isCLI);
}

if (!$isCLI) echo '</div></body></html>';
