<?php
/**
 * Eliminar Usuario — Panel Admin
 */
$rolRequerido = 1;
require_once __DIR__ . '/../../admin/includes/auth_check.php';
require_once __DIR__ . '/../../config/database.php';

$id = intval($_GET['id'] ?? 0);

if ($id <= 0) {
    $_SESSION['usuario_msg'] = 'ID de usuario no válido.';
    $_SESSION['usuario_msg_type'] = 'error';
    header('Location: index.php');
    exit;
}

// No permitir eliminarse a sí mismo
if ($id == $usuarioId) {
    $_SESSION['usuario_msg'] = 'No puedes eliminar tu propia cuenta.';
    $_SESSION['usuario_msg_type'] = 'error';
    header('Location: index.php');
    exit;
}

try {
    $pdo = getConnection();

    $stmt = $pdo->prepare('SELECT nombre, apellido FROM usuarios WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $usuario = $stmt->fetch();

    if (!$usuario) {
        $_SESSION['usuario_msg'] = 'El usuario no existe.';
        $_SESSION['usuario_msg_type'] = 'error';
        header('Location: index.php');
        exit;
    }

    // Eliminar (CASCADE eliminará sus materiales también)
    $stmt = $pdo->prepare('DELETE FROM usuarios WHERE id = :id');
    $stmt->execute([':id' => $id]);

    $_SESSION['usuario_msg'] = 'Usuario "' . $usuario['nombre'] . ' ' . $usuario['apellido'] . '" eliminado correctamente.';
    $_SESSION['usuario_msg_type'] = 'success';

} catch (PDOException $e) {
    $_SESSION['usuario_msg'] = 'Error al eliminar el usuario.';
    $_SESSION['usuario_msg_type'] = 'error';
}

header('Location: index.php');
exit;
