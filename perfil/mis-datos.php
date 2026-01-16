<?php
require_once '../includes/config.php';

if (!estaLogueado()) {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit();
}

$user = obtenerUsuario();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = sanitizar($_POST['nombre']);
    $apellido = sanitizar($_POST['apellido']);
    $telefono = sanitizar($_POST['telefono']);
    $direccion = sanitizar($_POST['direccion']);

    $stmt = $pdo->prepare("UPDATE usuarios SET nombre = ?, apellido = ?, telefono = ?, direccion = ? WHERE id_usuario = ?");
    $stmt->execute([$nombre, $apellido, $telefono, $direccion, $_SESSION['user_id']]);

    $_SESSION['success'] = "Datos actualizados correctamente";
    header("Location: " . BASE_URL . "/perfil/mis-datos.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Datos - AdoptaWeb</title>
    <?php include '../includes/header.php'; ?>
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-3">
                <?php include 'menu-lateral.php'; ?>
            </div>
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header bg-light">
                        <h4>Mis Datos</h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($_SESSION['success'])): ?>
                            <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
                            <?php unset($_SESSION['success']); ?>
                        <?php endif; ?>
                        <form method="POST">
                            <div class="mb-3">
                                <label>Nombre</label>
                                <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($user['nombre']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label>Apellido</label>
                                <input type="text" name="apellido" class="form-control" value="<?= htmlspecialchars($user['apellido']) ?>">
                            </div>
                            <div class="mb-3">
                                <label>Teléfono</label>
                                <input type="text" name="telefono" class="form-control" value="<?= htmlspecialchars($user['telefono']) ?>">
                            </div>
                            <div class="mb-3">
                                <label>Dirección</label>
                                <textarea name="direccion" class="form-control"><?= htmlspecialchars($user['direccion']) ?></textarea>
                            </div>
                            <button type="submit" class="btn btn-success">Guardar cambios</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>