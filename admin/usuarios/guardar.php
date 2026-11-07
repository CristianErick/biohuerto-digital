<?php
/**
 * Guardar Nuevo Usuario — Panel Admin
 */
$rolRequerido = 1;
require_once __DIR__ . '/../../admin/includes/auth_check.php';
require_once __DIR__ . '/../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: crear.php');
    exit;
}

$nombre       = trim($_POST['nombre'] ?? '');
$apellido     = trim($_POST['apellido'] ?? '');
$email        = trim($_POST['email'] ?? '');
$password     = $_POST['password'] ?? '';
$rol_id       = intval($_POST['rol_id'] ?? 2);
$especialidad = trim($_POST['especialidad'] ?? '');
$activo       = isset($_POST['activo']) ? 1 : 0;

// Guardar datos para repoblar formulario
$_SESSION['usuario_old'] = compact('nombre', 'apellido', 'email', 'rol_id', 'especialidad');

// Validaciones
$errores = [];
if (empty($nombre))    $errores[] = 'El nombre es obligatorio.';
if (empty($apellido))  $errores[] = 'El apellido es obligatorio.';
if (empty($email))     $errores[] = 'El correo es obligatorio.';
if (strlen($password) < 6) $errores[] = 'La contraseña debe tener al menos 6 caracteres.';

if (empty($errores)) {
    $pdo = getConnection();

    // Verificar email duplicado
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM usuarios WHERE email = :email');
    $stmt->execute([':email' => $email]);
    if ($stmt->fetchColumn() > 0) {
        $errores[] = 'Ya existe un usuario con ese correo electrónico.';
    }
}

if (!empty($errores)) {
    $_SESSION['usuario_msg'] = implode(' ', $errores);
    $_SESSION['usuario_msg_type'] = 'error';
    header('Location: crear.php');
    exit;
}

try {
    $hash = password_hash($password, PASSWORD_BCRYPT);

    $stmt = $pdo->prepare('
        INSERT INTO usuarios (rol_id, nombre, apellido, email, password, especialidad, activo)
        VALUES (:rol_id, :nombre, :apellido, :email, :password, :especialidad, :activo)
    ');
    $stmt->execute([
        ':rol_id'       => $rol_id,
        ':nombre'       => $nombre,
        ':apellido'     => $apellido,
        ':email'        => $email,
        ':password'     => $hash,
        ':especialidad' => $especialidad ?: null,
        ':activo'       => $activo,
    ]);

    unset($_SESSION['usuario_old']);
    $_SESSION['usuario_msg'] = 'Usuario "' . $nombre . ' ' . $apellido . '" creado exitosamente.';
    $_SESSION['usuario_msg_type'] = 'success';
    header('Location: index.php');
    exit;

} catch (PDOException $e) {
    $_SESSION['usuario_msg'] = 'Error al guardar el usuario en la base de datos.';
    $_SESSION['usuario_msg_type'] = 'error';
    header('Location: crear.php');
    exit;
}
