<?php
/**
 * Eliminar Recurso de la Biblioteca — Semillitas del Futuro
 * Solo Administradores y Docentes pueden eliminar recursos de la biblioteca.
 * Docentes solo pueden eliminar los recursos que ellos agregaron.
 */
session_start();

if (!isset($_SESSION['usuario_id']) || !in_array((int)$_SESSION['rol_id'], [1, 2])) {
    $_SESSION['login_error'] = 'Solo Administradores y Docentes pueden eliminar recursos de la Biblioteca.';
    header('Location: ../auth/login.php');
    exit;
}

$id = intval($_GET['id'] ?? 0);
$usuarioId = $_SESSION['usuario_id'];
$usuarioNombre = $_SESSION['usuario_nombre'] ?? 'Docente';
$isAdmin = ($_SESSION['rol_id'] == 1);

require_once __DIR__ . '/../libs/biblioteca_store.php';

$recursos = biblioteca_recursos_agregados();

// Buscar el recurso
$recursoIndex = -1;
$recurso = null;
$origen = 'json'; // Los recursos del JSON siempre son eliminables

foreach ($recursos as $i => $r) {
    if (isset($r['id']) && $r['id'] == $id) {
        $recursoIndex = $i;
        $recurso = $r;
        break;
    }
}

if (!$recurso) {
    $_SESSION['biblio_msg'] = 'El recurso no existe o no es eliminable.';
    $_SESSION['biblio_msg_type'] = 'error';
    header('Location: ./index.php');
    exit;
}

// Verificar permisos: admin puede eliminar todo, docente solo sus propios recursos
if (!$isAdmin && ($recurso['creado_por'] ?? '') != $usuarioNombre) {
    $_SESSION['biblio_msg'] = 'No tienes permiso para eliminar este recurso.';
    $_SESSION['biblio_msg_type'] = 'error';
    header('Location: ./index.php');
    exit;
}

// Eliminar el recurso (solo recursos del JSON se pueden eliminar, no los estáticos del sistema)
if ($origen === 'json') {
    array_splice($recursos, $recursoIndex, 1);

    if (biblioteca_guardar_agregados($recursos)) {
        $_SESSION['biblio_msg'] = 'Recurso eliminado correctamente.';
        $_SESSION['biblio_msg_type'] = 'success';
    } else {
        $_SESSION['biblio_msg'] = 'Error al eliminar el recurso.';
        $_SESSION['biblio_msg_type'] = 'error';
    }
} else {
    $_SESSION['biblio_msg'] = 'No se pueden eliminar recursos del sistema.';
    $_SESSION['biblio_msg_type'] = 'error';
}

header('Location: ./index.php');
exit;
