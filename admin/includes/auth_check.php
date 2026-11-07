<?php
/**
 * Auth Check — Verificación de sesión para el panel de administración.
 * Incluir al inicio de cada página protegida:
 *   require_once __DIR__ . '/../includes/auth_check.php';
 *
 * Variables disponibles después de incluir:
 *   $usuarioId, $usuarioNombre, $rolId, $rolNombre
 *
 * Parámetro opcional (definir ANTES de incluir):
 *   $rolRequerido — ID del rol mínimo requerido (1 = Admin, 2 = Docente)
 */
session_start();

// ¿Hay sesión activa?
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ' . getBasePath() . 'auth/login.php');
    exit;
}

// ¿Se requiere un rol específico?
$rolRequerido = $rolRequerido ?? null;
if ($rolRequerido !== null && $_SESSION['rol_id'] != $rolRequerido) {
    http_response_code(403);
    die('
    <!DOCTYPE html>
    <html lang="es"><head><meta charset="UTF-8"><title>Acceso Denegado</title></head>
    <body style="font-family:sans-serif;text-align:center;padding:80px;background:#f8fafc;color:#0f172a;">
      <div style="max-width:440px;margin:0 auto;background:#fff;padding:32px;border-radius:16px;border:1px solid #e2e8f0;box-shadow:0 4px 14px rgba(0,0,0,0.06);">
        <h2 style="color:#e11d48;margin-bottom:8px;">Acceso Denegado</h2>
        <p style="color:#475569;margin-bottom:20px;">No cuentas con los permisos requeridos para acceder a esta sección.</p>
        <a href="' . getBasePath() . 'index.php" style="display:inline-block;padding:10px 18px;background:#059669;color:#fff;text-decoration:none;border-radius:8px;font-weight:bold;">Volver al inicio</a>
      </div>
    </body></html>
    ');
}

// Variables de sesión accesibles
$usuarioId     = $_SESSION['usuario_id'];
$usuarioNombre = $_SESSION['usuario_nombre'];
$usuarioEmail  = $_SESSION['usuario_email'] ?? '';
$rolId         = $_SESSION['rol_id'];
$rolNombre     = $_SESSION['rol_nombre'];

/**
 * Calcula la ruta base relativa al archivo que llamó este script.
 */
function getBasePath(): string
{
    $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
    $depth = substr_count(str_replace('\\', '/', $scriptDir), '/') - 1;
    // /semillitas = depth 1, /semillitas/admin = depth 2, /semillitas/admin/plantas = depth 3
    return str_repeat('../', max(0, $depth));
}
