<?php
/**
 * Guardar Material en Biblioteca — Semillitas del Futuro
 */
session_start();

if (!isset($_SESSION['usuario_id']) || !in_array((int)$_SESSION['rol_id'], [1, 2])) {
    $_SESSION['login_error'] = 'Acceso denegado.';
    header('Location: ../auth/login.php');
    exit;
}

require_once __DIR__ . '/../includes/icons.php';
require_once __DIR__ . '/../libs/biblioteca_store.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ./subir.php');
    exit;
}

$titulo    = trim($_POST['titulo'] ?? '');
$area      = trim($_POST['area'] ?? '');
$tipo      = trim($_POST['tipo'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');
$duracion  = trim($_POST['duracion'] ?? '');
$enlace    = trim($_POST['enlace'] ?? '');

if (empty($titulo) || empty($area) || empty($tipo)) {
    $_SESSION['biblio_msg'] = 'Por favor completa los campos obligatorios.';
    $_SESSION['biblio_msg_type'] = 'error';
    header('Location: biblioteca/subir.php');
    exit;
}

// Recursos agregados guardados en la base de datos
$recursos = biblioteca_recursos_agregados();

$nuevoId = empty($recursos) ? 1 : (max(array_column($recursos, 'id')) + 1);

$recurso = [
    'id'          => $nuevoId,
    'tipo'        => $tipo,
    'area'        => $area,
    'titulo'      => $titulo,
    'descripcion' => $descripcion,
    'duracion'    => $duracion ?: 'Recurso educativo',
    'enlace'      => $enlace ?: '#',
    'icono'       => $tipo === 'video' ? 'video' : ($tipo === 'imagen' ? 'image' : 'file-text'),
    'creado_por'  => $_SESSION['usuario_nombre'] ?? 'Docente',
    'fecha'       => date('d/m/Y')
];

$recursos[] = $recurso;

if (biblioteca_guardar_agregados($recursos)) {
    $_SESSION['biblio_msg'] = 'Recurso agregado a la Biblioteca correctamente.';
    $_SESSION['biblio_msg_type'] = 'success';
    header('Location: ./index.php');
} else {
    $_SESSION['biblio_msg'] = 'Error al guardar el recurso. Intenta nuevamente.';
    $_SESSION['biblio_msg_type'] = 'error';
    header('Location: ./subir.php');
}
exit;
