<<<<<<< HEAD
<?php
require_once '../includes/config.php';
/*Funciones sacadas del config para validar todos los datos correctamente y realizar el cambio de contraseña en la
base de datos*/
if (!estaLogueado()) {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit();
}

$user = obtenerUsuario();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $actual = $_POST['actual'] ?? '';
    $nueva = $_POST['nueva'] ?? '';
    $confirmar = $_POST['confirmar'] ?? '';

    if (!password_verify($actual, $user['password_hash'])) {
        $error = 'La contraseña actual es incorrecta.';
    } elseif (strlen($nueva) < 6) {
        $error = 'La nueva contraseña debe tener al menos 6 caracteres.';
    } elseif ($nueva !== $confirmar) {
        $error = 'Las contraseñas no coinciden.';
    } else {
        $hash = password_hash($nueva, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE usuarios SET password_hash = ? WHERE id_usuario = ?");
        $stmt->execute([$hash, $_SESSION['user_id']]);
        $success = 'Contraseña actualizada correctamente.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cambiar Contraseña - AdoptaWeb</title>
    <?php include '../includes/header.php'; ?>

   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
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
                        <h4>Cambiar Contraseña</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?= $success ?></div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label>Contraseña actual</label>
                                <input type="password" name="actual" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Nueva contraseña</label>
                                <input type="password" name="nueva" class="form-control" required minlength="6">
                            </div>
                            <div class="mb-3">
                                <label>Confirmar nueva contraseña</label>
                                <input type="password" name="confirmar" class="form-control" required minlength="6">
                            </div>
                            <button type="submit" class="btn btn-success">Guardar cambios</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
   </div>
<?php include '../includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap.bundle.min.js"></script>
</body>
=======
<?php
require_once '../includes/config.php';

if (!estaLogueado()) {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit();
}

$user = obtenerUsuario();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $actual = $_POST['actual'] ?? '';
    $nueva = $_POST['nueva'] ?? '';
    $confirmar = $_POST['confirmar'] ?? '';

    if (!password_verify($actual, $user['password_hash'])) {
        $error = 'La contraseña actual es incorrecta.';
    } elseif (strlen($nueva) < 6) {
        $error = 'La nueva contraseña debe tener al menos 6 caracteres.';
    } elseif ($nueva !== $confirmar) {
        $error = 'Las contraseñas no coinciden.';
    } else {
        $hash = password_hash($nueva, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE usuarios SET password_hash = ? WHERE id_usuario = ?");
        $stmt->execute([$hash, $_SESSION['user_id']]);
        $success = 'Contraseña actualizada correctamente.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cambiar Contraseña - AdoptaWeb</title>
    <?php include '../includes/header.php'; ?>

   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
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
                        <h4>Cambiar Contraseña</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?= $success ?></div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label>Contraseña actual</label>
                                <input type="password" name="actual" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Nueva contraseña</label>
                                <input type="password" name="nueva" class="form-control" required minlength="6">
                            </div>
                            <div class="mb-3">
                                <label>Confirmar nueva contraseña</label>
                                <input type="password" name="confirmar" class="form-control" required minlength="6">
                            </div>
                            <button type="submit" class="btn btn-success">Guardar cambios</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
   </div>
<?php include '../includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap.bundle.min.js"></script>
</body>
>>>>>>> 9eda46afd468fe512e1c54b728d4cf4768644f34
</html>