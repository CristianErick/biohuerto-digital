<?php
/**
 * Procesar Login — Semillitas del Futuro
 * Valida credenciales y crea la sesión.
 */
session_start();
require_once __DIR__ . '/../config/database.php';

// Solo aceptar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// Validar campos vacíos
if (empty($email) || empty($password)) {
    $_SESSION['login_error'] = 'Por favor, completa todos los campos.';
    header('Location: login.php');
    exit;
}

try {
    $pdo = getConnection();

    // Buscar usuario activo por email
    $stmt = $pdo->prepare('
        SELECT u.id, u.rol_id, u.nombre, u.apellido, u.email, u.password, u.avatar, r.nombre AS rol_nombre
        FROM usuarios u
        INNER JOIN roles r ON u.rol_id = r.id
        WHERE u.email = :email AND u.activo = 1
        LIMIT 1
    ');
    $stmt->execute([':email' => $email]);
    $usuario = $stmt->fetch();

    // Verificar usuario y contraseña
    if (!$usuario || !password_verify($password, $usuario['password'])) {
        $_SESSION['login_error'] = 'Correo o contraseña incorrectos.';
        header('Location: login.php?email=' . urlencode($email));
        exit;
    }

    // Crear sesión
    session_regenerate_id(true);
    $_SESSION['usuario_id']     = $usuario['id'];
    $_SESSION['usuario_nombre'] = $usuario['nombre'] . ' ' . $usuario['apellido'];
    $_SESSION['usuario_email']  = $usuario['email'];
    $_SESSION['usuario_avatar'] = $usuario['avatar'];
    $_SESSION['rol_id']         = $usuario['rol_id'];
    $_SESSION['rol_nombre']     = $usuario['rol_nombre'];

    // Redirigir según rol
    if ($usuario['rol_id'] == 1) {
        header('Location: ../admin/index.php');
    } else {
        header('Location: ../repositorio/mis_materiales.php');
    }
    exit;

} catch (PDOException $e) {
    $_SESSION['login_error'] = 'Error del servidor. Intenta más tarde.';
    header('Location: login.php');
    exit;
}
