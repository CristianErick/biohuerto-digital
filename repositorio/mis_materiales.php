<?php
/**
 * Mis Materiales — Panel del Docente
 * Semillitas del Futuro · Sin Emojis · Iconos SVG
 */
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/icons.php';

$pdo = getConnection();

$usuarioId = $_SESSION['usuario_id'];
$usuarioNombre = $_SESSION['usuario_nombre'];
$isAdmin = ($_SESSION['rol_id'] == 1);

// Mensajes flash
$msg     = $_SESSION['repo_msg'] ?? null;
$msgType = $_SESSION['repo_msg_type'] ?? 'success';
unset($_SESSION['repo_msg'], $_SESSION['repo_msg_type']);

$verTodos = $isAdmin && isset($_GET['ver']) && $_GET['ver'] === 'todos';

if ($verTodos) {
    $stmt = $pdo->query('
        SELECT m.*, u.nombre AS autor_nombre, u.apellido AS autor_apellido
        FROM materiales m
        INNER JOIN usuarios u ON m.usuario_id = u.id
        ORDER BY m.created_at DESC
    ');
    $materiales = $stmt->fetchAll();
} else {
    $stmt = $pdo->prepare('
        SELECT m.*, u.nombre AS autor_nombre, u.apellido AS autor_apellido
        FROM materiales m
        INNER JOIN usuarios u ON m.usuario_id = u.id
        WHERE m.usuario_id = :uid
        ORDER BY m.created_at DESC
    ');
    $stmt->execute([':uid' => $usuarioId]);
    $materiales = $stmt->fetchAll();
}

$pageTitle = 'Mis Materiales — Docente';
$extraCSS  = ['css/repositorio.css'];
$isSubfolder = true;
include __DIR__ . '/../includes/header.php';
?>

<div class="sections" style="max-width: 1100px; margin: 0 auto; padding-top: 1.5rem;">

  <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:1.5rem;">
    <div>
      <h2 style="font-family:var(--font-heading); color:var(--color-primary-dark); font-size:1.8rem; margin:0; display:flex; align-items:center; gap:8px;">
        <?= icon('folder', '', 26) ?>
        <span><?= $verTodos ? 'Todos los Materiales (Vista Admin)' : 'Mis Materiales Educativos' ?></span>
      </h2>
      <p style="color:var(--color-text-muted); font-size:0.9rem; margin:4px 0 0 0;">
        Docente: <strong><?= htmlspecialchars($usuarioNombre) ?></strong>
      </p>
    </div>
    <div style="display:flex; gap:10px; flex-wrap:wrap;">
      <a href="subir.php" class="btn btn--primary">
        <?= icon('plus', '', 16) ?> Subir Material
      </a>
      <a href="index.php" class="btn btn--secondary">
        <?= icon('eye', '', 16) ?> Ver Catálogo
      </a>
      <?php if ($isAdmin): ?>
        <a href="mis_materiales.php<?= $verTodos ? '' : '?ver=todos' ?>" class="btn btn--outline">
          <?= $verTodos ? 'Ver solo mis materiales' : 'Ver materiales de todos' ?>
        </a>
        <a href="../admin/index.php" class="btn btn--warning">
          <?= icon('shield', '', 16) ?> Panel Admin
        </a>
      <?php endif; ?>
    </div>
  </div>

  <?php if ($msg): ?>
    <div class="alert-banner alert-banner--<?= $msgType ?>" style="margin-bottom:1.5rem;">
      <span class="alert-banner__icon">
        <?= icon($msgType === 'success' ? 'check-circle' : 'alert-circle', '', 18) ?>
      </span>
      <div class="alert-banner__content">
        <span><?= htmlspecialchars($msg) ?></span>
      </div>
      <button class="alert-banner__close" aria-label="Cerrar aviso">
        <?= icon('x', '', 15) ?>
      </button>
    </div>
  <?php endif; ?>

  <?php if (empty($materiales)): ?>
    <div class="card" style="text-align:center; padding: 3rem 1rem;">
      <div style="color:var(--color-text-muted); margin-bottom:1rem;">
        <?= icon('file-text', '', 48) ?>
      </div>
      <h3 style="color:var(--color-text); margin-bottom:0.5rem;">Aún no has subido ningún material</h3>
      <p style="color:var(--color-text-muted); font-size:0.95rem; margin-bottom:1.5rem;">
        Comparte lecturas, fichas de trabajo, ejercicios o presentaciones para tus alumnos de primaria.
      </p>
      <a href="subir.php" class="btn btn--primary">
        <?= icon('plus', '', 16) ?> Subir mi primer material
      </a>
    </div>
  <?php else: ?>
    <div class="admin-table-wrapper" style="background:#fff; border-radius:16px; box-shadow:var(--shadow-xs); border:1px solid var(--color-border);">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Tipo</th>
            <th>Título</th>
            <th>Área</th>
            <th>Grado</th>
            <th>Tamaño</th>
            <th>Descargas</th>
            <th>Fecha</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($materiales as $mat): ?>
            <?php
              $tamBytes = $mat['tamanio'];
              $tamStr = ($tamBytes >= 1048576)
                  ? number_format($tamBytes / 1048576, 1) . ' MB'
                  : number_format($tamBytes / 1024, 0) . ' KB';
            ?>
            <tr>
              <td style="text-align:center;">
                <span class="badge badge--tipo"><?= strtoupper($mat['tipo_archivo']) ?></span>
              </td>
              <td>
                <strong><?= htmlspecialchars($mat['titulo']) ?></strong>
                <br>
                <small style="color:var(--color-text-muted);"><?= htmlspecialchars($mat['nombre_archivo']) ?></small>
                <?php if ($verTodos): ?>
                  <br><small style="color:var(--color-primary-dark);">Subido por: <?= htmlspecialchars($mat['autor_nombre'] . ' ' . $mat['autor_apellido']) ?></small>
                <?php endif; ?>
              </td>
              <td><span class="badge badge--area"><?= htmlspecialchars($mat['area']) ?></span></td>
              <td><span class="badge badge--grado"><?= htmlspecialchars($mat['grado']) ?></span></td>
              <td style="font-size:0.85rem; color:var(--color-text-muted);"><?= $tamStr ?></td>
              <td style="text-align:center;">
                <strong><?= (int)$mat['descargas'] ?></strong>
              </td>
              <td style="font-size:0.8rem; white-space:nowrap;">
                <?= date('d/m/Y', strtotime($mat['created_at'])) ?>
              </td>
              <td>
                <div style="display:flex; gap:6px; flex-wrap:wrap;">
                  <a href="descargar.php?id=<?= $mat['id'] ?>" class="btn btn--secondary btn--sm" title="Descargar">
                    <?= icon('download', '', 14) ?>
                  </a>
                  <a href="editar.php?id=<?= $mat['id'] ?>" class="btn btn--warning btn--sm" title="Editar">
                    <?= icon('edit', '', 14) ?>
                  </a>
                  <a href="eliminar.php?id=<?= $mat['id'] ?>" class="btn btn--danger btn--sm"
                     onclick="confirmDelete('¿Confirmas eliminar el recurso: <?= htmlspecialchars(addslashes($mat['titulo'])) ?>?', 'eliminar.php?id=<?= $mat['id'] ?>'); return false;"
                     title="Eliminar">
                    <?= icon('trash', '', 14) ?>
                  </a>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <p style="text-align:center; margin-top:16px; color:var(--color-text-muted); font-size:0.85rem;">
      Total: <strong><?= count($materiales) ?></strong> recurso(s) publicado(s).
    </p>
  <?php endif; ?>

</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
