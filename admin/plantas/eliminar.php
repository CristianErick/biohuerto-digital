<?php
/**
 * Eliminar Planta — Panel Admin
 * Semillitas del Futuro
 */
$rolRequerido = 1;
require_once __DIR__ . '/../../admin/includes/auth_check.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../libs/storage.php';

$id = intval($_GET['id'] ?? 0);

if ($id <= 0) {
    $_SESSION['planta_msg'] = 'ID de planta no válido.';
    $_SESSION['planta_msg_type'] = 'error';
    header('Location: index.php');
    exit;
}

try {
    $pdo = getConnection();

    // Obtener rutas de archivos antes de eliminar
    $stmt = $pdo->prepare('SELECT nombre, ruta_foto, ruta_qr FROM plantas WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $planta = $stmt->fetch();

    if (!$planta) {
        $_SESSION['planta_msg'] = 'La planta no existe.';
        $_SESSION['planta_msg_type'] = 'error';
        header('Location: index.php');
        exit;
    }

    // Eliminar archivos (de la BD o del disco, según corresponda)
    storage_eliminar($planta['ruta_foto'] ?? null);
    storage_eliminar($planta['ruta_qr'] ?? null);

    // Eliminar registro de la BD
    $stmt = $pdo->prepare('DELETE FROM plantas WHERE id = :id');
    $stmt->execute([':id' => $id]);

    $_SESSION['planta_msg'] = 'Planta "' . $planta['nombre'] . '" eliminada correctamente.';
    $_SESSION['planta_msg_type'] = 'success';

} catch (PDOException $e) {
    $_SESSION['planta_msg'] = 'Error al eliminar la planta.';
    $_SESSION['planta_msg_type'] = 'error';
}

header('Location: index.php');
exit;
