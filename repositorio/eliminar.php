<?php
/**
 * Eliminar Material Educativo — Semillitas del Futuro
 */
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../libs/storage.php';
$pdo = getConnection();

$id = intval($_GET['id'] ?? 0);
$usuarioId = $_SESSION['usuario_id'];
$isAdmin = ($_SESSION['rol_id'] == 1);

if ($id <= 0) {
    $_SESSION['repo_msg'] = 'ID de material no válido.';
    $_SESSION['repo_msg_type'] = 'error';
    header('Location: mis_materiales.php');
    exit;
}

try {
    // Buscar material
    $stmt = $pdo->prepare('SELECT * FROM materiales WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $material = $stmt->fetch();

    if (!$material) {
        $_SESSION['repo_msg'] = 'El material no existe o ya fue eliminado.';
        $_SESSION['repo_msg_type'] = 'error';
        header('Location: mis_materiales.php');
        exit;
    }

    // Comprobar permisos
    if (!$isAdmin && $material['usuario_id'] != $usuarioId) {
        http_response_code(403);
        die('No tienes permiso para eliminar este material.');
    }

    // Eliminar archivo (de la BD o del disco, según corresponda)
    storage_eliminar($material['ruta_archivo'] ?? null);

    // Eliminar registro
    $delStmt = $pdo->prepare('DELETE FROM materiales WHERE id = :id');
    $delStmt->execute([':id' => $id]);

    $_SESSION['repo_msg'] = 'Material "' . $material['titulo'] . '" eliminado correctamente.';
    $_SESSION['repo_msg_type'] = 'success';

} catch (PDOException $e) {
    $_SESSION['repo_msg'] = 'Error al eliminar el material de la base de datos.';
    $_SESSION['repo_msg_type'] = 'error';
}

header('Location: mis_materiales.php');
exit;
