<?php
/**
 * Editar Usuario — Panel Admin
 * Sin Emojis · Iconos SVG
 */
$rolRequerido = 1;
require_once __DIR__ . '/../../admin/includes/auth_check.php';
require_once __DIR__ . '/../../config/database.php';

$pdo = getConnection();
$id  = intval($_GET['id'] ?? $_POST['id'] ?? 0);

if ($id <= 0) {
    $_SESSION['usuario_msg'] = 'ID de usuario no válido.';
    $_SESSION['usuario_msg_type'] = 'error';
    header('Location: index.php');
    exit;
}

$roles = $pdo->query('SELECT * FROM roles ORDER BY id')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre       = trim($_POST['nombre'] ?? '');
    $apellido     = trim($_POST['apellido'] ?? '');
    $email        = trim($_POST['email'] ?? '');
    $password     = $_POST['password'] ?? '';
    $rol_id       = intval($_POST['rol_id'] ?? 2);
    $especialidad = trim($_POST['especialidad'] ?? '');
    $activo       = isset($_POST['activo']) ? 1 : 0;

    $errores = [];
    if (empty($nombre))   $errores[] = 'El nombre es obligatorio.';
    if (empty($apellido)) $errores[] = 'El apellido es obligatorio.';
    if (empty($email))    $errores[] = 'El correo es obligatorio.';
    if (!empty($password) && strlen($password) < 6) {
        $errores[] = 'La contraseña debe tener al menos 6 caracteres.';
    }

    if (empty($errores)) {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM usuarios WHERE email = :email AND id != :id');
        $stmt->execute([':email' => $email, ':id' => $id]);
        if ($stmt->fetchColumn() > 0) {
            $errores[] = 'Ya existe otro usuario con ese correo.';
        }
    }

    if (!empty($errores)) {
        $_SESSION['usuario_msg'] = implode(' ', $errores);
        $_SESSION['usuario_msg_type'] = 'error';
    } else {
        try {
            if (!empty($password)) {
                $stmt = $pdo->prepare('
                    UPDATE usuarios SET rol_id=:rol_id, nombre=:nombre, apellido=:apellido,
                    email=:email, password=:password, especialidad=:especialidad, activo=:activo
                    WHERE id=:id
                ');
                $params = [
                    ':rol_id' => $rol_id, ':nombre' => $nombre, ':apellido' => $apellido,
                    ':email' => $email, ':password' => password_hash($password, PASSWORD_BCRYPT),
                    ':especialidad' => $especialidad ?: null, ':activo' => $activo, ':id' => $id,
                ];
            } else {
                $stmt = $pdo->prepare('
                    UPDATE usuarios SET rol_id=:rol_id, nombre=:nombre, apellido=:apellido,
                    email=:email, especialidad=:especialidad, activo=:activo
                    WHERE id=:id
                ');
                $params = [
                    ':rol_id' => $rol_id, ':nombre' => $nombre, ':apellido' => $apellido,
                    ':email' => $email, ':especialidad' => $especialidad ?: null,
                    ':activo' => $activo, ':id' => $id,
                ];
            }
            $stmt->execute($params);

            $_SESSION['usuario_msg'] = 'Usuario actualizado correctamente.';
            $_SESSION['usuario_msg_type'] = 'success';
            header('Location: index.php');
            exit;

        } catch (PDOException $e) {
            $_SESSION['usuario_msg'] = 'Error al actualizar el usuario.';
            $_SESSION['usuario_msg_type'] = 'error';
        }
    }
}

$stmt = $pdo->prepare('SELECT * FROM usuarios WHERE id = :id');
$stmt->execute([':id' => $id]);
$usuario = $stmt->fetch();

if (!$usuario) {
    $_SESSION['usuario_msg'] = 'El usuario no existe.';
    $_SESSION['usuario_msg_type'] = 'error';
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = array_merge($usuario, [
        'nombre' => $nombre, 'apellido' => $apellido, 'email' => $email,
        'rol_id' => $rol_id, 'especialidad' => $especialidad, 'activo' => $activo,
    ]);
}

$msg     = $_SESSION['usuario_msg'] ?? null;
$msgType = $_SESSION['usuario_msg_type'] ?? 'error';
unset($_SESSION['usuario_msg'], $_SESSION['usuario_msg_type']);

$adminPageTitle = 'Editar Usuario — Admin';
include __DIR__ . '/../../admin/includes/header.php';
?>

<div class="admin-page-header">
  <h2>Editar Usuario #<?= $id ?>: <?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']) ?></h2>
  <a href="index.php" class="btn btn--outline btn--sm">
    <?= icon('arrow-left', '', 14) ?> Volver al listado
  </a>
</div>

<?php if ($msg): ?>
  <div class="alert-banner alert-banner--<?= $msgType === 'success' ? 'success' : 'danger' ?>">
    <span class="alert-banner__icon"><?= icon('alert-circle', '', 18) ?></span>
    <div class="alert-banner__content"><?= htmlspecialchars($msg) ?></div>
  </div>
<?php endif; ?>

<div class="admin-card">
  <form action="editar.php" method="POST" class="admin-form">
    <input type="hidden" name="id" value="<?= $id ?>">

    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(240px, 1fr)); gap:16px;">
      <div class="form-group">
        <label for="nombre">Nombre <span style="color:var(--color-rose);">*</span></label>
        <input type="text" id="nombre" name="nombre" class="form-control" required
               maxlength="100" value="<?= htmlspecialchars($usuario['nombre']) ?>">
      </div>
      <div class="form-group">
        <label for="apellido">Apellido <span style="color:var(--color-rose);">*</span></label>
        <input type="text" id="apellido" name="apellido" class="form-control" required
               maxlength="100" value="<?= htmlspecialchars($usuario['apellido']) ?>">
      </div>
    </div>

    <div class="form-group">
      <label for="email">Correo Institucional <span style="color:var(--color-rose);">*</span></label>
      <input type="email" id="email" name="email" class="form-control" required
             maxlength="150" value="<?= htmlspecialchars($usuario['email']) ?>">
    </div>

    <div class="form-group">
      <label for="password">Nueva Contraseña</label>
      <input type="password" id="password" name="password" class="form-control"
             minlength="6" placeholder="Dejar vacío para conservar la contraseña actual">
      <small style="color:var(--color-text-muted); display:block; margin-top:4px;">
        Solo completa este campo si deseas cambiar la contraseña del usuario.
      </small>
    </div>

    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(240px, 1fr)); gap:16px;">
      <div class="form-group">
        <label for="rol_id">Rol Asignado <span style="color:var(--color-rose);">*</span></label>
        <select id="rol_id" name="rol_id" class="form-control" required>
          <?php foreach ($roles as $rol): ?>
            <option value="<?= $rol['id'] ?>"
              <?= ($usuario['rol_id'] == $rol['id']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($rol['nombre']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label for="especialidad">Especialidad o Área Curricular</label>
        <input type="text" id="especialidad" name="especialidad" class="form-control"
               maxlength="100" value="<?= htmlspecialchars($usuario['especialidad'] ?? '') ?>">
      </div>
    </div>

    <div class="form-group" style="margin-top:10px;">
      <label style="cursor:pointer; display:flex; align-items:center; gap:8px;">
        <input type="checkbox" name="activo" value="1"
          <?= $usuario['activo'] ? 'checked' : '' ?>>
        <span>Usuario activo en la plataforma</span>
      </label>
    </div>

    <div style="display:flex; gap:12px; margin-top:20px; flex-wrap:wrap;">
      <button type="submit" class="btn btn--primary" style="min-width:180px;">
        <?= icon('check', '', 16) ?> Guardar Cambios
      </button>
      <a href="index.php" class="btn btn--outline">Cancelar</a>
    </div>
  </form>
</div>

<?php include __DIR__ . '/../../admin/includes/footer.php'; ?>
